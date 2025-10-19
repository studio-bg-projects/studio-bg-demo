@extends('layouts.app')

@section('content')
  @include('erp.config.partials.navbar')

  <h1 class="h4 mb-5">Конфигурация</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf
    <div class="card">
      <div class="card-body pb-1">
        @foreach ($configs as $config)
          @if (!$config->isHidden)
            <div class="mb-4">
              <label class="app-form-label" for="f-{{ $config->key }}">{{ $config->description }} [{{ $config->key }}]</label>
              <textarea class="form-control @if($errors->has($config->key)) is-invalid @endif" id="f-{{ $config->key }}" name="{{ $config->key }}" @if($config->isLocked) disabled @endif>{{ $config->value }}</textarea>
              @if($errors->has($config->key))
                <div class="invalid-feedback">
                  {{ $errors->first($config->key) }}
                </div>
              @endif
            </div>
          @endif
        @endforeach

        <hr class="my-4"/>

        <div class="text-end">
          <button class="btn btn-primary btn-lg mb-4" type="submit">
            <i class="fa-regular fa-pen-to-square me-2"></i>
            Редакция
          </button>
        </div>
      </div>
    </div>
  </form>
@endsection
