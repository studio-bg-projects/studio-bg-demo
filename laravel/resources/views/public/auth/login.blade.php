@extends('layouts.empty')

@section('content')
  <main class="main" id="top">
    <div class="container">
      <div class="row flex-center min-vh-100 py-5">
        <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3">
          <a class="d-flex flex-center text-decoration-none mb-6 text-center" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.svg') }}" alt="{{ env('APP_NAME') }}" style="width: 80%;"/>
          </a>
          <div class="text-center mb-7">
            <h3 class="text-body-highlight">Вход</h3>
          </div>
          <div class="position-relative">
            <hr class="bg-body-secondary mt-5 mb-4"/>
            <div class="divider-content-center">вход с email</div>
          </div>
          <form method="post" action="?{{ $backto ? 'backto=' . urlencode($backto) : '' }}">
            @csrf

            <div class="form-icon-container">
              <div class="form-floating mb-3">
                <input class="form-control form-icon-input @if($errors->has('email')) is-invalid @endif" id="f-email" type="email" name="email" value="{{ request()->email }}" placeholder="user@insidetrading.bg..."/>
                <label class="form-icon-label" for="f-email">Имейл адрес</label>
                @if($errors->has('email'))
                  <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                  </div>
                @endif
              </div>
              <i class="fa-regular fa-envelope text-body fs-9 form-icon"></i>
            </div>

            <div class="form-icon-container" data-app-password>
              <div class="form-floating mb-3">
                <input class="form-control form-icon-input pe-6 @if($errors->has('password')) is-invalid @endif" id="f-password" type="password" name="password" value="{{ request()->password }}" placeholder="Парола" data-app-password-input/>
                <label class="form-icon-label" for="f-password">Парола</label>
                @if($errors->has('password'))
                  <div class="invalid-feedback">
                    {{ $errors->first('password') }}
                  </div>
                @endif
                <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
                  <i class="fa-regular fa-eye show"></i>
                  <i class="fa-regular fa-eye-slash hide"></i>
                </div>
              </div>
              <i class="fa-regular fa-lock text-body fs-9 form-icon"></i>
            </div>
            <button class="btn btn-primary w-100 mb-3" type="submit">Вход в системата</button>
          </form>
        </div>
      </div>
    </div>
  </main>
@endsection
