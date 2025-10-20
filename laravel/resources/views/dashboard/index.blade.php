@extends('layouts.app')

@section('content')
  <div class="row mt-2 align-items-center justify-content-between text-center text-lg-start mb-6 mb-lg-0">
    <div class="col-lg-5 text-center text-lg-start">
      <h3 class="fw-bolder mb-3">Hello World!</h3>
      <p class="mb-1 px-md-7 px-lg-0">Welcome to my demo project.</p>
      <p class="mb-1 px-md-7 px-lg-0">This page is a simple starting point - here you can explore several modules I’ve developed to showcase different parts of the system.</p>
      <p class="mb-1 px-md-7 px-lg-0">Have fun exploring!</p>
    </div>
    <div class="col-lg-7">
      <img class="feature-image img-fluid mb-9 mb-lg-0 d-dark-none" src="{{ asset('/img/spot-illustrations/24_2.png') }}" height="394" alt="">
      <img class="feature-image img-fluid mb-9 mb-lg-0 d-light-none" src="{{ asset('/img/spot-illustrations/dark_24.png') }}" height="394" alt="">
    </div>
  </div>
@endsection
