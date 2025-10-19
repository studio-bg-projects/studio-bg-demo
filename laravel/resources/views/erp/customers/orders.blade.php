@extends('layouts.app')

@section('content')
  @include('erp.customers.partials.navbar')

  <h1 class="h4 mb-5">{{ $customer->companyName }} - Свързани поръчки</h1>

  <div class="text-end mt-n8 mb-4">
    <a href="{{ url('/erp/orders/prepare/?customerId=' . $customer->id) }}" class="btn btn-sm btn-primary">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Създай нова поръчка
    </a>
  </div>

  @include('erp.orders.partials.results')
@endsection
