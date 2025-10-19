@extends('layouts.app')

@section('content')
  @include('erp.customers.partials.navbar')

  <h1 class="h4 mb-5">{{ $customer->companyName }} - Свързани плащания</h1>

  @include('erp.incomes.partials.results')
@endsection
