<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/customers') }}" class="text-body-tertiary">
            <i class="fa-regular fa-person"></i>
            Клиенти
          </a>
        </li>
        @if (!empty($customer->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/customers/update/' . $customer->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $customer->getOriginal('companyName') ?: $customer->getOriginal('email') }}
            </a>
          </li>
        @elseif (Request::is('erp/customers/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/customers/create') }}" class="text-body-tertiary">
              Добавяне на клиент
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($customer->id))
      @if (!Request::is('erp/customers/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/customers/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добавяне на клиент
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/customers/update/' . $customer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers/addresses/' . $customer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers/addresses/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-address-book"></i>
            Адреси
            <span class="badge text-bg-info">{{ $customer->addresses->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers/orders/' . $customer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers/orders/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-cart-shopping"></i>
            Поръчки
            <span class="badge text-bg-info">{{ $customer->orders->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers/incomes/' . $customer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers/incomes/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-money-bill-transfer"></i>
            Приходни плащания
            <span class="badge text-bg-info">{{ $customer->incomes->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers/documents/' . $customer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/customers/documents/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-file-lines"></i>
            Документи
            <span class="badge text-bg-info">{{ $customer->documents->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/customers/delete/' . $customer->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този КЛИЕНТ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий клиента
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
