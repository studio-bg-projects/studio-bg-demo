<?php

namespace App\Services\Jobs;

use App\Models\Customer;
use App\Models\CustomersAddress;
use App\Models\CustomersGroup;

class SyncCustomersAddressesJob extends BaseSyncJob
{
  protected array $customFields = [
    // Hidden fields - System
    '_waitingSyncToErp' => [
      'id' => 2900,
      'lang1' => 'Waiting for synchronisation to the ERP (Customer)',
      'lang2' => 'Чака синхронизация към ERP (Клиент)',
      'required' => false,
      'sortOrder' => 2900,
      'type' => 'hidden',
    ],
  ];

  public function run(): void
  {
    // Run delete before other syncs
    $this->deleteAddressesErp2Shop();
    $this->deleteAddressesShop2Erp();

    $addresses = CustomersAddress::all();

    $this->syncShopCustomFields();
    $this->syncAddressesShop2Erp($addresses); // Shop sync 1st
    $this->syncAddressesErp2Shop($addresses);

    $this->out('All good :)');
  }

  protected function deleteAddressesErp2Shop(): void
  {
    $addresses = CustomersAddress::where('isDeleted', true)->get();

    /* @var $addresses CustomersAddress[] */
    foreach ($addresses as $address) {
      // Delete from shop
      $this->shopConn()->table(self::PREFIX . 'address')
        ->where([
          'address_id' => $address->id,
        ])
        ->delete();

      // Delete from here
      $address->delete();

      $this->out(sprintf('Address was deleted %s (ERP 2 SHOP)', $address->id));
    }
  }


  protected function deleteAddressesShop2Erp(): void
  {
    $addresses = $this->shopConn()->select('SELECT * FROM `' . self::PREFIX . 'address` WHERE `_is_deleted` = 1');

    foreach ($addresses as $address) {
      // Delete from ERP
      CustomersAddress::where('id', '=', $address->address_id)
        ->delete();

      // Delete from here
      $this->shopConn()->table(self::PREFIX . 'address')
        ->where([
          'address_id' => $address->address_id,
        ])
        ->delete();

      $this->out(sprintf('Address was deleted %s (SHOP 2 ERP)', $address->address_id));
    }
  }

  protected function syncShopCustomFields(): void
  {
    $allIds = [];

    foreach ($this->customFields as $field) {
      $allIds[] = (int)$field['id'];

      // custom_field
      $this->shopConn()->table(self::PREFIX . 'custom_field')->upsert(
        [
          'custom_field_id' => $field['id'],
          'type' => $field['type'],
          'value' => '',
          'validation' => '',
          'location' => 'address',
          'status' => 1,
          'sort_order' => $field['sortOrder'],
        ],
        ['custom_field_id'],
        [
          'type',
          'value',
          'validation',
          'location',
          'status',
          'sort_order',
        ]
      );

      // custom_field_description
      foreach (self::$languages as $langId => $langName) {
        $this->shopConn()->table(self::PREFIX . 'custom_field_description')->upsert(
          [
            'custom_field_id' => $field['id'],
            'language_id' => $langId,
            'name' => $field['lang' . $langId],
          ],
          ['custom_field_id'],
          [
            'language_id',
            'name',
          ]
        );
      }

      // custom_field_customer_group
      foreach (CustomersGroup::all() as $group) {
        $this->shopConn()->table(self::PREFIX . 'custom_field_customer_group')->upsert(
          [
            'custom_field_id' => $field['id'],
            'customer_group_id' => $group->id,
            'required' => $field['required'],
          ],
          ['custom_field_id'],
          [
            'customer_group_id',
            'required',
          ]
        );
      }
    }

    // Cleanup
    $this->shopConn()->delete('
      DELETE FROM `' . self::PREFIX . 'custom_field` WHERE `location` = "address" AND `custom_field_id` NOT IN (' . implode(',', $allIds) . ')
    ');
    $this->cleanupEmptyRelations('custom_field_description', 'custom_field', 'custom_field_id');
    $this->cleanupEmptyRelations('custom_field_customer_group', 'custom_field', 'custom_field_id');
  }

  protected function syncAddressesShop2Erp($addresses): void
  {
    $addressesMap = [];
    foreach ($addresses as $address) {
      $addressesMap[$address->id] = $address;
    }

    $customersIds = Customer::pluck('id')->toArray();

    $waitingFiledId = $this->customFields['_waitingSyncToErp']['id'];
    $shopAddresses = $this->shopConn()->select('SELECT * FROM `' . self::PREFIX . 'address`');
    foreach ($shopAddresses as $shopAddress) {
      $shopAddress->custom_field = json_decode($shopAddress->custom_field ?: '{}', true);
      if (!is_array($shopAddress->custom_field)) {
        $shopAddress->custom_field = [];
      }
      $waitingForUpdate = !empty($shopAddress->custom_field[$waitingFiledId]);

      if (!in_array($shopAddress->customer_id, $customersIds)) {
        $this->out(sprintf('Skip address %s to because the customer %s is missing', $shopAddress->address_id, $shopAddress->customer_id));
        continue;
      }

      /* @var $matchAddress CustomersAddress */
      $matchAddress = $addressesMap[$shopAddress->address_id] ?? null;

      // Add to erp
      if (!$matchAddress) {
        $this->out(sprintf('Add address %s to ERP', $shopAddress->address_id));

        $address = new CustomersAddress();
        $address->id = $shopAddress->address_id;
        $address->customerId = $shopAddress->customer_id;
        $address->firstName = $shopAddress->firstname;
        $address->lastName = $shopAddress->lastname;
        $address->companyName = $shopAddress->company;
        $address->zipCode = $shopAddress->postcode;
        $address->countryId = $shopAddress->country_id;
        $address->city = $shopAddress->city;
        $address->citySpeedyId = $shopAddress->city_speedy_id;
        $address->street = $shopAddress->address_1;
        $address->streetSpeedyId = $shopAddress->street_speedy_id;
        $address->addressDetails = $shopAddress->address_2;
        $address->streetNo = $shopAddress->street_no;
        $address->blockNo = $shopAddress->block_no;
        $address->entranceNo = $shopAddress->entrance_no;
        $address->floor = $shopAddress->floor;
        $address->apartmentNo = $shopAddress->apartment_no;
        $address->phone = $shopAddress->phone;
        $address->email = $shopAddress->email;
        $address->operatingHours = $shopAddress->operating_hours;

        $address->save();

        // Reload the address
        $address = CustomersAddress::where('id', $address->id)->first();

        // Add to existing records
        $addresses[] = $address;

        // Set as match and waiting
        $matchAddress = $address;
        $waitingForUpdate = true;
      }

      // Update the address (if needed)
      if ($waitingForUpdate) {
        // Compare the records and put them in a map to debug where the differences
        if ((int)$matchAddress->customerId !== (int)$shopAddress->customer_id) $matchAddress->customerId = (int)$shopAddress->customer_id;
        if ((string)$matchAddress->firstName !== (string)$shopAddress->firstname) $matchAddress->firstName = (string)$shopAddress->firstname;
        if ((string)$matchAddress->lastName !== (string)$shopAddress->lastname) $matchAddress->lastName = (string)$shopAddress->lastname;
        if ((string)$matchAddress->companyName !== (string)$shopAddress->company) $matchAddress->companyName = (string)$shopAddress->company;
        if ((string)$matchAddress->zipCode !== (string)$shopAddress->postcode) $matchAddress->zipCode = (string)$shopAddress->postcode;
        if ((int)$matchAddress->countryId !== (int)$shopAddress->country_id) $matchAddress->countryId = (int)$shopAddress->country_id;
        if ((string)$matchAddress->city !== (string)$shopAddress->city) $matchAddress->city = (string)$shopAddress->city;
        if ((int)$matchAddress->citySpeedyId !== (int)$shopAddress->city_speedy_id) $matchAddress->citySpeedyId = (int)$shopAddress->city_speedy_id;
        if ((string)$matchAddress->street !== (string)$shopAddress->address_1) $matchAddress->street = (string)$shopAddress->address_1;
        if ((int)$matchAddress->streetSpeedyId !== (int)$shopAddress->street_speedy_id) $matchAddress->streetSpeedyId = (int)$shopAddress->street_speedy_id;
        if ((string)$matchAddress->addressDetails !== (string)$shopAddress->address_2) $matchAddress->addressDetails = (string)$shopAddress->address_2;
        if ((string)$matchAddress->streetNo !== (string)$shopAddress->street_no) $matchAddress->streetNo = (string)$shopAddress->street_no;
        if ((string)$matchAddress->blockNo !== (string)$shopAddress->block_no) $matchAddress->blockNo = (string)$shopAddress->block_no;
        if ((string)$matchAddress->entranceNo !== (string)$shopAddress->entrance_no) $matchAddress->entranceNo = (string)$shopAddress->entrance_no;
        if ((string)$matchAddress->floor !== (string)$shopAddress->floor) $matchAddress->floor = (string)$shopAddress->floor;
        if ((string)$matchAddress->apartmentNo !== (string)$shopAddress->apartment_no) $matchAddress->apartmentNo = (string)$shopAddress->apartment_no;
        if ((string)$matchAddress->phone !== (string)$shopAddress->phone) $matchAddress->phone = (string)$shopAddress->phone;
        if ((string)$matchAddress->email !== (string)$shopAddress->email) $matchAddress->email = (string)$shopAddress->email;
        if ((string)$matchAddress->operatingHours !== (string)$shopAddress->operating_hours) $matchAddress->operatingHours = (string)$shopAddress->operating_hours;

        // Compare custom fields
        foreach ($this->customFields as $fieldKey => $field) {
          if (!array_key_exists($field['id'], $shopAddress->custom_field ?: [])) {
            continue;
          }

          if (!$matchAddress->hasAttribute($fieldKey)) {
            continue;
          }

          if ($shopAddress->custom_field[$field['id']] !== $matchAddress->{$fieldKey}) {
            $matchAddress->{$fieldKey} = $shopAddress->custom_field[$field['id']];
          }
        }

        // Mark the address as not waiting
        $shopAddress->custom_field[$waitingFiledId] = false;

        $this->shopConn()->table(self::PREFIX . 'address')
          ->where('address_id', $shopAddress->address_id)
          ->update([
            'custom_field' => json_encode($shopAddress->custom_field)
          ]);

        // Do the update
        if ($matchAddress->getDirty()) {
          $this->out(sprintf('Update address (ERP) %s with differences %s', $matchAddress->id, json_encode($matchAddress->getDirty())));
          $matchAddress->save();
        }
      }
    }
  }

  protected function syncAddressesErp2Shop($addresses): void
  {
    $shopAddresses = $this->dictionarizeShopRecords('address', 'address_id');

    /* @var $addresses CustomersAddress[] */
    foreach ($addresses as $address) {
      // Add new record
      if (!isset($shopAddresses[$address->id])) {
        $this->out(sprintf('Add address %s', $address->id));

        $this->shopConn()->table(self::PREFIX . 'address')->insert([
          'address_id' => $address->id,
          'customer_id' => $address->customerId,
          'firstname' => $address->firstName,
          'lastname' => $address->lastName,
          'company' => $address->companyName,
          'city' => $address->city,
          'city_speedy_id' => $address->citySpeedyId,
          'address_1' => $address->street,
          'street_speedy_id' => $address->streetSpeedyId,
          'address_2' => (string)$address->addressDetails,
          'postcode' => (string)$address->zipCode,
          'country_id' => $address->countryId,
          'zone_id' => 0,
          'custom_field' => '{}',
          'default' => 0,
          'street_no' => $address->streetNo,
          'block_no' => $address->blockNo,
          'entrance_no' => $address->entranceNo,
          'floor' => $address->floor,
          'apartment_no' => $address->apartmentNo,
          'phone' => $address->phone,
          'email' => $address->email,
          'operating_hours' => $address->operatingHours,
        ]);
        $shopAddresses[$address->id] = $this->shopConn()->table(self::PREFIX . 'address')
          ->where('address_id', $address->id)
          ->first();
      }

      // Compare the records and put them in a map to debug where the differences
      $updates = [];
      if ((int)$address->customerId !== (int)$shopAddresses[$address->id]->customer_id) $updates['customer_id'] = (int)$address->customerId;
      if ((string)$address->firstName !== (string)$shopAddresses[$address->id]->firstname) $updates['firstname'] = (string)$address->firstName;
      if ((string)$address->lastName !== (string)$shopAddresses[$address->id]->lastname) $updates['lastname'] = (string)$address->lastName;
      if ((string)$address->companyName !== (string)$shopAddresses[$address->id]->company) $updates['company'] = (string)$address->companyName;
      if ((string)$address->city !== (string)$shopAddresses[$address->id]->city) $updates['city'] = (string)$address->city;
      if ((int)$address->citySpeedyId !== (int)$shopAddresses[$address->id]->city_speedy_id) $updates['city_speedy_id'] = (int)$address->citySpeedyId;
      if ((string)$address->street !== (string)$shopAddresses[$address->id]->address_1) $updates['address_1'] = (string)$address->street;
      if ((int)$address->streetSpeedyId !== (int)$shopAddresses[$address->id]->street_speedy_id) $updates['street_speedy_id'] = (int)$address->streetSpeedyId;
      if ((string)$address->addressDetails !== (string)$shopAddresses[$address->id]->address_2) $updates['address_2'] = (string)$address->addressDetails;
      if ((string)$address->zipCode !== (string)$shopAddresses[$address->id]->postcode) $updates['postcode'] = (string)$address->zipCode;
      if ((int)$address->countryId !== (int)$shopAddresses[$address->id]->country_id) $updates['country_id'] = (int)$address->countryId;
      if ((string)$address->streetNo !== (string)$shopAddresses[$address->id]->street_no) $updates['street_no'] = (string)$address->streetNo;
      if ((string)$address->blockNo !== (string)$shopAddresses[$address->id]->block_no) $updates['block_no'] = (string)$address->blockNo;
      if ((string)$address->entranceNo !== (string)$shopAddresses[$address->id]->entrance_no) $updates['entrance_no'] = (string)$address->entranceNo;
      if ((string)$address->floor !== (string)$shopAddresses[$address->id]->floor) $updates['floor'] = (string)$address->floor;
      if ((string)$address->apartmentNo !== (string)$shopAddresses[$address->id]->apartment_no) $updates['apartment_no'] = (string)$address->apartmentNo;
      if ((string)$address->phone !== (string)$shopAddresses[$address->id]->phone) $updates['phone'] = (string)$address->phone;
      if ((string)$address->email !== (string)$shopAddresses[$address->id]->email) $updates['email'] = (string)$address->email;
      if ((string)$address->operatingHours !== (string)$shopAddresses[$address->id]->operating_hours) $updates['operating_hours'] = (string)$address->operatingHours;

      // Compare custom fields
      $shopCustomFields = json_decode($shopAddresses[$address->id]->custom_field, true) ?: [];

      foreach ($this->customFields as $fieldKey => $field) {
        if (!array_key_exists($field['id'], $shopCustomFields) || (string)$shopCustomFields[$field['id']] !== (string)$address->{$fieldKey}) {
          $updates['custom_field'][$field['id']] = (string)$address->{$fieldKey};
        }
      }

      // If there are custom fields for update fill the existing values
      $_customFieldsLog = [];
      if (!empty($updates['custom_field'])) {
        $_customFieldsLog = $updates['custom_field'];

        foreach ($shopCustomFields as $key => $value) {
          $updates['custom_field'][$key] = array_key_exists($key, $updates['custom_field']) ? $updates['custom_field'][$key] : $value;
        }
        $updates['custom_field'] = json_encode($updates['custom_field']);
      }

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update address (SHOP) %s with differences %s', $address->id, json_encode($_customFieldsLog ? [...$updates, 'custom_field' => $_customFieldsLog] : $updates)));

        $this->shopConn()->table(self::PREFIX . 'address')
          ->where('address_id', $address->id)
          ->update($updates);
      }
    }
  }
}
