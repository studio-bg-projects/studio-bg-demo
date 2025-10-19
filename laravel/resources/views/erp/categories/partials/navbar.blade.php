<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/categories') }}" class="text-body-tertiary">
            <i class="fa-regular fa-layer-group"></i>
            Категории
          </a>
        </li>
        @if (!empty($category->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/categories/update/' . $category->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $category->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('erp/categories/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/categories/create') }}" class="text-body-tertiary">
              Добавяне на категория
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($category->id))
      @if (!Request::is('erp/categories/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/categories/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави категория
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/categories/update/' . $category->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/categories/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/categories/products/' . $category->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/categories/products/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Продукти
          <span class="badge text-bg-info">{{ $category->products->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/categories/specifications/' . $category->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/categories/specifications/*') ? 'active' : '' }}">
          <i class="fa-regular fa-circle-nodes"></i>
          Спецификации
          <span class="badge text-bg-info">{{ $category->specifications->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/categories/delete/' . $category->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете тази КАТЕГОРИЯ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий категорията
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
