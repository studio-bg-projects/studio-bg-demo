@extends('layouts.app')

@section('content')
  @include('erp.shipments.speedy.partials.navbar')

  <h1 class="h4 mb-5">Пратки &mdash; DPD/Speedy</h1>

  @include('erp.shipments.speedy.partials.filters')
  @include('erp.shipments.speedy.partials.results')
@endsection
