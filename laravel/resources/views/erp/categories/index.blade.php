@extends('layouts.app')

@section('content')
  @include('erp.categories.partials.navbar')

  <h1 class="h4 mb-5">Категории</h1>

  @include('erp.categories.partials.results')
@endsection
