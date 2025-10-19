<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за офертата</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-offerNumber">Номер на оферта</label>
        <input type="text" class="form-control @if($errors->has('offerNumber')) is-invalid @endif" id="f-offerNumber" name="offerNumber" value="{{ $offer->offerNumber }}" placeholder="OFR-2025-00012..." required/>
        @if($errors->has('offerNumber'))
          <div class="invalid-feedback">
            {{ $errors->first('offerNumber') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-status">Статус</label>
        <select class="form-select @if($errors->has('status')) is-invalid @endif" id="f-status" name="status">
          @foreach (App\Enums\OfferStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ $offer->status && $offer->status->value == $status->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::offerStatuses($status)->label }}
            </option>
          @endforeach
        </select>
        @if($errors->has('status'))
          <div class="invalid-feedback">
            {{ $errors->first('status') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-validUntil">Валидна до</label>
        <input type="date" class="form-control @if($errors->has('validUntil')) is-invalid @endif" id="f-validUntil" name="validUntil" value="{{ $offer->validUntil }}" placeholder="2020-01-15..."/>
        @if($errors->has('validUntil'))
          <div class="invalid-feedback">
            {{ $errors->first('validUntil') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-validUntil');
        </script>
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Фирма - получател</h2>
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-customerId">Клиент</label>
        <select class="form-select @if($errors->has('customerId')) is-invalid @endif" id="f-customerId" name="customerId">
          <option value="">-</option>
          @foreach ($customers as $row)
            <option value="{{ $row->id }}" {{ $offer->customerId == $row->id ? 'selected' : '' }} data-client='@json($row)'>
              {{ $row->companyName }}
              / {{ $row->companyId }}
              / {{ $row->firstName }} {{ $row->lastName }}
            </option>
          @endforeach
        </select>
        @if($errors->has('customerId'))
          <div class="invalid-feedback">
            {{ $errors->first('customerId') }}
          </div>
        @endif

        <script type="module">
          window.showCustomerOrders = function () {
            const customerId = $('#f-customerId').val();

            $('#f-orderId option').each(function () {
              const $this = $(this);
              if ($this.data('customer-id') && parseInt($this.data('customer-id')) !== parseInt(customerId)) {
                $this.hide();

                // Reset if the selected value is hidden
                if ($this.is(':selected')) {
                  $('#f-orderId').val('');
                }
              } else {
                $this.show();
              }
            });
          };

          $(function () {
            $('#f-customerId')
              .select2()
              .on('change', function (e) {
                const customerData = $(e.currentTarget).find('option:selected').data('client');

                $('#f-companyId').val(customerData.companyId);
                $('#f-companyName').val(customerData.companyName);
                $('#f-companyPerson').val(`${customerData.firstName} ${customerData.lastName}`);
                $('#f-companyEmail').val(customerData.email);
                $('#f-companyPhone').val(customerData.contactPhone);
                $('#f-companyAddress').val(`${customerData.companyCity}, ${customerData.companyAddress}`);
              });
          });
        </script>
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyId">ЕИК</label>
        <input type="text" class="form-control @if($errors->has('companyId')) is-invalid @endif" id="f-companyId" name="companyId" value="{{ $offer->companyId }}" placeholder="202303404..."/>
        @if($errors->has('companyId'))
          <div class="invalid-feedback">
            {{ $errors->first('companyId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyName">Име на фирмата</label>
        <input type="text" class="form-control @if($errors->has('companyName')) is-invalid @endif" id="f-companyName" name="companyName" value="{{ $offer->companyName }}" placeholder="Фирма ЕООД..."/>
        @if($errors->has('companyName'))
          <div class="invalid-feedback">
            {{ $errors->first('companyName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyPerson">Адресирано до (Име)</label>
        <input type="text" class="form-control @if($errors->has('companyPerson')) is-invalid @endif" id="f-companyPerson" name="companyPerson" value="{{ $offer->companyPerson }}" placeholder="Петър Петров..."/>
        @if($errors->has('companyPerson'))
          <div class="invalid-feedback">
            {{ $errors->first('companyPerson') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyEmail">Имейл</label>
        <input type="text" class="form-control @if($errors->has('companyEmail')) is-invalid @endif" id="f-companyEmail" name="companyEmail" value="{{ $offer->companyEmail }}" placeholder="email@company.com..."/>
        @if($errors->has('companyEmail'))
          <div class="invalid-feedback">
            {{ $errors->first('companyEmail') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyPhone">Телефон</label>
        <input type="text" class="form-control @if($errors->has('companyPhone')) is-invalid @endif" id="f-companyPhone" name="companyPhone" value="{{ $offer->companyPhone }}" placeholder="+359 888 123 123..."/>
        @if($errors->has('companyPhone'))
          <div class="invalid-feedback">
            {{ $errors->first('companyPhone') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyAddress">Адрес</label>
        <input type="text" class="form-control @if($errors->has('companyAddress')) is-invalid @endif" id="f-companyAddress" name="companyAddress" value="{{ $offer->companyAddress }}" placeholder="София, Александър Стамболийски 62..."/>
        @if($errors->has('companyAddress'))
          <div class="invalid-feedback">
            {{ $errors->first('companyAddress') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Бележки</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPrivate">Вътрешна бележка</label>
        <textarea type="text" class="form-control @if($errors->has('notesPrivate')) is-invalid @endif" id="f-notesPrivate" name="notesPrivate" rows="4" placeholder="...">{{ $offer->notesPrivate }}</textarea>
        @if($errors->has('notesPrivate'))
          <div class="invalid-feedback">
            {{ $errors->first('notesPrivate') }}
          </div>
        @endif
        <p class="text-body-tertiary fs-9 fw-semibold mb-0 mt-1">Този текст ще бъде достъпен само от операторите на системата.</p>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPublic">Бележка към клиента</label>
        <textarea type="text" class="form-control @if($errors->has('notesPublic')) is-invalid @endif" id="f-notesPublic" name="notesPublic" rows="4" placeholder="...">{{ $offer->notesPublic }}</textarea>
        @if($errors->has('notesPublic'))
          <div class="invalid-feedback">
            {{ $errors->first('notesPublic') }}
          </div>
        @endif
        <p class="text-body-tertiary fs-9 fw-semibold mb-0 mt-1">Този текст ще бъде видим от клиента.</p>
      </div>

      <div class="col-12">
        @if($errors->has('items'))
          <div class="alert alert-phoenix-danger fs-9 m-4 mt-0 p-3">
            {{ $errors->first('items') }}
          </div>
        @endif

        <div id="js-items"></div>

        <div class="row">
          <div class="col-12 col-xl-6">
            <h5 class="h6 fs-9">Ръчно добавяне на ред</h5>
            <button type="button" class="btn btn-sm btn-phoenix-info text-primary w-100 mb-4" onclick="erpOffer.addItem();">
              <i class="fa-regular fa-plus"></i>
              Добави нов ред
            </button>
          </div>

          <div class="col-12 col-xl-6">
            <h5 class="h6 fs-9">Добавяне на продукт</h5>
            <div>
              <select class="form-select" id="erp-form-products"></select>
              <script type="module">
                $('#erp-form-products').select2({
                  placeholder: 'Изберете продукт, който ще добавите...',
                  minimumInputLength: 1,
                  ajax: {
                    url: "{{ url('/erp/products/') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                      return {
                        filter: {
                          q: params.term
                        },
                        page: params.page
                      };
                    },
                    processResults: (rs) => {
                      return {
                        results: rs.products.data
                      };
                    },
                    cache: true
                  },
                  templateSelection: (item) => (item.text || [item.mpn, item.nameBg].filter(Boolean).join(' | ')),
                  templateResult: (item) => {
                    if (item.loading) {
                      return item.text;
                    }

                    const preview = item?.uploads?.[0]?.urls?.tiny;

                    return $(
                      `<div class="d-flex">
                        <div style="width: 50px; height: 50px;">
                          ${preview ? `<img src="${preview}" style="height: 50px; width: 50px; object-fit: cover;"  alt=""/>` : ''}
                        </div>
                        <div class="d-flex align-items-center ps-2">
                          ${[item.mpn, item.ean, item.nameBg].filter(Boolean).join(' | ')}
                        </div>
                      </div>`
                    );
                  }
                }).on('select2:select', function (e) {
                  const itemData = e.params.data;

                  erpOffer.addItem({
                    id: null,
                    productId: itemData.id,
                    name: itemData.nameBg,
                    mpn: itemData.mpn,
                    ean: itemData.ean,
                    price: itemData.price,
                    quantity: 1,
                    discountPercent: 0,
                    totalPrice: itemData.price,
                    error: null,
                  });
                });
              </script>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="module">
  class ErpOffer {
    itemIdx = 0;
    itemsData = {};

    refillData(itemIdx) {
      this.itemsData[itemIdx] = {
        id: parseFloat($(`#erp-form-id-${itemIdx}`).val()) ?? null,
        productId: parseFloat($(`#erp-form-productId-${itemIdx}`).val()) ?? null,
        name: $(`#erp-form-name-${itemIdx}`).val() ?? null,
        mpn: $(`#erp-form-mpn-${itemIdx}`).val() ?? null,
        ean: $(`#erp-form-ean-${itemIdx}`).val() ?? null,
        price: parseFloat($(`#erp-form-price-${itemIdx}`).val()) ?? null,
        quantity: parseFloat($(`#erp-form-quantity-${itemIdx}`).val()) ?? null,
        discountPercent: parseFloat($(`#erp-form-discountPercent-${itemIdx}`).val()) ?? null,
        totalPrice: parseFloat($(`#erp-form-totalPrice-${itemIdx}`).val()) ?? null,
        error: parseFloat($(`#erp-form-error-${itemIdx}`).val()) ?? null,
      };
    }

    removeItem(itemIdx) {
      if (!confirm('Сигурни ли сте, че искате да премахнете този артикул?')) {
        return;
      }

      $(`#item-${itemIdx}`).fadeOut(() => {
        $(`#item-${itemIdx}`).remove();

        if (this.itemsData[itemIdx]) {
          delete this.itemsData[itemIdx];
        }
      });
    }

    duplicate(itemIdx) {
      this.addItem({
        ...this.itemsData[itemIdx],
        id: null,
        error: null,
      });
    }

    addItem(item) {
      const itemIdx = this.itemIdx++;

      if (typeof item !== 'object') {
        item = {};
      }

      item = {
        id: item.id ?? null,
        productId: item.productId ?? null,
        name: item.name ?? null,
        mpn: item.mpn ?? null,
        ean: item.ean ?? null,
        price: item.price ? parseFloat(item.price) : null,
        quantity: item.quantity ? parseFloat(item.quantity) : null,
        discountPercent: item.discountPercent ? parseFloat(item.discountPercent) : null,
        totalPrice: item.totalPrice ? parseFloat(item.totalPrice) : null,
        error: item.error ?? null,
        attribute: item.attribute ?? '',
      };

      this.itemsData[itemIdx] = item;

      const $html = $(
        `<div class="row g-2 border-1 border-bottom border-dashed pb-4 mb-3 position-relative" id="item-${itemIdx}" ${item.attribute}>
          <input type="hidden" name="items[${itemIdx}][id]" id="erp-form-id-${itemIdx}" value="${item.id ?? ''}" />
          <input type="hidden" id="erp-form-productId-${itemIdx}" name="items[${itemIdx}][productId]" value="${item.productId ?? ''}" />

          <div class="col-auto align-content-end">
            <div class="btn-reveal-trigger position-absolute top-0 end-0">
              <button type="button" class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                <i class="fa-regular fa-ellipsis-h fs-10"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end py-2">
                <button type="button" class="dropdown-item" onclick="erpOffer.duplicate(${itemIdx})">Дублирай</button>
                <div class="dropdown-divider"></div>
                <button type="button" class="dropdown-item text-danger" onclick="erpOffer.removeItem(${itemIdx})">Изтрий</button>
              </div>
            </div>
          </div>

          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-name-${itemIdx}">Описание</label>
            <input type="text" class="form-control" id="erp-form-name-${itemIdx}" name="items[${itemIdx}][name]" value="${item.name ?? ''}" placeholder="Продукт..."/>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-mpn-${itemIdx}">MPN</label>
            <input type="text" class="form-control" id="erp-form-mpn-${itemIdx}" name="items[${itemIdx}][mpn]" value="${item.mpn ?? ''}" placeholder="mpn..."/>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-ean-${itemIdx}">EAN</label>
            <input type="text" class="form-control" id="erp-form-ean-${itemIdx}" name="items[${itemIdx}][ean]" value="${item.ean ?? ''}" placeholder="ean..."/>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-price-${itemIdx}">Единична цена</label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control" id="erp-form-price-${itemIdx}" name="items[${itemIdx}][price]" value="${item.price ?? 0}" placeholder="80..."/>
            </div>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-quantity-${itemIdx}">Количество</label>
            <input type="number" step="1" min="1" class="form-control" id="erp-form-quantity-${itemIdx}" name="items[${itemIdx}][quantity]" value="${item.quantity ?? 1}" placeholder="2..."/>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-discountPercent-${itemIdx}">Отстъпка %</label>
            <input type="number" step="0.01" class="form-control" id="erp-form-discountPercent-${itemIdx}" name="items[${itemIdx}][discountPercent]" value="${item.discountPercent ?? 0}" placeholder="0"/>
          </div>
          <div class="col-12 col-xxl align-content-end">
            <label class="form-label" for="erp-form-totalPrice-${itemIdx}">Обща сума</label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control" id="erp-form-totalPrice-${itemIdx}" name="items[${itemIdx}][totalPrice]" value="${item.totalPrice ?? 0}" placeholder="160..." readonly/>
            </div>
          </div>

          ${item.error ? `<div class="text-danger fw-bold fs-9 mt-1">${item.error}</div>` : ''}
        </div>`
      );

      $html.hide().appendTo('#js-items').fadeIn();

      const that = this;
      $html.find('input,select,textarea').each(function () {
        const $input = $(this);
        $input.change(that.refillData.bind(that, itemIdx));
      });

      const $price = $html.find(`#erp-form-price-${itemIdx}`);
      const $quantity = $html.find(`#erp-form-quantity-${itemIdx}`);
      const $discount = $html.find(`#erp-form-discountPercent-${itemIdx}`);
      const $totalPrice = $html.find(`#erp-form-totalPrice-${itemIdx}`);

      const calc = () => {
        const price = parseFloat($price.val()) || 0;
        const qty = parseFloat($quantity.val()) || 0;
        const disc = parseFloat($discount.val()) || 0;
        const total = price * qty * (1 - disc / 100);
        $totalPrice.val(total.toFixed(2));
      };

      $price.on('change keyup', calc);
      $quantity.on('change keyup', calc);
      $discount.on('change keyup', calc);

      calc();
    }
  }

  const erpOffer = new ErpOffer();
  window.erpOffer = erpOffer;

  const errors = @json($errors);
  const items = @json($items);
  items.forEach((item, itemIdx) => {
    item.error = '';
    for (const [key, value] of Object.entries(errors)) {
      if (key.startsWith(`items.${itemIdx}.`)) {
        item.error += value.join('; ') + '; ';
      }
    }

    erpOffer.addItem(item);
  });
</script>
