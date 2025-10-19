<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/erp/mails') }}" class="text-body-tertiary">
            <i class="fa-regular fa-envelope"></i>
            Имейли
          </a>
        </li>
        @if (!empty($mail->id))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/mails/view/' . $mail->id) }}" class="text-body-tertiary text-truncate d-inline-block" style="max-width: 10rem;">
              #{{ $mail->id }} - {{ $mail->to }}
            </a>
          </li>
        @elseif (Request::is('erp/mails/test'))
          <li class="breadcrumb-item active">
            <a href="{{ url('/erp/mails/test') }}" class="text-body-tertiary">
              Тест на имейлите
            </a>
          </li>
        @endif
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    @if (empty($mail->id))
      @if (!Request::is('erp/mails/test'))
        <li class="nav-item">
          <a href="{{ url('/erp/mails/test') }}" class="nav-link px-2 fw-bold">
            <i class="fa-regular fa-flask"></i>
            Тест на имейлите
          </a>
        </li>
      @endif
    @endif
  </ul>
</div>
