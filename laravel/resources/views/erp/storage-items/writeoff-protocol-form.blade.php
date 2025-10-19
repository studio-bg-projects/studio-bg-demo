@extends('layouts.app')

@section('content')
  @include('erp.storage-items.partials.navbar')

  <h1 class="h4 mb-5">Протокол за отписване/бракуване</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit>
    @csrf

    <div class="alert alert-subtle-warning" role="alert">
      Ако бракувате този артикул, той ще излезе от склада, а действието ще бъде необратимо.
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row gy-2">
          <div class="col-12">
            {{-- no .mt-3 for 1st title --}}
            <h2 class="h5 pb-2 border-bottom border-dashed">Данни за протокола</h2>
          </div>

          <div class="col-12">
            <label class="app-form-label required" for="f-date">Дата на събитието</label>
            <input type="date" class="form-control @if($errors->has('date')) is-invalid @endif" id="f-date" name="date" value="{{ old('date') }}" placeholder="2024-12-30" required/>
            @if($errors->has('date'))
              <div class="invalid-feedback">
                {{ $errors->first('date') }}
              </div>
            @endif

            <script type="module">
              flatpickr('#f-date');
            </script>
          </div>

          <div class="col-12">
            <label class="app-form-label required" for="f-reason">Причина за отписване</label>
            <textarea id="f-reason" name="reason" class="form-control @if($errors->has('reason')) is-invalid @endif" rows="4" required>{{ old('reason') }}</textarea>
            @if($errors->has('reason'))
              <div class="invalid-feedback">
                {{ $errors->first('reason') }}
              </div>
            @endif
          </div>

          <div class="text-end">
            <button class="btn btn-primary btn-lg mt-3" type="submit">
              <i class="fa-regular fa-save me-2"></i>
              Запази
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
