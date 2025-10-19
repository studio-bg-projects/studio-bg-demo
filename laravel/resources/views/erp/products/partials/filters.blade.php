<form method="get" action="{{ url('/erp/products') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-mpn" name="filter[mpn]" value="{{ request()->filter['mpn'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-mpn">MPN</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-mpn" name="op[mpn]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['mpn']) && request()->op['mpn'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-mpn">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-ean" name="filter[ean]" value="{{ request()->filter['ean'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-ean">EAN</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-ean" name="op[ean]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['ean']) && request()->op['ean'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-ean">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-nameBg" name="filter[nameBg]" value="{{ request()->filter['nameBg'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-nameBg">Име</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-nameBg" name="op[nameBg]">
          @foreach(App\Http\Controllers\Erp\ProductsController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['nameBg']) && request()->op['nameBg'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-nameBg">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[onStock]" value="eq"/>
        <select class="form-select" id="f-filter-onStock" name="filter[onStock]">
          <option value="" {{ (request()->filter['onStock'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          <option value="1" {{ (request()->filter['onStock'] ?? '') === '1' ? 'selected' : '' }}>На склад</option>
          <option value="0" {{ (request()->filter['onStock'] ?? '') === '0' ? 'selected' : '' }}>Изискващи доставка</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-onStock">На склад</label>
      </div>
    </div>

    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[usageStatus]" value="eq"/>
        <select class="form-select" id="f-filter-usageStatus" name="filter[usageStatus]">
          <option value="" {{ (request()->filter['usageStatus'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          @foreach (App\Enums\ProductUsageStatus::cases() as $usageStatus)
            <option value="{{ $usageStatus->value }}" {{ (request()->filter['usageStatus'] ?? '') === $usageStatus->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::productUsageStatus($usageStatus)->label }}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-type">Статус</label>
      </div>
    </div>

    <div class="col">
      <div class="form-floating">
        <select class="form-select" id="f-cFilter-check" name="cFilter[check]">
          <option value="" {{ (request()->cFilter['check'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          <option value="noCategories" {{ (request()->cFilter['check'] ?? '') === 'noCategories' ? 'selected' : '' }}>Без категория</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-cFilter-check">Проверка</label>
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

    <div class="d-flex ms-auto align-items-center">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="f-tool-showNumber" name="tool[showNumber]" {{ request()->tool['showNumber'] ?? false ? 'checked' : '' }} onchange="this.form.submit()"/>
        <label class="form-check-label" for="f-tool-showNumber">Покажи номера</label>
      </div>
    </div>
  </div>
</form>
