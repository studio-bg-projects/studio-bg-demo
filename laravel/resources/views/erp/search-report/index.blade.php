@extends('layouts.app')

@section('content')
  @include('erp.search-report.partials.navbar')

  <h1 class="h4 mb-5">Търсения от клиенти</h1>

  @include('erp.search-report.partials.filters')
  @include('erp.search-report.partials.results')
@endsection
