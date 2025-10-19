@extends('layouts.app')

@section('content')
  @include('erp.feeds-imports-items.partials.conflicts.navbar')

  <h1 class="h4 mb-5">Конфликтни записи</h1>

  @include('erp.feeds-imports-items.partials.conflicts.filters')
  @include('erp.feeds-imports-items.partials.conflicts.results')
@endsection

