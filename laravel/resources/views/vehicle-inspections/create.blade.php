@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">Създаване на инспекция</h1>

  <hr class="my-5"/>

  <form method="post" action="?" class="mb-5">
    @csrf
    @include('vehicle-inspections.partials.form')

    <hr class="my-5"/>

    <div class="row justify-content-end">
      <div class="col-12 col-xl-8">
        <button class="btn btn-primary w-100" type="submit">
          <i class="fas fa-plus me-2"></i>
          Добави
        </button>
      </div>
    </div>
  </form>
@endsection
