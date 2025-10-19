<?php

namespace App\Http\Controllers\Erp;

use App\Helpers\FilterAndSort;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomersAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class CustomersAddressesController extends Controller
{
  use FilterAndSort;

  public function index(int $customerId)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    $addressesQuery = CustomersAddress::query();
    $addressesQuery->where('customerId', $customerId);
    $addressesQuery->orderBy('id', 'DESC');
    $addresses = $addressesQuery->get();

    return view('erp.customers-addresses.index', [
      'customer' => $customer,
      'addresses' => $addresses,
    ]);
  }

  public function create(int $customerId, Request $request)
  {
    /* @var $customer Customer */
    $customer = Customer::where('id', $customerId)->firstOrFail();

    $errors = session()->get('errors') ?? new MessageBag();
    $address = new CustomersAddress();
    $address->customerId = $customer->id;

    if ($request->isMethod('post')) {
      $address->fill($request->all());

      $validator = Validator::make($request->all(), [
        'firstName' => ['required', 'string', 'max:255'],
        'lastName' => ['required', 'string', 'max:255'],
        'companyName' => ['required', 'string', 'max:255'],
        'zipCode' => ['nullable', 'string', 'max:10'],
        'countryId' => ['nullable', 'required', 'integer', 'exists:countries,id'],
        'city' => ['nullable', 'string', 'max:100'],
        'citySpeedyId' => ['nullable', 'integer'],
        'street' => ['nullable', 'string', 'max:255'],
        'streetSpeedyId' => ['nullable', 'integer'],
        'streetNo' => ['nullable', 'string', 'max:255'],
        'blockNo' => ['nullable', 'string', 'max:255'],
        'entranceNo' => ['nullable', 'string', 'max:255'],
        'floor' => ['nullable', 'string', 'max:255'],
        'apartmentNo' => ['nullable', 'string', 'max:255'],
        'addressDetails' => ['nullable', 'string', 'max:255'],
        'phone' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'operatingHours' => ['nullable', 'string', 'max:255'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $address->save();

        return redirect('/erp/customers/addresses/update/' . $address->id)
          ->with('success', 'Успешно създадохте нов адрес.');
      }
    } else {
      $address->firstName = $customer->firstName;
      $address->lastName = $customer->lastName;
      $address->companyName = $customer->companyName;
      $address->countryId = $customer->companyCountryId;
    }

    $countries = Country::orderByRaw('isoCode2 <> \'BG\', name ASC')->get();

    return view('erp.customers-addresses.create', [
      'customer' => $customer,
      'address' => $address,
      'errors' => $errors,
      'countries' => $countries,
    ]);
  }

  public function update(int $addressId, Request $request)
  {
    $errors = session()->get('errors') ?? new MessageBag();

    /* @var $address CustomersAddress */
    $address = CustomersAddress::where('id', $addressId)->firstOrFail();

    if ($request->isMethod('post')) {
      $address->fill($request->all());

      $validator = Validator::make($request->all(), [
        'firstName' => ['required', 'string', 'max:255'],
        'lastName' => ['required', 'string', 'max:255'],
        'companyName' => ['required', 'string', 'max:255'],
        'zipCode' => ['nullable', 'string', 'max:10'],
        'countryId' => ['nullable', 'required', 'integer', 'exists:countries,id'],
        'city' => ['nullable', 'string', 'max:100'],
        'citySpeedyId' => ['nullable', 'integer'],
        'street' => ['nullable', 'string', 'max:255'],
        'streetSpeedyId' => ['nullable', 'integer'],
        'streetNo' => ['nullable', 'string', 'max:255'],
        'blockNo' => ['nullable', 'string', 'max:255'],
        'entranceNo' => ['nullable', 'string', 'max:255'],
        'floor' => ['nullable', 'string', 'max:255'],
        'apartmentNo' => ['nullable', 'string', 'max:255'],
        'addressDetails' => ['nullable', 'string', 'max:255'],
        'phone' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'operatingHours' => ['nullable', 'string', 'max:255'],
      ]);

      $errors->merge($validator->errors());

      if ($errors->isEmpty()) {
        $address->save();

        return redirect('/erp/customers/addresses/update/' . $address->id)
          ->with('success', 'Успешно редактирахте адреса.');
      }
    }

    $countries = Country::orderByRaw('isoCode2 <> \'BG\', name ASC')->get();

    return view('erp.customers-addresses.update', [
      'customer' => $address->customer,
      'address' => $address,
      'errors' => $errors,
      'countries' => $countries,
    ]);
  }

  public function delete(int $addressId)
  {
    /* @var $address CustomersAddress */
    $address = CustomersAddress::where('id', $addressId)->firstOrFail();

    if ($address->customer->addresses->count() <= 1) {
      return redirect('/erp/customers/addresses/' . $address->customer->id)
        ->withErrors(['msg' => 'Клиентът трябва да има поне един адрес!']);
    }

    $address->isDeleted = true;
    $address->save();

    return redirect('/erp/customers/addresses/' . $address->customer->id)
      ->with('success', 'Успешно маркирахте адреса за изтриване. Изтриването ще стане със следващата синхронизация.');
  }
}
