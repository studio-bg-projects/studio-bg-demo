<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PingController extends Controller
{
  public function index()
  {
    return [
      'pong' => time(),
    ];
  }
}
