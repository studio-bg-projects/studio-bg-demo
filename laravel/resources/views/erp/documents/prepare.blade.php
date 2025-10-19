@extends('layouts.app')

@section('content')
  @include('erp.documents.partials.navbar')

  <h1 class="h4 mb-5">Добавяне на документ - {{ \App\Services\MapService::documentTypes($type)->labelBg }}</h1>

  <div class="card">
    <div class="card-body">
      <div class="row gy-2">
        <div class="col-12">
          {{-- no .mt-3 for 1st title --}}
          <h2 class="h5 pb-2 border-bottom border-dashed">Избор на свързан документ</h2>
        </div>

        <div class="col-12 col-xl-4">
          <label class="app-form-label" for="f-customerId">Клиент</label>
          <select class="form-select" id="f-customerId">
            <option value="">-</option>
            @foreach ($customers as $row)
              <option value="{{ $row->id }}" {{ $customerId == $row->id ? 'selected' : '' }}>
                {{ $row->companyName }} / {{ $row->companyId }} / {{ $row->firstName }} {{ $row->lastName }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-xl-4">
          <label class="app-form-label @if($relatedRequired) required @endif" for="f-relatedId">Свързан документ</label>
          <select class="form-select" id="f-relatedId" @if($relatedRequired) required @endif>
            <option value="">-</option>
            @foreach ($relatedDocuments as $row)
              <option value="{{ $row->id }}" data-customer-id="{{ $row->customerId }}" data-order-id="{{ $row->orderId }}" {{ $relatedId == $row->id ? 'selected' : '' }}>
                {{ $row->documentNumber }} / {{ $row->type->value }} / {{ $row->recipientName }} / {{ $row->totalAmount }}{{ dbConfig('currency:symbol') }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-xl-4">
          <label class="app-form-label" for="f-orderId">Поръчка</label>
          <select class="form-select" id="f-orderId">
            <option value="">-</option>
            @foreach ($orders as $row)
              <option value="{{ $row->id }}" data-customer-id="{{ $row->customerId }}" {{ $orderId == $row->id ? 'selected' : '' }}>
                #{{ $row->id }} / {{ price($row->shopData->order->total ?? 0) }} / {{ count($row->shopData->order_product ?? []) }} продукт(а)
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12">
          <div class="text-end">
            <button class="btn btn-primary btn-lg mt-3" id="btn-continue" type="button">Продължи</button>
          </div>
        </div>

        <script type="module">
          function filterOptions($select, allOptions, changeHandler) {
            const customerId = $('#f-customerId').val();
            const documentOrderId = $('#f-relatedId').find(':selected').data('order-id');

            const isOrderFields = $select.attr('id') === 'f-orderId';
            const value = $select.val();

            $select.select2('destroy');
            $select.empty().append(
              allOptions
                .filter(function () {
                  const subCustomerId = $(this).data('customer-id');
                  const val = $(this).val();

                  if (isOrderFields && documentOrderId) {
                    if (val && parseInt(val) !== parseInt(documentOrderId)) {
                      return false;
                    }
                  }

                  if (!customerId) {
                    return true;
                  } else {
                    return !val || parseInt(subCustomerId) === parseInt(customerId);
                  }
                })
                .clone()
            );
            $select.select2();
            if (changeHandler) {
              $select.on('change', changeHandler);
            }
            if ($select.find('option[value="' + value + '"]').length) {
              $select.val(value).trigger('change');
            } else {
              $select.val('').trigger('change');
            }
          }

          function updateButton() {
            if ({{ $relatedRequired ? 'true' : 'false' }} && !$('#f-relatedId').val()) {
              $('#btn-continue').prop('disabled', true);
            } else {
              $('#btn-continue').prop('disabled', false);
            }
          }

          $(function () {
            const $customer = $('#f-customerId').select2();
            const $related = $('#f-relatedId').select2();
            const $order = $('#f-orderId').select2();

            const relatedOptions = $related.find('option').clone();
            const orderOptions = $order.find('option').clone();

            $customer.on('change', function () {
              filterOptions($related, relatedOptions, updateButton);
              filterOptions($order, orderOptions);
              updateButton();
            });

            $related.on('change', function () {
              filterOptions($order, orderOptions);
            });

            filterOptions($related, relatedOptions, updateButton);
            filterOptions($order, orderOptions);
            updateButton();

            $('#btn-continue').on('click', function () {
              const refDocumentId = $('#f-relatedId').val() || 0;
              let url = '{{ url('/erp/documents/create/' . $type->value) }}/' + refDocumentId;
              const params = new URLSearchParams();
              const customerId = $('#f-customerId').val();
              const orderId = $('#f-orderId').val();
              if (customerId) {
                params.set('customerId', customerId);
              }
              if (orderId) {
                params.set('orderId', orderId);
              }
              if (params.toString()) {
                url += '?' + params.toString();
              }
              window.location.href = url;
            });
          });
        </script>
      </div>
    </div>
  </div>
@endsection
