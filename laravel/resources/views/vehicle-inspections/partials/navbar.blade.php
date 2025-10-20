<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/vehicle-inspections') }}" class="text-body-tertiary">
            <i class="fa-regular fa-car-burst"></i>
            Vehicle Inspections
          </a>
        </li>
        @if (!empty($gptRequest->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/vehicle-inspections/view/' . $gptRequest->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $gptRequest->getOriginal('nameBg') }}
            </a>
          </li>
        @elseif (Request::is('vehicle-inspections/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/vehicle-inspections/create') }}" class="text-body-tertiary ">
              Add inspection
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($gptRequest->id))
      @if (!Request::is('vehicle-inspections/create'))
        <li class="nav-item">
          <a href="{{ url('/vehicle-inspections/create') }}" class="nav-link px-2 fw-bold pulse-btn-primary">
            <i class="fa-regular fa-plus"></i>
            Add inspection
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/vehicle-inspections/delete/' . $gptRequest->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Are you sure you want to delete this record?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Delete inspection
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
