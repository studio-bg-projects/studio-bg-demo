<?php

namespace App\Services;

use App\Enums\OrderEventAction;
use App\Enums\OrderEventActorType;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Mail;
use App\Models\Order;
use App\Models\OrdersEvent;
use App\Models\Product;
use App\Services\Jobs\BaseSyncJob;
use Illuminate\Support\Facades\Auth;

class MailMakerService
{
  protected $handler;

  public function setHandler(callable $handler)
  {
    $this->handler = $handler;
  }

  public function api(array $params)
  {
    return $this->send($params);
  }

  public function orderNew(int $orderId)
  {
    return $this->order($orderId, true);
  }

  public function order(int $orderId, $isNewOrder = false)
  {
    /* @var $order Order */
    $order = Order::where('id', $orderId)->first();

    if (!$order) {
      return false;
    }

    $lang = 'bg';
    if (isset($order->shopData->order->language_id) && isset(BaseSyncJob::$languages[$order->shopData->order->language_id])) {
      $lang = strtolower(BaseSyncJob::$languages[$order->shopData->order->language_id]);
    }

    // Products
    $products = [];
    foreach ($order->shopData->order_product ?? [] as $orderProduct) {
      $erpProduct = Product::where(['id' => $orderProduct->product_id])->first();

      $orderProduct->mpn = '-';
      $orderProduct->ean = '-';

      if ($erpProduct) {
        $orderProduct->mpn = $erpProduct->mpn;
        $orderProduct->ean = $erpProduct->ean;
      }

      $products[] = $orderProduct;
    }

    $shopLink = str_replace('{orderId}', $order->id, env('SHOP_ORDER_URL'));

    // Relations
    $customer = Customer::where(['id' => $order->shopData->order->customer_id ?? -1])->first();

    $content = (string)view('mails.order', [
      'isNewOrder' => $isNewOrder,
      'lang' => $lang,
      'order' => $order,
      'products' => $products,
      'customer' => $customer,
      'shopLink' => $shopLink,
    ]);

    $this->send([
      'to' => $order->customer->email,
      'subject' => [
          'bg' => 'Поръчка',
          'en' => 'Order'
        ][$lang] . '#' . $order->id . ' - Inside Trading',
      'content' => $content,
      'lang' => $lang,
    ]);

    return true;
  }

  public function orderNewNotify(int $orderId)
  {
    /* @var $order Order */
    $order = Order::where('id', $orderId)->first();

    if (!$order) {
      return false;
    }

    $lang = 'bg';

    // Relations
    /* @var $customer Customer */
    $customer = Customer::where(['id' => $order->shopData->order->customer_id ?? -1])->first();

    $content = (string)view('mails.order-new-notify', [
      'lang' => $lang,
      'order' => $order,
      'customer' => $customer,
    ]);

    $emails = [dbConfig('mail:shop-notifications')];
    if ($customer->salesRepresentative) {
      $emails[] = $customer->salesRepresentative->email1;
      $emails[] = $customer->salesRepresentative->email2;
    }

    foreach (array_unique($emails) as $email) {
      if ($email) {
        $this->send([
          'to' => $email,
          'subject' => 'Имате нова поръчка #' . $order->id,
          'content' => $content,
          'lang' => $lang,
        ]);
      }
    }

    $this->logOrderEvent($order->id, 'order', [
      'status', $order->status->value,
    ]);

    return true;
  }

  public function customerWelcome(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->first();

    if (!$customer) {
      return false;
    }

    $lang = $customer->preferredLang ?: 'bg';

    $content = (string)view('mails.customer-welcome', [
      'lang' => $lang,
      'customer' => $customer,
    ]);

    $this->send([
      'to' => $customer->email,
      'subject' => [
        'bg' => 'Добре дошли в магазина на Inside Trading – очаквайте одобрение',
        'en' => 'Welcome to the Inside Trading store – pending approval'
      ][$lang],
      'content' => $content,
      'lang' => $lang,
    ]);

    return true;
  }

  public function customerWelcomeNotify(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->first();

    if (!$customer) {
      return false;
    }

    $lang = 'bg';

    $content = (string)view('mails.customer-welcome-notify', [
      'lang' => $lang,
      'customer' => $customer,
    ]);

    $emails = [dbConfig('mail:shop-notifications')];

    foreach (array_unique($emails) as $email) {
      if ($email) {
        $this->send([
          'to' => $email,
          'subject' => 'Нов клиент - ' . $customer->companyName,
          'content' => $content,
          'lang' => $lang,
        ]);
      }
    }

    return true;
  }

  public function customerApproved(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->first();

    if (!$customer) {
      return false;
    }

    $lang = $customer->preferredLang ?: 'bg';

    $content = (string)view('mails.customer-approved', [
      'lang' => $lang,
      'customer' => $customer,
    ]);

    $this->send([
      'to' => $customer->email,
      'subject' => [
        'bg' => 'Одобрение в магазина на Inside Trading',
        'en' => 'Approved for the Inside Trading store'
      ][$lang],
      'content' => $content,
      'lang' => $lang,
    ]);

    return true;
  }

  public function customerCreditLineValue(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->first();

    if (!$customer) {
      return false;
    }

    $lang = $customer->preferredLang ?: 'bg';

    $content = (string)view('mails.customer-credit-line-value', [
      'lang' => $lang,
      'customer' => $customer,
    ]);

    $this->send([
      'to' => $customer->email,
      'subject' => [
        'bg' => 'Информация за вашата кредитна линия – Inside Trading',
        'en' => 'Information about your credit line – pending approval'
      ][$lang],
      'content' => $content,
      'lang' => $lang,
    ]);

    return true;
  }

  public function customerCreditLineRequestNotify(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->first();

    if (!$customer) {
      return false;
    }

    $lang = 'bg';

    $content = (string)view('mails.customer-credit-line-value-notify', [
      'lang' => $lang,
      'customer' => $customer,
    ]);

    $emails = [dbConfig('mail:shop-notifications')];
    if ($customer->salesRepresentative) {
      $emails[] = $customer->salesRepresentative->email1;
      $emails[] = $customer->salesRepresentative->email2;
    }

    foreach (array_unique($emails) as $email) {
      if ($email) {
        $this->send([
          'to' => $email,
          'subject' => 'Заявка за кредитна линия #' . $customer->companyName,
          'content' => $content,
          'lang' => $lang,
        ]);
      }
    }

    return true;
  }

  public function document(int $documentId)
  {
    /* @var $document Document */
    $document = Document::where('id', $documentId)->firstOrFail();

    if (!$document || !$document->customer) {
      return false;
    }

    /* @var $customer Customer */
    $customer = $document->customer;
    $lang = $customer->preferredLang ?: 'bg';

    $content = (string)view('mails.document', [
      'lang' => $lang,
      'document' => $document,
      'customer' => $customer,
    ]);

    $emails = [];
    $emails[] = $customer->email;
    $emails[] = $customer->financialContactEmail;

    foreach (array_unique($emails) as $email) {
      if ($email) {
        $this->send([
          'to' => $email,
          'subject' => [
              'bg' => 'Документ',
              'en' => 'Document'
            ][$lang] . '#' . $document->documentNumber . ' - Inside Trading',
          'content' => $content,
          'lang' => $lang,
        ]);
      }
    }

    if ($document->orderId) {
      $this->logOrderEvent($document->orderId, 'document', [
        'id', $document->id,
        'type', $document->type->value,
      ]);
    }

    return true;
  }

  protected function send($params)
  {
    ksort($params);
    $hash = md5(json_encode($params));
    $exists = Mail::where('hash', $hash)
      ->where('createdAt', '>=', now()->subHour())
      ->first();

    if ($exists) {
      return false;
    }

    if ($this->handler) {
      $params['hash'] = $hash;
      call_user_func($this->handler, $params);
      return true;
    } else {
      $mail = new Mail();
      $mail->to = $params['to'] ?? null;
      $mail->subject = $params['subject'] ?? null;
      $mail->content = $params['content'] ?? null;
      $mail->lang = $params['lang'] ?? null;
      $mail->addHtmlWrapper = !empty($params['addHtmlWrapper']);
      $mail->hash = $hash;
      $mail->save();

      return $mail;
    }
  }

  protected function logOrderEvent(int $orderId, string $emailType, $actionData = null)
  {
    $ordersEvent = new OrdersEvent();
    $ordersEvent->orderId = $orderId;
    $ordersEvent->action = OrderEventAction::SentMail;
    $ordersEvent->actionNote = $emailType;
    $ordersEvent->actionData = $actionData;
    $ordersEvent->actorType = Auth::check() ? OrderEventActorType::Operator : OrderEventActorType::SystemSync;
    $ordersEvent->actorId = Auth::check() ? Auth::id() : null;
    $ordersEvent->save();
  }
}
