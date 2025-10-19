@extends('layouts.app')

@section('content')
  @include('erp.storage-items.partials.navbar')

  <h1 class="h4 mb-5">Складови артикули</h1>

  @include('erp.storage-items.partials.filters')
  @php($showWriteOffProtocolLink = true)
  @include('erp.storage-items.partials.results')
@endsection
