<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/incomes') }}" class="text-body-tertiary">
            <i class="fa-regular fa-money-bill"></i>
            Приходни плащания
          </a>
        </li>
        @if (!empty($income->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/incomes/update/' . $income->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              Плащане #{{ $income->getOriginal('id') }}
            </a>
          </li>
        @elseif (Request::is('erp/incomes/create'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/incomes/create') }}" class="text-body-tertiary">
              Добавяне на плащане
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($income->id))
      @if (!Request::is('erp/incomes/create'))
        <li class="nav-item">
          <a href="{{ url('/erp/incomes/create') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-plus"></i>
            Добави плащане
          </a>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/incomes/update/' . $income->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/incomes/update/*') ? 'active' : '' }}">
          <i class="fa-regular fa-pen-to-square"></i>
          Редакция
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/incomes/delete/' . $income->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ПРИХОД?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий плащането
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
