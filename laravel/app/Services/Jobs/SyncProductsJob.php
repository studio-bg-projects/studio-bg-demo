<?php

namespace App\Services\Jobs;

use App\Enums\ProductUsageStatus;
use App\Models\Product;
use App\Models\Upload;
use Illuminate\Support\Facades\DB;

class SyncProductsJob extends BaseSyncJob
{
  protected int|null $syncOnly = null;
  protected int $defaultShipping = 1;
  protected int $defaultSubtract = 1;
  protected bool $resetCache = false;

  public function run(int $productId = null): void
  {
    $this->syncOnly = $productId;

    $products = $this->syncOnly ? Product::where(['id' => $this->syncOnly])->get() : Product::all();

    $this->syncProducts($products);
    $this->syncProductsDescription($products);
    $this->syncProductsAddition($products);
    $this->syncProductsRelated();
    $this->syncProductsFeatured();
    $this->syncProductsToStore($products);
    $this->syncProductsImages();
    $this->cleanup();

    $this->out('All good :)');
  }

  protected function syncProducts($products): void
  {
    $shopProductsFilter = $this->syncOnly ? ['product_id' => $this->syncOnly] : [];
    $shopProducts = $this->dictionarizeShopRecords('product', 'product_id', null, null, $shopProductsFilter);
    $setInactive = $shopProducts; // Collect records to be inactive

    /* @var $products Product[] */
    foreach ($products as $product) {
      // Add new record
      if (!isset($shopProducts[$product->id])) {
        $this->out(sprintf('Add product %s', $product->id));

        $this->shopConn()->table(self::PREFIX . 'product')->insert([
          'product_id' => $product->id,
          'model' => '',
          'sku' => '',
          'upc' => '',
          'ean' => '',
          'jan' => '',
          'isbn' => '',
          'mpn' => '',
          'location' => '',
          'variant' => '',
          'override' => '',
          'quantity' => 0,
          'stock_status_id' => 5,
          'image' => '',
          'manufacturer_id' => 0,
          'shipping' => $this->defaultShipping,
          'price' => 0,
          'points' => 0,
          'tax_class_id' => 9,
          'date_available' => '2020-01-01',
          'weight' => 0,
          'weight_class_id' => 1,
          'length' => 0,
          'width' => 0,
          'height' => 0,
          'length_class_id' => 0,
          'subtract' => $this->defaultShipping,
          'minimum' => 0,
          'rating' => 0,
          'sort_order' => 0,
          'status' => 0,
          'date_modified' => now(),
          'date_added' => now(),
        ]);
        $shopProducts[$product->id] = $this->shopConn()->table(self::PREFIX . 'product')
          ->where('product_id', $product->id)
          ->first();

        $this->resetCache = true;
      }

      // Remove from inactive
      if (isset($setInactive[$product->id])) unset($setInactive[$product->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];
      $productIsActive = $product->usageStatus->value == ProductUsageStatus::ListedOnline->value;

      // Default compare
      if ('Inside Trading' !== $shopProducts[$product->id]->model) $updates['model'] = 'Inside Trading';

      // Product compare
      if ((string)$product->ean !== (string)$shopProducts[$product->id]->ean) $updates['ean'] = (string)$product->ean;
      if ((string)$product->mpn !== (string)$shopProducts[$product->id]->mpn) $updates['mpn'] = (string)$product->mpn;
      if ((string)$product->mpn !== (string)$shopProducts[$product->id]->sku) $updates['sku'] = (string)$product->mpn;
      if ((int)$product->quantity !== (int)$shopProducts[$product->id]->quantity) $updates['quantity'] = (int)$product->quantity;
      if ((int)$product->manufacturerId !== (int)$shopProducts[$product->id]->manufacturer_id) $updates['manufacturer_id'] = (int)$product->manufacturerId;
      if ((double)$shopProducts[$product->id]->price !== (double)$product->price) $updates['price'] = (double)$product->price;
      if ((double)$product->weight !== (double)$shopProducts[$product->id]->weight) $updates['weight'] = (double)$product->weight;
      if ((double)$product->length !== (double)$shopProducts[$product->id]->length) $updates['length'] = (double)$product->length;
      if ((double)$product->width !== (double)$shopProducts[$product->id]->width) $updates['width'] = (double)$product->width;
      if ((double)$product->height !== (double)$shopProducts[$product->id]->height) $updates['height'] = (double)$product->height;
      if ($this->defaultShipping !== (int)$shopProducts[$product->id]->shipping) $updates['shipping'] = $this->defaultShipping;
      if ($this->defaultSubtract !== (int)$shopProducts[$product->id]->subtract) $updates['subtract'] = $this->defaultSubtract;
      if ((int)$productIsActive !== (int)$shopProducts[$product->id]->status) $updates['status'] = (int)$productIsActive;

      // Image compare
      $imagePath = $product->uploads->first() ? 'erp/' . $product->uploads->first()->urls->path : '';
      if ($imagePath !== (string)$shopProducts[$product->id]->image) $updates['image'] = $imagePath;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update product %s with differences %s', $product->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'product')
          ->where('product_id', $product->id)
          ->update([
            ...$updates,
            'date_modified' => now(),
          ]);

        $this->resetCache = true;
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopProduct) {
      if (!$shopProduct->status) {
        continue;
      }

      $this->out(sprintf('Set inactive product %s (not exists in ERP)', $shopProduct->product_id));

      $this->shopConn()->table(self::PREFIX . 'product')
        ->where('product_id', $shopProduct->product_id)
        ->update([
          'status' => 0,
          'date_modified' => now(),
        ]);

      $this->resetCache = true;
    }
  }

  protected function syncProductsDescription($products): void
  {
    $shopProductsFilter = $this->syncOnly ? ['product_id' => $this->syncOnly] : [];
    $shopProductDescription = $this->dictionarizeShopRecords('product_description', 'product_id', 'language_id', null, $shopProductsFilter);

    foreach (self::$languages as $langId => $langName) {
      /* @var $products Product[] */
      foreach ($products as $product) {
        // Add new record
        if (!isset($shopProductDescription[$product->id][$langId])) {
          $this->out(sprintf('Add product_description %s (%s)', $product->id, $langName));

          $this->shopConn()->table(self::PREFIX . 'product_description')->insert([
            'product_id' => $product->id,
            'language_id' => $langId,
            'name' => '',
            'description' => '',
            'tag' => '',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keyword' => '',
          ]);
          $shopProductDescription[$product->id][$langId] = $this->shopConn()->table(self::PREFIX . 'product_description')
            ->where('product_id', $product->id)
            ->where('language_id', $langId)->first();

          $this->resetCache = true;
        }

        // Compare the records and put them in a map to debug where the differences
        $updates = [];

        $name = $product->{'name' . $langName};
        $description = $product->{'description' . $langName};

        if ((string)$name !== (string)$shopProductDescription[$product->id][$langId]->name) $updates['name'] = (string)$name;
        if ((string)$name !== (string)$shopProductDescription[$product->id][$langId]->meta_title) $updates['meta_title'] = (string)$name;
        if ((string)$description !== (string)$shopProductDescription[$product->id][$langId]->description) $updates['description'] = (string)$description;

        // Do the update
        if ($updates) {
          $this->out(sprintf('Update product_description %s (%s) with differences %s', $product->id, $langName, json_encode($updates)));

          $this->shopConn()->table(self::PREFIX . 'product_description')
            ->where([
              'product_id' => $product->id,
              'language_id' => $langId,
            ])
            ->update($updates);

          $this->resetCache = true;
        }
      }
    }
  }

  protected function syncProductsAddition($products): void
  {
    $shopProductsFilter = $this->syncOnly ? ['product_id' => $this->syncOnly] : [];
    $shopProductAdditions = $this->dictionarizeShopRecords('product_addition', 'product_id', null, null, $shopProductsFilter);

    $downloadsTmp = Upload::where('groupType', 'productDownloads')
      ->join('products', 'uploads.groupId', '=', 'products.downloadsGroupId')
      ->select('uploads.*', 'products.id AS productId')
      ->orderBy('uploads.sortOrder')
      ->get();
    $downloads = [];
    foreach ($downloadsTmp as $row) {
      if (!isset($downloads[$row->productId])) {
        $downloads[$row->productId] = [];
      }
      $downloads[$row->productId][] = [
        'path' => $row->groupType->value . '/' . $row->groupId . '/' . $row->name,
        'size' => $row->size,
        'originalName' => $row->originalName,
        'extension' => $row->extension,
        'mimeType' => $row->mimeType,
      ];
    }

    /* @var $products Product[] */
    foreach ($products as $product) {
      // Add new record
      if (!isset($shopProductAdditions[$product->id])) {
        $this->out(sprintf('Add product_addition %s', $product->id));

        $this->shopConn()->table(self::PREFIX . 'product_addition')->insert([
          'product_id' => $product->id,
        ]);
        $shopProductAdditions[$product->id] = $this->shopConn()->table(self::PREFIX . 'product_addition')
          ->where('product_id', $product->id)
          ->first();
      }

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      if ((int)$product->warrantyPeriod !== (int)$shopProductAdditions[$product->id]->warranty) $updates['warranty'] = (int)$product->warrantyPeriod;
      if ((int)$product->deliveryDays !== (int)$shopProductAdditions[$product->id]->delivery_days) $updates['delivery_days'] = (int)$product->deliveryDays;
      if ((int)$product->onStock !== (int)$shopProductAdditions[$product->id]->on_stock) $updates['on_stock'] = (int)$product->onStock;
      $productDownloads = !empty($downloads[$product->id]) ? json_encode($downloads[$product->id]) : null;
      if ($productDownloads !== $shopProductAdditions[$product->id]->downloads) $updates['downloads'] = $productDownloads;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update product_addition %s with differences %s', $product->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'product_addition')
          ->where([
            'product_id' => $product->id,
          ])
          ->update($updates);
      }
    }
  }

  protected function syncProductsFeatured(): void
  {
    // Collect
    $featuredIds = [];
    foreach (Product::where('isFeatured', true)->get() as $product) {
      $featuredIds[] = $product->id;
    }

    // Get from OC
    $module = $this->shopConn()->table(self::PREFIX . 'module')
      ->where('code', 'opencart.featured')
      ->first();
    if (!$module) {
      return;
    }

    $setting = json_decode($module->setting);
    $setting->product = $featuredIds;

    if (json_encode($setting) !== $module->setting) {
      $this->out(sprintf('Set opencart.featured ids: %s', json_encode($featuredIds)));

      $this->shopConn()->table(self::PREFIX . 'module')
        ->where('code', 'opencart.featured')
        ->update([
          'setting' => json_encode($setting)
        ]);
    }
  }

  protected function syncProductsRelated(): void
  {
    $shopRelated = $this->dictionarizeShopRecords('product_related', 'product_id', 'related_id');

    $related = [];
    foreach (DB::table('productRelated')->get() as $row) {
      if (!isset($related[$row->productId])) {
        $related[$row->productId] = [];
      }
      $related[$row->productId][] = $row->relatedId;
      $related[$row->productId] = array_unique($related[$row->productId]);

      if (!isset($related[$row->relatedId])) {
        $related[$row->relatedId] = [];
      }
      $related[$row->relatedId][] = $row->productId;
      $related[$row->relatedId] = array_unique($related[$row->relatedId]);
    }

    // Add missing records
    foreach ($related as $productId => $relatedIds) {
      foreach ($relatedIds as $relatedId) {
        // Add new record
        if (!isset($shopRelated[$productId][$relatedId])) {
          $this->out(sprintf('Add product_related %s', $productId));

          $this->shopConn()->table(self::PREFIX . 'product_related')->insert([
            'product_id' => $productId,
            'related_id' => $relatedId,
          ]);

          $this->resetCache = true;
        } else {
          // Cleanup
          unset($shopRelated[$productId][$relatedId]);
        }
      }
    }

    // Delete old records
    foreach ($shopRelated as $productId => $relatedIds) {
      foreach ($relatedIds as $relatedId => $row) {
        $this->out(sprintf('Delete product_related %s - %s', $productId, $relatedId));

        $this->shopConn()->table(self::PREFIX . 'product_related')
          ->where([
            'product_id' => $productId,
            'related_id' => $relatedId,
          ])
          ->delete();

        $this->resetCache = true;
      }
    }
  }

  protected function syncProductsToStore($products): void
  {
    $shopProductToStore = $this->dictionarizeShopRecords('product_to_store', 'product_id', 'store_id');

    foreach ($products as $product) {
      if (!isset($shopProductToStore[$product->id][$this->storeId])) {
        $this->out(sprintf('Add product_to_store %s', $product->id));

        $this->shopConn()->table(self::PREFIX . 'product_to_store')->insert([
          'product_id' => $product->id,
          'store_id' => $this->storeId
        ]);
      }
    }
  }

  protected function syncProductsImages()
  {
    $images = Upload::where('groupType', 'products')
      // ->where('sortOrder', '>', 0)
      ->join('products', 'uploads.groupId', '=', 'products.fileGroupId')
      ->select('uploads.*', 'products.id AS productId')
      ->get();

    $shopProductImages = $this->dictionarizeShopRecords('product_image', 'product_image_id');
    $setInactive = $shopProductImages;

    /* @var $images Upload[] */
    foreach ($images as $image) {
      $productId = $image->productId;

      // Add new record
      if (!isset($shopProductImages[$image->id])) {
        $this->out(sprintf('Add product_image %s', $image->id));

        $this->shopConn()->table(self::PREFIX . 'product_image')->insert([
          'product_image_id' => $image->id,
          'product_id' => $productId,
          'image' => $image->urls->path,
          'sort_order' => $image->sortOrder,
        ]);
        $shopProductImages[$image->id] = $this->shopConn()->table(self::PREFIX . 'product_image')
          ->where([
            'product_image_id' => $image->id,
          ])
          ->first();
      }

      // Remove from inactive
      if (isset($setInactive[$image->id])) unset($setInactive[$image->id]);

      // Compare the records and put them in a map to debug where the differences
      $updates = [];

      $imagePath = 'erp/' . $image->urls->path;

      if ((int)$productId !== (int)$shopProductImages[$image->id]->product_id) $updates['product_id'] = (int)$productId;
      if ((string)$imagePath !== (string)$shopProductImages[$image->id]->image) $updates['image'] = (string)$imagePath;
      if ((int)$image->sortOrder !== (int)$shopProductImages[$image->id]->sort_order) $updates['sort_order'] = (int)$image->sortOrder;

      // Do the update
      if ($updates) {
        $this->out(sprintf('Update product_image %s with differences %s', $image->id, json_encode($updates)));

        $this->shopConn()->table(self::PREFIX . 'product_image')
          ->where([
            'product_image_id' => $image->id,
          ])
          ->update($updates);
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopImage) {
      $this->out(sprintf('Delete product_image %s (not exists in ERP)', $shopImage->product_image_id));

      $this->shopConn()->table(self::PREFIX . 'product_image')
        ->where('product_image_id', $shopImage->product_image_id)
        ->delete();
    }
  }

  protected function cleanup(): void
  {
    $this->cleanupEmptyRelations('product_addition', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_attribute', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_description', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_discount', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_filter', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_image', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_option', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_option_value', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_related', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_related', 'product', 'related_id');
    $this->cleanupEmptyRelations('product_report', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_reward', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_special', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_subscription', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_to_category', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_to_download', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_to_layout', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_to_store', 'product', 'product_id');
    $this->cleanupEmptyRelations('product_viewed', 'product', 'product_id');

    if ($this->resetCache) {
      $this->deleteRedisKeys('product');
    }
  }
}
