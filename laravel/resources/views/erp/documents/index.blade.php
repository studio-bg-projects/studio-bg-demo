@extends('layouts.app')

@section('content')
  @include('erp.documents.partials.navbar')

  <h1 class="h4 mb-5">Документи</h1>

  @include('erp.documents.partials.filters')
  @include('erp.documents.partials.results')
@endsection
