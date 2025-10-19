<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/storage-entries') }}" class="text-body-tertiary">
            <i class="fa-regular fa-inbox"></i>
            Заприхождаване
          </a>
        </li>
        @if (!empty($document->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/storage-entries/update/' . $document->id) }}" class="text-body-terтиary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $document->getOriginal('documentNumber') }}
            </a>
          </li>
        @elseif (Request::is('erp/storage-entries/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/storage-entries/create') }}" class="text-body-terтиary">
              Добавяне
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($document->id))
      @if (!Request::is('erp/storage-entries/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/storage-entries/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добавяне
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/storage-entries/update/' . $document->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/storage-entries/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/storage-entries/income-credit-memos/' . $document->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/storage-entries/income-credit-memos/*') ? 'active' : '' }}">
          <span>
            <i class="fa-regular fa-file-circle-plus"></i>
            Кредитни известия
            <span class="badge text-bg-info">{{ $document->incomeCreditMemos->count() }}</span>
          </span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/storage-entries/delete/' . $document->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този запис?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий записа
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
