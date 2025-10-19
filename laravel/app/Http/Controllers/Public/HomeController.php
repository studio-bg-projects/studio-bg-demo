<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
  public function index()
  {
    if (Auth::check()) {
      return redirect('/erp');
    } else {
      return redirect('/auth/login');
    }
  }
}
