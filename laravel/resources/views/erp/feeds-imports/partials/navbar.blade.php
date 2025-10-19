<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/feeds-imports') }}" class="text-body-tertiary">
            <i class="fa-regular fa-rss"></i>
            Доставчици - XML Feed
          </a>
        </li>
        @if (!empty($feed->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/feeds-imports/update/' . $feed->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $feed->getOriginal('providerName') }}
            </a>
          </li>
        @elseif (Request::is('erp/feeds-imports/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/feeds-imports/create') }}" class="text-body-tertiary">
              Добавяне на feed
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($feed->id))
      @if (!Request::is('erp/feeds-imports/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/feeds-imports/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави feed
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/feeds-imports/update/' . $feed->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/feeds-imports/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/feeds-imports/items/' . $feed->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/feeds-imports/items/*') ? 'active' : '' }}">
          <i class="fa-regular fa-box"></i>
          Записи
          <span class="badge text-bg-info">{{ $feed->items->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/feeds-imports/delete/' . $feed->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този FEED?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий feed-ът
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
