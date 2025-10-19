<?php

namespace App\Http\Controllers\Erp;

use App\Enums\CustomerStatusType;
use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomersGroup;
use App\Models\Document;
use App\Models\Order;
use App\Models\Income;
use App\Models\SalesRepresentative;
use App\Services\MailMakerService;
use App\Services\MapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Enum;

class CustomersController extends Controller
{
  use FilterAndSort;

  public function index()
  {
    $customersQuery = Customer::query();
    $customersQuery = $this->applySort($customersQuery, [], ['statusType', 'creditLineRequested'], 'asc');
    $customersQuery = $this->applyFilter($customersQuery);
    $customers = $customersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    $customersGroups = CustomersGroup::all();

    return view('erp.customers.index', [
      'customers' => $customers,
      'customersGroups' => $customersGroups,
    ]);
  }

  public function create(Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();
    $customer = new Customer();

    if ($request->isMethod('post')) {
      $customer->fill($request->all());

      $allowInShop = $customer->statusType && MapService::customerStatusType($customer->statusType)->allowInShop;

      $validator = Validator::make($request->all(), [
        'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
        'password' => [$allowInShop ? 'required' : 'nullable', 'string', 'min:6'],
        'firstName' => ['required', 'string', 'max:255'],
        'lastName' => ['required', 'string', 'max:255'],
        'groupId' => ['required', 'integer', 'exists:customersGroups,id'],
        'statusType' => ['required', new Enum(CustomerStatusType::class)],
        'salesRepresentativeId' => ['nullable', 'integer', 'exists:salesRepresentatives,id'],
        'paymentTerm' => ['nullable', 'integer'],
        'preferredLang' => ['required', 'string', 'max:3'],
        // Custom fields
        'companyName' => ['required', 'string', 'max:255'],
        'companyAddress' => ['nullable', 'string', 'max:255'],
        'companyZipCode' => ['nullable', 'string', 'max:255'],
        'companyCity' => ['nullable', 'string', 'max:255'],
        'companyCountryId' => ['nullable', 'integer', 'exists:countries,id'],
        'companyId' => ['nullable', 'string', 'max:255'],
        'companyVatNumber' => ['nullable', 'string', 'max:255'],
        'contactSales' => ['nullable', 'string', 'max:255'],
        'contactPhone' => ['nullable', 'string', 'max:255'],
        'contactEmail' => ['nullable', 'string', 'max:255'],
        'financialContactPhone' => ['nullable', 'string', 'max:255'],
        'financialContactEmail' => ['nullable', 'string', 'max:255'],
        'creditLineRequested' => ['nullable', 'boolean'],
        'creditLineRequestValue' => ['nullable', 'integer'],
        'creditLineValue' => ['nullable', 'integer'],
        'creditLineUsed' => ['nullable', 'numeric'],
        'creditLineLeft' => ['nullable', 'numeric'],
        'totalPayableOrdersAmount' => ['nullable', 'numeric'],
        'totalPayableOrdersIncomes' => ['nullable', 'numeric'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $customer->password = Hash::make($customer->password);
        $customer->save();

        $extraInfo = '';

        if ($request->get('clientNotifyCreditLine')) {
          $mailMaker = new MailMakerService();
          $mailMaker->customerCreditLineValue($customer->id);

          $extraInfo .= "\n✉️Клиентът беше уведомент за кредитната му линия.";
        }

        return redirect('/erp/customers/update/' . $customer->id)
          ->with('success', 'Успешно създадохте нов клиент.' . $extraInfo);
      }
    } else {
      // Defaults
      $customer->paymentTerm = 7;
    }

    $customersGroups = CustomersGroup::all();
    $countries = Country::orderByRaw('isoCode2 <> \'BG\', name ASC')->get();

    /* @var $salesRepresentatives SalesRepresentative[] */
    $salesRepresentatives = SalesRepresentative::orderBy('nameBg')->get();

    return view('erp.customers.create', [
      'customer' => $customer,
      'errors' => $errors,
      'customersGroups' => $customersGroups,
      'countries' => $countries,
      'salesRepresentatives' => $salesRepresentatives,
    ]);
  }

  public function update(int $customerId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();
    $customer->password = null;

    if ($request->isMethod('post')) {
      $customer->fill($request->all());

      $validator = Validator::make($request->all(), [
        'email' => ['required', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
        'firstName' => ['required', 'string', 'max:255'],
        'lastName' => ['required', 'string', 'max:255'],
        'groupId' => ['required', 'integer', 'exists:customersGroups,id'],
        'statusType' => ['required', new Enum(CustomerStatusType::class)],
        'salesRepresentativeId' => ['nullable', 'integer', 'exists:salesRepresentatives,id'],
        'paymentTerm' => ['nullable', 'integer'],
        'preferredLang' => ['required', 'string', 'max:3'],
        // Custom fields
        'companyName' => ['required', 'string', 'max:255'],
        'companyAddress' => ['nullable', 'string', 'max:255'],
        'companyZipCode' => ['nullable', 'string', 'max:255'],
        'companyCity' => ['nullable', 'string', 'max:255'],
        'companyCountryId' => ['nullable', 'integer', 'exists:countries,id'],
        'companyId' => ['nullable', 'string', 'max:255'],
        'companyVatNumber' => ['nullable', 'string', 'max:255'],
        'contactSales' => ['nullable', 'string', 'max:255'],
        'contactPhone' => ['nullable', 'string', 'max:255'],
        'contactEmail' => ['nullable', 'string', 'max:255'],
        'financialContactPhone' => ['nullable', 'string', 'max:255'],
        'financialContactEmail' => ['nullable', 'string', 'max:255'],
        'creditLineRequested' => ['nullable', 'boolean'],
        'creditLineRequestValue' => ['nullable', 'integer'],
        'creditLineValue' => ['nullable', 'integer'],
        'creditLineUsed' => ['nullable', 'numeric'],
        'creditLineLeft' => ['nullable', 'numeric'],
        'totalPayableOrdersAmount' => ['nullable', 'numeric'],
        'totalPayableOrdersIncomes' => ['nullable', 'numeric'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $customer->password = $customer->password ? Hash::make($customer->password) : $customer->getOriginal('password');
        $customer->save();

        $customer->recalc();

        $extraInfo = '';

        if ($request->get('clientNotifyCreditLine')) {
          $mailMaker = new MailMakerService();
          $mailMaker->customerCreditLineValue($customer->id);

          $extraInfo .= "\n✉️Клиентът беше уведомент за кредитната му линия.";
        }

        return redirect('/erp/customers/update/' . $customer->id)
          ->with('success', 'Успешно редактирахте клиента.' . $extraInfo);
      }
    }

    $customersGroups = CustomersGroup::all();
    $countries = Country::orderByRaw('isoCode2 <> \'BG\', name ASC')->get();

    /* @var $salesRepresentatives SalesRepresentative[] */
    $salesRepresentatives = SalesRepresentative::orderBy('nameBg')->get();

    return view('erp.customers.update', [
      'customer' => $customer,
      'errors' => $errors,
      'customersGroups' => $customersGroups,
      'countries' => $countries,
      'salesRepresentatives' => $salesRepresentatives,
    ]);
  }

  public function delete(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    if ($customer->orders->count()) {
      return redirect('/erp/customers/update/' . $customer->id)
        ->withErrors(['msg' => 'Не може да триете клиенти към които има прикачени поръчки!']);
    }

    if ($customer->documents->count()) {
      return redirect('/erp/customers/update/' . $customer->id)
        ->withErrors(['msg' => 'Не може да триете клиенти към които има прикачени документи!']);
    }

    // @todo customer - да не може да се трият клиенти, които имат заприходени документи (КИ или фактура)

    $customer->isDeleted = true;
    $customer->save();

    return redirect('/erp/customers')
      ->with('success', 'Успешно маркирахте клиента за изтриване. Изтриването ще стане със следващата синхронизация.');
  }

  public function orders(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    // Orders
    $ordersQuery = Order::query();
    $ordersQuery = $this->applySort($ordersQuery);
    $ordersQuery->where([
      'customerId' => $customer->id,
    ]);
    $orders = $ordersQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.customers.orders', [
      'customer' => $customer,
      'orders' => $orders,
    ]);
  }

  public function incomes(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    // Incomes
    $incomesQuery = Income::query();
    $incomesQuery = $this->applySort($incomesQuery);
    $incomesQuery->where([
      'customerId' => $customer->id,
    ]);
    $incomes = $incomesQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.customers.incomes', [
      'customer' => $customer,
      'incomes' => $incomes,
    ]);
  }

  public function documents(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    // Documents
    $documentsQuery = Document::query();
    $documentsQuery = $this->applySort($documentsQuery);
    $documentsQuery->where([
      'customerId' => $customer->id,
    ]);
    $documents = $documentsQuery->paginate(request()->page === 'all' ? PHP_INT_MAX : 50)->withQueryString();

    return view('erp.customers.documents', [
      'customer' => $customer,
      'documents' => $documents,
    ]);
  }
}
