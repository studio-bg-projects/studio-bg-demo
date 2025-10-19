<?php

namespace App\Services\Jobs;

use App\Models\StoreSearch;

class SyncStoreSearchesJob extends BaseSyncJob
{
  public function run(): void
  {
    // Get latest record
    $latest = StoreSearch::latest('id')->first();

    // Get the records after the latest sync
    $csQuery = $this->shopConn()->table(self::PREFIX . 'customer_search')
      ->where('store_id', $this->storeId);
    if ($latest) {
      $csQuery->where('customer_search_id', '>', $latest->id);
    }

    foreach ($csQuery->get() as $row) {
      $s = new StoreSearch();
      $s->id = $row->customer_search_id;
      $s->keyword = $row->keyword;
      $s->language = $row->language_id === 0 ? 'bg' : 'en';
      $s->categoryId = $row->category_id;
      $s->subCategoryId = $row->sub_category;
      $s->customerId = $row->customer_id;
      $s->results = $row->products;
      $s->ip = $row->ip;
      $s->save();
    };

    $this->out('All good :)');
  }
}
