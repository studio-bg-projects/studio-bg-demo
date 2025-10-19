@extends('layouts.app')

@section('content')
  @include('erp.feeds-imports.partials.navbar')

  <h1 class="h4 mb-5">{{ $feed->getOriginal('providerName') }} - Записи</h1>

  @include('erp.feeds-imports-items.partials.filters')
  @include('erp.feeds-imports-items.partials.results')
@endsection

