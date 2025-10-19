<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/documents') }}" class="text-body-tertiary">
            <i class="fa-regular fa-file-lines"></i>
            Документи
          </a>
        </li>
        @if (!empty($document->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/documents/view/' . $document->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ \App\Services\MapService::documentTypes($document->getOriginal('type'))->labelBg }}
              #{{ $document->getOriginal('documentNumber') }}
            </a>
          </li>
        @elseif (Request::is('/erp/documents/create/*'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/documents/prepare/' . $document->type->value) }}" class="text-body-tertiary">
              Добавяне на документ - {{ \App\Services\MapService::documentTypes($document->type)->labelBg }}
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($document->id))
      @if (!Request::is('/erp/documents/create/*'))
        <li class="nav-item dropdown">
          <a class="nav-link px-2 fw-bold dropdown-toggle" href="#" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent" data-bs-popper-config='{"strategy":"fixed"}'>
            <i class="fa-regular fa-plus"></i>
            Добави документ
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            @foreach (App\Enums\DocumentType::cases() as $type)
              <li>
                <a class="dropdown-item" href="{{ url('/erp/documents/prepare/' . $type->value) }}">
                  {{ \App\Services\MapService::documentTypes($type)->labelBg }}
                </a>
              </li>
            @endforeach
          </ul>
        </li>
      @endif
    @else
      <li class="nav-item">
        <a href="{{ url('/erp/documents/view/' . $document->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/documents/view/*') ? 'active' : '' }}">
          <i class="fa-regular fa-file"></i>
          Преглед
        </a>
      </li>
      @if (\App\Services\MapService::documentTypes($document->type)->isPayable)
        <li class="nav-item">
          <a href="{{ url('/erp/documents/incomes-allocations/' . $document->id) }}" class="nav-link px-2 fw-bold {{ Request::is('erp/documents/incomes-allocations/*') ? 'active' : '' }}">
            <span>
              <i class="fa-regular fa-money-bill-transfer"></i>
              Плащания
              <span class="badge text-bg-info">{{ $document->incomesAllocations->count() }}</span>
            </span>
          </a>
        </li>
      @endif
      <li class="nav-item">
        <a class="nav-link px-2 fw-bold" href="#" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent" data-bs-popper-config='{"strategy":"fixed"}'>
          <i class="fa-regular fa-magnifying-glass"></i>
          Визия
          <i class="fa-regular fa-angle-down ms-2"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret">
          <div class="card position-relative border-0">
            <div class="card-header p-2">
              <h5 class="text-body-emphasis mb-0">Визия на документа</h5>
            </div>
            <div class="card-body p-0">
              <div class="scrollbar-overlay" style="max-height: 27rem;">
                @foreach (App\Enums\DocumentType::cases() as $type)
                  <div class="px-2 px-sm-3 py-3 position-relative border-bottom">
                    <h2 class="h4 fs-9 text-body-emphasis">
                      {{ \App\Services\MapService::documentTypes($type)->labelBg }}
                    </h2>

                    <div class="row g-2">
                      <div class="col">
                        <a href="{{ url('/erp/documents/preview/bg/' . $document->id . '/' . $type->value . '/html') }}" target="_blank" class="btn btn-sm btn-subtle-secondary w-100">
                          HTML
                          <br/>
                          <span class="badge badge text-bg-secondary">BG</span>
                        </a>
                      </div>
                      <div class="col">
                        <a href="{{ url('/erp/documents/preview/bg/' . $document->id . '/' . $type->value . '/pdf') }}" target="_blank" class="btn btn-sm btn-subtle-secondary w-100">
                          PDF
                          <br/>
                          <span class="badge badge text-bg-secondary">BG</span>
                        </a>
                      </div>
                      <div class="col">
                        <a href="{{ url('/erp/documents/preview/en/' . $document->id . '/' . $type->value . '/html') }}" target="_blank" class="btn btn-sm btn-subtle-secondary w-100">
                          HTML
                          <br/>
                          <span class="badge badge text-bg-secondary">EN</span>
                        </a>
                      </div>
                      <div class="col">
                        <a href="{{ url('/erp/documents/preview/en/' . $document->id . '/' . $type->value . '/pdf') }}" target="_blank" class="btn btn-sm btn-subtle-secondary w-100">
                          PDF
                          <br/>
                          <span class="badge badge text-bg-secondary">EN</span>
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a href="{{ url('/erp/documents/delete/' . $document->id) }}" class="nav-link px-2 fw-bold" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ДОКУМЕНТ?')">
          <span class="text-danger">
            <i class="fa-regular fa-trash-can"></i>
            Изтрий документа
          </span>
        </a>
      </li>
    @endif
  </ul>
</div>
