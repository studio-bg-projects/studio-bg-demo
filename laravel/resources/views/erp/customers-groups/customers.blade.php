@extends('layouts.app')

@section('content')
  @include('erp.customers-groups.partials.navbar')

  <h1 class="h4 mb-5">{{ $customersGroup->getOriginal('nameBg') }} - Свързани клиенти</h1>

  @include('erp.customers.partials.results')
@endsection
