@if (\App\Services\MapService::orderStatus($order->status)->isCompleted)
  <div class="alert alert-subtle-info px-3 py-2" role="alert">
    Тази поръчка е приключена и е със статус:
    <strong style="color: {{ \App\Services\MapService::orderStatus($order->status)->color }}">
      {{ \App\Services\MapService::orderStatus($order->status)->labelBg }}
    </strong>
  </div>
@else
  <div class="row g-3">
    <div class="col-12 col-md-6 col-xl-4 col-xxl">
      <div class="card h-100">
        <div class="card-body">
          <div class="border-bottom-sm border-translucent d-flex flex-row">
            <p class="text-info mt-2 fs-8 fw-bold mb-3">
              1.
              <span class="text-body lh-lg">Нова поръчка</span>
            </p>
            <a class="ms-auto mt-2 dropdown-indicator-icon text-body" href="#order-flow-basic-{{ $order->id }}" role="button" data-bs-toggle="collapse">
              <i class="fa-regular fa-angle-up"></i>
            </a>
          </div>

          <div class="collapse bg-secondary-lighter p-3 mb-2" id="order-flow-basic-{{ $order->id }}">
            <table class="w-100">
              <tr>
                <td class="py-1 fw-semibold fs-9 mb-0 text-body-tertiary">
                  Статус
                </td>
                <td class="py-1 d-none d-sm-block pe-sm-2">:</td>
                <td class="py-1 ps-6 ps-sm-0 fw-semibold fs-9 mb-0 mb-0 pb-3 pb-sm-0 text-body-emphasis">
                  <span style="color: {{ \App\Services\MapService::orderStatus($order->status)->color }}">
                    {{ \App\Services\MapService::orderStatus($order->status)->labelBg }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="py-1 fw-semibold fs-9 mb-0 text-body-tertiary">
                  Дата на създаване
                </td>
                <td class="py-1 d-none d-sm-block pe-sm-2">:</td>
                <td class="py-1 ps-6 ps-sm-0 fw-semibold fs-9 mb-0 mb-0 pb-3 pb-sm-0 text-body-emphasis">
                  {{ $order->shopData->order->date_added ?? null }}
                </td>
              </tr>
            </table>
          </div>

          <ol class="list-group list-group-flush border-bottom border-translucent list-group-numbered">
            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Поръчката се обработва
                </span>
                <span>
                  <a href="{{ url('/erp/orders/update/' . $order->id . '?') }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Поръчката трябва да е имала статус '{{ \App\Services\MapService::orderStatus(\App\Enums\OrderStatus::Processing)->labelBg }}'">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::SetStatus, \App\Enums\OrderStatus::Processing->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>

            @if (!$order->customer || !$order->customer->creditLineValue)
              <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
                <div class="d-flex justify-content-between align-items-center w-100">
                  <span class="fw-semibold text-body-highlight mx-1">
                    Генерирана проформа фактура
                  </span>
                  <span>
                    <a href="{{ url('/erp/documents/prepare/' . \App\Enums\DocumentType::ProformaInvoice->value) . '?orderId=' . $order->id }}" class="hover-text-decoration-none" target="_blank">
                      <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Създайте проформа фактура към тази поръчка">
                        <i class="fa-regular fa-info"></i>
                      </span>
                    </a>

                    <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::AddDocument, \App\Enums\DocumentType::ProformaInvoice->value)) opacity-25 @endif">
                      <i class="fa-regular fa-check"></i>
                    </span>
                  </span>
                </div>
              </li>

              <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
                <div class="d-flex justify-content-between align-items-center w-100">
                  <span class="fw-semibold text-body-highlight mx-1">
                    Изпратете проформа фактурата
                  </span>
                  <span>
                    <a href="{{ url('/erp/documents?filter[orderId]=' . $order->id . '&filter[type]=' . \App\Enums\DocumentType::ProformaInvoice->value) }}&op[type]=eq" class="hover-text-decoration-none" target="_blank">
                      <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Уведомете клиента за създадената проформа фактура">
                        <i class="fa-regular fa-info"></i>
                      </span>
                    </a>

                    <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::SentMail, 'document', ['type' => \App\Enums\DocumentType::ProformaInvoice->value])) opacity-25 @endif">
                      <i class="fa-regular fa-check"></i>
                    </span>
                  </span>
                </div>
              </li>
            @endif

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Генерирана фактура
                </span>
                <span>
                  <a href="{{ url('/erp/documents/prepare/' . \App\Enums\DocumentType::Invoice->value) . '?orderId=' . $order->id }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Създайте фактура към тази поръчка">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::AddDocument, \App\Enums\DocumentType::Invoice->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Изпратете фактурата
                </span>
                <span>
                  <a href="{{ url('/erp/documents?filter[orderId]=' . $order->id . '&filter[type]=' . \App\Enums\DocumentType::Invoice->value) }}&op[type]=eq" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Уведомете клиента за създадената проформа фактура">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::SentMail, 'document', ['type' => \App\Enums\DocumentType::Invoice->value])) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>
          </ol>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 col-xxl">
      <div class="card h-100">
        <div class="card-body">
          <div class="border-bottom-sm border-translucent d-flex flex-row">
            <p class="text-info mt-2 fs-8 fw-bold mb-3">
              2.
              <span class="text-body lh-lg">Клиент</span>
            </p>
            <a class="ms-auto mt-2 dropdown-indicator-icon text-body" href="#order-flow-customer-{{ $order->id }}" role="button" data-bs-toggle="collapse">
              <i class="fa-regular fa-angle-up"></i>
            </a>
          </div>

          <div class="collapse bg-secondary-lighter p-3 mb-2" id="order-flow-customer-{{ $order->id }}">
            @if ($order->customer)
              <table class="w-100">
                <tr>
                  <td class="py-1 fw-semibold fs-9 mb-0 text-body-tertiary">
                    Клиент
                  </td>
                  <td class="py-1 d-none d-sm-block pe-sm-2">:</td>
                  <td class="py-1 ps-6 ps-sm-0 fw-semibold fs-9 mb-0 mb-0 pb-3 pb-sm-0 text-body-emphasis">
                    <a href="{{ url('/erp/customers/update/' . $order->customer->id) }}" target="_blank">
                      <i class="fa-regular fa-square-arrow-up-right"></i>
                      Виж
                    </a>
                  </td>
                </tr>
                <tr>
                  <td class="py-1 fw-semibold fs-9 mb-0 text-body-tertiary">
                    Фирма
                  </td>
                  <td class="py-1 d-none d-sm-block pe-sm-2">:</td>
                  <td class="py-1 ps-6 ps-sm-0 fw-semibold fs-9 mb-0 mb-0 pb-3 pb-sm-0 text-body-emphasis">
                    {{ $order->customer->companyName }}
                  </td>
                </tr>
                <tr>
                  <td class="py-1 fw-semibold fs-9 mb-0 text-body-tertiary">
                    ЕИК
                  </td>
                  <td class="py-1 d-none d-sm-block pe-sm-2">:</td>
                  <td class="py-1 ps-6 ps-sm-0 fw-semibold fs-9 mb-0 mb-0 pb-3 pb-sm-0 text-body-emphasis">
                    {{ $order->customer->companyId }}
                  </td>
                </tr>
              </table>
            @else
              <div class="text-center">
                <span class="badge badge-phoenix badge-phoenix-warning">
                  Поръчката няма свързан клиент!
                </span>
              </div>
            @endif
          </div>

          <ul class="list-group list-group-flush border-bottom border-translucent">
            @php ($unpaidStatuses = [])
            @php ($unpaidTitles = [])
            @foreach (\App\Enums\OrderStatus::cases() as $status)
              @if (\App\Services\MapService::orderStatus($status)->isPayable)
                @php ($unpaidStatuses[] = $status->value)
                @php ($unpaidTitles[] = \App\Services\MapService::orderStatus($status)->labelBg)
              @endif
            @endforeach

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Задължения по поръчки
                </span>

                <span>
                  <a href="{{ url('/erp/orders/?filter[status]=' . implode(',', $unpaidStatuses)) . '&op[status]=in&filter[customerId]=' . $order->customerId . '&op[customerId]=eq' }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Общата стойност на поръчки със статуси: {{ implode(', ', $unpaidTitles) }}">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix fs-10 badge-phoenix-info">
                    {{ price($order->customer->totalPayableOrdersAmount) }}
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Плащания по поръчки
                </span>
                <span>
                  <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Плащанията от клиента свързани с поръчките опоментаи по-горе">
                    <i class="fa-regular fa-info"></i>
                  </span>

                  <span class="badge badge-phoenix fs-10 badge-phoenix-info">
                    {{ price($order->customer->totalPayableOrdersIncomes) }}
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Разлика
                </span>
                @php($diff = $order->customer->totalPayableOrdersIncomes - $order->customer->totalPayableOrdersAmount)

                <span class="badge badge-phoenix fs-10 @if ($diff < 0) badge-phoenix-danger @else badge-phoenix-success @endif">
                  <span class="fw-bold">
                    @if ($diff > 0)
                      +
                    @endif</span>
                  {{ price($diff) }}
                </span>
              </div>
            </li>
          </ul>

          @if ($order->customer && $order->customer->creditLineValue)
            <div class="row g-2 fs-9 mt-3">
              <div class="col-4">
                <div class="h-100 bg-success-subtle rounded-4 text-center px-1 py-2">
                  <p class="fs-sm mb-0">Кредитна линия</p>
                  <strong>{{ price($order->customer->creditLineValue) }}</strong>
                </div>
              </div>
              <div class="col-4">
                <div class="h-100 bg-info-subtle rounded-4 text-center px-1 py-2">
                  <p class="fs-sm mb-0">Остатъчна сума</p>
                  <strong>{{ price($order->customer->creditLineUsed) }}</strong>
                </div>
              </div>
              <div class="col-4">
                <div class="h-100 bg-warning-subtle rounded-4 text-center px-1 py-2">
                  <p class="fs-sm mb-0">Използвана сума</p>
                  <strong>{{ price($order->customer->creditLineLeft) }}</strong>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 col-xxl">
      <div class="card h-100">
        <div class="card-body">
          <div class="border-bottom-sm border-translucent d-flex flex-row">
            <p class="text-info mt-2 fs-8 fw-bold mb-3">
              3.
              <span class="text-body lh-lg">Завършване</span>
            </p>
          </div>

          <ol class="list-group list-group-flush border-bottom border-translucent list-group-numbered">
            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <div class="fw-semibold text-body-highlight mx-1">
                  Изпращане на поръчката

                  <div class="btn-reveal-trigger d-inline-block">
                    <a class="badge badge-phoenix badge-phoenix-secondary text-secondary cursor-pointer" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                      <i class="fa-regular fa-ellipsis-h fs-10"></i>
                    </a>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ url('/erp/shipments/speedy/create?orderId=' . $order->id) }}">
                        Изпрати през DPD/Speedy
                      </a>
                    </div>
                  </div>
                </div>
                <span>
                  <a href="{{ url('/erp/orders/update/' . $order->id) }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Поръчката трябва да е имала статус '{{ \App\Services\MapService::orderStatus(\App\Enums\OrderStatus::Shipped)->labelBg }}'">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::SetStatus, \App\Enums\OrderStatus::Shipped->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Генериране на потвърждение
                </span>
                <span>
                  <a href="{{ url('/erp/documents/prepare/' . \App\Enums\DocumentType::OrderConfirmation->value) . '?orderId=' . $order->id }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Създайте потвърждение на поръчката">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::AddDocument, \App\Enums\DocumentType::OrderConfirmation->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Генерирана ППП
                </span>
                <span>
                  <a href="{{ url('/erp/documents/prepare/' . \App\Enums\DocumentType::DeliveryNote->value) . '?orderId=' . $order->id }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Създайте ППП към поръчката">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::AddDocument, \App\Enums\DocumentType::DeliveryNote->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>

            <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2 d-flex">
              <div class="d-flex justify-content-between align-items-center w-100">
                <span class="fw-semibold text-body-highlight mx-1">
                  Поръчката е приключена
                </span>
                <span>
                  <a href="{{ url('/erp/orders/update/' . $order->id) }}" class="hover-text-decoration-none" target="_blank">
                    <span class="badge badge-phoenix badge-phoenix-info text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Поръчката трябва да е имала статус '{{ \App\Services\MapService::orderStatus(\App\Enums\OrderStatus::Complete)->labelBg }}'">
                      <i class="fa-regular fa-info"></i>
                    </span>
                  </a>

                  <span class="badge badge-phoenix badge-phoenix-success text-success @if (!$order->hasEvent(\App\Enums\OrderEventAction::SetStatus, \App\Enums\OrderStatus::Complete->value)) opacity-25 @endif">
                    <i class="fa-regular fa-check"></i>
                  </span>
                </span>
              </div>
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>
@endif
