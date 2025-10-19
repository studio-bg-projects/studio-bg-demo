@extends('layouts.app')

@section('content')
  @include('erp.specifications.partials.navbar')

  <h1 class="h4 mb-5">{{ $specification->getOriginal('nameBg') }} - Свързани продукти</h1>

  @include('erp.products.partials.results')
@endsection
