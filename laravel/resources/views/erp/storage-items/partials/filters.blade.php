<form method="get" action="{{ url('/erp/storage-items') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-productId" name="filter[productId]" value="{{ request()->filter['productId'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-productId">Продукт ID</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-productId" name="op[productId]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['productId']) && request()->op['productId'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-productId">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-invoiceNumber" name="filter[invoiceNumber]" value="{{ request()->filter['invoiceNumber'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-invoiceNumber">Номер на фактура</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-invoiceNumber" name="op[invoiceNumber]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['invoiceNumber']) && request()->op['invoiceNumber'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-invoiceNumber">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-serialNumber" name="filter[serialNumber]" value="{{ request()->filter['serialNumber'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-serialNumber">SN</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-serialNumber" name="op[serialNumber]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['serialNumber']) && request()->op['serialNumber'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-serialNumber">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-note" name="filter[note]" value="{{ request()->filter['note'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-note">Бележка</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-note" name="op[note]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['note']) && request()->op['note'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-note">Оператор</label>
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
