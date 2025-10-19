@extends('layouts.app')

@section('content')
  @include('erp.storage-entries.partials.navbar')

  <h1 class="h4 mb-5">Заприхождаване</h1>

  @include('erp.storage-entries.partials.filters')
  @include('erp.storage-entries.partials.results')
@endsection
