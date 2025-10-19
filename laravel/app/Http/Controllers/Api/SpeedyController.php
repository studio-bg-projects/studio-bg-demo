<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpeedyController extends Controller
{
  public function search(Request $request)
  {
    return app(\App\Http\Controllers\Erp\Shipments\SpeedyController::class)->search($request);
  }

  public function calculate(Request $request)
  {
    return app(\App\Http\Controllers\Erp\Shipments\SpeedyController::class)->calculate($request);
  }
}
