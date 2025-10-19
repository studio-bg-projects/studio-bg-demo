<form method="get" action="{{ url('/erp/storage-report/products') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <select class="form-select" id="f-filter-products" name="filter[products]">
          <option value="all" {{ (request()->filter['products'] ?? '') === 'all' ? 'selected' : '' }}>Покажи всички</option>
          <option value="withItems" {{ (request()->filter['products'] ?? 'withItems') === 'withItems' ? 'selected' : '' }}>Покажи само със складови артикули</option>
          <option value="withoutItems" {{ (request()->filter['products'] ?? '') === 'withoutItems' ? 'selected' : '' }}>Покажи само без складови артикули</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-products">Продукти</label>
      </div>
    </div>
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-q" name="filter[q]" value="{{ request()->filter['q'] ?? '' }}" placeholder="..."/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-q">Търсене</label>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="text" class="form-control" id="f-filter-period" name="filter[period]" value="{{ request()->filter['period'] ?? '' }}" placeholder="Y-m-d"/>
        <label class="fw-bold mb-2 text-body-highlight" for="f-filter-period">Период</label>
      </div>

      <script type="module">
        flatpickr('#f-filter-period', {
          mode: 'range'
        });
      </script>
    </div>
    <div class="col">
      <div class="form-floating">
        <select class="form-select" id="f-direction" name="direction">
          <option value="all" {{ request('direction', 'all') === 'all' ? 'selected' : '' }}>Всички</option>
          <option value="in" {{ request('direction') === 'in' ? 'selected' : '' }}>Само заприхождавания</option>
          <option value="out" {{ request('direction') === 'out' ? 'selected' : '' }}>Само изписвания</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-direction">Посока</label>
      </div>
    </div>
  </div>

  <div class="d-inline-flex gap-2 mb-5 w-100">
    <button class="btn btn-primary px-9" type="submit">Покажи</button>
    <a href="?" class="btn btn-phoenix-primary px-4 position-relative">
      <i class="fa-regular fa-arrows-rotate"></i>
      Изчисти
      @if (request()->filter || request('direction') !== 'all')
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      @endif
    </a>

    <div class="d-flex ms-auto align-items-center">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="f-no-pagination" name="filter[noPagination]" {{ request()->filter['noPagination'] ?? false ? 'checked' : '' }} onchange="this.form.submit()"/>
        <label class="form-check-label" for="f-no-pagination">Без странициране</label>
      </div>
    </div>
  </div>
</form>
