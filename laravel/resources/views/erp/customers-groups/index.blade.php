@extends('layouts.app')

@section('content')
  @include('erp.customers-groups.partials.navbar')

  <h1 class="h4 mb-5">Клиентски групи</h1>

  @include('erp.customers-groups.partials.results')
@endsection
