<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/sales-representatives') }}" class="text-body-tertiary">
            <i class="fa-regular fa-user-headset"></i>
            Търговски преставители
          </a>
        </li>
        @if (!empty($salesRepresentative->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/sales-representatives/update/' . $salesRepresentative->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $salesRepresentative->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('erp/sales-representatives/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/sales-representatives/create') }}" class="text-body-tertiary">
              Добавяне на търговски преставител
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($salesRepresentative->id))
      @if (!Request::is('erp/sales-representatives/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/sales-representatives/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави търговски преставител
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/sales-representatives/update/' . $salesRepresentative->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/sales-representatives/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/sales-representatives/customers/' . $salesRepresentative->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/sales-representatives/customers/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Клиенти
          <span class="badge text-bg-info">{{ $salesRepresentative->customers->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/sales-representatives/documents/' . $salesRepresentative->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/sales-representatives/documents/*') ? 'active' : '' }}">
          <i class="fa-regular fa-file-lines"></i>
          Документи
          <span class="badge text-bg-info">{{ $salesRepresentative->documents->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/sales-representatives/delete/' . $salesRepresentative->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ТЪРГОВСКИ ПРЕСТАВИТЕЛ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий преставителя
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
