@extends('layouts.app')

@section('content')
  @include('erp.mails.partials.navbar')

  <h1 class="h4 mb-5">Тест на имейлите</h1>

  <hr class="my-5">

  <div class="alert alert-subtle-info mb-5" role="alert">Изберете вида имейл, който искате да изпратите, попълнете необходимата информация и ще видите как ще изглежда, какво заглавие ще има и до кого ще бъде изпратен.</div>

  <div class="row g-0" id="js-mail-panels">
    <div class="col-12 col-md-4 border border-bottom-0 border-end-0">
      <div class="nav flex-md-column fs-9 vertical-tab h-100 justify-content-between" role="tablist" aria-orientation="vertical">
        <a data-action="orderNew" data-bs-target="#orderPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-cart-circle-plus"></i>
          <span class="d-none d-md-inline">
            Нова поръчка
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
        <a data-action="order" data-bs-target="#orderPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-cart-shopping"></i>
          <span class="d-none d-md-inline">
            Поръчка (известие)
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
        <a data-action="orderNewNotify" data-bs-target="#orderPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-cart-shopping-fast"></i>
          <span class="d-none d-md-inline">
            Нова поръчка
            <span class="badge badge-phoenix badge-phoenix-primary">оператор</span>
          </span>
        </a>
        <a data-action="customerWelcome" data-bs-target="#customerPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-person"></i>
          <span class="d-none d-md-inline">
            Нов клиент - Welcome
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
        <a data-action="customerWelcomeNotify" data-bs-target="#customerPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-user-tie"></i>
          <span class="d-none d-md-inline">
            Нов клиент - Welcome
            <span class="badge badge-phoenix badge-phoenix-primary">оператор</span>
          </span>
        </a>
        <a data-action="customerApproved" data-bs-target="#customerPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-circle-check"></i>
          <span class="d-none d-md-inline">
            Одобрение на клиент
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
        <a data-action="customerCreditLineValue" data-bs-target="#customerPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-credit-card"></i>
          <span class="d-none d-md-inline">
            Кредитна линия - стойност
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
        <a data-action="customerCreditLineRequestNotify" data-bs-target="#customerPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-credit-card"></i>
          <span class="d-none d-md-inline">
            Кредитна линия - искане
            <span class="badge badge-phoenix badge-phoenix-primary">оператор</span>
          </span>
        </a>
        <a data-action="document" data-bs-target="#documentPanel" class="nav-link border-end border-bottom text-center text-md-start cursor-pointer outline-none d-md-flex align-items-md-center" data-bs-toggle="tab" role="tab">
          <i class="me-md-2 fa-regular fa-file-lines"></i>
          <span class="d-none d-md-inline">
            Известяване за документ
            <span class="badge badge-phoenix badge-phoenix-info">клиент</span>
          </span>
        </a>
      </div>
    </div>

    <div class="col-12 col-md-8">
      <form method="post" action="{{ url('erp/mails/test') }}" data-disable-on-submit>
        @csrf
        <input type="hidden" name="action" value="{{ request()->get('action') }}"/>

        <div class="tab-content py-3 ps-md-4 h-100">
          <!-- Order -->
          <div class="tab-pane fade" id="orderPanel" role="tabpanel">
            <h3 class="h5 mb-3 text-body-highlight js-title">-</h3>

            <div class="mb-4">
              <label class="app-form-label" for="f-orderId">Order ID</label>
              <input type="text" class="form-control form-control-sm" id="f-orderId" name="orderId" value="{{ request()->get('orderId') }}"/>
            </div>

            <button class="btn btn-sm btn-primary">
              <i class="fa-regular fa-check me-1 fs-10"></i>
              Продължи
            </button>
          </div>

          <!-- Customer -->
          <div class="tab-pane fade" id="customerPanel" role="tabpanel">
            <h3 class="h5 mb-3 text-body-highlight js-title">-</h3>

            <div class="mb-4">
              <label class="app-form-label" for="f-customerId">Customer ID</label>
              <input type="text" class="form-control form-control-sm" id="f-customerId" name="customerId" value="{{ request()->get('customerId') }}"/>
            </div>

            <button class="btn btn-sm btn-primary">
              <i class="fa-regular fa-check me-1 fs-10"></i>
              Продължи
            </button>
          </div>

          <!-- Document -->
          <div class="tab-pane fade" id="documentPanel" role="tabpanel">
            <h3 class="h5 mb-3 text-body-highlight js-title">-</h3>

            <div class="mb-4">
              <label class="app-form-label" for="f-documentId">Document ID</label>
              <input type="text" class="form-control form-control-sm" id="f-documentId" name="documentId" value="{{ request()->get('documentId') }}"/>
            </div>

            <button class="btn btn-sm btn-primary">
              <i class="fa-regular fa-check me-1 fs-10"></i>
              Продължи
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script type="module">
    $(function () {
      $('#js-mail-panels .nav-link').click(function () {
        const $this = $(this);
        const $target = $($this.data('bs-target'));
        const $actionInput = $('#js-mail-panels input[name="action"]');
        $target.find('h3').html($this.text());
        $actionInput.val($this.data('action'));
      });

      const currentAction = @json(request()->get('action'));
      if (currentAction) {
        const $link = $(`#js-mail-panels .nav-link[data-action="${currentAction}"]`);
        $link.addClass('active').click();
        $($link.data('bs-target')).addClass('active show');
      }
    });
  </script>

  @if ($mails)
    <hr class="my-3"/>

    <ul class="nav nav-underline fs-9" id="myTab" role="tablist">
      @foreach ($mails as $key => $data)
        <li class="nav-item" role="presentation">
          <a class="nav-link @if ($key === 0) active @endif" data-bs-toggle="tab" href="#mail-tab-{{ $key }}" role="tab" tabindex="-1">{{ $data['to'] }}</a>
        </li>
      @endforeach
    </ul>
    <div class="tab-content mt-3" id="myTabContent">
      @foreach ($mails as $key => $data)
        <div class="tab-pane fade @if ($key === 0) active show @endif" id="mail-tab-{{ $key }}" role="tabpanel">
          <h4 class="h5 my-3">Тема</h4>
          <p>{{ $data['subject'] }}</p>

          <h4 class="h5 my-3">Език</h4>
          <p>{{ $data['lang'] }}</p>

          <h4 class="h5 my-3">Съдържание</h4>
          <iframe id="preview-frame-{{ $key }}" class="border border-dashed mb-5" style="width: 100%; height: 800px;"></iframe>
          <script type="module">
            $(function () {
              $('#preview-frame-{{ $key }}').prop('srcdoc', @json($data['content']));
            });
          </script>
        </div>
      @endforeach
    </div>
  @else
    <div class="mt-5">
      @include('shared.no-rs', [
        'noRsTitle' => 'Преглед на визията на имейла',
        'noRsSubTitle' => 'Попълнете формата към някой имейл и ще видите как ще изглежда.',
      ])
    </div>
  @endif
@endsection
