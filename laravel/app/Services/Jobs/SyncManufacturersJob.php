<?php

namespace App\Services\Jobs;

use App\Models\Manufacturer;

class SyncManufacturersJob extends BaseSyncJob
{
  protected bool $resetCache = false;

  public function run(): void
  {
    $manufacturers = Manufacturer::all();

    $this->syncManufacturer($manufacturers);
    $this->syncManufacturersToStore($manufacturers);
    $this->cleanup();

    $this->out('All good :)');
  }

  public function syncManufacturer($manufacturers): void
  {
    $shopManufacturers = $this->dictionarizeShopRecords('manufacturer', 'manufacturer_id');
    $setInactive = $shopManufacturers; // Collect records to be inactive

    /* @var $manufacturers Manufacturer[] */
    foreach ($manufacturers as $manufacturer) {
      if (!$manufacturer->isActive) {
        continue;
      }

      // Add new record
      if (!isset($shopManufacturers[$manufacturer->id])) {
        $this->out(sprintf('Add manufacturer %s', $manufacturer->id));

        $this->shopConn()->table(self::PREFIX . 'manufacturer')->insert([
          'manufacturer_id' => $manufacturer->id,
          'name' => '',
          'image' => '',
          'sort_order' => '0',
        ]);
        $shopManufacturers[$manufacturer->id] = $this->shopConn()->table(self::PREFIX . 'manufacturer')
          ->where('manufacturer_id', $manufacturer->id)
          ->first();

        $this->resetCache = true;
      }

      // Remove from inactive
      if (isset($setInactive[$manufacturer->id])) unset($setInactive[$manufacturer->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Manufacturer compare
      if ((string)$manufacturer->name !== (string)$shopManufacturers[$manufacturer->id]->name) $updates['name'] = (string)$manufacturer->name;
      if ((int)$manufacturer->sortOrder !== (int)$shopManufacturers[$manufacturer->id]->sort_order) $updates['sort_order'] = (int)$manufacturer->sortOrder;

      // Image compare
      $image = $manufacturer->uploads->first() ? 'erp/' . $manufacturer->uploads->first()->urls->path : '';
      if ($image !== (string)$shopManufacturers[$manufacturer->id]->image) $updates['image'] = $image;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update manufacturer %s with differences %s', $manufacturer->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'manufacturer')
          ->where('manufacturer_id', $manufacturer->id)
          ->update($updates);

        $this->resetCache = true;
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopManufacturer) {
      $this->out(sprintf('Delete inactive manufacturer %s (not exists in ERP)', $shopManufacturer->manufacturer_id));

      $this->shopConn()->table(self::PREFIX . 'manufacturer')
        ->where('manufacturer_id', $shopManufacturer->manufacturer_id)
        ->delete();

      $this->resetCache = true;
    }
  }

  protected function syncManufacturersToStore($manufacturers): void
  {
    $shopManufacturersToStore = $this->dictionarizeShopRecords('manufacturer_to_store', 'manufacturer_id', 'store_id');

    foreach ($manufacturers as $manufacturer) {
      if (!$manufacturer->isActive) {
        continue;
      }

      if (!isset($shopManufacturersToStore[$manufacturer->id][$this->storeId])) {
        $this->out(sprintf('Add manufacturer_to_store %s', $manufacturer->id));

        $this->shopConn()->table(self::PREFIX . 'manufacturer_to_store')->insert([
          'manufacturer_id' => $manufacturer->id,
          'store_id' => $this->storeId
        ]);

        $this->resetCache = true;
      }
    }
  }

  protected function cleanup(): void
  {
    $this->cleanupEmptyRelations('manufacturer_to_store', 'manufacturer', 'manufacturer_id');

    if ($this->resetCache) {
      $this->deleteRedisKeys('manufacturer');
    }
  }
}
