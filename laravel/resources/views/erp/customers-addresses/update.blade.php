@extends('layouts.app')

@section('content')
  @include('erp.customers.partials.navbar')

  <h1 class="h4 mb-4">{{ $customer->getOriginal('companyName') ?: $customer->getOriginal('email') }} - Редакция на адрес</h1>

  <div class="text-end mt-n8 mb-4">
    <a href="{{ url('/erp/customers/addresses/delete/' . $address->id) }}" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Сигурни ли сте, че искате да изтриете този АДРЕС?')">
      <i class="fa-regular fa-trash-can me-2"></i>
      Изтрий този адрес
    </a>
  </div>

  <hr class="my-3"/>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    @include('erp.customers-addresses.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-pen-to-square me-2"></i>
        Редакция
      </button>
    </div>
  </form>
@endsection
