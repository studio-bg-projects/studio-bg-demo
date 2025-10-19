<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function index()
  {
    return view('erp.dashboard.index');
  }
}
