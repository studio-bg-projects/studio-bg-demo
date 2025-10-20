@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">Create Inspection</h1>

  <hr class="my-5"/>

  <form method="post" action="?" class="mb-5" enctype="multipart/form-data" data-disable-on-submit>
    @csrf
    <div class="row">
      <div class="col-12 col-xl-4 pb-3 pb-lg-0">
        <h4 class="card-title mt-4 mb-2">Images</h4>
        <p class="text-body-secondary mb-0 fs-8">Attach images from
          <strong class="text-primary">a single composition</strong>
          . You can attach up to
          <strong>10 photos</strong>
          .
        </p>
      </div>

      <div class="col-12 col-xl-8">
        <div class="card">
          <div class="card-body pb-5">
            <h4 class="card-title mb-4">Upload Images</h4>
            <input type="file" class="form-control @if($errors->has('photos')) is-invalid @endif" id="f-photos" name="photos[]" accept="image/*" multiple/>
            @if($errors->has('photos'))
              <div class="invalid-feedback">
                {{ $errors->first('photos') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <hr class="my-5"/>

    <div class="row justify-content-end">
      <div class="col-12 col-xl-8">
        <button class="btn btn-primary w-100" type="submit">
          <i class="fas fa-plus me-2"></i>
          Add
        </button>
      </div>
    </div>
  </form>
@endsection
