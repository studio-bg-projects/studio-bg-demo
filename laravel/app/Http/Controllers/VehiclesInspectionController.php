<?php

namespace App\Http\Controllers;

class VehiclesInspectionController extends Controller
{
  public function index()
  {
    return view('vehicles-inspection.index');
  }
}
