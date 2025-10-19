<?php

namespace App\Http\Controllers\Erp;

use App\Enums\ProductUsageStatus;
use App\Enums\ProductSource;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\DataSourceProduct;
use App\Models\FeedImport;
use App\Models\FeedImportItem;
use App\Models\Product;
use App\Services\ProductSyncService;
use Illuminate\Http\Request;

class FeedsImportItemsController extends Controller
{
  use FilterAndSort;

  protected ProductSyncService $productSync;

  public function __construct()
  {
    $this->productSync = new ProductSyncService();
    parent::__construct();
  }

  public function related()
  {
    $itemsQuery = FeedImportItem::query()->with(['feedImport', 'product']);

    // Default filter value
    request()->mergeIfMissing(['filter.isIgnored' => 0]);

    $related = request()->input('cFilter.related');
    if ($related === '1') {
      $itemsQuery->whereNotNull('productId');
    } elseif ($related === '0') {
      $itemsQuery->whereNull('productId');
      $itemsQuery->where('skipSync', '=', 0);
    }

    $itemsQuery = $this->applySort($itemsQuery);
    $itemsQuery = $this->applyFilter($itemsQuery);
    $items = $itemsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    $feeds = FeedImport::orderBy('providerName')->get();

    $eans = $items->filter(function ($item) {
      return trim($item->itemEan) !== '';
    })->pluck('itemEan')->unique()->all();
    $mpns = $items->filter(function ($item) {
      return trim($item->itemMpn) !== '';
    })->pluck('itemMpn')->unique()->all();
    $autoProducts = Product::query()
      ->whereIn('ean', $eans)
      ->orWhereIn('mpn', $mpns)
      ->get(['id', 'nameBg', 'ean', 'mpn']);
    $identifiers = collect($eans)
      ->merge($mpns)
      ->map(function ($value) {
        return trim((string)$value);
      })
      ->filter()
      ->unique()
      ->values();

    $dataSourceProducts = collect();

    if ($identifiers->isNotEmpty()) {
      $dataSourceProducts = DataSourceProduct::query()
        ->select(['externalProductId', 'identifiers', 'modelName', 'categoryId', 'pictureUrl'])
        ->where(function ($query) use ($identifiers) {
          foreach ($identifiers as $identifier) {
            $query->orWhereJsonContains('identifiers', $identifier);
          }
        })
        ->get();
    }

    $identifierLookup = array_fill_keys($identifiers->all(), true);
    $dataSourceByIdentifier = [];

    foreach ($dataSourceProducts as $dataSourceProduct) {
      $productIdentifiers = (array)$dataSourceProduct->identifiers;

      foreach ($productIdentifiers as $identifier) {
        $normalizedIdentifier = trim((string)$identifier);

        if ($normalizedIdentifier === '') {
          continue;
        }

        if (!isset($identifierLookup[$normalizedIdentifier])) {
          continue;
        }

        if (!isset($dataSourceByIdentifier[$normalizedIdentifier])) {
          $dataSourceByIdentifier[$normalizedIdentifier] = $dataSourceProduct;
        }
      }
    }

    foreach ($items as $item) {
      $item->autoProduct = $autoProducts->firstWhere('ean', $item->itemEan)
        ?? $autoProducts->firstWhere('mpn', $item->itemMpn);

      $itemEan = trim((string)$item->itemEan);
      $itemMpn = trim((string)$item->itemMpn);

      $item->dataSourceProduct = $dataSourceByIdentifier[$itemEan] ?? $dataSourceByIdentifier[$itemMpn] ?? null;
    }

    return view('erp.feeds-imports-items.related', [
      'items' => $items,
      'feeds' => $feeds,
    ]);
  }

  public function setRelatedProduct(int $itemId, Request $request)
  {
    $request->validate([
      'productId' => ['nullable', 'integer', 'exists:products,id'],
    ]);

    /* @var $item FeedImportItem */
    $item = FeedImportItem::where('id', $itemId)->firstOrFail();
    $item->productId = $request->input('productId');
    $item->isLeadRecord = false;
    $item->isSynced = false;
    $item->save();

    if ($item->productId) {
      $count = FeedImportItem::where('productId', $item->productId)->count();
      if ($count > 1) {
        FeedImportItem::where('productId', $item->productId)
          ->update(['isLeadRecord' => false]);
      } else {
        $item->isLeadRecord = true;
        $item->isSynced = false;
        $item->save();
      }
    }

    return response()->json(['success' => true]);
  }

  public function unsetRelatedProduct(int $itemId)
  {
    /* @var $item FeedImportItem */
    $item = FeedImportItem::where('id', $itemId)->firstOrFail();

    $productId = $item->productId;
    $wasLead = (bool)$item->isLeadRecord;

    $item->productId = null;
    $item->isLeadRecord = false;
    $item->isSynced = false;
    $item->save();

    if ($productId && $wasLead) {
      FeedImportItem::where('productId', $productId)->update([
        'isLeadRecord' => false,
        'isSynced' => false,
      ]);
    }

    return response()->json(['success' => true]);
  }

  public function conflicts()
  {
    $resolved = request()->input('cFilter.resolved');

    $productsQuery = FeedImportItem::query()
      ->selectRaw('productId, SUM(isLeadRecord) AS leadCount, COUNT(*) AS total')
      ->whereNotNull('productId')
      ->groupBy('productId')
      ->havingRaw('COUNT(*) > 1');

    if ($resolved === '0') {
      $productsQuery->havingRaw('SUM(isLeadRecord) = 0');
    } elseif ($resolved === '1') {
      $productsQuery->havingRaw('SUM(isLeadRecord) > 0');
    }

    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    $items = FeedImportItem::with(['feedImport', 'product'])
      ->whereIn('productId', $products->pluck('productId'))
      ->orderBy('id')
      ->get()
      ->groupBy('productId');

    return view('erp.feeds-imports-items.conflicts', [
      'products' => $products,
      'items' => $items,
    ]);
  }

  public function conflictsSetLeadRecord(int $itemId)
  {
    /* @var $item FeedImportItem */
    $item = FeedImportItem::where('id', $itemId)->firstOrFail();

    if ($item->productId) {
      FeedImportItem::where('productId', $item->productId)
        ->update(['isLeadRecord' => false]);
      $item->isLeadRecord = true;
      $item->isSynced = false;
      $item->save();
    }

    return response()->json(['success' => true]);
  }

  public function setSkipSync(int $itemId, Request $request)
  {
    $request->validate([
      'skipSync' => ['required', 'boolean'],
    ]);

    /* @var $item FeedImportItem */
    $item = FeedImportItem::where('id', $itemId)->firstOrFail();
    $item->skipSync = $request->boolean('skipSync');

    if ($item->skipSync) {
      $item->productId = null;
      $item->isLeadRecord = false;
    }

    $item->save();

    return response()->json(['success' => true]);
  }

  public function relatedAddProduct(int $itemId)
  {
    /* @var $item FeedImportItem */
    $item = FeedImportItem::where('id', $itemId)
      ->whereNull('productId')
      ->where('skipSync', false)
      ->firstOrFail();

    $product = new Product();
    $product->ean = $item->itemEan;
    $product->mpn = $item->itemMpn;
    $product->price = 0;
    $product->quantity = 0;
    $product->nameBg = $item->itemName;
    $product->nameEn = $item->itemName;
    $product->usageStatus = ProductUsageStatus::Draft->value;
    $product->source = ProductSource::FeedsImportItems->value;
    $product->save();

    $item->productId = $product->id;
    $item->isLeadRecord = true;
    $item->isSynced = false;
    $item->save();

    $this->productSync->updateProductFromFeed($product, $item);

    return response()->json([
      'success' => true,
      'product' => [
        'id' => $product->id,
        'nameBg' => $product->nameBg,
        'mpn' => $product->mpn,
      ],
    ]);
  }

  public function bulk(Request $request)
  {
    $appendMsg = '';

    foreach ($request->input('ids') as $id) {
      $item = FeedImportItem::where('id', $id)->first();

      if (!$item) {
        continue;
      }

      if ($request->input('action') === 'ignore') {
        $item->isIgnored = true;
      } elseif ($request->input('action') === 'unignore') {
        $item->isIgnored = false;
      }

      $item->save();
    }

    return redirect($request->input('backto') ?: '/')
      ->with('success', 'Промените са нанесени успешно.' . $appendMsg);
  }
}
