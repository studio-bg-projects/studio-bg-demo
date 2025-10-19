<?php

namespace App\Services\Jobs;

use App\Enums\SpecificationValueType;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

class SyncFiltersJob extends BaseSyncJob
{
  public function run(): void
  {
    $this->syncFilters();
    $this->out('All good :)');
  }

  protected function syncFilters(): void
  {
    // Shop data
    $shopFilters = $this->dictionarizeShopRecords('extend_filter', 'extend_filter_id');
    foreach ($shopFilters as $shopFilter) {
      $shopFilter->name = json_decode($shopFilter->name);
      $shopFilter->values = json_decode($shopFilter->values);
    }
    $setInactive = $shopFilters; // Collect records to be inactive

    // Spec map
    /* @var $specMap Specification[] */
    $specMap = [];
    foreach (Specification::where('isActive', true)->get() as $row) {
      $specMap[$row->id] = $row;
    }

    // Product specifications
    $prodSpecsTmp = DB::select('
      SELECT `productId`, `categoryId`, `specificationId`, `specificationValueBg`, `specificationValueEn`
      FROM `productsSpecifications`
    ');
    $prodSpecsMap = [];
    foreach ($prodSpecsTmp as $prodSpec) {
      foreach (self::$languages as $langId => $langName) {
        $specVal = $prodSpec->{'specificationValue' . $langName};
        $prodSpecsMap[$prodSpec->categoryId][$prodSpec->specificationId][$langId][$specVal][] = $prodSpec->productId;
      }
    }

    // Category specifications
    $catSpecs = DB::table('categoriesSpecifications')->get();

    foreach ($catSpecs as $catSpec) {
      /* @var $spec Specification */
      $spec = $specMap[$catSpec->specificationId] ?? null;

      if (!isset($spec)) {
        continue;
      }

      $nameObj = (object)array_map(function ($langName) use ($spec) {
        return $spec->{'name' . $langName};
      }, self::$languages);
      $nameJson = json_encode($nameObj);

      $valuesObj = (object)($prodSpecsMap[$catSpec->categoryId][$catSpec->specificationId] ?? []);
      $valuesJson = json_encode($valuesObj);

      // Add new record
      if (!isset($shopFilters[$catSpec->id])) {
        $this->out(sprintf('Add extend_filter %s', $catSpec->id));

        $this->shopConn()->table(self::PREFIX . 'extend_filter')->insert([
          'extend_filter_id' => $catSpec->id,
          'category_id' => $catSpec->categoryId,
          'sort_order' => $catSpec->sortOrder,
          'name' => $nameJson,
          'type' => $spec->valueType->value,
          'values' => $valuesJson
        ]);
        $shopFilters[$catSpec->id] = $this->shopConn()->table(self::PREFIX . 'extend_filter')
          ->where('extend_filter_id', $catSpec->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$catSpec->id])) unset($setInactive[$catSpec->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Default compare
      if ((int)$catSpec->categoryId !== (int)$shopFilters[$catSpec->id]->category_id) $updates['category_id'] = (int)$catSpec->categoryId;
      if ((int)$catSpec->sortOrder !== (int)$shopFilters[$catSpec->id]->sort_order) $updates['sort_order'] = (int)$catSpec->sortOrder;
      if ($nameJson !== json_encode($shopFilters[$catSpec->id]->name)) $updates['name'] = $nameJson;
      if ($spec->valueType->value !== $shopFilters[$catSpec->id]->type) $updates['type'] = $spec->valueType->value;
      if ($valuesJson != json_encode($shopFilters[$catSpec->id]->values)) $updates['values'] = $valuesJson;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update extend_filter %s with differences %s', $catSpec->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'extend_filter')
          ->where('extend_filter_id', $catSpec->id)
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $extendFilter) {
      $this->out(sprintf('Delete extend_filter %s (not exists in ERP)', $extendFilter->extend_filter_id));

      $this->shopConn()->table(self::PREFIX . 'extend_filter')
        ->where('extend_filter_id', $extendFilter->extend_filter_id)
        ->delete();
    }
  }
}
