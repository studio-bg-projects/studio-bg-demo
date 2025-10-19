<form method="get" action="{{ url('/erp/feeds-imports-items/conflicts') }}">
  <div class="row g-3 mb-3">
    <div class="col">
      <div class="form-floating">
        <input type="hidden" name="op[resolved]" value="eq"/>
        <select class="form-select" id="f-cFilter-resolved" name="cFilter[resolved]">
          <option value="" {{ (request()->cFilter['resolved'] ?? '') === '' ? 'selected' : '' }}>Всички</option>
          <option value="0" {{ (request()->cFilter['resolved'] ?? '') === '0' ? 'selected' : '' }}>Само нерешени</option>
          <option value="1" {{ (request()->cFilter['resolved'] ?? '') === '1' ? 'selected' : '' }}>Само решени</option>
        </select>
        <label class="fw-bold mb-2 text-body-highlight" for="f-cFilter-resolved">Конфликт</label>
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

