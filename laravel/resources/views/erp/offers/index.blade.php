@extends('layouts.app')

@section('content')
  @include('erp.offers.partials.navbar')

  <h1 class="h4 mb-5">Оферти</h1>

  @include('erp.offers.partials.filters')
  @include('erp.offers.partials.results')
@endsection
