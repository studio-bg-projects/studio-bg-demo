<form method="get" action="{{ url('/erp/shipments/speedy') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-parcelId" name="filter[parcelId]" value="{{ request()->filter['parcelId'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-parcelId">Товарителница</label>
      </div>
    </div>
    <div class="col-3 col-md-2 col-xxl-1">
      <div class="form-floating">
        <select class="form-select" id="f-op-parcelId" name="op[parcelId]">
          @foreach(App\Http\Controllers\Erp\Shipments\SpeedyController::$filterOperators as $value => $label)
            <option value="{{ $value }}" {{ isset(request()->op['parcelId']) && request()->op['parcelId'] == $value ? 'selected' : '' }}>
              {!! $label !!}
            </option>
          @endforeach
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-op-parcelId">Оператор</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[createdAt]" value="interval"/>
        <input type="text" class="form-control" id="f-filter-createdAt" name="filter[createdAt]" value="{{ request()->filter['createdAt'] ?? '' }}" placeholder="Y-m-d"/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-createdAt">Период</label>
      </div>

      <script type="module">
        flatpickr('#f-filter-createdAt', {
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
