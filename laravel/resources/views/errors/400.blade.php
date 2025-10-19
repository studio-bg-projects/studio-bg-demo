@extends('layouts.empty')

@section('content')
  <div class="px-3">
    <div class="row min-vh-100 flex-center p-5">
      <div class="col-12 col-xl-10 col-xxl-8">
        <div class="row justify-content-center align-items-center g-5">
          <div class="col-12 col-lg-6 text-center order-lg-1">
            <img class="img-fluid w-lg-100 d-dark-none" src="{{ asset('/img/spot-illustrations/16.png') }}" alt="" width="400"/>
            <img class="img-fluid w-md-50 w-lg-100 d-light-none" src="{{ asset('/img/spot-illustrations/dark_16.png') }}" alt="" width="400"/>
          </div>
          <div class="col-12 col-lg-6 text-center text-lg-start">
            <h2 class="text-body-secondary fw-bolder mb-3">Възникна грешка!</h2>
            <p class="text-body mb-5">{{ $exception->getMessage() }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
