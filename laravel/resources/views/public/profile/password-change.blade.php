@extends('public.profile.layout')

@section('profile-content')
  <h2 class="h4 card-title">Смяна на парола</h2>

  <form method="post" action="?">
    @csrf

    <div class="row g-3 mb-5">
      <div class="col-12 col-xl-4">
        <div class="form-icon-container" data-app-password>
          <div class="form-floating">
            <input class="form-control form-icon-input pe-6 @if($errors->has('oldPassword')) is-invalid @endif" id="f-oldPassword" type="password" name="oldPassword" value="{{ request()->oldPassword }}" placeholder="Парола" data-app-password-input/>
            <label class="form-icon-label" for="f-oldPassword">Стара парола</label>
            @if($errors->has('oldPassword'))
              <div class="invalid-feedback">
                {{ $errors->first('oldPassword') }}
              </div>
            @endif
            <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
              <i class="fa-regular fa-eye show"></i>
              <i class="fa-regular fa-eye-slash hide"></i>
            </div>
          </div>
          <i class="fa-regular fa-lock text-body fs-9 form-icon"></i>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="form-icon-container" data-app-password>
          <div class="form-floating">
            <input class="form-control form-icon-input pe-6 @if($errors->has('newPassword')) is-invalid @endif" id="f-newPassword" type="password" name="newPassword" value="{{ request()->newPassword }}" placeholder="Парола" data-app-password-input/>
            <label class="form-icon-label" for="f-newPassword">Нова парола</label>
            @if($errors->has('newPassword'))
              <div class="invalid-feedback">
                {{ $errors->first('newPassword') }}
              </div>
            @endif
            <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
              <i class="fa-regular fa-eye show"></i>
              <i class="fa-regular fa-eye-slash hide"></i>
            </div>
          </div>
          <i class="fa-regular fa-lock text-body fs-9 form-icon"></i>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="form-icon-container" data-app-password>
          <div class="form-floating">
            <input class="form-control form-icon-input pe-6 @if($errors->has('newPassword_confirmation')) is-invalid @endif" id="f-newPassword_confirmation" type="password" name="newPassword_confirmation" value="{{ request()->newPassword_confirmation }}" placeholder="Парола" data-app-password-input/>
            <label class="form-icon-label" for="f-newPassword_confirmation">Потвърдете новата парола</label>
            @if($errors->has('newPassword_confirmation'))
              <div class="invalid-feedback">
                {{ $errors->first('newPassword_confirmation') }}
              </div>
            @endif
            <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
              <i class="fa-regular fa-eye show"></i>
              <i class="fa-regular fa-eye-slash hide"></i>
            </div>
          </div>
          <i class="fa-regular fa-lock text-body fs-9 form-icon"></i>
        </div>
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-primary btn-lg mb-2 mb-sm-0" type="submit">Смени</button>
    </div>
  </form>
@endsection
