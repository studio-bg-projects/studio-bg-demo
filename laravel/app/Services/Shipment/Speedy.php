<?php

namespace App\Services\Shipment;

use Illuminate\Support\Facades\Validator;

class Speedy
{
  public function request(string $path, array $data, $lang = 'bg')
  {
    $curl = curl_init('https://api.speedy.bg/v1/' . $path);

    $data = [
      'userName' => env('SPEEDY_API_USER'),
      'password' => env('SPEEDY_API_PASS'),
      'language' => strtoupper($lang),
      ...$data,
    ];

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

    $rs = curl_exec($curl);

    if ($rs === false) {
      throw new Exception('Curl error: ' . curl_error($curl));
    }

    return json_decode($rs, false, 512, JSON_THROW_ON_ERROR);
  }

  public function countrySearch($name)
  {
    return $this->request('location/country/', [
      'name' => $name
    ]);
  }

  // City search
  public function siteSearch($countryId, $name)
  {
    return $this->request('location/site/', [
      'countryId' => $countryId,
      'name' => $name,
    ]);
  }

  // District search
  public function complexSearch($siteId, $name)
  {
    return $this->request('location/complex/', [
      'siteId' => $siteId,
      'name' => $name,
    ]);
  }

  public function streetSearch($siteId, $complexId, $name)
  {
    return $this->request('location/street/', [
      'siteId' => $siteId,
      'complexId' => $complexId,
      'name' => $name,
    ]);
  }

  public function officeSearch($siteId, $name)
  {
    return $this->request('location/office/', [
      'siteId' => $siteId,
      'name' => $name,
    ]);
  }

  public function clientContracts()
  {
    return $this->request('client/contract/', []);
  }

  public function shipment($data)
  {
    return $this->request('shipment/', $data);
  }

  public function calculate($data)
  {
    return $this->request('calculate/', $data);
  }

  public function track($parcelId)
  {
    return $this->request('track/', [
      'parcels' => [
        ['id' => $parcelId]
      ]
    ]);
  }

  public function createShipmentRequest($data)
  {
    $data = (object)$data;

    // Validator
    {
      $validator = Validator::make((array)$data, [
        'senderClientIdId' => ['required', 'string', 'max:255'],
        'senderPhone1' => ['required', 'string', 'max:255'],
        'senderPhone2' => ['nullable', 'string', 'max:255'],
        'senderEmail' => ['required', 'email', 'max:255'],
        'recipientPhone1' => ['required', 'string', 'max:255'],
        'recipientPhone2' => ['nullable', 'string', 'max:255'],
        'recipientClientName' => ['required', 'string', 'max:255'],
        'recipientContactName' => ['nullable', 'string', 'max:255'],
        'recipientObjectName' => ['nullable', 'string', 'max:255'],
        'recipientEmail' => ['required', 'email', 'max:255'],
        'officeShipment' => ['required', 'boolean', 'max:255'],
        'recipientCountryId' => ['required', 'string', 'max:255'],
        'recipientSiteId' => ['required', 'string', 'max:255'],
        'recipientComplexId' => ['nullable', 'string', 'max:255'],
        'recipientStreetId' => ['required_if:officeShipment,0', 'string', 'max:255'],
        'recipientStreetNo' => ['nullable', 'string', 'max:255'],
        'recipientBlockNo' => ['nullable', 'string', 'max:255'],
        'recipientEntranceNo' => ['nullable', 'string', 'max:255'],
        'recipientFloorNo' => ['nullable', 'string', 'max:255'],
        'recipientApartmentNo' => ['nullable', 'string', 'max:255'],
        'recipientOfficeId' => ['required_if:officeShipment,1', 'string', 'max:255'],
        'serviceId' => ['required', 'string', 'max:255'],
        'serviceCodCurrencyCode' => ['nullable', 'string', 'max:255'],
        'serviceCodAmount' => ['nullable', 'string', 'max:255'],
        'servicePickupDate' => ['nullable', 'string', 'max:255'],
        'serviceAutoAdjustPickupDate' => ['nullable', 'string', 'max:255'],
        'paymentCourierServicePayer' => ['nullable', 'string', 'max:255'],
        'paymentDeclaredValuePayer' => ['nullable', 'string', 'max:255'],
        'serviceObpdOption' => ['nullable', 'string', 'max:255'],
        'serviceObpdReturnShipmentPayer' => ['nullable', 'string', 'max:255'],
        'contentContents' => ['required', 'string', 'max:255'],
        'contentPackage' => ['required', 'string', 'max:255'],

        'parcels' => ['required', 'array', 'min:1'],
        'parcels.*.ref1' => ['nullable', 'integer', 'exists:products,id'],
        'parcels.*.ref2' => ['required', 'string', 'max:20'],
        'parcels.*.width' => ['required', 'numeric', 'max:255'],
        'parcels.*.depth' => ['required', 'numeric', 'max:255'],
        'parcels.*.height' => ['required', 'numeric', 'max:255'],
        'parcels.*.weight' => ['required', 'numeric', 'max:255'],
      ]);
    }

    // # Sender
    // (Shipment): https://api.speedy.bg/api/docs/#href-ds-shipment-sender
    // (Calculation): https://api.speedy.bg/api/docs/#href-ds-calculation-sender
    {
      $sender = [];

      // (Shipment) & (Calculation)
      $sender['clientId'] = $data->senderClientIdId ?? null;

      // (Shipment)
      $sender['phone1'] = ['number' => $data->senderPhone1 ?? null];
      $sender['phone2'] = ['number' => $data->senderPhone2 ?? null];
      $sender['email'] = $data->senderEmail ?? null;
    }

    // # Recipient
    // (Shipment): https://api.speedy.bg/api/docs/#href-ds-shipment-recipient
    // (Calculation): https://api.speedy.bg/api/docs/#href-ds-calculation-recipient
    {
      $recipient = [];

      $recipient['privatePerson'] = !empty($data->recipientPrivatePerson);
      $recipient['phone1'] = ['number' => $data->recipientPhone1 ?? null];
      $recipient['phone2'] = ['number' => $data->recipientPhone2 ?? null];
      $recipient['clientName'] = $data->recipientClientName ?? null;
      $recipient['contactName'] = $recipient['privatePerson'] ? null : $data->recipientContactName ?? null;
      $recipient['objectName'] = $recipient['privatePerson'] ? null : $data->recipientObjectName ?? null;
      $recipient['email'] = $data->recipientEmail ?? null;

      if (!empty($data->officeShipment)) {
        // (Shipment) & (Calculation)
        $recipient['pickupOfficeId'] = $data->recipientOfficeId ?? null;
      } else {
        // (Shipment)
        $recipient['address'] = [
          'countryId' => $data->recipientCountryId ?? null,
          'siteId' => $data->recipientSiteId ?? null,
          'complexId' => $data->recipientComplexId ?? null,
          'streetId' => $data->recipientStreetId ?? null,
          'officeId' => $data->recipientOfficeId ?? null,

          'streetNo' => $data->recipientStreetNo ?? null,
          'blockNo' => $data->recipientBlockNo ?? null,
          'entranceNo' => $data->recipientEntranceNo ?? null,
          'floorNo' => $data->recipientFloorNo ?? null,
          'apartmentNo' => $data->recipientApartmentNo ?? null,
        ];

        // (Calculation)
        $recipient['addressLocation'] = $recipient['address'];
      }
    }

    // # Service
    // (Shipment): https://api.speedy.bg/api/docs/#href-ds-shipment-service
    // (Calculation): https://api.speedy.bg/api/docs/#href-ds-calculation-service
    {
      $service = [];

      // (Shipment)
      $service['serviceId'] = $data->serviceId ?? null; // (505: Standard 24 Hours; 515: Standard 24 Hours Package; 412: Pallet One BG - Premium; 413: Pallet One BG - Economy; 704: Tires)

      // (Calculation)
      $service['serviceIds'] = [$service['serviceId']];

      // (Shipment) & (Calculation)
      $service['pickupDate'] = $data->servicePickupDate ?? null;
      $service['autoAdjustPickupDate'] = isset($data->serviceAutoAdjustPickupDate) ? (int)$data->serviceAutoAdjustPickupDate : null;
      $service['additionalServices'] = [
        // Cash on delivery - Not mandatory
        'cod' => [
          'amount' => $data->serviceCodAmount ?? null,
          'currencyCode' => $data->serviceCodCurrencyCode ?? null,
          // 'processingType' => $data->serviceCodProcessingType ?? null, // (CASH, POSTAL_MONEY_TRANSFER)
        ],
        // Options before payment - Not mandatory
        'obpd' => [
          'option' => $data->serviceObpdOption ?? null, // (OPEN, TEST)
          'returnShipmentServiceId' => $data->obpdReturnShipmentServiceId ?? $service['serviceId'],
          'returnShipmentPayer' => $data->serviceObpdReturnShipmentPayer ?? null, // (SENDER, RECIPIENT, THIRD_PARTY). The sender of the returning shipment is the recipient of the primary shipment.
        ],
      ];
    }

    // Payment
    // Shipment (same for Calculation): https://api.speedy.bg/api/docs/#href-ds-shipment-payment
    {
      $payment = [];

      $payment['courierServicePayer'] = $data->paymentCourierServicePayer ?? null; // (SENDER, RECIPIENT, THIRD_PARTY)
      $payment['declaredValuePayer'] = $data->paymentDeclaredValuePayer ?? null; // Mandatory only if the shipment has a 'declaredValue'
    }

    // Content
    // (Shipment): https://api.speedy.bg/api/docs/#href-ds-shipment-content
    // (Calculation): https://api.speedy.bg/api/docs/#href-ds-calculation-content
    {
      $content = [];

      // (Shipment) & (Calculation)
      $content['parcels'] = [];
      foreach ($data->parcels ?? [] as $seq => $parcel) {
        $parcel = (object)$parcel;
        $content['parcels'][] = [
          'seqNo' => (int)$seq + 1,
          'ref1' => $parcel->ref1 ?? null,
          'ref2' => $parcel->ref2 ?? null,
          'width' => (double)$parcel->width ?? null,
          'depth' => (double)$parcel->depth ?? null,
          'height' => (double)$parcel->height ?? null,
          'weight' => (double)$parcel->weight ?? null,
        ];
      }

      // (Shipment)
      $content['contents'] = $data->contentContents ?? null;
      $content['package'] = $data->contentPackage ?? null;
    }

    // Request
    // (Shipment): https://api.speedy.bg/api/docs/#href-create-shipment-req
    // (Calculation): https://api.speedy.bg/api/docs/#href-calculation-req
    {
      $request = [
        'sender' => $sender,
        'recipient' => $recipient,
        'service' => $service,
        'payment' => $payment,
        'content' => $content,
      ];
    }

    return [
      'request' => $request,
      'validator' => $validator,
    ];
  }
}
