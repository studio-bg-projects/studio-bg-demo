@extends('layouts.app')

@section('content')
  @include('erp.demos.partials.navbar')

  <h1 class="h4 mb-5">Демо</h1>

  @include('erp.demos.partials.filters')
  @include('erp.demos.partials.results')
@endsection
