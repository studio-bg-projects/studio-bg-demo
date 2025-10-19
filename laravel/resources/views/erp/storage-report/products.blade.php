@extends('layouts.app')

@section('content')
  @include('erp.storage-report.partials.tabs-navigation')

  <h1 class="h4 mb-5">Справка - Продукти</h1>

  @include('erp.storage-report.partials.products.filters')
  @include('erp.storage-report.partials.products.results')
@endsection
