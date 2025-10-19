<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class ManufacturersController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $manufacturersQuery = Manufacturer::query();
    $manufacturersQuery = $this->applySort($manufacturersQuery, [], 'sortOrder', 'asc');
    $manufacturersQuery = $this->applyFilter($manufacturersQuery);
    $manufacturers = $manufacturersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.manufacturers.index', [
      'manufacturers' => $manufacturers,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $manufacturer = new Manufacturer();

    if ($request->isMethod('post')) {
      $manufacturer->fill($request->all());

      $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255', 'unique:manufacturers,name'],
        'sortOrder' => ['required', 'integer'],
        'isActive' => ['required', 'boolean'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $manufacturer->save();

        return redirect('/erp/manufacturers/update/' . $manufacturer->id)
          ->with('success', 'Успешно създадохте нов производител.');
      }
    } else {
      // Defaults
      $manufacturer->isActive = true;
      $manufacturer->sortOrder = 0;
    }

    return view('erp.manufacturers.create', [
      'manufacturer' => $manufacturer,
      'errors' => $errors,
    ]);
  }

  public function update(int $manufacturerId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $manufacturer Manufacturer */
    $manufacturer = Manufacturer::where('id', $manufacturerId)->firstOrFail();

    if ($request->isMethod('post')) {
      $manufacturer->fill($request->all());

      $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255', 'unique:manufacturers,name,' . $manufacturer->id],
        'sortOrder' => ['required', 'integer'],
        'isActive' => ['required', 'boolean'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $manufacturer->save();

        return redirect('/erp/manufacturers/update/' . $manufacturer->id)
          ->with('success', 'Успешно редактирахте производителя.');
      }
    }

    return view('erp.manufacturers.update', [
      'manufacturer' => $manufacturer,
      'errors' => $errors,
    ]);
  }

  public function delete(int $manufacturerId)
  {
    /* @var $manufacturer Manufacturer */
    $manufacturer = Manufacturer::where('id', $manufacturerId)->firstOrFail();

    $manufacturer->delete();

    return redirect('/erp/manufacturers')
      ->with('success', 'Успешно изтрихте производителя.');
  }

  public function products(int $manufacturerId)
  {
    /* @var $manufacturer Manufacturer */
    $manufacturer = Manufacturer::where('id', $manufacturerId)->firstOrFail();

    // Products
    $productsQuery = Product::query();
    $productsQuery = $this->applySort($productsQuery);
    $productsQuery->where([
      'manufacturerId' => $manufacturer->id,
    ]);
    $products = $productsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.manufacturers.products', [
      'manufacturer' => $manufacturer,
      'products' => $products,
    ]);
  }
}
