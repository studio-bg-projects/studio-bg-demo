@extends('layouts.app')

@section('content')
  @include('erp.categories.partials.navbar')

  <h1 class="h4 mb-5">{{ $category->getOriginal('nameBg') }} - Свързани продукти</h1>

  @include('erp.products.partials.results')
@endsection
