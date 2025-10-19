<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/products') }}" class="text-body-tertiary">
            <i class="fa-regular fa-box"></i>
            Продукти
          </a>
        </li>
        @if (!empty($product->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/products/update/' . $product->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $product->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('erp/products/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/products/create') }}" class="text-body-tertiary">
              Добавяне на продукт
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($product->id))
      @if (!Request::is('erp/products/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/products/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави продукт
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/products/update/' . $product->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/products/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/products/related/' . $product->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/products/related/*') ? 'active' : '' }}">
          <i class="fa-regular fa-cubes"></i>
          Свързани продукти
          <span class="badge text-bg-info d-none d-md-inline">{{ $product->related->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/products/specifications/' . $product->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/products/specifications/*') ? 'active' : '' }}">
          <i class="fa-regular fa-circle-nodes"></i>
          Спецификации
          <span class="badge text-bg-info d-none d-md-inline">{{ $product->specifications->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/products/preview/' . $product->id) }}" class="nav-link px-2 fw-bold" target="shop-preview">
          <i class="fa-regular fa-square-arrow-up-right"></i>
          Преглед в магазина
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link px-2 fw-bold dropdown-toggle" href="#" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent" data-bs-popper-config='{"strategy":"fixed"}'>
          <i class="fa-regular fa-plus"></i>
          Още
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item {{ Request::is('erp/products/history/*') ? 'active' : '' }}" href="{{ url('/erp/products/history/' . $product->id) }}">
              История
              <span class="badge text-bg-info d-none d-md-inline">{{ $product->logs->count() }}</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item {{ Request::is('erp/products/storage-items/*') ? 'active' : '' }}" href="{{ url('/erp/products/storage-items/' . $product->id) }}">
              Склад
              <span class="badge text-bg-info d-none d-md-inline">{{ $product->storageItems->count() }}</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/products/delete/' . $product->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ПРОДУКТ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий продукта
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
