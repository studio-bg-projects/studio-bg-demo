@extends('layouts.app')

@section('content')
  @include('erp.feeds-imports-items.partials.related.navbar')

  <h1 class="h4 mb-5">Свързване на продукти</h1>

  @include('erp.feeds-imports-items.partials.related..filters')
  @include('erp.feeds-imports-items.partials.related..results')
@endsection
