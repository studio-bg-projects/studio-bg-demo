<form method="get" action="{{ url('/erp/orders') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-id" name="filter[id]" value="{{ request()->filter['id'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-id">ID</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-id" name="op[id]">
          @foreach(App\Http\Controllers\Erp\OrdersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['id']) && request()->op['id'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-id">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="form-floating">
      <input type="hidden" name="op[status]" value="eq"/>
      <select class="form-select" id="f-filter-status" name="filter[status]">
        <option value="" {{ (request()->filter['status'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
        @foreach (App\Enums\OrderStatus::cases() as $status)
          <option value="{{ $status->value }}" {{ (request()->filter['status'] ?? '') === $status->value ? 'selected' : '' }} style="color:{{ \App\Services\MapService::orderStatus($status)->color }} ">
            {{ \App\Services\MapService::orderStatus($status)->labelBg }} [{{ $status->value }}]
          </option>
        @endforeach
      </select>
      <label class="fw-bold mb-2 text-body-highlight" for="f-filter-status">Статус</label>
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
