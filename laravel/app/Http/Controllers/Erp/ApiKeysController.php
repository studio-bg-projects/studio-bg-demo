<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;

class ApiKeysController extends Controller
{
  public function index()
  {
    $apiKeys = ApiKey::all();

    return view('erp.api-keys.index', [
      'apiKeys' => $apiKeys,
    ]);
  }
}
