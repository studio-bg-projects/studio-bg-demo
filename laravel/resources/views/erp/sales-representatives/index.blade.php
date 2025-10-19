@extends('layouts.app')

@section('content')
  @include('erp.sales-representatives.partials.navbar')

  <h1 class="h4 mb-5">Търговски преставители</h1>

  @include('erp.sales-representatives.partials.filters')
  @include('erp.sales-representatives.partials.results')
@endsection
