@extends('layouts.app')

@section('content')
  @include('erp.manufacturers.partials.navbar')

  <h1 class="h4 mb-5">Производители</h1>

  @include('erp.manufacturers.partials.filters')
  @include('erp.manufacturers.partials.results')
@endsection
