<form method="get" action="{{ url('/erp/incomes') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-incomeId" name="filter[incomeId]" value="{{ request()->filter['incomeId'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-incomeId">Номер на приходното плащане</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-incomeId" name="op[incomeId]">
          @foreach(App\Http\Controllers\Erp\IncomesController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['incomeId']) && request()->op['incomeId'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-incomeId">Оператор</label>
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
