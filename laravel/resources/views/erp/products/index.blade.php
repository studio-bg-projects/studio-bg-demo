@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">Продукти</h1>

  @include('erp.products.partials.filters')
  @include('erp.products.partials.results')
@endsection
