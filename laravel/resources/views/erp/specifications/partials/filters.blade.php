<form method="get" action="{{ url('/erp/specifications') }}">
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
          @foreach(App\Http\Controllers\Erp\SpecificationsController::$filterOperators as $value => $label)
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
        <input type="hidden" name="op[valueType]" value="eq"/>
        <select class="form-select" id="f-filter-valueType" name="filter[valueType]">
          <option value="" {{ (request()->filter['valueType'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          @foreach (App\Enums\SpecificationValueType::cases() as $valueType)
            <option value="{{ $valueType->value }}" {{ (request()->filter['valueType'] ?? '') === $valueType->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::specificationValueTypes($valueType)->label }} [{{ $valueType->value }}]
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-valueType">Вид</label>
      </div>
    </div>

    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[isActive]" value="eq"/>
        <select class="form-select" id="f-filter-isActive" name="filter[isActive]">
          <option value="" {{ (request()->filter['isActive'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          <option value="1" {{ (request()->filter['isActive'] ?? '') === '1' ? 'selected' : '' }}>Активни</option>
          <option value="0" {{ (request()->filter['isActive'] ?? '') === '0' ? 'selected' : '' }}>Неактивни</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-isActive">Статус</label>
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

