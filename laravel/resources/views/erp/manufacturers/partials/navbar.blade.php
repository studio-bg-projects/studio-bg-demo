<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/manufacturers') }}" class="text-body-tertiary">
            <i class="fa-regular fa-industry"></i>
            Производители
          </a>
        </li>
        @if (!empty($manufacturer->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/manufacturers/update/' . $manufacturer->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $manufacturer->getOriginal('name') }}
            </a>
          </li>
        @elseif (Request::is('erp/manufacturers/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/manufacturers/create') }}" class="text-body-tertiary">
              Добавяне на производител
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($manufacturer->id))
      @if (!Request::is('erp/manufacturers/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/manufacturers/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави производител
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/manufacturers/update/' . $manufacturer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/manufacturers/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/manufacturers/products/' . $manufacturer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/manufacturers/products/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Продукти
          <span class="badge text-bg-info">{{ $manufacturer->products->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/manufacturers/delete/' . $manufacturer->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ПРОИЗВОДИТЕЛ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий производителя
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
