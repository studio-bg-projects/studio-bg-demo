<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\FeedImport;
use App\Models\FeedImportItem;

class FeedsImportsDashboardController extends Controller
{
  public function index()
  {
    $unlinkedCount = FeedImportItem::whereNull('productId')
      ->where('skipSync', '=', 0)
      ->count();

    $conflictsCount = FeedImportItem::query()
      ->selectRaw('productId')
      ->whereNotNull('productId')
      ->groupBy('productId')
      ->havingRaw('COUNT(*) > 1')
      ->havingRaw('SUM(isLeadRecord) = 0')
      ->get()
      ->count();

    // @todo
    $promoItems = [];
//    $promoItems = FeedImportItem::with(['feedImport', 'product'])
//      ->where('nonSyncStatus', FeedImportItemNonSyncStatus::ProductInPromo)
//      ->orderByDesc('id')
//      ->limit(50)
//      ->get();

    $feeds = FeedImport::withCount('items')
      ->orderBy('providerName')
      ->get();

    return view('erp.feeds-imports-dashboard.index', [
      'unlinkedCount' => $unlinkedCount,
      'conflictsCount' => $conflictsCount,
      'promoItems' => $promoItems,
      'feeds' => $feeds,
    ]);
  }
}
