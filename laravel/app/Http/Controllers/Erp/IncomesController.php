<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Income;
use App\Models\IncomesAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class IncomesController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $incomesQuery = Income::query();
    $incomesQuery = $this->applySort($incomesQuery);
    $incomesQuery = $this->applyFilter($incomesQuery);
    $incomes = $incomesQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.incomes.index', [
      'incomes' => $incomes,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $income = new Income();

    if ($request->isMethod('post')) {
      $income->fill($request->all());
      $allocations = $request->input('allocations') ?? [];

      $validator = Validator::make($request->all(), [
        'customerId' => ['nullable', 'integer', 'exists:customers,id'],
        'paymentDate' => ['required', 'date_format:Y-m-d'],
        'paidAmount' => ['required', 'numeric', 'gt:0'],
        'notesPrivate' => ['nullable', 'string'],
        'notesPublic' => ['nullable', 'string'],

        'allocations' => ['required', 'array', 'min:1'],
        'allocations.*.id' => ['nullable', 'integer'],
        'allocations.*.documentId' => ['nullable', 'integer', 'exists:documents,id'],
        'allocations.*.description' => ['required', 'string', 'max:255'],
        'allocations.*.allocatedAmount' => ['required', 'numeric', 'gt:0'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $income->save();

        // Allocations - add
        foreach ($allocations as $inputAllocation) {
          $incomesAllocation = new IncomesAllocation();
          $incomesAllocation->fill($inputAllocation);
          $incomesAllocation->id = $inputAllocation['id'] ?: null;
          $incomesAllocation->incomeId = $income->id;
          $incomesAllocation->save();

          if ($incomesAllocation->document) {
            $incomesAllocation->document->resyncPaid();
          }
        }

        return redirect('/erp/incomes/update/' . $income->id)
          ->with('success', 'Успешно създадохте ново приходно плащане.');
      }
    } else {
      $allocations = [];

      $income->customerId = $request->input('customerId');
      $income->paidAmount = $request->input('paidAmount');

      if ($request->input('documentId')) {
        $allocations = [[
          'documentId' => $request->input('documentId')
        ]];
      }
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.incomes.create', [
      'income' => $income,
      'errors' => $errors,
      'customers' => $customers,
      'allocations' => $allocations,
    ]);
  }

  public function update(int $incomeId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $income Income */
    $income = Income::where('id', $incomeId)->firstOrFail();

    if ($request->isMethod('post')) {
      $income->fill($request->all());
      $allocations = $request->input('allocations') ?? [];

      $validator = Validator::make($request->all(), [
        'customerId' => ['required', 'integer', 'exists:customers,id'],
        'paymentDate' => ['required', 'date_format:Y-m-d'],
        'paidAmount' => ['required', 'numeric', 'gt:0'],
        'notesPrivate' => ['nullable', 'string'],
        'notesPublic' => ['nullable', 'string'],

        'allocations' => ['required', 'array', 'min:1'],
        'allocations.*.id' => ['nullable', 'integer'],
        'allocations.*.documentId' => ['nullable', 'integer', 'exists:documents,id'],
        'allocations.*.description' => ['required', 'string', 'max:255'],
        'allocations.*.allocatedAmount' => ['required', 'numeric', 'gt:0'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $income->save();

        // Allocations - delete non existing
        $existingAllocations = $income->allocations->pluck('id')->toArray();
        $inputAllocationsIds = array_filter(array_column($allocations, 'id'));
        $allocationsToDelete = array_diff($existingAllocations, $inputAllocationsIds);
        if (!empty($allocationsToDelete)) {
          foreach ($allocationsToDelete as $allocationId) {
            /* @var $allocationToDelete IncomesAllocation */
            $allocationToDelete = IncomesAllocation::where('id', $allocationId)->first();

            if ($allocationToDelete) {
              $document = $allocationToDelete->document;
              $allocationToDelete->delete();
              $document?->resyncPaid();
            }
          }
        }

        // Allocations - add or update
        foreach ($allocations as $inputAllocation) {
          /* @var $incomesAllocation IncomesAllocation */
          $incomesAllocation = IncomesAllocation::where('id', $inputAllocation['id'])->first() ?? new IncomesAllocation();
          $incomesAllocation->fill($inputAllocation);
          $incomesAllocation->id = $inputAllocation['id'] ?: null;
          $incomesAllocation->incomeId = $income->id;
          $incomesAllocation->save();

          if ($incomesAllocation->document) {
            $incomesAllocation->document->resyncPaid();
          }
        }

        return redirect('/erp/incomes/update/' . $income->id)
          ->with('success', 'Успешно редактирахте приходното плащане.');
      }
    } else {
      $allocations = $income->allocations;
    }

    /* @var $customers Customer[] */
    $customers = Customer::orderBy('id', 'desc')->get();

    return view('erp.incomes.update', [
      'income' => $income,
      'errors' => $errors,
      'customers' => $customers,
      'allocations' => $allocations,
    ]);
  }
}
