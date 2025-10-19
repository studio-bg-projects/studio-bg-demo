<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\StoreSearch;

class SearchReportController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $storeSearchQuery = StoreSearch::query();
    $storeSearchQuery = $this->applySort($storeSearchQuery);
    $storeSearchQuery = $this->applyFilter($storeSearchQuery);
    $searches = $storeSearchQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 100)->withQueryString();

    return view('erp.search-report.index', [
      'searches' => $searches,
    ]);
  }
}
