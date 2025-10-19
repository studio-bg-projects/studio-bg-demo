<?php

namespace App\Services\Jobs;

use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;

class SyncSeoJob extends BaseSyncJob
{
  public function run(): void
  {
    $seoMap = [];
    $items = [];

    // Collect shop seo data
    foreach ($this->shopConn()->table(self::PREFIX . 'seo_url')->select()->get() as $row) {
      $seoMap[$row->store_id][$row->language_id][$row->key][$row->value] = $row;
    }

    $setInactive = $seoMap;

    // Items
    foreach (Category::all() as $category) {
      $items[] = (object)[
        'qKey' => 'path',
        'qValue' => $category->id,
        'slugBg' => $this->slug('c-' . $category->id . '-' . $category->nameBg),
        'slugEn' => $this->slug('c-' . $category->id . '-' . $category->nameEn),
      ];
    }

    foreach (Manufacturer::all() as $manufacturer) {
      $items[] = (object)[
        'qKey' => 'manufacturer_id',
        'qValue' => $manufacturer->id,
        'slugBg' => $this->slug('m-' . $manufacturer->id . '-' . $manufacturer->name),
        'slugEn' => $this->slug('m-' . $manufacturer->id . '-' . $manufacturer->name),
      ];
    }

    foreach (Product::all() as $product) {
      $items[] = (object)[
        'qKey' => 'product_id',
        'qValue' => $product->id,
        'slugBg' => $this->slug('p-' . $product->id . '-' . $product->nameBg),
        'slugEn' => $this->slug('p-' . $product->id . '-' . $product->nameBg),
      ];
    }

    // Sync
    foreach (self::$languages as $langId => $langName) {
      foreach ($items as $item) {
        $shopRecord = $seoMap[$this->storeId][$langId][$item->qKey][$item->qValue] ?? null;
        $slug = $item->{'slug' . $langName} . '-' . $langName;

        // Remove from inactive
        if ($shopRecord) unset($setInactive[$this->storeId][$langId][$item->qKey][$item->qValue]);

        // Add new record
        if (!isset($shopRecord)) {
          $this->out(sprintf('Add seo_url %s=%s (%s) - %s', $item->qKey, $item->qValue, $langName, $slug));

          $this->shopConn()->table(self::PREFIX . 'seo_url')->insert([
            'store_id' => $this->storeId,
            'language_id' => $langId,
            'key' => $item->qKey,
            'value' => $item->qValue,
            'keyword' => $slug,
            'sort_order' => 0,
          ]);

          continue;
        }

        if ($slug !== $shopRecord->keyword) {
          $this->out(sprintf('Update seo_url %s=%s (%s) - %s', $item->qKey, $item->qValue, $langName, $slug));

          $this->shopConn()->table(self::PREFIX . 'seo_url')
            ->where([
              'seo_url_id' => $shopRecord->seo_url_id,
            ])
            ->update(['keyword' => $slug]);
        }
      }
    }

    // Clear
    if (isset($setInactive['information_id'])) unset($setInactive['information_id']);

    foreach ($setInactive as $storeId => $languages) {
      foreach ($languages as $langId => $keys) {
        foreach ($keys as $queryKey => $values) {
          foreach ($values as $valueId => $row) {
            if ($queryKey === 'information_id') {
              continue;
            }

            $this->out(sprintf('Delete seo_url_id %s', json_encode([
              'storeId' => $storeId,
              'langId' => $langId,
              'queryKey' => $queryKey,
              'valueId' => $valueId,
              'seo_url_id' => $row->seo_url_id,
            ])));

            $this->shopConn()->table(self::PREFIX . 'seo_url')
              ->where([
                'seo_url_id' => $row->seo_url_id,
              ])
              ->delete();
          }
        }
      }
    }

    $this->out('All good :)');
  }
}
