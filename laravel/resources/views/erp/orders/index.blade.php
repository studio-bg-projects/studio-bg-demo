@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">Поръчки</h1>

  @include('erp.orders.partials.filters')
  @include('erp.orders.partials.results')
@endsection
