@extends('layouts.app')

@section('content')
  @include('erp.customers.partials.navbar')

  <h1 class="h4 mb-5">Клиенти</h1>

  @include('erp.customers.partials.filters')
  @include('erp.customers.partials.results')
@endsection
