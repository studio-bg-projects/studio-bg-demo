<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/specifications') }}" class="text-body-tertiary">
            <i class="fa-regular fa-circle-nodes"></i>
            Продуктови спецификации
          </a>
        </li>
        @if (!empty($specification->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/specifications/update/' . $specification->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $specification->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('erp/specifications/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/specifications/create') }}" class="text-body-tertiary">
              Добавяне на спецификация
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($specification->id))
      @if (!Request::is('erp/specifications/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/specifications/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави спецификация
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/specifications/update/' . $specification->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/specifications/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/specifications/products/' . $specification->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/specifications/products/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Продукти
          {{--<span class="badge text-bg-info">???</span>--}}
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/specifications/delete/' . $specification->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете тази СПЕЦИФИКАЦИЯ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий спецификацията
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
