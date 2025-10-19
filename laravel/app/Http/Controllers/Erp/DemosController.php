<?php

namespace App\Http\Controllers\Erp;

use App\Enums\DemoStatus;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Demo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class DemosController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $demosQuery = Demo::query();
    $demosQuery = $this->applySort($demosQuery);
    $demosQuery = $this->applyFilter($demosQuery);
    $demos = $demosQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.demos.index', [
      'demos' => $demos,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $demo = new Demo();

    if ($request->isMethod('post')) {
      $demo->fill($request->all());

      $validator = Validator::make($request->all(), [
        'demoNumber' => ['required', 'string', 'max:255', 'unique:demos,demoNumber'],
        'status' => ['required', new Enum(DemoStatus::class)],
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'addedDate' => ['nullable', 'date_format:Y-m-d'],
        'companyName' => ['nullable', 'string', 'max:255'],
        'notesPublic' => ['nullable', 'string'],
        'notesPrivate' => ['nullable', 'string'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $demo->save();

        return redirect('/erp/demos/update/' . $demo->id)
          ->with('success', 'Успешно създадохте нов запис.');
      }
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.demos.create', [
      'demo' => $demo,
      'errors' => $errors,
      'customers' => $customers,
    ]);
  }

  public function update(int $demoId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $demo Demo */
    $demo = Demo::where('id', $demoId)->firstOrFail();

    if ($request->isMethod('post')) {
      $demo->fill($request->all());

      $validator = Validator::make($request->all(), [
        'demoNumber' => ['required', 'string', 'max:255', 'unique:demos,demoNumber,' . $demo->id],
        'status' => ['required', new Enum(DemoStatus::class)],
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'addedDate' => ['nullable', 'date_format:Y-m-d'],
        'companyName' => ['nullable', 'string', 'max:255'],
        'notesPublic' => ['nullable', 'string'],
        'notesPrivate' => ['nullable', 'string'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $demo->save();

        return redirect('/erp/demos/update/' . $demo->id)
          ->with('success', 'Успешно редактирахте записа.');
      }
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.demos.update', [
      'demo' => $demo,
      'errors' => $errors,
      'customers' => $customers,
    ]);
  }

  public function delete(int $demoId)
  {
    /* @var $demo Demo */
    $demo = Demo::where('id', $demoId)->firstOrFail();

    $demo->delete();

    return redirect('/erp/demos')
      ->with('success', 'Успешно изтрихте записа.');
  }
}
