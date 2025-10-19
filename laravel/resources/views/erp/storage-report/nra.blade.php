@extends('layouts.app')

@section('content')
  @include('erp.storage-report.partials.tabs-navigation')

  <h1 class="h4 mb-5">Справка - НАП</h1>

  @include('erp.storage-report.partials.nra.filters')
  @include('erp.storage-report.partials.nra.results')
@endsection
