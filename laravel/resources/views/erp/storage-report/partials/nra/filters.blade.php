<form method="get" action="{{ url('/erp/storage-report/nra') }}" class="mb-5">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-period" name="filter[period]" value="{{ request()->filter['period'] ?? '' }}" placeholder="Y-m-d"/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-period">Период</label>
      </div>
    </div>
  </div>

  <div class="d-inline-flex gap-2">
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

<script type="module">
  flatpickr('#f-filter-period', {mode: 'range'});
</script>
