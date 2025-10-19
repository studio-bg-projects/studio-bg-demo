<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/users') }}" class="text-body-tertiary">
            <i class="fa-regular fa-user"></i>
            Потребители
          </a>
        </li>
        @if (!empty($user->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/users/update/' . $user->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $user->getOriginal('email') }}
            </a>
          </li>
        @elseif (Request::is('erp/users/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/users/create') }}" class="text-body-tertiary">
              Добавяне на потребител
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($user->id))
      @if (!Request::is('erp/users/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/users/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави потребител
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/users/update/' . $user->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/users/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/impersonate/login-as/' . $user->id) }}" class="nav-link px-2 fw-bold">
          <i class="fa-regular fa-eyes"></i>
          Влез като
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/users/delete/' . $user->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ПОТРЕБИТЕЛ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий потребителя
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
