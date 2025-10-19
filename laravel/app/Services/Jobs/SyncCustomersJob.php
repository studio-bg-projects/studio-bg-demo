<?php

namespace App\Services\Jobs;

use App\Enums\CustomerStatusType;
use App\Models\Customer;
use App\Models\CustomersGroup;
use App\Services\MailMakerService;

class SyncCustomersJob extends BaseSyncJob
{
  protected bool $resetCache = false;
  protected array $customFields = [
    // Check: opencart/system/extend/functions.php
    // Company Information
    'companyName' => [
      'id' => 1100,
      'lang1' => 'Registered name',
      'lang2' => 'Име на фирмата',
      'required' => true,
      'sortOrder' => 1100,
      'type' => 'text',
      'rules' => [],
    ],
    'companyId' => [
      'id' => 1101,
      'lang1' => 'Compnay ID',
      'lang2' => 'ЕИК',
      'required' => true,
      'sortOrder' => 1101,
      'type' => 'text',
      'rules' => [],
    ],
    'companyVatNumber' => [
      'id' => 1102,
      'lang1' => 'VAT number',
      'lang2' => 'ДДС номер',
      'required' => false,
      'sortOrder' => 1102,
      'type' => 'text',
      'rules' => [],
    ],
    'companyCountryId' => [
      'id' => 1103,
      'lang1' => 'Country',
      'lang2' => 'Държава',
      'required' => true,
      'sortOrder' => 1103,
      'type' => 'country',
      'rules' => ['blankToNull'],
    ],
    'companyCity' => [
      'id' => 1104,
      'lang1' => 'City',
      'lang2' => 'Град',
      'required' => true,
      'sortOrder' => 1104,
      'type' => 'text',
      'rules' => [],
    ],
    'companyZipCode' => [
      'id' => 1105,
      'lang1' => 'ZIP code',
      'lang2' => 'ПК',
      'required' => true,
      'sortOrder' => 1105,
      'type' => 'text',
      'rules' => [],
    ],
    'companyAddress' => [
      'id' => 1106,
      'lang1' => 'Registered address',
      'lang2' => 'Адрес по регистрация',
      'required' => true,
      'sortOrder' => 1106,
      'type' => 'text',
      'rules' => [],
    ],
    // Contacts
    'contactSales' => [
      'id' => 1201,
      'lang1' => 'Sales contact',
      'lang2' => 'Търговски контакт',
      'required' => false,
      'sortOrder' => 1201,
      'type' => 'text',
      'rules' => [],
    ],
    'contactPhone' => [
      'id' => 1202,
      'lang1' => 'Telephone',
      'lang2' => 'Телефон',
      'required' => false,
      'sortOrder' => 1202,
      'type' => 'text',
      'rules' => [],
    ],
    'contactEmail' => [
      'id' => 1203,
      'lang1' => 'E-mail',
      'lang2' => 'Имейл',
      'required' => false,
      'sortOrder' => 1203,
      'type' => 'text',
      'rules' => [],
    ],
    // Financial contact
    'financialContactPhone' => [
      'id' => 1300,
      'lang1' => 'Telephone',
      'lang2' => 'Телефон',
      'required' => false,
      'sortOrder' => 1300,
      'type' => 'text',
      'rules' => [],
    ],
    'financialContactEmail' => [
      'id' => 1301,
      'lang1' => 'E-mail',
      'lang2' => 'Имейл',
      'required' => false,
      'sortOrder' => 1301,
      'type' => 'text',
      'rules' => [],
    ],
    // Credit line
    'creditLineRequested' => [
      'id' => 1501,
      'lang1' => 'The client has requested a new credit line',
      'lang2' => 'Клиентът е поискал нова кредитна линия',
      'required' => false,
      'sortOrder' => 1501,
      'type' => 'hidden',
      'rules' => ['boolean'],
    ],
    'creditLineRequestValue' => [
      'id' => 1502,
      'lang1' => 'Requested credit line amount',
      'lang2' => 'Поискана стойност на кредитната линия',
      'required' => false,
      'sortOrder' => 1502,
      'type' => 'hidden',
      'rules' => ['blankToNull'],
    ],
    'creditLineValue' => [
      'id' => 1503,
      'lang1' => 'Credit line amount',
      'lang2' => 'Стойност на кредитната линия',
      'required' => false,
      'sortOrder' => 1503,
      'type' => 'hidden',
      'rules' => [],
    ],
    'creditLineUsed' => [
      'id' => 1504,
      'lang1' => 'Used credit line amount',
      'lang2' => 'Използвана стойност на кредитната линия',
      'required' => false,
      'sortOrder' => 1504,
      'type' => 'hidden',
      'rules' => ['blankToNull'],
    ],
    'creditLineLeft' => [
      'id' => 1505,
      'lang1' => 'Remaining credit line amount',
      'lang2' => 'Остатъчна стойност на кредитната линия',
      'required' => false,
      'sortOrder' => 1505,
      'type' => 'hidden',
      'rules' => ['blankToNull'],
    ],
    // Hidden fields - User
    // 'hasSalesRepresentative' => [
    //   'id' => 1800,
    //   'lang1' => 'Has sales representative',
    //   'lang2' => 'Начислен търговски представител',
    //   'required' => false,
    //   'sortOrder' => 1800,
    //   'type' => 'hidden',
    //    'rules' => [],
    // ],
    'salesRepresentativeId' => [
      'id' => 1801,
      'lang1' => 'Sales representative id',
      'lang2' => 'Търговския представител',
      'required' => false,
      'sortOrder' => 1801,
      'type' => 'hidden',
      'rules' => ['blankToNull'],
    ],
    // Hidden fields - System
    '_waitingSyncToErp' => [
      'id' => 1900,
      'lang1' => 'Waiting for synchronisation to the ERP (Customer)',
      'lang2' => 'Чака синхронизация към ERP (Клиент)',
      'required' => false,
      'sortOrder' => 1900,
      'type' => 'hidden',
      'rules' => [],
    ],
  ];

  protected $approvalDefault = 1;

  public function run(): void
  {
    // Run delete before other syncs
    $this->deleteCustomers();

    $customers = Customer::all();
    $groups = CustomersGroup::all();

    $this->syncGroups($groups);
    $this->syncGroupsDescription($groups);
    $this->syncShopCustomFields();
    $this->syncCustomersShop2Erp($customers); // Shop sync 1st
    $this->syncCustomersErp2Shop($customers);
    $this->syncPromotions($groups);
    $this->cleanup();

    $this->out('All good :)');
  }

  protected function deleteCustomers(): void
  {
    $customers = Customer::where('isDeleted', true)->get();

    /* @var $customers Customer[] */
    foreach ($customers as $customer) {
      // Delete from store
      $this->shopConn()->table(self::PREFIX . 'customer')
        ->where([
          'customer_id' => $customer->id,
        ])
        ->delete();

      // Delete from here
      try {
        $customer->delete();
        $this->out(sprintf('Customer was deleted %s (%s)', $customer->id, $customer->email));
      } catch (\Exception $e) {
        $this->out('CUSTOMER DELETE FAIL: ' . $e->getMessage());
      }
    }
  }

  protected function syncGroups($groups): void
  {
    $shopGroups = $this->dictionarizeShopRecords('customer_group', 'customer_group_id');
    $setInactive = $shopGroups; // Collect records to be inactive

    /* @var $groups CustomersGroup[] */
    foreach ($groups as $group) {
      // Add new record
      if (!isset($shopGroups[$group->id])) {
        $this->out(sprintf('Add customer_group %s', $group->id));

        $this->shopConn()->table(self::PREFIX . 'customer_group')->insert([
          'customer_group_id' => $group->id,
          'approval' => $this->approvalDefault,
          'sort_order' => 0,
        ]);
        $shopGroups[$group->id] = $this->shopConn()->table(self::PREFIX . 'customer_group')
          ->where('customer_group_id', $group->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$group->id])) unset($setInactive[$group->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Default compare
      if ($this->approvalDefault !== (int)$shopGroups[$group->id]->approval) $updates['approval'] = (int)$this->approvalDefault;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update customer_group %s with differences %s', $group->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'customer_group')
          ->where('customer_group_id', $group->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopGroup) {
      $this->out(sprintf('Delete customer_group %s (not exists in ERP)', $shopGroup->customer_group_id));

      $this->shopConn()->table(self::PREFIX . 'customer_group')
        ->where('customer_group_id', $shopGroup->customer_group_id)
        ->delete();
    }
  }

  protected function syncGroupsDescription($groups): void
  {
    $shopGroupDescription = $this->dictionarizeShopRecords('customer_group_description', 'customer_group_id', 'language_id');

    foreach (self::$languages as $langId => $langName) {
      /* @var $groups CustomersGroup[] */
      foreach ($groups as $group) {
        // Add new record
        if (!isset($shopGroupDescription[$group->id][$langId])) {
          $this->out(sprintf('Add customer_group_description %s (%s)', $group->id, $langName));

          $this->shopConn()->table(self::PREFIX . 'customer_group_description')->insert([
            'customer_group_id' => $group->id,
            'language_id' => $langId,
            'name' => '',
            'description' => '',
          ]);
          $shopGroupDescription[$group->id][$langId] = $this->shopConn()->table(self::PREFIX . 'customer_group_description')
            ->where('customer_group_id', $group->id)
            ->where('language_id', $langId)->first();
        }

        // Compare the records and put them in a map to debug where the differences
        $updates = [];

        $name = $group->{'name' . $langName};
        if ((string)$name !== (string)$shopGroupDescription[$group->id][$langId]->name) $updates['name'] = (string)$name;
        if ((string)$name !== (string)$shopGroupDescription[$group->id][$langId]->description) $updates['description'] = (string)$name;

        // Do the update
        if ($updates) {
          $this->out(sprintf('Update customer_group_description %s (%s) with differences %s', $group->id, $langName, json_encode($updates)));

          $this->shopConn()->table(self::PREFIX . 'customer_group_description')
            ->where([
              'customer_group_id' => $group->id,
              'language_id' => $langId,
            ])
            ->update($updates);
        }
      }
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
          'location' => 'account',
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
      DELETE FROM `' . self::PREFIX . 'custom_field` WHERE `location` = "account" AND `custom_field_id` NOT IN (' . implode(',', $allIds) . ')
    ');
    $this->cleanupEmptyRelations('custom_field_description', 'custom_field', 'custom_field_id');
    $this->cleanupEmptyRelations('custom_field_customer_group', 'custom_field', 'custom_field_id');
  }

  protected function syncCustomersShop2Erp($customers): void
  {
    $customersMap = [];
    foreach ($customers as $customer) {
      $customersMap[$customer->id] = $customer;
    }

    $waitingFiledId = $this->customFields['_waitingSyncToErp']['id'];
    $shopCustomers = $this->shopConn()->select('SELECT * FROM `' . self::PREFIX . 'customer`');
    foreach ($shopCustomers as $shopCustomer) {
      $shopCustomer->custom_field = json_decode($shopCustomer->custom_field ?: '{}', true);
      $waitingForUpdate = !empty($shopCustomer->custom_field[$waitingFiledId]);
      $isNewCustomer = false;

      /* @var $customer Customer */
      $customer = $customersMap[$shopCustomer->customer_id] ?? null;

      // Add to erp
      if (!$customer) {
        $this->out(sprintf('Add customer %s to ERP', $shopCustomer->customer_id));

        $customer = new Customer();
        $customer->id = $shopCustomer->customer_id;
        $customer->firstName = $shopCustomer->firstname;
        $customer->lastName = $shopCustomer->lastname;
        $customer->email = $shopCustomer->email;
        $customer->password = $shopCustomer->password;
        $customer->statusType = (bool)$shopCustomer->status ? CustomerStatusType::Customer->value : CustomerStatusType::WaitingApproval->value;
        $customer->save();

        // Reload the customer
        $customer = Customer::where('id', $customer->id)->first();

        // Set as match and waiting
        $waitingForUpdate = true;

        // Mark it as new customer (to send emails after filing all the data)
        $isNewCustomer = true;
      }

      // Update the customer (if needed)
      if ($waitingForUpdate) {
        // Compare the records and put them in a map to debug where the differences
        if ($customer->firstName !== $shopCustomer->firstname) $customer->firstName = $shopCustomer->firstname;
        if ($customer->lastName !== $shopCustomer->lastname) $customer->lastName = $shopCustomer->lastname;
        if ($customer->email !== $shopCustomer->email) $customer->email = $shopCustomer->email;
        if ($customer->password !== $shopCustomer->password) $customer->password = $shopCustomer->password;
        $preferredLang = strtolower(parent::$languages[$shopCustomer->language_id] ?? 'bg');
        if ($customer->preferredLang !== $preferredLang) $customer->preferredLang = $preferredLang;

        // Compare custom fields
        foreach ($this->customFields as $fieldKey => $field) {
          if (!array_key_exists($field['id'], $shopCustomer->custom_field ?: [])) {
            continue;
          }

          if (!$customer->hasAttribute($fieldKey)) {
            continue;
          }

          if ($shopCustomer->custom_field[$field['id']] !== $customer->{$fieldKey}) {
            $value = $shopCustomer->custom_field[$field['id']];

            if (in_array('bool', $field['rules'])) {
              $value = (bool)$value;
            }

            if (in_array('blankToNull', $field['rules'])) {
              if ($value === '') {
                $value = null;
              }
            }

            $customer->{$fieldKey} = $value;
          }
        }

        // Mark the customer as not waiting
        $shopCustomer->custom_field[$waitingFiledId] = false;

        $this->shopConn()->table(self::PREFIX . 'customer')
          ->where('customer_id', $shopCustomer->customer_id)
          ->update([
            'custom_field' => json_encode($shopCustomer->custom_field)
          ]);

        // Do the update
        if ($customer->getDirty()) {
          $dirty = $customer->getDirty();

          $this->out(sprintf('Update customer (ERP) %s with differences %s', $customer->id, json_encode($dirty)));
          $customer->save();

          if (isset($dirty['creditLineRequestValue'])) {
            $mailMaker = new MailMakerService();
            $mailMaker->customerCreditLineRequestNotify($customer->id);
          }
        }
      }

      // Send mails
      if ($isNewCustomer) {
        $mailMaker = new MailMakerService();
        $mailMaker->customerWelcome($customer->id);
        $mailMaker->customerWelcomeNotify($customer->id);
      }
    }
  }

  protected function syncCustomersErp2Shop($customers): void
  {
    $shopCustomers = $this->dictionarizeShopRecords('customer', 'customer_id');

    /* @var $customers Customer[] */
    foreach ($customers as $customer) {
      // Add new record
      if (!isset($shopCustomers[$customer->id])) {
        $this->out(sprintf('Add customer %s', $customer->id));

        $this->shopConn()->table(self::PREFIX . 'customer')->insert([
          'customer_id' => $customer->id,
          'customer_group_id' => $customer->groupId,
          'store_id' => $this->storeId,
          'language_id' => 1,
          'firstname' => $customer->firstName,
          'lastname' => $customer->lastName,
          'email' => $customer->email,
          'telephone' => '',
          'password' => $customer->password,
          'custom_field' => '{}',
          'newsletter' => 0,
          'ip' => '127.0.0.1',
          'status' => \App\Services\MapService::customerStatusType($customer->statusType)->allowInShop,
          'safe' => 0,
          'token' => '',
          'code' => '',
          'date_added' => $customer->createdAt,
        ]);
        $shopCustomers[$customer->id] = $this->shopConn()->table(self::PREFIX . 'customer')
          ->where('customer_id', $customer->id)
          ->first();
      }

      // Compare the records and put them in a map to debug where the differences
      $updates = [];
      $isActive = \App\Services\MapService::customerStatusType($customer->statusType)->allowInShop;
      if ($this->storeId !== (int)$shopCustomers[$customer->id]->store_id) $updates['store_id'] = $this->storeId;
      if ((int)$customer->groupId !== (int)$shopCustomers[$customer->id]->customer_group_id) $updates['customer_group_id'] = (int)$customer->groupId;
      if ((string)$customer->firstName !== (string)$shopCustomers[$customer->id]->firstname) $updates['firstname'] = (string)$customer->firstName;
      if ((string)$customer->lastName !== (string)$shopCustomers[$customer->id]->lastname) $updates['lastname'] = (string)$customer->lastName;
      if ((string)$customer->email !== (string)$shopCustomers[$customer->id]->email) $updates['email'] = (string)$customer->email;
      if ((string)$customer->password !== (string)$shopCustomers[$customer->id]->password) $updates['password'] = (string)$customer->password;
      if ($isActive !== (bool)$shopCustomers[$customer->id]->status) $updates['status'] = $isActive;
      $languageId = array_search(ucfirst($customer->preferredLang), parent::$languages) ?: array_keys(parent::$languages)[0];
      if ((int)$languageId !== (int)$shopCustomers[$customer->id]->language_id) $updates['language_id'] = (int)$languageId;

      // Compare custom fields
      $shopCustomFields = json_decode($shopCustomers[$customer->id]->custom_field, true) ?: [];

      foreach ($this->customFields as $fieldKey => $field) {
        if (!array_key_exists($field['id'], $shopCustomFields) || (string)$shopCustomFields[$field['id']] !== (string)$customer->{$fieldKey}) {
          $updates['custom_field'][$field['id']] = (string)$customer->{$fieldKey};
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
        $this->out(sprintf('Update customer (SHOP) %s with differences %s', $customer->id, json_encode($_customFieldsLog ? [...$updates, 'custom_field' => $_customFieldsLog] : $updates)));

        $this->shopConn()->table(self::PREFIX . 'customer')
          ->where('customer_id', $customer->id)
          ->update($updates);
      }

      // Send welcome mail
      if (!empty($updates['status'])) {
        $mailMaker = new MailMakerService();
        $mailMaker->customerApproved($customer->id);
      }
    }
  }

  protected function syncPromotions($groups): void
  {
    $affected = 0;

    /* @var $groups CustomersGroup[] */
    foreach ($groups as $group) {
      if ($group->discountPercent) {
        // Add missing records
        $affected += $this->shopConn()->affectingStatement('
          INSERT INTO `' . self::PREFIX . 'product_special` (
            `product_id`,
            `customer_group_id`,
            `price`,
            `date_start`,
            `date_end`
          )
          SELECT
              `p`.`product_id`,
              ' . $group->id . ',
               `p`.`price` * (1 - (' . $group->discountPercent . ' / 100)),
              \'2000-01-01\',
              \'2500-01-01\'
          FROM
              `' . self::PREFIX . 'product` AS `p`
          LEFT JOIN
              `' . self::PREFIX . 'product_special` AS `ps` ON `p`.`product_id` = `ps`.`product_id` AND `ps`.`customer_group_id` = ' . $group->id . '
          WHERE
              `ps`.`product_id` IS NULL
        ');

        // Update existing records
        $affected += $this->shopConn()->affectingStatement('
          UPDATE `' . self::PREFIX . 'product_special` AS `ps`
          JOIN `' . self::PREFIX . 'product` AS `p` ON `p`.`product_id` = `ps`.`product_id`
          SET `ps`.`price` =  `p`.`price` * (1 - (' . $group->discountPercent . ' / 100))
          WHERE `ps`.`customer_group_id` = ' . $group->id . '
        ');
      } else {
        $affected += $this->shopConn()->affectingStatement('
          DELETE FROM `' . self::PREFIX . 'product_special`  WHERE customer_group_id = ' . $group->id . '
        ');
      }
    }

    if ($affected) {
      $this->out(sprintf('Affected price discount records/operations %s', $affected));

      $this->resetCache = true;
    }
  }

  protected function cleanup(): void
  {
    $this->cleanupEmptyRelations('address', 'customer', 'customer_id');
    $this->cleanupEmptyRelations('customer_group_description', 'customer_group', 'customer_group_id');

    if ($this->resetCache) {
      $this->deleteRedisKeys('product');
    }
  }
}
