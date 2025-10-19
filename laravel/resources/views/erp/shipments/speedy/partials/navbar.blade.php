<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/shipments/speedy') }}" class="text-body-tertiary">
            <i class="fa-regular fa-truck"></i>
            Пратки (DPD/Speedy)
          </a>
        </li>

        @if (!empty($shipment->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/shipments/speedy/view/' . $shipment->parcelId) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              #{{ $shipment->parcelId }}
            </a>
          </li>
        @elseif (Request::is('erp/shipments/speedy/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/shipments/speedy/create?orderId=' .  request()->get('orderId')) }}" class="text-body-tertiary">
              Създаване на пратка
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($shipment->id))
      @if (!Request::is('erp/shipments/speedy/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/shipments/speedy/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Създаване на пратка
          </a>
        </li>
      @endif
    @endif
  </ul>
</div>
