<?php

namespace App\Services\Jobs;

use App\Models\DataSourceMatch;
use App\Models\DataSourceProduct;
use App\Models\FeedImportItem;
use App\Models\Product;

class DataSourcesMatchJob extends BaseSyncJob
{
  protected int $limitProducts = 500;
  protected int $limitFeedItems = 500;
  protected int $chunkSize = 1000;

  public function run(): void
  {
    $this->matchProducts();
    $this->matchFeedItems();
    $this->out('All good :)');
  }

  protected function matchProducts(): void
  {
    $products = collect();

    Product::query()
      ->chunkById($this->chunkSize, function ($items) use (&$products) {
        $matchedIds = DataSourceMatch::query()
          ->whereIn('erpProductId', $items->pluck('id')->all())
          ->pluck('erpProductId')
          ->all();

        $matchedIdMap = array_flip($matchedIds);

        foreach ($items as $item) {
          if (isset($matchedIdMap[$item->id])) {
            continue;
          }

          $products->push($item);

          if ($products->count() >= $this->limitProducts) {
            return false;
          }
        }
      });

    if ($products->isEmpty()) {
      return;
    }

    $added = 0;

    foreach ($products as $product) {
      $identifiers = collect([$product->mpn, $product->ean])
        ->filter()
        ->unique()
        ->values();

      $matches = [];
      if ($identifiers->isNotEmpty()) {
        $matches = DataSourceProduct::query()
          ->where(function ($query) use ($identifiers) {
            foreach ($identifiers as $identifier) {
              $query->orWhereJsonContains('identifiers', $identifier);
            }
          })
          ->pluck('externalProductId')
          ->unique()
          ->values()
          ->all();
      }

      $dataSourceMatch = new DataSourceMatch();
      $dataSourceMatch->erpProductId = $product->id;
      $dataSourceMatch->hasMatch = !empty($matches);
      $dataSourceMatch->matches = $matches;
      $dataSourceMatch->save();

      $added++;
    }

    $this->out('Added ' . $added . ' products');
  }

  protected function matchFeedItems(): void
  {
    $feedItems = collect();

    FeedImportItem::query()
      ->chunkById($this->chunkSize, function ($items) use (&$feedItems) {
        $matchedIds = DataSourceMatch::query()
          ->whereIn('feedItemId', $items->pluck('id')->all())
          ->pluck('feedItemId')
          ->all();

        $matchedIdMap = array_flip($matchedIds);

        foreach ($items as $item) {
          if (isset($matchedIdMap[$item->id])) {
            continue;
          }

          $feedItems->push($item);

          if ($feedItems->count() >= $this->limitFeedItems) {
            return false;
          }
        }
      });

    if ($feedItems->isEmpty()) {
      return;
    }

    $added = 0;

    foreach ($feedItems as $feedItem) {
      $identifiers = collect([$feedItem->itemMpn, $feedItem->itemEan])
        ->filter()
        ->unique()
        ->values();

      $matches = [];
      if ($identifiers->isNotEmpty()) {
        $matches = DataSourceProduct::query()
          ->where(function ($query) use ($identifiers) {
            foreach ($identifiers as $identifier) {
              $query->orWhereJsonContains('identifiers', $identifier);
            }
          })
          ->pluck('externalProductId')
          ->unique()
          ->values()
          ->all();
      }

      $dataSourceMatch = new DataSourceMatch();
      $dataSourceMatch->feedItemId = $feedItem->id;
      $dataSourceMatch->hasMatch = !empty($matches);
      $dataSourceMatch->matches = $matches;
      $dataSourceMatch->save();

      $added++;
    }

    $this->out('Added ' . $added . ' feed items');
  }
}
