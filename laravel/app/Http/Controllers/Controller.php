<?php

namespace App\Http\Controllers;

use App\Enums\CustomerStatusType;
use App\Enums\DocumentType;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Order;
use App\Models\FeedImportItem;
use App\Services\MapService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

abstract class Controller extends BaseController
{
  use AuthorizesRequests, ValidatesRequests;

  public function __construct()
  {
    // Waiting customers
    $waitingCustomers = Customer::where('statusType', CustomerStatusType::WaitingApproval->value)->count();

    View::composer('*', function ($view) use ($waitingCustomers) {
      $view->with('waitingCustomers', $waitingCustomers);
    });

    // Waiting customers (credit line)
    $waitingCustomersCreditLine = Customer::where('creditLineRequested', true)->count();

    View::composer('*', function ($view) use ($waitingCustomersCreditLine) {
      $view->with('waitingCustomersCreditLine', $waitingCustomersCreditLine);
    });

    // Waiting orders
    $statuses = [];
    foreach (OrderStatus::cases() as $status) {
      if (!MapService::orderStatus($status)->isCompleted) {
        $statuses[] = $status->value;
      }
    }
    $waitingOrders = Order::whereIn('status', $statuses)->count();

    View::composer('*', function ($view) use ($waitingOrders) {
      $view->with('waitingOrders', $waitingOrders);
    });

    // Unpaid documents
    $payableDocumentsTypes = [];
    foreach (DocumentType::cases() as $type) {
      if (MapService::documentTypes($type)->isPayable) {
        $payableDocumentsTypes[] = $type->value;
      }
    }
    $unpaidDocuments = Document::where('leftAmount', '>', '0')->
    whereIn('type', $payableDocumentsTypes)
      ->count();

    View::composer('*', function ($view) use ($unpaidDocuments) {
      $view->with('unpaidDocuments', $unpaidDocuments);
    });

    // Feed import items - unlinked
    $unlinkedItemsCount = FeedImportItem::whereNull('productId')
      ->where('skipSync', '=', 0)
      ->count();

    View::composer('*', function ($view) use ($unlinkedItemsCount) {
      $view->with('unlinkedItemsCount', $unlinkedItemsCount);
    });

    // Feed import items - conflicts
    $conflictsItemsCount = FeedImportItem::query()
      ->selectRaw('productId')
      ->whereNotNull('productId')
      ->groupBy('productId')
      ->havingRaw('COUNT(*) > 1')
      ->havingRaw('SUM(isLeadRecord) = 0')
      ->get()
      ->count();

    View::composer('*', function ($view) use ($conflictsItemsCount) {
      $view->with('conflictsItemsCount', $conflictsItemsCount);
    });
  }
}
