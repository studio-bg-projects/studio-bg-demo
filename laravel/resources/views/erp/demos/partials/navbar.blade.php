<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/demos') }}" class="text-body-tertiary">
            <i class="fa-regular fa-envelope-open-dollar"></i>
            Демо
          </a>
        </li>
        @if (!empty($demo->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/demos/update/' . $demo->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $demo->getOriginal('demoNumber') }}
            </a>
          </li>
        @elseif (Request::is('erp/demos/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/demos/create') }}" class="text-body-tertiary">
              Добавяне на демо
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($demo->id))
      @if (!Request::is('erp/demos/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/demos/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добавяне на демо
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/demos/update/' . $demo->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/demos/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/demos/delete/' . $demo->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете това ДЕМО?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий демото
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
