<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/orders') }}" class="text-body-tertiary">
            <i class="fa-regular fa-cart-shopping"></i>
            Поръчки
          </a>
        </li>
        @if (!empty($order->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/orders/view/' . $order->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              Поръчка #{{ $order->getOriginal('id') }}
            </a>
          </li>
        @elseif (Request::is('erp/orders/prepare') || Request::is('erp/orders/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/orders/prepare') }}" class="text-body-tertiary">
              Добавяне на поръчка
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($order->id))
      @if (!Request::is('erp/orders/prepare') && !Request::is('erp/orders/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/orders/prepare') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави поръчка
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/orders/view/' . $order->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/orders/view/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Преглед
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/orders/update/' . $order->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/orders/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2 fw-bold" href="#" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent" data-bs-popper-config='{"strategy":"fixed"}'>
          <i class="fa-regular fa-truck"></i>
          Изпращане
          <i class="fa-regular fa-angle-down ms-2"></i>
        </a>

        <div class="dropdown-menu">
          <a class="dropdown-item" href="{{ url('/erp/shipments/speedy/create?orderId=' . $order->id) }}">
            Изпрати през DPD/Speedy
          </a>
        </div>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/orders/documents/' . $order->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/orders/documents/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-file-lines"></i>
            Документи
            <span class="badge text-bg-info">{{ $order->documents->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/orders/incomes-allocations/' . $order->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/orders/incomes-allocations/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-money-bill-transfer"></i>
            Плащания
            <span class="badge text-bg-info">{{ $order->incomesAllocations->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#orderDebugModal" class="nav-link px-2 fw-bold" data-bs-toggle="modal" data-bs-target="#orderDebugModal">
          <i class="fa-regular fa-code"></i>
          Debug
        </a>
      </li>
    @endif
  </ul>
</div>

@if (!empty($order->id))
  <div class="modal fade modal-xl" id="orderDebugModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="orderDebugModalLabel">Order Data</h5>
          <button class="btn btn-close p-1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <pre>{{ json_encode($order->shopData, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
      </div>
    </div>
  </div>
@endif
