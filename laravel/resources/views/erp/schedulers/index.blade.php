@extends('layouts.app')

@section('content')
  @include('erp.schedulers.partials.navbar')

  <h1 class="h4 mb-5">Сървърни задачи</h1>

  @include('erp.schedulers.partials.results')
@endsection
