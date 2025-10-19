@extends('layouts.app')

@section('content')
  @include('erp.manufacturers.partials.navbar')

  <h1 class="h4 mb-5">{{ $manufacturer->getOriginal('name') }} - Свързани продукти</h1>

  @include('erp.products.partials.results')
@endsection
