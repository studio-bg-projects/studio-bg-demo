<div class="card h-100">
  <div class="card-body d-flex flex-row">
    <div>
      <h2 class="h4 mb-1">Конфликтни продукти</h2>
      <div class="fs-2 fw-bold">{{ $conflictsCount }}</div>
    </div>
    <a href="{{ url('/erp/feeds-imports-items/conflicts?cFilter[resolved]=0') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-arrow-right"></i>
      Към списъка
    </a>
  </div>
</div>
