<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
  public function index()
  {
    return redirect('/erp/dashboard');
  }
}
