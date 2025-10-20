@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">Vehicle Inspections</h1>

  @include('vehicle-inspections.partials.results')

  <div class="text-center mb-5">
    <a href="{{ url('/vehicle-inspections/create') }}" class="btn btn-primary">
      <i class="fa-regular fa-plus"></i>
      Create inspection
    </a>
  </div>
@endsection
