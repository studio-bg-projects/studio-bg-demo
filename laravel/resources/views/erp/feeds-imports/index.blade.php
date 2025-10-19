@extends('layouts.app')

@section('content')
  @include('erp.feeds-imports.partials.navbar')

  <h1 class="h4 mb-5">Доставчици - XML Feed</h1>

  @include('erp.feeds-imports.partials.filters')
  @include('erp.feeds-imports.partials.results')
@endsection
