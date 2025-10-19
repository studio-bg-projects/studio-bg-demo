<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/offers') }}" class="text-body-tertiary">
            <i class="fa-regular fa-envelope-open-dollar"></i>
            Оферти
          </a>
        </li>
        @if (!empty($offer->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/offers/update/' . $offer->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $offer->getOriginal('offerNumber') }}
            </a>
          </li>
        @elseif (Request::is('erp/offers/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/offers/create') }}" class="text-body-tertiary">
              Добавяне на оферта
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($offer->id))
      @if (!Request::is('erp/offers/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/offers/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добавяне на оферта
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/offers/update/' . $offer->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/offers/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/offers/preview/bg/' . $offer->id . '/pdf') }}" class="nav-link px-2 fw-bold" target="_blank">
          <i class="fa-regular fa-file-pdf"></i>
          PDF
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/offers/preview/bg/' . $offer->id . '/html') }}" class="nav-link px-2 fw-bold" target="_blank">
          <i class="fa-regular fa-print"></i>
          Принт
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/offers/delete/' . $offer->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете тази ОФЕРТА?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий офертата
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
