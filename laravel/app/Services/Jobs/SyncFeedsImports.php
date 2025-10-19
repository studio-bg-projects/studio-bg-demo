<?php

namespace App\Services\Jobs;

use App\Models\FeedImport;
use App\Models\FeedImportItem;
use App\Services\FeedsImportsLoader\BaseLoader;
use App\Services\ProductSyncService;
use DateTime;

class SyncFeedsImports extends BaseSyncJob
{
  protected ProductSyncService $productSync;

  public function __construct()
  {
    $this->productSync = new ProductSyncService();
  }

  public function run(): void
  {
    $this->syncFeeds();
    $this->syncProducts();

    $this->out('All good :)');
  }

  public function syncFeeds(): void
  {
    $feeds = FeedImport::all();
    $now = new DateTime();

    $feedsToSync = [];

    foreach ($feeds as $feed) {
      $schedule = $feed->syncSchedule ?: [];
      $lastSync = $feed->lastSync;
      $shouldSync = false;

      foreach ($schedule as $time) {
        $timeDt = DateTime::createFromFormat('H:i', $time);
        if (!$timeDt) {
          continue;
        }

        $timeDt->setDate((int)$now->format('Y'), (int)$now->format('m'), (int)$now->format('d'));

        if ($timeDt > $now) {
          continue;
        }

        if (!$lastSync || $timeDt > $lastSync) {
          $shouldSync = true;
          break;
        }
      }

      if ($shouldSync) {
        $feedsToSync[] = $feed->id;

        $feed->lastSync = $now;
        $feed->save();
      }
    }

    // Perform synchronization only after the records have been marked as synchronized to avoid collisions
    foreach ($feedsToSync as $feedId) {
      $this->syncFeed($feedId);
    }
  }

  public function syncProducts()
  {
    /* @var $feedItems FeedImportItem[] */
    $feedItems = FeedImportItem::query()
      ->where('isSynced', false)
      ->where('skipSync', false)
      ->where('isLeadRecord', true)
      ->whereNotNull('productId')
      ->with(['product', 'feedImport'])
      ->get();

    foreach ($feedItems as $feedItem) {
      if (!$feedItem->productId) {
        continue;
      }

      $changes = $this->productSync->updateProductFromFeed($feedItem->product, $feedItem);

      if (!$changes) {
        continue;
      }

      $this->out('Update product ' . $feedItem->productId . ': ' . json_encode($changes));
    }

    FeedImportItem::where([
      'isSynced' => false
    ])->update([
      'isSynced' => true
    ]);
  }

  protected function syncFeed(int $feedId): void
  {
    /* @var $feed FeedImport */
    $feed = FeedImport::find($feedId);

    if (!$feed) {
      $this->out('Missing record: ' . $feedId);
      return;
    }

    $class = '\\App\\Services\\FeedsImportsLoader\\' . $feed->adapterName;
    if (!class_exists($class)) {
      $this->out('Missing adapter: ' . $feed->adapterName);
      return;
    }

    /** @var BaseLoader $loader */
    $loader = new $class($feed->feedUrl);

    try {
      $loader->load();
    } catch (\Exception $e) {
      $this->out('Can`t load the items: ' . $e->getMessage());
      return;
    }

    foreach ($loader->getItems() as $item) {
      $feedItem = FeedImportItem::where('parentId', $feed->id)
        ->where('uniqueId', $item['uniqueId'])
        ->first();

      $needsSave = false;

      if (!$feedItem) {
        $feedItem = new FeedImportItem();
        $feedItem->parentId = $feed->id;
        $feedItem->isSynced = false;

        $needsSave = true;
      }

      if ($feedItem->data != $item) {
        $feedItem->uniqueId = $item['uniqueId'];
        $feedItem->itemName = $item['name'];
        $feedItem->itemEan = $item['ean'];
        $feedItem->itemMpn = $item['mpn'];
        $feedItem->itemPrice = $item['price'];
        $feedItem->itemQuantity = $item['quantity'];
        $feedItem->data = $item;
        $feedItem->isSynced = false;

        $needsSave = true;
      }

      if ($needsSave) {
        $feedItem->save();
      }
    }
  }
}
