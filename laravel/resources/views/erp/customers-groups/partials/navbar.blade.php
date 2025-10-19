<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/customers-groups') }}" class="text-body-tertiary">
            <i class="fa-regular fa-poll-people"></i>
            Клиентски групи
          </a>
        </li>
        @if (!empty($customersGroup->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/customers-groups/update/' . $customersGroup->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $customersGroup->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('erp/customers-groups/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/customers-groups/create') }}" class="text-body-tertiary">
              Добавяне на група
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($customersGroup->id))
      @if (!Request::is('erp/customers-groups/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/customers-groups/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави група
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/customers-groups/update/' . $customersGroup->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers-groups/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers-groups/customers/' . $customersGroup->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers-groups/customers/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-person"></i>
            Клиенти
            <span class="badge text-bg-info">{{ $customersGroup->customers->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers-groups/delete/' . $customersGroup->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете тази ГРУПА?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий групата
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
