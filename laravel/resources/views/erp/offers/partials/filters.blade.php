<form method="get" action="{{ url('/erp/offers') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-offerNumber" name="filter[offerNumber]" value="{{ request()->filter['offerNumber'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-offerNumber">Заглавие</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-offerNumber" name="op[offerNumber]">
          @foreach(App\Http\Controllers\Erp\UsersController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['offerNumber']) && request()->op['offerNumber'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-offerNumber">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[status]" value="eq"/>
        <select class="form-select" id="f-filter-status" name="filter[status]">
          <option value="" {{ (request()->filter['status'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          @foreach (App\Enums\OfferStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ (request()->filter['status'] ?? '') === $status->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::offerStatuses($status)->label }}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-type">Статус</label>
      </div>
    </div>

    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[validUntil]" value="interval"/>
        <input type="text" class="form-control" id="f-filter-validUntil" name="filter[validUntil]" value="{{ request()->filter['validUntil'] ?? '' }}" placeholder="Y-m-d"/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-validUntil">Период</label>
      </div>

      <script type="module">
        flatpickr('#f-filter-validUntil', {
          mode: 'range'
        });
      </script>
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
