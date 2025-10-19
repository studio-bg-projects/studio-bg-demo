@extends('layouts.app')

@section('content')
  @include('erp.mails.partials.navbar')

  <h1 class="h4 mb-5">Имейли</h1>

  @include('erp.mails.partials.results')
@endsection
