<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/products-import') }}" class="text-body-tertiary">
            <i class="fa-regular fa-file-import"></i>
            Импорт на продукти
          </a>
        </li>
        <li class="breadcrumb-item active">
          <a href="{{ url('/erp/products-import/excel') }}" class="text-body-tertiary">
            Excel
          </a>
        </li>
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    <li class="nav-item">
      <a href="{{ url('/erp/products-import/export-all') }}" class="nav-link px-2 fw-bold" onclick="return confirm('Това е частичен експорт, а не пълен бекъп на продуктите!')">
        <span class="text-primary">
          <i class="fa-regular fa-file-arrow-down"></i>
          Екпорт на продуктите
        </span>
      </a>
    </li>
  </ul>
</div>
