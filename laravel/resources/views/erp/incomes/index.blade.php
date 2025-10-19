@extends('layouts.app')

@section('content')
  @include('erp.incomes.partials.navbar')

  <h1 class="h4 mb-5">Приходни плащания</h1>

  @include('erp.incomes.partials.filters')
  @include('erp.incomes.partials.results')
@endsection
