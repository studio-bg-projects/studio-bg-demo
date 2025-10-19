@extends('layouts.app')

@section('content')
  @include('erp.sales-representatives.partials.navbar')

  <h1 class="h4 mb-5">{{ $salesRepresentative->getOriginal('nameBg') }} - Свързани документи</h1>

  @include('erp.documents.partials.results')
@endsection
