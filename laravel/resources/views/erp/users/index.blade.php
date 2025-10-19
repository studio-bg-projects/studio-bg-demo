@extends('layouts.app')

@section('content')
  @include('erp.users.partials.navbar')

  <h1 class="h4 mb-5">Потребители</h1>

  @include('erp.users.partials.filters')
  @include('erp.users.partials.results')
@endsection
