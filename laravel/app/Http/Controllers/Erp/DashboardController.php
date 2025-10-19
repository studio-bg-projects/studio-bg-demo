<?php

namespace App\Http\Controllers\Erp;

use App\Enums\CustomerStatusType;
use App\Enums\DocumentType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Order;
use App\Services\MapService;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $chartOrders = $this->chartOrders();
    $pendingOrdersListStatuses = [];
    $pendingOrdersList = $this->pendingOrdersList($pendingOrdersListStatuses);

    $chartCustomers = $this->chartCustomers();
    $waitingCustomersList = $this->waitingCustomersList();

    $unpaidDocumentsTypes = [];
    $unpaidDocumentsList = $this->unpaidDocumentsList($unpaidDocumentsTypes);

    $chartCategoriesProducts = $this->chartCategoriesProducts();
    $chartCustomersGroups = $this->chartCustomersGroups();

    return view('erp.dashboard.index', [
      'chartOrders' => $chartOrders,

      'pendingOrdersListStatuses' => implode(',', $pendingOrdersListStatuses),
      'pendingOrdersList' => $pendingOrdersList,

      'chartCustomers' => $chartCustomers,

      'waitingCustomersList' => $waitingCustomersList,

      'unpaidDocumentsTypes' => implode(',', $unpaidDocumentsTypes),
      'unpaidDocumentsList' => $unpaidDocumentsList,

      'chartCategoriesProducts' => $chartCategoriesProducts,
      'chartCustomersGroups' => $chartCustomersGroups,
    ]);
  }

  public function chartOrders(): array
  {
    $rs = DB::select('
      SELECT
        `date`,
        COUNT(*) AS `cnt`
      FROM
        ( SELECT DATE_FORMAT( `createdAt`, "%Y-%m-%d" ) AS `date` FROM `orders` ) AS `rs`
      GROUP BY
        `date`
      ORDER BY
        `date` ASC
    ');

    // Преобразуваме резултата в асоциативен масив за по-лесна работа
    $data = [];
    foreach ($rs as $order) {
      $data[$order->date] = $order->cnt;
    }

    // Вземаме началната и крайната дата от резултата
    $startDate = new DateTime(array_key_first($data));
    $endDate = new DateTime();

    // Създаваме масив с всички дати в диапазона
    $currentDate = $startDate;
    $filledRs = [];

    while ($currentDate <= $endDate) {
      $dateString = $currentDate->format('Y-m-d');

      // Ако датата не съществува в оригиналните резултати, добавяме стойност 0
      $filledRs[] = [
        'date' => $dateString,
        'cnt' => $data[$dateString] ?? 0
      ];

      // Прибавяме един ден към текущата дата
      $currentDate->modify('+1 day');
    }

    return $filledRs;
  }

  public function pendingOrdersList(&$activeStatuses = [])
  {
    foreach (OrderStatus::cases() as $status) {
      if (!MapService::orderStatus($status)->isCompleted) {
        $activeStatuses[] = $status->value;
      }
    }

    return Order::whereIn('status', $activeStatuses)
      ->orderBy('createdAt', 'desc')
      ->get();
  }

  public function unpaidDocumentsList(&$unpaidDocumentsTypes = [])
  {
    foreach (DocumentType::cases() as $type) {
      if (MapService::documentTypes($type)->isPayable) {
        $unpaidDocumentsTypes[] = $type->value;
      }
    }

    return Document::whereIn('type', $unpaidDocumentsTypes)
      ->where('leftAmount', '!=', 0)
      ->orderBy('createdAt', 'desc')
      ->get();
  }

  public function chartCustomers(): array
  {
    $rs = DB::select('
      SELECT
        `date`,
        COUNT(*) AS `cnt`
      FROM
        ( SELECT DATE_FORMAT( `createdAt`, "%Y-%m-%d" ) AS `date` FROM `customers` ) AS `rs`
      GROUP BY
        `date`
      ORDER BY
        `date` ASC
    ');

    // Преобразуваме резултата в асоциативен масив за по-лесна работа
    $data = [];
    foreach ($rs as $order) {
      $data[$order->date] = $order->cnt;
    }

    // Вземаме началната и крайната дата от резултата
    $startDate = new DateTime(array_key_first($data));
    $endDate = new DateTime();

    // Създаваме масив с всички дати в диапазона
    $currentDate = $startDate;
    $filledRs = [];

    while ($currentDate <= $endDate) {
      $dateString = $currentDate->format('Y-m-d');

      // Ако датата не съществува в оригиналните резултати, добавяме стойност 0
      $filledRs[] = [
        'date' => $dateString,
        'cnt' => $data[$dateString] ?? 0
      ];

      // Прибавяме един ден към текущата дата
      $currentDate->modify('+1 day');
    }

    return $filledRs;
  }

  public function waitingCustomersList()
  {
    return Customer::where('statusType', CustomerStatusType::WaitingApproval->value)
      ->orWhere('creditLineRequested', true)
      ->orderBy('createdAt', 'desc')
      ->get();
  }

  public function chartCategoriesProducts(): array
  {
    return DB::select('
      SELECT
        `c1`.`id` AS `id`,
        `c1`.`nameBg` AS `name`,
        COUNT( `cp`.`productId` ) AS `count`
      FROM
        `categories` AS `c1`
        LEFT JOIN `categories` AS `c2` ON `c2`.`parentId` = `c1`.`id`
        LEFT JOIN `categoriesProducts` AS `cp` ON `cp`.`categoryId` = `c1`.`id`
        OR `cp`.`categoryId` = `c2`.`id`
      WHERE
        `c1`.`parentId` IS NULL
      GROUP BY
        `c1`.`id`,
        `c1`.`nameBg`
    ');
  }

  public function chartCustomersGroups(): array
  {
    return DB::select('
      SELECT
        `cg`.`id` AS `id`,
        `cg`.`nameBg` AS `name`,
        COUNT( `c`.`groupId` ) AS `count`
      FROM
        `customersGroups` AS `cg`
        LEFT JOIN `customers` AS `c` ON `c`.`groupId` = `cg`.`id`
      GROUP BY
        `cg`.`id`,
        `cg`.`nameBg`
    ');
  }
}
