@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">Vehicle Inspections</h1>

  @include('vehicle-inspections.partials.results')
@endsection
