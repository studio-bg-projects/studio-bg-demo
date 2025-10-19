@extends('layouts.app')

@section('content')
  @include('erp.customers.partials.navbar')

  <h1 class="h4 mb-5">{{ $customer->getOriginal('companyName') }} - Адреси</h1>

  <div class="text-end mt-n8 mb-4">
    <a href="{{ url('/erp/customers/addresses/create/' . $customer->id) }}" class="btn btn-sm btn-primary">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Добави нов адрес
    </a>
  </div>

  @include('erp.customers-addresses.partials.results')
@endsection
