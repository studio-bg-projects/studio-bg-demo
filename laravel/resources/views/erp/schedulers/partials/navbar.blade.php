<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/schedulers') }}" class="text-body-tertiary">
            <i class="fa-regular fa-clock"></i>
            Сървърни задачи
          </a>
        </li>
        @if (!empty($schedule->jobId))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/schedulers/view/' . $schedule->jobId) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              {{ $schedule->getOriginal('jobId') }}
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (!empty($schedule->jobId))
      <li class="nav-item">
        <a href="{{ url('/erp/schedulers/run/' . $schedule->jobId) }}" class="nav-link px-2 fw-bold" target="_blank">
          <i class="fa-regular fa-rocket"></i>
          Стартирай ръчно
        </a>
      </li>
    @endif
  </ul>
</div>
