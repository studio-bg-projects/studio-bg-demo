<?php

namespace App\Services\Jobs;

use App\Models\Category;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

class SyncSpecificationsJob extends BaseSyncJob
{
  public function run(): void
  {
    $categories = Category::whereHas('specifications')->get();

    $this->syncGroup($categories);
    $this->syncGroupDescription($categories);
    $this->syncSpecifications($categories);
    $this->syncSpecificationsDescription($categories);
    $this->syncProductRelations();
    $this->cleanup();

    $this->out('All good :)');
  }

  protected function syncGroup($categories): void
  {
    $shopGroups = $this->dictionarizeShopRecords('attribute_group', 'attribute_group_id');
    $setInactive = $shopGroups; // Collect records to be inactive

    /* @var $categories Category[] */
    foreach ($categories as $category) {
      // Add new record
      if (!isset($shopGroups[$category->id])) {
        $this->out(sprintf('Add attribute_group %s', $category->id));

        $this->shopConn()->table(self::PREFIX . 'attribute_group')->insert([
          'attribute_group_id' => $category->id,
          'sort_order' => $category->sortOrder,
        ]);
        $shopGroups[$category->id] = $this->shopConn()->table(self::PREFIX . 'attribute_group')
          ->where('attribute_group_id', $category->id)->first();
      }

      // Remove from inactive
      if (isset($setInactive[$category->id])) unset($setInactive[$category->id]);
    }

    // Delete or set inactive
    foreach ($setInactive as $shopGroup) {
      $this->out(sprintf('Delete attribute_group %s (not exists or inactive in ERP)', $shopGroup->attribute_group_id));

      $this->shopConn()->table(self::PREFIX . 'attribute_group')
        ->where('attribute_group_id', $shopGroup->attribute_group_id)
        ->delete();
    }
  }

  protected function syncGroupDescription($categories): void
  {
    $shopGroupsDescr = $this->dictionarizeShopRecords('attribute_group_description', 'attribute_group_id', 'language_id');
    $setInactive = $shopGroupsDescr; // Collect records to be inactive

    foreach (self::$languages as $langId => $langName) {
      /* @var $categories Category[] */
      foreach ($categories as $category) {
        // Add new record
        if (!isset($shopGroupsDescr[$category->id][$langId])) {
          $this->out(sprintf('Add attribute_group_description %s', $category->id));

          $this->shopConn()->table(self::PREFIX . 'attribute_group_description')->insert([
            'attribute_group_id' => $category->id,
            'language_id' => $langId,
            'name' => 0,
          ]);
          $shopGroupsDescr[$category->id][$langId] = $this->shopConn()->table(self::PREFIX . 'attribute_group_description')
            ->where('attribute_group_id', $category->id)
            ->where('language_id', $langId)->first();
        }

        // Remove from inactive
        if (isset($setInactive[$category->id][$langId])) unset($setInactive[$category->id][$langId]);

        // Compare the records and put them in a map to debug where the differences
        $updates = [];

        $name = $category->{'name' . $langName};
        if ((string)$name !== (string)$shopGroupsDescr[$category->id][$langId]->name) $updates['name'] = (string)$name;

        // Do the update
        if ($updates) {
          $this->out(sprintf('Update attribute_group_description %s lang %s with differences %s', $category->id, $langId, json_encode($updates)));

          $this->shopConn()->table(self::PREFIX . 'attribute_group_description')
            ->where('attribute_group_id', $category->id)
            ->where('language_id', $langId)
            ->update($updates);
        }
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopGroupsDescr) {
      foreach ($shopGroupsDescr as $shopGroupDescr) {
        $this->out(sprintf('Delete attribute_group_description %s / lang id %s (not exists in ERP)', $shopGroupDescr->attribute_group_id, $shopGroupDescr->language_id));

        $this->shopConn()->table(self::PREFIX . 'attribute_group_description')
          ->where('attribute_group_id', $shopGroupDescr->attribute_group_id)
          ->where('language_id', $shopGroupDescr->language_id)
          ->delete();
      }
    }
  }

  protected function syncSpecifications($categories): void
  {
    $shopAttributes = $this->dictionarizeShopRecords('attribute', 'attribute_id');
    $setInactive = $shopAttributes; // Collect records to be inactive

    /* @var $categories Category[] */
    foreach ($categories as $category) {
      /* @var $specification Specification */
      foreach ($category->specifications as $specification) {
        if (!$specification->isActive) {
          continue;
        }

        $attributeId = $specification->pivot->id;

        // Add new record
        if (!isset($shopAttributes[$attributeId])) {
          $this->out(sprintf('Add specification (Attribute ID: %s)', $attributeId));

          $this->shopConn()->table(self::PREFIX . 'attribute')->insert([
            'attribute_id' => $attributeId,
            'attribute_group_id' => $category->id,
            'sort_order' => $specification->pivot->sortOrder,
          ]);
          $shopAttributes[$attributeId] = $this->shopConn()->table(self::PREFIX . 'attribute')
            ->where('attribute_id', $attributeId)->first();
        }

        // Remove from inactive
        if (isset($setInactive[$attributeId])) unset($setInactive[$attributeId]);

        // Compare the records and put them in a map to debug where the differences
        $updates = [];
        // $valueType = in_array($specification->valueType, ['string', 'integer', 'float']) ? $specification->valueType : 'string';
        // if ($valueType !== (string)$shopAttributes[$attributeId]->values_type) $updates['values_type'] = $valueType;
        if ((int)$specification->sortOrder !== (int)$shopAttributes[$attributeId]->sort_order) $updates['sort_order'] = (int)$specification->sortOrder;

        // Do the update
        if ($updates) {
          $this->out(sprintf('Update specification %s with differences %s', $attributeId, json_encode($updates)));

          $this->shopConn()->table(self::PREFIX . 'attribute')
            ->where('attribute_id', $attributeId)
            ->update($updates);
        }
      }
    }

    // Delete or set inactive
    foreach ($setInactive as $shopAttribute) {
      $this->out(sprintf('Delete specification %s (not exists in ERP)', $shopAttribute->attribute_id));

      $this->shopConn()->table(self::PREFIX . 'attribute')
        ->where('attribute_id', $shopAttribute->attribute_id)
        ->delete();
    }
  }

  protected function syncSpecificationsDescription($categories): void
  {
    $shopAttributeDescription = $this->dictionarizeShopRecords('attribute_description', 'attribute_id', 'language_id');

    foreach (self::$languages as $langId => $langName) {
      /* @var $categories Category[] */
      foreach ($categories as $category) {
        /* @var $specification Specification */
        foreach ($category->specifications as $specification) {
          if (!$specification->isActive) {
            continue;
          }

          $attributeId = $specification->pivot->id;

          // Add new record
          if (!isset($shopAttributeDescription[$attributeId][$langId])) {
            $this->out(sprintf('Add specification_description %s (%s)', $attributeId, $langName));

            $this->shopConn()->table(self::PREFIX . 'attribute_description')->insert([
              'attribute_id' => $attributeId,
              'language_id' => $langId,
              'name' => '',
            ]);
            $shopAttributeDescription[$attributeId][$langId] = $this->shopConn()->table(self::PREFIX . 'attribute_description')
              ->where('attribute_id', $attributeId)
              ->where('language_id', $langId)->first();
          }

          // Compare the records and put them in a map to debug where the differences
          $updates = [];

          $name = $specification->{'name' . $langName};
          if ((string)$name !== (string)$shopAttributeDescription[$attributeId][$langId]->name) $updates['name'] = (string)$name;

          // Do the update
          if ($updates) {
            $this->out(sprintf('Update specification_description %s (%s) with differences %s', $attributeId, $langName, json_encode($updates)));

            $this->shopConn()->table(self::PREFIX . 'attribute_description')
              ->where([
                'attribute_id' => $attributeId,
                'language_id' => $langId,
              ])
              ->update($updates);
          }
        }
      }
    }
  }

  protected function syncProductRelations(): void
  {
    // All relations in ERP
    $erpAttrIdMap = [];
    foreach (DB::select('SELECT * FROM `categoriesSpecifications` WHERE `specificationId` NOT IN (SELECT `id` FROM `specifications` WHERE `isActive` = false)') as $row) {
      $attributeId = $row->id;
      $erpAttrIdMap[$row->categoryId][$row->specificationId] = $attributeId;
    }

    $erpMap = [];
    foreach (DB::table('productsSpecifications')->select()->get() as $row) {
      $attributeId = $erpAttrIdMap[$row->categoryId][$row->specificationId] ?? null;

      if (!isset($attributeId)) {
        continue;
      }

      $erpMap[$attributeId][$row->productId] = $row;
    }

    // All relations in shop
    $shopMap = [];
    foreach ($this->shopConn()->table(self::PREFIX . 'product_attribute')->select()->get() as $row) {
      $shopMap[$row->attribute_id][$row->product_id][$row->language_id] = $row->text;
    }

    // Add/update records
    foreach (self::$languages as $langId => $langName) {
      foreach ($erpMap as $attributeId => $products) {
        foreach ($products as $productId => $attribute) {
          $value = $attribute->{'specificationValue' . $langName};

          if (!isset($shopMap[$attributeId][$productId][$langId])) {
            // Add
            $this->out(sprintf('Add product_attribute - Attribute::%s / Product::%s (%s)', $attributeId, $productId, $langName));

            $this->shopConn()->table(self::PREFIX . 'product_attribute')->insert([
              'product_id' => $productId,
              'attribute_id' => $attributeId,
              'language_id' => $langId,
              'text' => $value,
            ]);
          } else {
            // Update
            if ($shopMap[$attributeId][$productId][$langId] !== $value) {
              $this->out(sprintf('Update product_attribute - Attribute::%s / Product::%s (%s) - %s', $attributeId, $productId, $langName, $value));

              $this->shopConn()->table(self::PREFIX . 'product_attribute')
                ->where([
                  'product_id' => $productId,
                  'attribute_id' => $attributeId,
                  'language_id' => $langId,
                ])
                ->update(['text' => $value]);
            }
          }
        }
      }
    }

    // Delete non-existing records
    foreach ($shopMap as $specificationId => $products) {
      foreach ($products as $productId => $rLanguages) {
        foreach ($rLanguages as $rLanguageId => $tmp) {
          if (!isset($erpMap[$specificationId][$productId])) {
            $this->out(sprintf('Delete product_attribute - Attribute::%s / Product::%s / Language::%s', $specificationId, $productId, $rLanguageId));

            $this->shopConn()->table(self::PREFIX . 'product_attribute')
              ->where([
                'product_id' => $productId,
                'attribute_id' => $specificationId,
                'language_id' => $rLanguageId,
              ])
              ->delete();
          }
        }
      }
    }
  }

  protected function cleanup(): void
  {
    $this->cleanupEmptyRelations('attribute_group_description', 'attribute_group', 'attribute_group_id');
    $this->cleanupEmptyRelations('attribute', 'attribute_group', 'attribute_group_id');
    $this->cleanupEmptyRelations('attribute_description', 'attribute', 'attribute_id');
    $this->cleanupEmptyRelations('product_attribute', 'attribute', 'attribute_id');
  }
}
