<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomersGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class CustomersGroupsController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $customersGroupsQuery = CustomersGroup::query();
    $customersGroupsQuery->orderBy('id', 'DESC');
    $customersGroups = $customersGroupsQuery->get();

    return view('erp.customers-groups.index', [
      'customersGroups' => $customersGroups,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $customersGroup = new CustomersGroup();

    if ($request->isMethod('post')) {
      $customersGroup->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'discountPercent' => ['required', 'numeric', 'min:-100', 'max:100'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $customersGroup->save();

        return redirect('/erp/customers-groups/update/' . $customersGroup->id)
          ->with('success', 'Успешно създадохте нова група.');
      }
    }

    return view('erp.customers-groups.create', [
      'customersGroup' => $customersGroup,
      'errors' => $errors,
    ]);
  }

  public function update(int $groupId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $customersGroup CustomersGroup */
    $customersGroup = CustomersGroup::where('id', $groupId)->firstOrFail();

    if ($request->isMethod('post')) {
      $customersGroup->fill($request->all());

      $validator = Validator::make($request->all(), [
        'nameBg' => ['required', 'string', 'max:255'],
        'nameEn' => ['required', 'string', 'max:255'],
        'discountPercent' => ['required', 'numeric', 'min:-100', 'max:100'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $customersGroup->save();

        return redirect('/erp/customers-groups/update/' . $customersGroup->id)
          ->with('success', 'Успешно редактирахте групата.');
      }
    }

    return view('erp.customers-groups.update', [
      'customersGroup' => $customersGroup,
      'errors' => $errors,
    ]);
  }

  public function delete(int $groupId)
  {
    /* @var $customersGroup CustomersGroup */
    $customersGroup = CustomersGroup::where('id', $groupId)->firstOrFail();

    if ($customersGroup->id === 1) {
      abort('400', 'Не може да изтриете основната група!');
    }

    if ($customersGroup->customers->count() > 0) {
      abort('400', 'Не може да изтриете група в която има клиенти! Трябва да преместите клиентите и чак тогава ще може да изтриете групата.');
    }

    $customersGroup->delete();

    return redirect('/erp/customers-groups')
      ->with('success', 'Успешно изтрихте групата.');
  }

  public function customers(int $groupId)
  {
    /* @var $customersGroup CustomersGroup */
    $customersGroup = CustomersGroup::where('id', $groupId)->firstOrFail();

    // Customers
    $customersQuery = Customer::query();
    $customersQuery = $this->applySort($customersQuery, [], 'statusType');
    $customersQuery->where([
      'groupId' => $customersGroup->id,
    ]);
    $customers = $customersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.customers-groups.customers', [
      'customersGroup' => $customersGroup,
      'customers' => $customers,
    ]);
  }
}
