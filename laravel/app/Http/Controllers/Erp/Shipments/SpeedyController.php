<?php

namespace App\Http\Controllers\Erp\Shipments;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Services\Shipment\Speedy;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class SpeedyController extends Controller
{
  use FilterAndSort;

  private Speedy $speedy;

  public function __construct()
  {
    parent::__construct();

    $this->speedy = new Speedy();
  }

  public function index()
  {
    $shipmentsQuery = Shipment::query();
    $shipmentsQuery = $this->applySort($shipmentsQuery);
    $shipmentsQuery = $this->applyFilter($shipmentsQuery);
    $shipments = $shipmentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 10)->withQueryString();

    return view('erp.shipments.speedy.index', [
      'shipments' => $shipments,
    ]);
  }

  public function view(int $shipmentId)
  {
    /* @var $shipment Shipment */
    $shipment = Shipment::where('id', $shipmentId)->firstOrFail();

    $trackingData = $this->speedy->track($shipment->parcelId);

    return view('erp.shipments.speedy.view', [
      'shipment' => $shipment,
      'trackingData' => $trackingData,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $shipmentData = (object)[];

    // Customer & order by request
    /* @var $order Order */
    $order = null;

    /* @var $customer Customer */
    $customer = null;

    if ($request->get('orderId')) {
      $order = Order::where(['id' => $request->get('orderId')])->first();
      $customer = $order?->customer;
    } elseif ($request->get('customerId')) {
      $customer = Customer::where(['id' => $request->get('customerId')])->first();
    }

    if ($request->isMethod('post')) {
      $shipmentData = (object)$request->all();

      $requestData = $this->speedy->createShipmentRequest($shipmentData);

      $validator = $requestData['validator'];
      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        try {
          $shipmentResponse = $this->speedy->shipment($requestData['request']);
          if (isset($shipmentResponse->error)) {
            $errors->add('speedy', $shipmentResponse->error->message);
          }

          if ($errors->isEmpty()) {
            $shipment = new Shipment();
            $shipment->parcelId = $shipmentResponse->id;
            $shipment->courier = 'speedy';
            $shipment->orderId = $order->id ?? null;
            $shipment->customerId = $customer->id ?? null;
            $shipment->requestData = $shipmentResponse;
            $shipment->responseData = $shipmentResponse;
            $shipment->save();

            return redirect('/erp/shipments/speedy/view/' . $shipment->id)
              ->with('success', 'Успешно създадохте заявката за доставка.');
          }
        } catch (\Exception $e) {
          $errors->add('exception', $e->getMessage());
        }
      }
    } else {
      // Defaults
      $shipmentData->recipientCountryId = 100;
      $shipmentData->recipientCountryTitle = 'БЪЛГАРИЯ';
      $shipmentData->serviceAutoAdjustPickupDate = true;
      $shipmentData->contentContents = 'Техника';
      $shipmentData->contentPackage = 'Опаковка';

      $shipmentData->senderPhone1 = dbConfig('default:shipping:phone1');
      $shipmentData->senderPhone2 = dbConfig('default:shipping:phone2');
      $shipmentData->senderEmail = dbConfig('default:shipping:email');

      // Data prefill
      if ($customer) {
        $shipmentData->recipientPhone1 = $customer->contactPhone;
        $shipmentData->recipientClientName = $customer->companyName;
        $shipmentData->recipientContactName = $customer->firstName . ' ' . $customer->lastName;
        $shipmentData->recipientEmail = $customer->contactEmail;
      }

      if ($order) {
        $shipmentData->recipientContactName = ($order->shopData->order->shipping_firstname ?? null) . ' ' . ($order->shopData->order->shipping_lastname ?? null);
        $shipmentData->recipientEmail = $order->shopData->order->email ?? '';

        // $shipmentData->serviceCodAmount = 0;
        $shipmentData->parcels = [];
        foreach ($order->shopData->order_product as $item) {
          /* @var $product Product */
          $product = Product::find($item->product_id);

          for ($i = 1; $i <= $item->quantity; $i++) {
            // $shipmentData->serviceCodAmount += (double)$item->price;
            $shipmentData->parcels[] = [
              'ref1' => $item->product_id,
              'ref2' => mb_substr($item->name, 0, 20),
              'width' => $product->width ?? 0,
              'depth' => $product->length ?? 0,
              'height' => $product->height ?? 0,
              'weight' => $product->weight ?? 0,
            ];
          }
        }

        $shipmentData->recipientInfo = 'order';
      }
    }

    return view('erp.shipments.speedy.create', [
      'errors' => $errors,
      'shipmentData' => $shipmentData,
      'customer' => $customer,
      'order' => $order,
    ]);
  }

  public function calculate(Request $request)
  {
    $products = (array)$request->get('products');
    $parcels = (array)$request->get('parcels');
    $address = (array)$request->get('address');

    $errors = new MessageBag();
    $calculation = (object)[];
    $requestData = null;

    $citySpeedyId = $address['city_speedy_id'] ?? $address['citySpeedyId'] ?? null;
    $addrStreetSpeedyId = $address['street_speedy_id'] ?? $address['streetSpeedyId'] ?? null;
    $addrOfficeSpeedyId = $address['office_speedy_id'] ?? $address['officeSpeedyId'] ?? null;

    if (empty($addrOfficeSpeedyId)) {
      if (empty($citySpeedyId)) {
        $errors->add('MISSING_CITY_SPEEDY_ID', 'Невалиден град за доставка. Моля, проверете и въведете отново името на вашия град');
      } elseif (empty($addrStreetSpeedyId)) {
        $errors->add('MISSING_STREET_SPEEDY_ID', 'Невалиден адрес за доставка. Моля, проверете и въведете отново вашата улица');
      }
    }

    if ($errors->isEmpty()) {
      $serviceCodAmount = 0;

      foreach ($products as $item) {
        // Products are parcels
        if (array_key_exists('ref1', $item)) {
          $parcels[] = $item;
        } else {
          /* @var $product Product */
          $product = Product::find($item['id'] ?? null);

          for ($i = 1; $i <= $item['quantity'] ?? 0; $i++) {
            $serviceCodAmount += (double)$product->price * 2;
            $parcels[] = [
              'ref1' => $product->id,
              'ref2' => mb_substr($product->ean . $product->nameBg, 0, 20),
              'width' => $product->width ?? 0,
              'depth' => $product->length ?? 0,
              'height' => $product->height ?? 0,
              'weight' => $product->weight ?? 0,
            ];
          }
        }
      }

      $requestData = $this->speedy->createShipmentRequest([
        'senderClientIdId' => dbConfig('default:shipping:speedyClientId'),
        'senderPhone1' => dbConfig('default:shipping:phone1'),
        'senderEmail' => dbConfig('default:shipping:email'),
        'recipientPhone1' => '0888888888',
        'recipientClientName' => 'Calculation',
        'recipientEmail' => 'calucation@insidetrading.bg',
        'recipientCountryId' => '100', // Bulgaria
        'recipientSiteId' => (string)$citySpeedyId,
        'recipientStreetId' => (string)$addrStreetSpeedyId,
        'recipientOfficeId' => (string)$addrOfficeSpeedyId,
        'officeShipment' => (string)($addrOfficeSpeedyId ? 1 : 0),
        'serviceId' => '505',
        'serviceObpdReturnShipmentPayer' => 'SENDER',
        'serviceAutoAdjustPickupDate' => '1',
        'paymentCourierServicePayer' => 'SENDER',
        'contentContents' => 'Техника',
        'contentPackage' => 'Опаковка',
        'serviceCodAmount' => (string)($serviceCodAmount * 1.95583),
        // 'serviceCodCurrencyCode' => 'EUR', //@todo
        'parcels' => $parcels,
      ]);

      $validator = $requestData['validator'];
      $errors->merge($validator->errors());

      $calculation = $this->speedy->calculate($requestData['request']);
      try {
        $calculation = $this->speedy->calculate($requestData['request']);
        if (isset($calculation->error)) {
          $errors->add('SPEEDY', $calculation->error->message);
        }
      } catch (\Exception $e) {
        $errors->add('SPEEDY_CALCULATE_EXCEPTION', $e);
      }
    }

    return [
      'errors' => $errors,
      'calculation' => $calculation,
      'request' => $requestData['request'] ?? null,
    ];
  }

  public function search(Request $request)
  {
    $rs = [];
    $q = $request->get('q');

    switch ($request->get('kind')) {
      case 'country':
      {
        $rs = array_map(function ($item) {
          return [
            'id' => $item->id,
            'title' => $item->name,
            'item' => $item,
          ];
        }, $this->speedy->countrySearch($q)->countries ?? []);
        break;
      }
      case 'site':
      {
        $countryId = $request->get('countryId');
        $rs = array_map(function ($item) {
          return [
            'id' => $item->id,
            'title' => $item->name,
            'item' => $item,
          ];
        }, $this->speedy->siteSearch($countryId, $q)->sites ?? []);
        break;
      }
      case 'complex':
      {
        $siteId = $request->get('siteId');
        $rs = array_map(function ($item) {
          return [
            'id' => $item->id,
            'title' => $item->name,
            'item' => $item,
          ];
        }, $this->speedy->complexSearch($siteId, $q)->complexes ?? []);
        break;
      }
      case 'street':
      {
        $siteId = $request->get('siteId');
        $complexId = $request->get('complexId');
        $rs = array_map(function ($item) {
          return [
            'id' => $item->id,
            'title' => $item->name,
            'item' => $item,
          ];
        }, $this->speedy->streetSearch($siteId, $complexId, $q)->streets ?? []);
        break;
      }
      case 'office':
      {
        $siteId = $request->get('siteId');
        $rs = array_map(function ($item) {
          return [
            'id' => $item->id,
            'title' => $item->name,
            'item' => $item,
          ];
        }, $this->speedy->officeSearch($siteId, $q)->offices ?? []);
        break;
      }
      case 'clientContract':
      {
        $rs = array_map(function ($item) {
          return [
            'id' => $item->clientId,
            'title' => $item->clientName . ' (' . $item->address->fullAddressString ?? null . ')',
            'item' => $item,
          ];
        }, $this->speedy->clientContracts()->clients ?? []);
        break;
      }
    }

    return [
      'data' => $rs
    ];
  }
}
