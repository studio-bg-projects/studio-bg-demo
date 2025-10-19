<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/storage-items') }}" class="text-body-tertiary">
            <i class="fa-regular fa-boxes-stacked"></i>
            Складови артикули
          </a>
        </li>
        @isset($storageItem->id)
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/storage-items/view/' . $storageItem->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $storageItem->product?->nameBg }} (ID: {{ $storageItem->id }})
            </a>
          </li>
        @endisset
      </ol>
    </li>
  </ul>
</div>
