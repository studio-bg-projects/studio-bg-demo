<?php

namespace App\Services\Jobs;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SyncCategoriesJob extends BaseSyncJob
{
  public function run(): void
  {
    $categories = Category::all();

    $this->syncCategories($categories);
    $this->syncCategoriesDescription($categories);
    $this->syncCategoriesToStore($categories);
    $this->syncProductRelations();
    $this->cleanup();

    $this->out('All good :)');
  }

  protected function syncCategories($categories): void
  {
    $shopCategories = $this->dictionarizeShopRecords('category', 'category_id');
    $setInactive = $shopCategories; // Collect records to be inactive

    /* @var $categories Category[] */
    foreach ($categories as $category) {
      // Add new record
      if (!isset($shopCategories[$category->id])) {
        $this->out(sprintf('Add category %s', $category->id));

        $this->shopConn()->table(self::PREFIX . 'category')->insert([
          'category_id' => $category->id,
          'image' => '',
          'parent_id' => 0,
          'top' => 1,
          'column' => 0,
          'sort_order' => 0,
          'status' => 0,
          'date_modified' => now(),
          'date_added' => now(),
        ]);
        $shopCategories[$category->id] = $this->shopConn()->table(self::PREFIX . 'category')
          ->where('category_id', $category->id)
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$category->id])) unset($setInactive[$category->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      // Default compare
      if ((int)$category->parentId !== (int)$shopCategories[$category->id]->parent_id) $updates['parent_id'] = (int)$category->parentId;
      if ((int)$category->sortOrder !== (int)$shopCategories[$category->id]->sort_order) $updates['sort_order'] = (int)$category->sortOrder;
      if ((int)$category->isActive !== (int)$shopCategories[$category->id]->status) $updates['status'] = (int)$category->isActive;
      if ((int)$category->isHidden !== (int)$shopCategories[$category->id]->_is_hidden) $updates['_is_hidden'] = (int)$category->isHidden;
      if ((int)$category->isHomeSlider !== (int)$shopCategories[$category->id]->_is_home_slider) $updates['_is_home_slider'] = (int)$category->isHomeSlider;

      // Image compare
      $image = $category->uploads->first() ? 'erp/' . $category->uploads->first()->urls->path : '';
      if ($image !== (string)$shopCategories[$category->id]->image) $updates['image'] = $image;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update category %s with differences %s', $category->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'category')
          ->where('category_id', $category->id)
          ->update([
            ...$updates,
            'date_modified' => now(),
          ]);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopCategory) {
      if (!$shopCategory->status) {
        continue;
      }

      $this->out(sprintf('Set inactive category %s (not exists in ERP)', $shopCategory->category_id));

      $this->shopConn()->table(self::PREFIX . 'category')
        ->where('category_id', $shopCategory->category_id)
        ->update([
          'status' => 0,
          'date_modified' => now(),
        ]);
    }
  }

  protected function syncCategoriesDescription($categories): void
  {
    $shopCategoryDescription = $this->dictionarizeShopRecords('category_description', 'category_id', 'language_id');

    foreach (self::$languages as $langId => $langName) {
      /* @var $categories Category[] */
      foreach ($categories as $category) {
        // Add new record
        if (!isset($shopCategoryDescription[$category->id][$langId])) {
          $this->out(sprintf('Add category_description %s (%s)', $category->id, $langName));

          $this->shopConn()->table(self::PREFIX . 'category_description')->insert([
            'category_id' => $category->id,
            'language_id' => $langId,
            'name' => '',
            'description' => '',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keyword' => '',
          ]);
          $shopCategoryDescription[$category->id][$langId] = $this->shopConn()->table(self::PREFIX . 'category_description')
            ->where('category_id', $category->id)
            ->where('language_id', $langId)->first();
        }

        // Compare the records and put them in a map to debug where the differences
        $updates = [];

        $name = $category->{'name' . $langName};
        if ((string)$name !== (string)$shopCategoryDescription[$category->id][$langId]->name) $updates['name'] = (string)$name;
        if ((string)$name !== (string)$shopCategoryDescription[$category->id][$langId]->meta_title) $updates['meta_title'] = (string)$name;

        // Do the update
        if ($updates) {
          $this->out(sprintf('Update category_description %s (%s) with differences %s', $category->id, $langName, json_encode($updates)));

          $this->shopConn()->table(self::PREFIX . 'category_description')
            ->where([
              'category_id' => $category->id,
              'language_id' => $langId,
            ])
            ->update($updates);
        }
      }
    }
  }

  protected function syncCategoriesToStore($categories): void
  {
    $storeId = 0;

    $shopCategoryToStore = $this->dictionarizeShopRecords('category_to_store', 'category_id', 'store_id');

    foreach ($categories as $category) {
      if (!isset($shopCategoryToStore[$category->id][$storeId])) {
        $this->out(sprintf('Add category_to_store %s', $category->id));

        $this->shopConn()->table(self::PREFIX . 'category_to_store')->insert([
          'category_id' => $category->id,
          'store_id' => $storeId
        ]);
      }
    }
  }

  protected function syncProductRelations(): void
  {
    // All relations in ERP
    $erpMap = [];
    foreach (DB::table('categoriesProducts')->select()->get() as $row) {
      $erpMap[$row->categoryId][$row->productId] = true;
    }

    // All relations in shop
    $shopMap = [];
    foreach ($this->shopConn()->table(self::PREFIX . 'product_to_category')->select()->get() as $row) {
      $shopMap[$row->category_id][$row->product_id] = true;
    }

    // Add missing records
    foreach ($erpMap as $categoryId => $products) {
      foreach ($products as $productId => $tmp) {
        if (!isset($shopMap[$categoryId][$productId])) {
          $this->out(sprintf('Add product_to_category - Category::%s / Product::%s', $categoryId, $productId));

          $this->shopConn()->table(self::PREFIX . 'product_to_category')->insert([
            'product_id' => $productId,
            'category_id' => $categoryId,
          ]);
        }
      }
    }

    // Delete non existing records
    foreach ($shopMap as $categoryId => $products) {
      foreach ($products as $productId => $tmp) {
        if (!isset($erpMap[$categoryId][$productId])) {
          $this->out(sprintf('Delete product_to_category - Category::%s / Product::%s', $categoryId, $productId));

          $this->shopConn()->table(self::PREFIX . 'product_to_category')
            ->where([
              'product_id' => $productId,
              'category_id' => $categoryId,
            ])
            ->delete();
        }
      }
    }
  }

  protected function cleanup(): void
  {
    $this->cleanupEmptyRelations('category_description', 'category', 'category_id');
    $this->cleanupEmptyRelations('category_filter', 'category', 'category_id');
    $this->cleanupEmptyRelations('category_path', 'category', 'category_id');
    $this->cleanupEmptyRelations('category_to_layout', 'category', 'category_id');
    $this->cleanupEmptyRelations('category_to_store', 'category', 'category_id');
    $this->cleanupEmptyRelations('coupon_category', 'category', 'category_id');

    $this->cleanupEmptyRelations('product_to_category', 'category', 'category_id');
  }
}
