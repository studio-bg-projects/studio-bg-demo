@extends('layouts.app')

@section('content')
  @include('erp.specifications.partials.navbar')

  <h1 class="h4 mb-5">Продуктови спецификации</h1>

  @include('erp.specifications.partials.filters')
  @include('erp.specifications.partials.results')
@endsection
