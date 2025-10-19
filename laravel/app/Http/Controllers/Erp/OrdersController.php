<?php

namespace App\Http\Controllers\Erp;

use App\Enums\OrderEventAction;
use App\Enums\OrderEventActorType;
use App\Enums\OrderStatus;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomersAddress;
use App\Models\Document;
use App\Models\IncomesAllocation;
use App\Models\Order;
use App\Models\OrdersEvent;
use App\Models\Product;
use App\Services\Jobs\BaseSyncJob;
use App\Services\MailMakerService;
use App\Services\MapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Enum;

class OrdersController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $ordersQuery = Order::query();
    $ordersQuery = $this->applySort($ordersQuery);
    $ordersQuery = $this->applyFilter($ordersQuery);
    $orders = $ordersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.orders.index', [
      'orders' => $orders,
    ]);
  }

  public function view(int $orderId)
  {
    /* @var $order Order */
    $order = Order::where('id', $orderId)->firstOrFail();

    // Products
    $products = [];
    foreach ($order->shopData->order_product ?? [] as $orderProduct) {
      $erpProduct = Product::where(['id' => $orderProduct->product_id])->first();

      $orderProduct->mpn = '-';
      $orderProduct->ean = '-';
      $orderProduct->image = asset('img/icons/file-placeholder.svg');

      if ($erpProduct) {
        $orderProduct->mpn = $erpProduct->mpn;
        $orderProduct->ean = $erpProduct->ean;

        if ($erpProduct->uploads->isNotEmpty()) {
          $orderProduct->image = $erpProduct->uploads->first()->urls->tiny;
        }
      }

      $products[] = $orderProduct;
    }

    // Relations
    $customer = Customer::where(['id' => $order->shopData->order->customer_id ?? -1])->first();

    return view('erp.orders.view', [
      'order' => $order,
      'products' => $products,
      'customer' => $customer,
    ]);
  }

  public function prepare(Request $request)
  {
    $customerId = $request->input('customerId');

    $customers = Customer::orderBy('companyName')->get();

    return view('erp.orders.prepare', [
      'customerId' => $customerId,
      'customers' => $customers,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $order = new Order();

    $customerId = (int)$request->input('customerId');
    $customerAddressId = (int)$request->input('customerAddressId');

    /* @var $customer Customer|null */
    $customer = $customerId ? Customer::find($customerId) : null;
    /* @var $customerAddress CustomersAddress|null */
    $customerAddress = $customerAddressId ? CustomersAddress::with('country')->find($customerAddressId) : null;

    if (!$customer || !$customerAddress || $customerAddress->customerId !== $customer->id) {
      $errorBag = new MessageBag();

      if (!$customer) {
        $errorBag->add('customerId', 'Моля, изберете валиден клиент.');
      }

      if (!$customerAddress) {
        $errorBag->add('customerAddressId', 'Моля, изберете валиден адрес.');
      } elseif ($customer && $customerAddress->customerId !== $customer->id) {
        $errorBag->add('customerAddressId', 'Избраният адрес не принадлежи на този клиент.');
      }

      if ($errorBag->isNotEmpty()) {
        return redirect('/erp/orders/prepare')
          ->with('errors', $errorBag);
      }
    }

    $nextOrderId = ((int)Order::max('id')) + 1;
    if ($nextOrderId <= 0) {
      $nextOrderId = 1;
    }

    $comment = trim($request->input('comment'));
    $orderTotalFormValues = $this->extractOrderTotalFormValues($request);
    $orderProductFormValues = $this->extractOrderProductFormValues($request);
    $productsMap = $this->loadProductsForOrderProducts($orderProductFormValues);

    $order->id = $nextOrderId;
    $order->customerId = $customer->id;
    $order->status = OrderStatus::Pending;
    $order->status = $this->resolveOrderStatusFromRequest($request, $order->status);

    if ($request->isMethod('post')) {
      $validator = $this->makeOrderFormValidator($request, $orderProductFormValues, $productsMap);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $formattedOrderTotals = $this->formatOrderTotalValues($orderTotalFormValues);
        $formattedOrderProducts = $this->buildOrderProducts(
          $order->id,
          $orderProductFormValues,
          $productsMap,
          true
        );
        $formattedOrderTotals = $this->recalculateOrderTotals($formattedOrderTotals, $formattedOrderProducts);
        $now = now()->format('Y-m-d H:i:s');
        $shopData = $this->buildNewOrderShopData(
          $order->id,
          $customer,
          $customerAddress,
          $comment,
          $order->status,
          $formattedOrderTotals,
          $formattedOrderProducts,
          $now
        );
        $order->shopData = $shopData;
        $order->save();
        $this->logOrderStatusEvent($order, $shopData, $now);
        $order->shopData = $shopData;
        $order->save();

        $customer->recalc();

        $extraInfo = '';

        if ($request->boolean('sendOrderMail')) {
          $mailMaker = new MailMakerService();
          $mailMaker->order($order->id);

          $extraInfo .= "\n✉️ Клиентът беше уведомен за поръчката по имейл.";
        }

        return redirect('/erp/orders/update/' . $order->id)
          ->with('success', 'Успешно създадохте поръчката.' . $extraInfo);
      }
    }

    $order->shopData = $this->convertArrayToObject(
      $this->buildNewOrderShopData(
        $order->id,
        $customer,
        $customerAddress,
        $comment,
        $order->status,
        $orderTotalFormValues,
        $this->buildOrderProducts($order->id, $orderProductFormValues, $productsMap, false)
      )
    );

    $orderProductsForView = $this->prepareOrderProductsForView($orderProductFormValues, $productsMap);

    return view('erp.orders.create', [
      'order' => $order,
      'customer' => $customer,
      'errors' => $errors,
      'orderProducts' => $orderProductsForView,
    ]);
  }

  public function update(int $orderId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $order Order */
    $order = Order::where('id', $orderId)->firstOrFail();
    $previousStatus = $order->status;
    $order->status = $this->resolveOrderStatusFromRequest($request, $order->status);
    $orderTotalFormValues = $this->extractOrderTotalFormValues($request);
    if ($request->isMethod('post')) {
      $orderProductFormValues = $this->extractOrderProductFormValues($request);
    } else {
      $orderProductFormValues = $this->extractOrderProductsFromShopData($order);
    }
    $productsMap = $this->loadProductsForOrderProducts($orderProductFormValues);

    if ($request->isMethod('post')) {
      $validator = $this->makeOrderFormValidator($request, $orderProductFormValues, $productsMap);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $shopData = json_decode(json_encode($order->shopData), true) ?: [];
        $comment = trim($request->input('comment'));
        $formattedOrderTotals = $this->formatOrderTotalValues($orderTotalFormValues);
        $formattedOrderProducts = $this->buildOrderProducts(
          $order->id,
          $orderProductFormValues,
          $productsMap,
          true
        );
        $formattedOrderTotals = $this->recalculateOrderTotals($formattedOrderTotals, $formattedOrderProducts);

        $now = now()->format('Y-m-d H:i:s');
        $newOrderStatusId = MapService::orderStatus($order->status)->shopId;
        $shopData['order']['comment'] = $comment;
        $shopData['order']['date_modified'] = $now;
        $shopData['order']['total'] = $formattedOrderTotals['total'] ?? '0';
        $shopData['order_total'] = $this->buildOrderTotals($order->id, $formattedOrderTotals);
        $shopData['order_product'] = $formattedOrderProducts;
        $shopData['order']['order_status_id'] = $newOrderStatusId;
        $this->logOrderStatusEvent($order, $shopData, $now, $previousStatus, $newOrderStatusId);

        $order->shopData = $shopData;
        $order->save();

        if ($order->customer) {
          $order->customer->recalc();
        }

        $extraInfo = '';

        if ($request->boolean('sendOrderMail')) {
          $mailMaker = new MailMakerService();
          $mailMaker->order($order->id);

          $extraInfo .= "\n✉️ Клиентът беше уведомен за поръчката по имейл.";
        }

        return redirect('/erp/orders/update/' . $order->id)
          ->with('success', 'Успешно редактирахте поръчката.' . $extraInfo);
      } else {
        $shopData = json_decode(json_encode($order->shopData), true) ?: [];
        $shopData['order']['comment'] = trim($request->input('comment'));
        $shopData['order_total'] = $this->buildOrderTotals($order->id, $orderTotalFormValues);
        $shopData['order']['total'] = $orderTotalFormValues['total'] ?? ($shopData['order']['total'] ?? '0.00');
        $shopData['order_product'] = $this->buildOrderProducts($order->id, $orderProductFormValues, $productsMap, false);
        $shopData['order']['order_status_id'] = MapService::orderStatus($order->status)->shopId;
        $order->shopData = $this->convertArrayToObject($shopData);
      }
    }

    $orderProductsForView = $this->prepareOrderProductsForView($orderProductFormValues, $productsMap);

    return view('erp.orders.update', [
      'order' => $order,
      'customer' => $order->customer,
      'errors' => $errors,
      'orderProducts' => $orderProductsForView,
    ]);
  }

  protected function buildNewOrderShopData(int $orderId, Customer $customer, CustomersAddress $address, ?string $comment, OrderStatus $status, array $orderTotalValues = [], array $orderProductValues = [], ?string $timestamp = null): array
  {
    $now = $timestamp ?: now()->format('Y-m-d H:i:s');
    $languageId = $this->resolveLanguageId($customer->preferredLang);
    $email = $address->email ?: $customer->email;
    $shippingCountry = $address->country ? $address->country->name : null;
    $orderStatusId = MapService::orderStatus($status)->shopId;

    // !IMPORTANT - ALL FIELDS MUST BE MAPPED WITH SHOP TABLES AND FIELDS! //
    return [
      'order' => [
        'ip' => '0.0.0.0',
        'email' => $email,
        'total' => $orderTotalValues['total'] ?? '0.00',
        'comment' => trim($comment),
        'lastname' => $address->lastName,
        'order_id' => $orderId,
        'store_id' => 0,
        'tracking' => '',
        'firstname' => $address->firstName,
        'store_url' => env('SHOP_URL'),
        'telephone' => (string)$address->phone,
        'commission' => '0.00',
        'date_added' => $now,
        'invoice_no' => 0,
        'store_name' => 'Inside Trading',
        'user_agent' => '',
        'currency_id' => 3,
        'customer_id' => $customer->id,
        'language_id' => $languageId,
        'affiliate_id' => 0,
        'custom_field' => '{}',
        'forwarded_ip' => '',
        'marketing_id' => 0,
        'payment_city' => '',
        'payment_zone' => '',
        'currency_code' => 'EUR',
        'date_modified' => $now,
        'language_code' => '',
        'shipping_city' => $address->city,
        'shipping_zone' => '',
        'currency_value' => '1',
        'invoice_prefix' => 'ERP-UNKNOWN',
        'payment_method' => json_encode([
          'code' => 'bank_transfer.bank_transfer',
          'name' => 'Банков превод',
        ]),
        'transaction_id' => 0,
        'accept_language' => '',
        'order_status_id' => $orderStatusId,
        'payment_company' => '',
        'payment_country' => '',
        'payment_zone_id' => 0,
        'shipping_method' => '{}',
        'subscription_id' => 0,
        'payment_lastname' => '',
        'payment_postcode' => '',
        'shipping_company' => $customer->id,
        'shipping_country' => $shippingCountry,
        'shipping_zone_id' => 0,
        'customer_group_id' => 0,
        'payment_address_1' => '',
        'payment_address_2' => '',
        'payment_firstname' => '',
        'shipping_lastname' => $address->lastName,
        'shipping_postcode' => $address->zipCode,
        'payment_address_id' => 0,
        'payment_country_id' => 0,
        'shipping_address_1' => $address->street,
        'shipping_address_2' => $address->addressDetails,
        'shipping_firstname' => $address->firstName,
        'shipping_address_id' => $address->id,
        'shipping_country_id' => $address->countryId,
        'payment_custom_field' => '[]',
        'shipping_custom_field' => '',
        'payment_address_format' => '',
        'shipping_address_format' => "{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{city}, {postcode}\r\n{country}",
      ],
      'order_total' => $this->buildOrderTotals($orderId, $orderTotalValues),
      'order_product' => $orderProductValues,
      'order_history' => [],
      'shipping_address' => [
        'address_id' => $address->id,
        'customer_id' => $customer->id,
        'firstname' => $address->firstName,
        'lastname' => $address->lastName,
        'company' => $address->companyName ?: $customer->companyName,
        'address_1' => $address->street,
        'address_2' => $address->addressDetails,
        'city' => $address->city,
        'postcode' => $address->zipCode,
        'country' => $shippingCountry,
        'country_id' => $address->countryId,
        'zone_id' => 0,
        'city_speedy_id' => $address->citySpeedyId,
        'street_speedy_id' => $address->streetSpeedyId,
        'street_no' => $address->streetNo,
        'block_no' => $address->blockNo,
        'entrance_no' => $address->entranceNo,
        'floor' => $address->floor,
        'apartment_no' => $address->apartmentNo,
        'custom_field' => '',
        'default' => '',
        '_is_deleted' => false,
      ],
    ];
  }

  protected function logOrderStatusEvent(Order $order, array &$shopData, string $timestamp, ?OrderStatus $previousStatus = null, ?int $orderStatusId = null): void
  {
    if ($previousStatus !== null && $previousStatus === $order->status) {
      return;
    }

    $resolvedStatusId = $orderStatusId ?? MapService::orderStatus($order->status)->shopId;
    $orderHistory = $shopData['order_history'] ?? [];
    $orderHistory[] = [
      'order_history_id' => null,
      'order_id' => $order->id,
      'order_status_id' => $resolvedStatusId,
      'notify' => 0,
      'comment' => '',
      'date_added' => $timestamp,
      '_erp_notified' => 0,
    ];
    $shopData['order_history'] = $orderHistory;

    $ordersEvent = new OrdersEvent();
    $ordersEvent->orderId = $order->id;
    $ordersEvent->action = OrderEventAction::SetStatus;
    $ordersEvent->actionNote = $order->status;
    $ordersEvent->actorType = OrderEventActorType::Operator;
    $ordersEvent->save();
  }

  protected function extractOrderTotalFormValues(Request $request): array
  {
    $fields = [
      'sub_total' => $request->input('order_total_sub_total'),
      'shipping' => $request->input('order_total_shipping'),
      'total' => $request->input('order_total_total'),
    ];

    $values = [];
    foreach ($fields as $code => $value) {
      if (is_string($value)) {
        $value = trim($value);
        if ($value === '') {
          $value = null;
        }
      }

      $values[$code] = $value;
    }

    return $values;
  }

  protected function formatOrderTotalValues(array $values): array
  {
    $formatted = [];

    foreach ($values as $code => $value) {
      if ($value === null || $value === '') {
        $formatted[$code] = null;
        continue;
      }

      $normalized = str_replace(',', '.', (string)$value);
      $formatted[$code] = number_format((double)$normalized, 2, '.', '');
    }

    return $formatted;
  }

  protected function buildOrderTotals(int $orderId, array $values): array
  {
    $definitions = [
      'sub_total' => [
        'title' => 'Стойност',
        'sort_order' => 1,
      ],
      'shipping' => [
        'title' => 'Speedy - Стандартна услуга',
        'sort_order' => 3,
      ],
      'total' => [
        'title' => 'Общо',
        'sort_order' => 9,
      ],
    ];

    $orderTotals = [];

    foreach ($definitions as $code => $definition) {
      $orderTotals[] = [
        'order_total_id' => null,
        'code' => $code,
        'title' => $definition['title'],
        'value' => (string)($values[$code] ?? 0),
        'order_id' => $orderId,
        'extension' => 'opencart',
        'sort_order' => $definition['sort_order'],
      ];
    }

    return $orderTotals;
  }

  protected function recalculateOrderTotals(array $orderTotalValues, array $orderProducts): array
  {
    $subTotal = 0.0;

    foreach ($orderProducts as $orderProduct) {
      $total = $orderProduct['total'] ?? null;
      if ($total !== null && $total !== '') {
        $subTotal += (double)str_replace(',', '.', (string)$total);
        continue;
      }

      $priceValue = $this->normalizeDecimal($orderProduct['price'] ?? null);
      $quantityValue = $this->normalizeInteger($orderProduct['quantity'] ?? null);

      if ($priceValue !== null && $quantityValue !== null) {
        $subTotal += $priceValue * $quantityValue;
      }
    }

    $shippingRaw = $orderTotalValues['shipping'] ?? null;
    $shippingAmount = 0.0;
    if ($shippingRaw === null || $shippingRaw === '') {
      $orderTotalValues['shipping'] = null;
    } else {
      $shippingDecimal = $this->normalizeDecimal($shippingRaw) ?? 0.0;
      $shippingAmount = $shippingDecimal;
      $orderTotalValues['shipping'] = $this->formatDecimal($shippingDecimal);
    }

    $orderTotalValues['sub_total'] = $this->formatDecimal($subTotal);
    $orderTotalValues['total'] = $this->formatDecimal($subTotal + $shippingAmount);

    return $orderTotalValues;
  }

  protected function makeOrderFormValidator(Request $request, array $orderProductValues, array $productsMap)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'comment' => ['nullable', 'string', 'max:2000'],
        'order_total_sub_total' => ['nullable', 'regex:/^-?\d+(?:[.,]\d+)?$/'],
        'order_total_shipping' => ['nullable', 'regex:/^-?\d+(?:[.,]\d+)?$/'],
        'order_total_total' => ['nullable', 'regex:/^-?\d+(?:[.,]\d+)?$/'],
        'order_products' => ['required', 'array', 'min:1'],
        'status' => ['required', new Enum(OrderStatus::class)],
        'sendOrderMail' => ['nullable', 'boolean'],
      ],
      [
        'order_total_sub_total.regex' => 'Моля, въведете валидна сума.',
        'order_total_shipping.regex' => 'Моля, въведете валидна сума.',
        'order_total_total.regex' => 'Моля, въведете валидна сума.',
        'order_products.required' => 'Моля, добавете поне един продукт към поръчката.',
        'order_products.min' => 'Моля, добавете поне един продукт към поръчката.',
        'order_products.array' => 'Моля, добавете поне един продукт към поръчката.',
      ],
      [
        'order_total_sub_total' => 'Стойност',
        'order_total_shipping' => 'Speedy - Стандартна услуга',
        'order_total_total' => 'Общо',
        'order_products' => 'Продукти',
        'status' => 'Статус',
        'sendOrderMail' => 'Изпрати имейл',
      ]
    );

    $validator->after(function ($validator) use ($orderProductValues, $productsMap) {
      foreach ($orderProductValues as $index => $row) {
        $baseKey = 'order_products.' . $index;

        $productId = $row['product_id'] ?? null;
        if ($productId === null || $productId === '') {
          $validator->errors()->add($baseKey . '.product_id', 'Моля, изберете продукт.');
          continue;
        }

        $productId = (int)$productId;
        $product = $productsMap[$productId] ?? null;
        if (!$product) {
          $validator->errors()->add($baseKey . '.product_id', 'Избраният продукт не е валиден.');
        }

        $quantityRaw = $row['quantity'] ?? null;
        if ($quantityRaw === null || $quantityRaw === '') {
          $validator->errors()->add($baseKey . '.quantity', 'Моля, въведете количество.');
        } else {
          $quantityValue = $this->normalizeInteger($quantityRaw);
          if ($quantityValue === null) {
            $validator->errors()->add($baseKey . '.quantity', 'Моля, въведете количество.');
          } elseif ($quantityValue < 1) {
            $validator->errors()->add($baseKey . '.quantity', 'Минималното количество е 1.');
          }
        }

        $priceRaw = $row['price'] ?? null;
        if ($priceRaw === null || $priceRaw === '') {
          $validator->errors()->add($baseKey . '.price', 'Моля, въведете единична цена.');
        } elseif ($this->normalizeDecimal($priceRaw) === null) {
          $validator->errors()->add($baseKey . '.price', 'Моля, въведете валидна единична цена.');
        }
      }
    });

    return $validator;
  }

  protected function resolveOrderStatusFromRequest(Request $request, ?OrderStatus $default = null): OrderStatus
  {
    $value = $request->input('status');

    if ($value instanceof OrderStatus) {
      return $value;
    }

    if (is_string($value) && $value !== '') {
      $status = OrderStatus::tryFrom($value);
      if ($status) {
        return $status;
      }
    }

    return $default ?? OrderStatus::Pending;
  }

  protected function extractOrderProductFormValues(Request $request): array
  {
    $rows = $request->input('order_products', []);

    if (!is_array($rows)) {
      return [];
    }

    $values = [];

    foreach ($rows as $key => $row) {
      if (!is_array($row)) {
        continue;
      }

      $values[$key] = [
        'order_product_id' => $row['order_product_id'] ?? ($row['orderProductId'] ?? null),
        'form_key' => $key,
        'product_id' => $row['product_id'] ?? null,
        'name' => $row['name'] ?? null,
        'sku' => $row['sku'] ?? null,
        'ean' => $row['ean'] ?? null,
        'image' => $row['image'] ?? null,
        'weight' => $row['weight'] ?? null,
        'width' => $row['width'] ?? null,
        'height' => $row['height'] ?? null,
        'length' => $row['length'] ?? null,
        'quantity' => isset($row['quantity']) ? trim((string)$row['quantity']) : null,
        'price' => isset($row['price']) ? trim((string)$row['price']) : null,
        'total' => isset($row['total']) ? trim((string)$row['total']) : null,
      ];
    }

    return $values;
  }

  protected function extractOrderProductsFromShopData(Order $order): array
  {
    $shopData = json_decode(json_encode($order->shopData), true) ?: [];
    $rows = $shopData['order_product'] ?? [];

    if (!is_array($rows)) {
      return [];
    }

    $values = [];

    foreach ($rows as $index => $row) {
      if (!is_array($row)) {
        continue;
      }

      $values[$index] = [
        'order_product_id' => $row['order_product_id'] ?? null,
        'form_key' => $index,
        'product_id' => $row['product_id'] ?? null,
        'name' => $row['name'] ?? null,
        'sku' => $row['sku'] ?? null,
        'ean' => $row['ean'] ?? null,
        'image' => $row['image'] ?? null,
        'weight' => $row['weight'] ?? null,
        'width' => $row['width'] ?? null,
        'height' => $row['height'] ?? null,
        'length' => $row['length'] ?? null,
        'quantity' => isset($row['quantity']) ? (string)$row['quantity'] : null,
        'price' => $row['price'] ?? null,
        'total' => $row['total'] ?? null,
      ];
    }

    return $values;
  }

  protected function loadProductsForOrderProducts(array $values): array
  {
    $ids = [];

    foreach ($values as $row) {
      $productId = $row['product_id'] ?? null;
      if ($productId === null || $productId === '') {
        continue;
      }

      $ids[] = (int)$productId;
    }

    if (!$ids) {
      return [];
    }

    return Product::with('uploads')
      ->whereIn('id', array_unique($ids))
      ->get()
      ->keyBy('id')
      ->all();
  }

  protected function buildOrderProducts(int $orderId, array $values, array $productsMap, bool $formatNumbers = true): array
  {
    $orderProducts = [];

    foreach ($values as $row) {
      $productIdRaw = $row['product_id'] ?? null;
      $productId = $productIdRaw !== null && $productIdRaw !== '' ? (int)$productIdRaw : null;
      $product = $productId !== null ? ($productsMap[$productId] ?? null) : null;

      $quantityRaw = $row['quantity'] ?? null;
      $priceRaw = $row['price'] ?? null;

      if ($formatNumbers) {
        $quantityValue = $this->normalizeInteger($quantityRaw) ?? 0;
        $priceValue = $this->normalizeDecimal($priceRaw) ?? 0.0;
        $priceFormatted = $this->formatDecimal($priceValue);
        $totalFormatted = $this->formatDecimal($priceValue * $quantityValue);
        $quantityForRow = $quantityValue;
      } else {
        $quantityForRow = $quantityRaw !== null ? (string)$quantityRaw : '';
        $priceFormatted = is_string($priceRaw) ? $priceRaw : ($priceRaw !== null ? (string)$priceRaw : '');
        if ($priceFormatted === '' && $product) {
          $priceFormatted = $this->formatDecimal($product->price);
        }

        $quantityValue = $this->normalizeInteger($quantityRaw);
        $priceValue = $this->normalizeDecimal($priceRaw);
        if ($quantityValue !== null && $priceValue !== null) {
          $totalFormatted = $this->formatDecimal($quantityValue * $priceValue);
        } else {
          $totalFormatted = isset($row['total']) ? (string)$row['total'] : '';
        }
      }

      $orderProducts[] = [
        'tax' => '0',
        'name' => $product?->nameBg ?? ($row['name'] ?? ''),
        'model' => 'Inside Trading',
        'price' => $priceFormatted,
        'total' => $totalFormatted,
        'reward' => 0,
        'order_id' => $orderId,
        'quantity' => $formatNumbers ? ($this->normalizeInteger($quantityRaw) ?? 0) : $quantityForRow,
        'master_id' => 0,
        'product_id' => $productId,
        'order_product_id' => $row['order_product_id'] ?? null,
      ];
    }

    return array_values($orderProducts);
  }

  protected function prepareOrderProductsForView(array $orderProductValues, array $productsMap): array
  {
    $rows = [];

    foreach ($orderProductValues as $key => $row) {
      $productIdRaw = $row['product_id'] ?? null;
      $productId = $productIdRaw !== null && $productIdRaw !== '' ? (int)$productIdRaw : null;
      $product = $productId !== null ? ($productsMap[$productId] ?? null) : null;

      $quantityRaw = $row['quantity'] ?? null;
      $priceRaw = $row['price'] ?? null;

      $quantityDisplay = $quantityRaw !== null ? (string)$quantityRaw : '';
      $priceDisplay = $priceRaw !== null ? (string)$priceRaw : '';
      if ($priceDisplay === '' && $product) {
        $priceDisplay = $this->formatDecimal($product->price);
      }

      $quantityValue = $this->normalizeInteger($quantityRaw);
      $priceValue = $this->normalizeDecimal($priceRaw);
      if ($quantityValue !== null && $priceValue !== null) {
        $totalDisplay = $this->formatDecimal($quantityValue * $priceValue);
      } else {
        $totalDisplay = isset($row['total']) ? (string)$row['total'] : '';
      }

      $maxQuantity = null;
      if ($product) {
        $maxQuantity = max(0, (int)$product->quantity);
      }

      $weight = array_key_exists('weight', $row) ? $row['weight'] : null;
      if (($weight === null || $weight === '') && $product) {
        $weight = $product->weight;
      }

      $width = array_key_exists('width', $row) ? $row['width'] : null;
      if (($width === null || $width === '') && $product) {
        $width = $product->width;
      }

      $height = array_key_exists('height', $row) ? $row['height'] : null;
      if (($height === null || $height === '') && $product) {
        $height = $product->height;
      }

      $length = array_key_exists('length', $row) ? $row['length'] : null;
      if (($length === null || $length === '') && $product) {
        $length = $product->length;
      }

      $imageUrl = asset('img/icons/file-placeholder.svg');
      if ($product && $product->uploads->isNotEmpty()) {
        $imageUrl = $product->uploads->first()->urls->tiny;
      } elseif (!empty($row['image'])) {
        $imageUrl = (string)$row['image'];
      }

      $rows[] = [
        'formKey' => (string)($row['form_key'] ?? $key),
        'productId' => $productId,
        'orderProductId' => $row['order_product_id'] ?? null,
        'name' => $product?->nameBg ?? ($row['name'] ?? ''),
        'sku' => $product?->mpn ?? ($row['sku'] ?? ''),
        'ean' => $product?->ean ?? ($row['ean'] ?? ''),
        'quantity' => $quantityDisplay,
        'price' => $priceDisplay,
        'total' => $totalDisplay,
        'maxQuantity' => $maxQuantity,
        'currentPrice' => $product ? $this->formatDecimal($product->price) : null,
        'image' => $imageUrl,
        'isOutOfStock' => $maxQuantity === 0,
        'weight' => $weight,
        'width' => $width,
        'height' => $height,
        'length' => $length,
      ];
    }

    return array_values($rows);
  }

  protected function normalizeDecimal(mixed $value): ?float
  {
    if ($value === null) {
      return null;
    }

    if (is_string($value)) {
      $value = trim($value);
      if ($value === '') {
        return null;
      }
    }

    if (!is_numeric(str_replace(',', '.', (string)$value))) {
      return null;
    }

    return (double)str_replace(',', '.', (string)$value);
  }

  protected function normalizeInteger(mixed $value): ?int
  {
    if ($value === null) {
      return null;
    }

    if (is_string($value)) {
      $value = trim($value);
      if ($value === '') {
        return null;
      }
    }

    if (!is_numeric($value)) {
      return null;
    }

    return (int)$value;
  }

  protected function formatDecimal(?float $value): string
  {
    $value = $value ?? 0.0;

    return number_format($value, 2, '.', '');
  }

  protected function resolveLanguageId(?string $preferredLang): int
  {
    $preferredLang = strtolower($preferredLang ?? '');

    foreach (BaseSyncJob::$languages as $languageId => $languageName) {
      if ($preferredLang === strtolower($languageName)) {
        return (int)$languageId;
      }
    }

    return 2;
  }

  protected function convertArrayToObject(array $data): object
  {
    return json_decode(json_encode($data));
  }

  public function documents(int $orderId)
  {
    /* @var $order Order */
    $order = Order::where('id', $orderId)->firstOrFail();

    // Documents
    $documentsQuery = Document::query();
    $documentsQuery = $this->applySort($documentsQuery);
    $documentsQuery->where([
      'orderId' => $order->id,
    ]);
    $documents = $documentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.orders.documents', [
      'order' => $order,
      'documents' => $documents,
    ]);
  }

  public function incomesAllocations(int $orderId)
  {
    /* @var $order Order */
    $order = Order::where('id', $orderId)->firstOrFail();

    // Income Allocations
    $incomesAllocationsQuery = IncomesAllocation::query();
    $incomesAllocationsQuery = $this->applySort($incomesAllocationsQuery);
    $incomesAllocationsQuery->where([
      'orderId' => $order->id,
    ]);
    $incomesAllocations = $incomesAllocationsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.orders.incomes-allocations', [
      'order' => $order,
      'incomesAllocations' => $incomesAllocations,
    ]);
  }
}
