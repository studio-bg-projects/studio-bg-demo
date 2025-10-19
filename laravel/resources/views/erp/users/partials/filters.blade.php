<form method="get" action="{{ url('/erp/users') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-email" name="filter[email]" value="{{ request()->filter['email'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-email">Имейл</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-email" name="op[email]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['email']) && request()->op['email'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-email">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-fullName" name="filter[fullName]" value="{{ request()->filter['fullName'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-fullName">Име</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-fullName" name="op[fullName]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['fullName']) && request()->op['fullName'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-fullName">Оператор</label>
      </div>
    </div>
  </div>

  <div class="d-inline-flex gap-2 mb-5 w-100">
    <button class="btn btn-primary px-9" type="submit">Покажи</button>
    <a href="?" class="btn btn-phoenix-primary px-4 position-relative">
      <i class="fa-regular fa-arrows-rotate"></i>
      Изчисти
      @if (request()->filter)
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      @endif
    </a>
  </div>
</form>
