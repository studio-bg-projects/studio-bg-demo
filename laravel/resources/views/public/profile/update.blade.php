@extends('public.profile.layout')

@section('profile-content')
  <h2 class="h4 card-title">Редакция на профил</h2>

  <form method="post" action="?">
    @csrf

    <div class="row g-3 mb-5">
      <div class="col-12">
        <div class="form-floating">
          <input type="text" class="form-control @if($errors->has('fullName')) is-invalid @endif" id="f-fullName" name="fullName" value="{{ $user->fullName }}" placeholder="Петър Петров..."/>
          <label class="form-label" for="f-fullName">Име и Фамилия</label>
          @if($errors->has('fullName'))
            <div class="invalid-feedback">
              {{ $errors->first('fullName') }}
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-primary btn-lg mb-2 mb-sm-0" type="submit">Редактирай</button>
    </div>
  </form>
@endsection
