<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Document;
use App\Models\SalesRepresentative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class SalesRepresentativesController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $salesRepresentativesQuery = SalesRepresentative::query();
    $salesRepresentativesQuery = $this->applySort($salesRepresentativesQuery);
    $salesRepresentativesQuery = $this->applyFilter($salesRepresentativesQuery);
    $salesRepresentatives = $salesRepresentativesQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.sales-representatives.index', [
      'salesRepresentatives' => $salesRepresentatives,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $salesRepresentative = new SalesRepresentative();

    if ($request->isMethod('post')) {
      $salesRepresentative->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'titleBg' => ['nullable', 'string', 'max:255'],
        'titleEn' => ['nullable', 'string', 'max:255'],
        'phone1' => ['required', 'string', 'max:255'],
        'phone2' => ['nullable', 'string', 'max:255'],
        'email1' => ['required', 'string', 'max:255'],
        'email2' => ['nullable', 'string', 'max:255'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $salesRepresentative->save();

        return redirect('/erp/sales-representatives/update/' . $salesRepresentative->id)
          ->with('success', 'Успешно създадохте нов търговски представител.');
      }
    }

    return view('erp.sales-representatives.create', [
      'salesRepresentative' => $salesRepresentative,
      'errors' => $errors,
    ]);
  }

  public function update(int $representativeId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $salesRepresentative SalesRepresentative */
    $salesRepresentative = SalesRepresentative::where('id', $representativeId)->firstOrFail();

    if ($request->isMethod('post')) {
      $salesRepresentative->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'titleBg' => ['nullable', 'string', 'max:255'],
        'titleEn' => ['nullable', 'string', 'max:255'],
        'phone1' => ['required', 'string', 'max:255'],
        'phone2' => ['nullable', 'string', 'max:255'],
        'email1' => ['required', 'string', 'max:255'],
        'email2' => ['nullable', 'string', 'max:255'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $salesRepresentative->save();

        return redirect('/erp/sales-representatives/update/' . $salesRepresentative->id)
          ->with('success', 'Успешно редактирахте търговския представител.');
      }
    }

    return view('erp.sales-representatives.update', [
      'salesRepresentative' => $salesRepresentative,
      'errors' => $errors,
    ]);
  }

  public function delete(int $representativeId)
  {
    /* @var $salesRepresentative SalesRepresentative */
    $salesRepresentative = SalesRepresentative::where('id', $representativeId)->firstOrFail();

    $salesRepresentative->delete();

    return redirect('/erp/sales-representatives')
      ->with('success', 'Успешно изтрихте търговския представител.');
  }

  public function customers(int $representativeId)
  {
    /* @var $salesRepresentative SalesRepresentative */
    $salesRepresentative = SalesRepresentative::where('id', $representativeId)->firstOrFail();

    // Customer
    $customersQuery = Customer::query();
    $customersQuery = $this->applySort($customersQuery);
    $customersQuery->where([
      'salesRepresentativeId' => $salesRepresentative->id,
    ]);
    $customers = $customersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.sales-representatives.customers', [
      'salesRepresentative' => $salesRepresentative,
      'customers' => $customers,
    ]);
  }

  public function documents(int $representativeId)
  {
    /* @var $salesRepresentative SalesRepresentative */
    $salesRepresentative = SalesRepresentative::where('id', $representativeId)->firstOrFail();

    // Documents
    $documentsQuery = Document::query();
    $documentsQuery = $this->applySort($documentsQuery);
    $documentsQuery->where([
      'salesRepresentativeId' => $salesRepresentative->id,
    ]);
    $documents = $documentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.sales-representatives.documents', [
      'salesRepresentative' => $salesRepresentative,
      'documents' => $documents,
    ]);
  }
}
