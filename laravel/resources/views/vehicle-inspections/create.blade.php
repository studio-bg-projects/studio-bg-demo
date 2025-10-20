@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">Създаване на инспекция</h1>

  <hr class="my-5"/>

  <form method="post" action="?" class="mb-5" enctype="multipart/form-data" data-disable-on-submit>
    @csrf
    <div class="row">
      <div class="col-12 col-xl-4 pb-3 pb-lg-0">
        <h4 class="card-title mt-4 mb-2">Изображения</h4>
        <p class="text-body-secondary mb-0 fs-8">Прикачете изображения от
          <strong class="text-primary">една композиция</strong>
          . Може да прикачите до
          <strong>10 снимки</strong>
          .
        </p>
      </div>

      <div class="col-12 col-xl-8">
        <div class="card">
          <div class="card-body pb-5">
            <h4 class="card-title mb-4">Прикачване на изображения</h4>
            <input type="file" class="form-control " id="f-photos" name="photos[]" accept="image/*" multiple/>
          </div>
        </div>
      </div>
    </div>

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
