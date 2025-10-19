<?php

namespace App\Services\Jobs;

use App\Enums\OrderEventAction;
use App\Enums\OrderEventActorType;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrdersEvent;
use App\Services\MailMakerService;
use App\Services\MapService;
use Exception;
use Illuminate\Support\Carbon;

class SyncOrdersJob extends BaseSyncJob
{
  public function run(): void
  {
    // Build maps
    $erpOrdersMap = [];
    foreach (Order::select(['id', 'updatedAt'])->get() as $order) {
      $erpOrdersMap[$order->id] = $order->updatedAt;
    }

    $shopOrdersMap = [];
    foreach ($this->shopConn()->table(self::PREFIX . 'order')->select(['order_id', 'order_status_id', 'date_modified'])->get() as $order) {
      if ($order->order_status_id == 0) {
        continue;
      }

      $shopOrdersMap[$order->order_id] = Carbon::parse($order->date_modified);
    }

    // Add to erp
    foreach ($shopOrdersMap as $shopOrderId => $shopUpdatedAt) {
      if (!isset($erpOrdersMap[$shopOrderId])) {
        $this->addToErp($shopOrderId);
      }
    }

    // Add to shop
    foreach ($erpOrdersMap as $erpOrderId => $erpUpdatedAt) {
      if (!isset($shopOrdersMap[$erpOrderId])) {
        $this->addToShop($erpOrderId);
      }
    }

    // Update in erp
    foreach ($shopOrdersMap as $shopOrderId => $shopUpdatedAt) {
      if (isset($erpOrdersMap[$shopOrderId]) && $shopUpdatedAt->gt($erpOrdersMap[$shopOrderId])) {
        $this->updateToErp($shopOrderId);
      }
    }

    // Update in shop
    foreach ($erpOrdersMap as $erpOrderId => $erpUpdatedAt) {
      if (isset($shopOrdersMap[$erpOrderId]) && $erpUpdatedAt->gt($shopOrdersMap[$erpOrderId])) {
        $this->updateToShop($erpOrderId);
      }
    }

    // Send notifications
    $this->sendNotifications();

    $this->out('All good :)');
  }

  protected function addToErp(int $shopOrderId): void
  {
    try {
      $erpOrder = new Order();
      $erpOrder->id = $shopOrderId;

      $this->setErpOrder($erpOrder, $shopOrderId);

      $this->out(sprintf('Add order %s to ERP', $shopOrderId));

      // Log
      $ordersEvent = new OrdersEvent();
      $ordersEvent->orderId = $shopOrderId;
      $ordersEvent->action = OrderEventAction::Create;
      $ordersEvent->actorType = OrderEventActorType::SystemSync;
      $ordersEvent->save();

      // Send mails
      $mailMaker = new MailMakerService();
      $mailMaker->orderNew($shopOrderId);
      $mailMaker->orderNewNotify($shopOrderId);
    } catch (Exception $e) {
      $this->out('Error: ' . $e->getMessage());
    }
  }

  protected function updateToErp(int $shopOrderId): void
  {
    try {
      $erpOrder = Order::select()->where(['id' => $shopOrderId])->first();

      $this->setErpOrder($erpOrder, $shopOrderId);

      $this->out(sprintf('Update order %s to ERP', $shopOrderId));
    } catch (Exception $e) {
      $this->out('Error: ' . $e->getMessage());
    }
  }

  protected function setErpOrder(Order $erpOrder, $shopOrderId): void
  {
    $shopOrder = $this->shopConn()->table(self::PREFIX . 'order')->select()->where(['order_id' => $shopOrderId])->first();

    $hasStatus = false;
    $statusIsChanged = false;
    foreach (\App\Enums\OrderStatus::cases() as $status) {
      if (MapService::orderStatus($status)->shopId === $shopOrder->order_status_id) {
        $hasStatus = true;

        if ($erpOrder->status !== $status) {
          $erpOrder->status = $status;
          $statusIsChanged = true;
        }
        break;
      }
    }

    if (!$hasStatus) {
      throw new Exception(sprintf('Unknown order status %s', $shopOrder->order_status_id));
    }

    if (!Customer::where('id', $shopOrder->customer_id)->exists()) {
      throw new Exception(sprintf('Missing customer %s', $shopOrder->customer_id));
    }

    $erpOrder->customerId = $shopOrder->customer_id;
    $erpOrder->shopData = [
      'order' => $shopOrder,
      'order_total' => $this->shopConn()->table(self::PREFIX . 'order_total')->where('order_id', $shopOrder->order_id)->get(),
      'order_product' => $this->shopConn()->table(self::PREFIX . 'order_product')->where('order_id', $shopOrder->order_id)->get(),
      'order_history' => $this->shopConn()->table(self::PREFIX . 'order_history')->where('order_id', $shopOrder->order_id)->get(),
      'shipping_address' => $this->shopConn()->table(self::PREFIX . 'address')->where('address_id', $shopOrder->shipping_address_id)->first(),
      // 'order_option' => $this->shopConn()->table(self::PREFIX . 'order_option')->where('order_id', $shopOrder->order_id)->get(),
      // 'order_voucher' => $this->shopConn()->table(self::PREFIX . 'order_voucher')->where('order_id', $shopOrder->order_id)->get(),
      // 'order_subscription' => $this->shopConn()->table(self::PREFIX . 'order_subscription')->where('order_id', $shopOrder->order_id)->get(),
      // 'payment_address' => $this->shopConn()->table(self::PREFIX . 'address')->where('address_id', $shopOrder->payment_address_id)->first(),
    ];
    $erpOrder->updatedAt = $shopOrder->date_modified;
    $erpOrder->save();

    if ($erpOrder->customer) {
      $erpOrder->customer->recalc();
    }

    if ($statusIsChanged) {
      $ordersEvent = new OrdersEvent();
      $ordersEvent->orderId = $shopOrderId;
      $ordersEvent->action = OrderEventAction::SetStatus;
      $ordersEvent->actionNote = $erpOrder->status;
      $ordersEvent->actorType = OrderEventActorType::SystemSync;
      $ordersEvent->save();
    }
  }

  protected function addToShop(int $erpOrderId): void
  {
    $this->out(sprintf('Add order %s to SHOP', $erpOrderId));

    $erpOrder = Order::select()->where(['id' => $erpOrderId])->first();

    $this->shopConn()->table(self::PREFIX . 'order')->insert((array)$erpOrder->shopData->order);

    $tables = [
      'order_total' => 'order_total',
      'order_product' => 'order_product',
      'order_history' => 'order_history',
    ];

    foreach ($tables as $table) {
      foreach ($erpOrder->shopData->{$table} as $row) {
        $row = (array)$row;

        if (isset($row[$table . '_id'])) {
          unset($row[$table . '_id']);
        }

        $this->shopConn()->table(self::PREFIX . $table)->insert($row);
      }
    }

    // Update back to erp to get the record ids
    $this->updateToErp($erpOrderId);
  }

  protected function updateToShop(int $erpOrderId): void
  {
    $erpOrder = Order::select()->where(['id' => $erpOrderId])->first();

    $this->shopConn()->table(self::PREFIX . 'order')
      ->where('order_id', '=', $erpOrderId)
      ->update((array)$erpOrder->shopData->order);

    $tables = [
      'order_total' => 'order_total',
      'order_product' => 'order_product',
      'order_history' => 'order_history',
    ];

    foreach ($tables as $table) {
      $idColumn = $table . '_id';
      $expectedIds = [];

      foreach ($erpOrder->shopData->{$table} as $row) {
        $row = (array)$row;

        if (!isset($row[$idColumn])) {
          $insertedId = $this->shopConn()->table(self::PREFIX . $table)->insertGetId($row, $idColumn);
          $expectedIds[] = $insertedId;

          continue;
        }

        $expectedIds[] = $row[$idColumn];

        $this->shopConn()->table(self::PREFIX . $table)->updateOrInsert(
          [$idColumn => $row[$idColumn]],
          $row
        );
      }

      $existingIds = $this->shopConn()->table(self::PREFIX . $table)
        ->where('order_id', $erpOrderId)
        ->pluck($idColumn)
        ->toArray();

      $idsToDelete = array_diff($existingIds, $expectedIds);

      if (!empty($idsToDelete)) {
        $this->shopConn()->table(self::PREFIX . $table)
          ->where('order_id', $erpOrderId)
          ->whereIn($idColumn, $idsToDelete)
          ->delete();
      }
    }

    // Set modified date
    $this->shopConn()->table(self::PREFIX . 'order')
      ->where('order_id', $erpOrderId)
      ->update([
        'date_modified' => $erpOrder->updatedAt,
      ]);

    $this->out(sprintf('Update order %s to SHOP', $erpOrderId));
  }

  protected function sendNotifications(): void
  {
    $notifications = $this->shopConn()->table(self::PREFIX . 'order_history')->whereColumn('notify', '>', '_erp_notified')->get();
    foreach ($notifications as $row) {
      // Send email
      $mailMaker = new MailMakerService();
      $prepared = $mailMaker->order($row->order_id);

      if ($prepared) {
        $this->out('Send mail for order #' . $row->order_id);
      }

      // Mark as notified
      $this->shopConn()->table(self::PREFIX . 'order_history')
        ->where('order_history_id', $row->order_history_id)
        ->update([
          '_erp_notified' => $row->notify,
        ]);
    }
  }
}
