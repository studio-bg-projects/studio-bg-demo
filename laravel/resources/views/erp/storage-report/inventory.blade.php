@extends('layouts.app')

@section('content')
  @include('erp.storage-report.partials.tabs-navigation')

  <h1 class="h4 mb-5">Справка - Инвентаризация</h1>

  @include('erp.storage-report.partials.inventory.filters')
  @include('erp.storage-report.partials.inventory.results')
@endsection
