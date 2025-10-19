@extends('layouts.empty')

@section('content')
  <div class="px-3">
    <div class="row min-vh-100 flex-center p-5">
      <div class="col-12 col-xl-10 col-xxl-8">
        <div class="row justify-content-center align-items-center g-5">
          <div class="col-12 col-lg-6 text-center order-lg-1">
            <img class="img-fluid w-lg-100 d-dark-none" src="{{ asset('/img/spot-illustrations/500-illustration.png') }}" alt="" width="400"/>
            <img class="img-fluid w-md-50 w-lg-100 d-light-none" src="{{ asset('/img/spot-illustrations/dark_500-illustration.png') }}" alt="" width="540"/>
          </div>
          <div class="col-12 col-lg-6 text-center text-lg-start">
            <img class="img-fluid mb-6 w-50 w-lg-75 d-dark-none" src="{{ asset('/img/spot-illustrations/500.png') }}" alt=""/>
            <img class="img-fluid mb-6 w-50 w-lg-75 d-light-none" src="{{ asset('/img/spot-illustrations/dark_500.png') }}" alt=""/>
            <h2 class="text-body-secondary fw-bolder mb-3">Internal server error!</h2>
            <p class="text-body mb-5">But don't worry! Our kitty is here to play you some music.</p>

            @if ($exception && $exception->getMessage())
              <div class="mb-5">
                <strong>{{ $exception->getMessage() }}</strong>
              </div>
            @endif

            <a class="btn btn-lg btn-primary" href="{{ url('/') }}">Go to the homepage</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
