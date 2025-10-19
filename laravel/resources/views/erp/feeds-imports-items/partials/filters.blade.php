<form method="get" action="?">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-itemMpn" name="filter[itemMpn]" value="{{ request()->filter['itemMpn'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-itemMpn">MPN</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-itemMpn" name="op[itemMpn]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['itemMpn']) && request()->op['itemMpn'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-itemMpn">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-itemEan" name="filter[itemEan]" value="{{ request()->filter['itemEan'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-itemEan">EAN</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-itemEan" name="op[itemEan]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['itemEan']) && request()->op['itemEan'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-itemEan">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-itemName" name="filter[itemName]" value="{{ request()->filter['itemName'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-itemName">Име</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-itemName" name="op[itemName]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['itemName']) && request()->op['itemName'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-itemName">Оператор</label>
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
