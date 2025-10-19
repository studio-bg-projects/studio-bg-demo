@php
  $shopData = $order->shopData ?? (object)[];
  $shopOrder = (object) data_get($shopData, 'order', []);
  $shippingAddress = (object) data_get($shopData, 'shipping_address', []);
  $customerModel = $customer ?? $order->customer ?? null;
  $customerAddressModel = $customerAddress ?? null;
  $orderTotalsRaw = data_get($shopData, 'order_total', []);
  $orderTotals = [];

  if (is_iterable($orderTotalsRaw)) {
    foreach ($orderTotalsRaw as $orderTotalRow) {
      if (is_array($orderTotalRow)) {
        $orderTotalRow = (object)$orderTotalRow;
      }

      if (!is_object($orderTotalRow)) {
        continue;
      }

      $code = $orderTotalRow->code ?? null;
      if ($code) {
        $orderTotals[$code] = $orderTotalRow;
      }
    }
  }

  $orderTotalSubTotalValue = $orderTotals['sub_total']->value ?? '';
  $orderTotalShippingValue = $orderTotals['shipping']->value ?? '';
  $orderTotalTotalValue = $orderTotals['total']->value ?? '';

  $customerSummaryParts = [];
  if ($customerModel) {
    if (!empty($customerModel->companyName)) {
      $customerSummaryParts[] = $customerModel->companyName;
    }
    if (!empty($customerModel->companyId)) {
      $customerSummaryParts[] = $customerModel->companyId;
    }

    $customerName = trim(($customerModel->firstName ?? '') . ' ' . ($customerModel->lastName ?? ''));
    if ($customerName !== '') {
      $customerSummaryParts[] = $customerName;
    }
  }

  $customerSummary = $customerSummaryParts ? implode(' / ', $customerSummaryParts) : '-';
  $orderProducts = $orderProducts ?? [];
  $orderProductErrors = [];
  foreach ($errors->getMessages() as $key => $messages) {
    if (str_starts_with($key, 'order_products.')) {
      if (preg_match('/^order_products\.(\d+)\.(.+)$/', $key, $matches)) {
        $orderProductErrors[$matches[1]][$matches[2]] = $messages[0];
      }
    }
  }
  $currencySymbol = dbConfig('currency:symbol');
@endphp

@if ($customerModel)
  <input type="hidden" name="customerId" value="{{ $customerModel->id }}"/>
@endif

@if ($customerAddressModel)
  <input type="hidden" name="customerAddressId" value="{{ $customerAddressModel->id }}"/>
@endif

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за поръчката</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-status">Статус</label>
        <select class="form-select @if($errors->has('status')) is-invalid @endif" id="f-status" name="status" required>
          @foreach (\App\Enums\OrderStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ $order->status && $order->status->value === $status->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::orderStatus($status)->labelBg }}
            </option>
          @endforeach
        </select>
        @if($errors->has('status'))
          <div class="invalid-feedback">
            {{ $errors->first('status') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="form-label" for="f-sendOrderMail">Уведоми клиента за статуса на поръчката</label>
        <select class="form-select @if($errors->has('sendOrderMail')) is-invalid @endif" id="f-sendOrderMail" name="sendOrderMail">
          <option value="0" @if (!request()->sendOrderMail) selected @endif>Не изпращай мейл</option>
          <option value="1" @if (request()->sendOrderMail) selected @endif>Изпрати емейл с информация за поръчката</option>
        </select>
        @if($errors->has('sendOrderMail'))
          <div class="invalid-feedback">
            {{ $errors->first('sendOrderMail') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за клиента</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderId">Номер на поръчка</label>
        <input type="text" class="form-control" id="f-orderId" value="{{ $shopOrder->order_id ?? $order->id ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-customerSummary">Клиент</label>
        <input type="text" class="form-control" id="f-customerSummary" value="{{ $customerSummary }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderEmail">Имейл</label>
        <input type="email" class="form-control" id="f-orderEmail" value="{{ $shopOrder->email ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderTelephone">Телефон</label>
        <input type="text" class="form-control" id="f-orderTelephone" value="{{ $shopOrder->telephone ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderFirstname">Име</label>
        <input type="text" class="form-control" id="f-orderFirstname" value="{{ $shopOrder->firstname ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderLastname">Фамилия</label>
        <input type="text" class="form-control" id="f-orderLastname" value="{{ $shopOrder->lastname ?? '-' }}" disabled/>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Адрес за доставка</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingCountry">Държава</label>
        <input type="text" class="form-control" id="f-shippingCountry" value="{{ $shopOrder->shipping_country ?? $shippingAddress->country ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingCity">Град</label>
        <input type="text" class="form-control" id="f-shippingCity" value="{{ $shopOrder->shipping_city ?? $shippingAddress->city ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingPostcode">Пощенски код</label>
        <input type="text" class="form-control" id="f-shippingPostcode" value="{{ $shopOrder->shipping_postcode ?? $shippingAddress->postcode ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingFirstname">Име на получател</label>
        <input type="text" class="form-control" id="f-shippingFirstname" value="{{ $shopOrder->shipping_firstname ?? $shippingAddress->firstname ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingLastname">Фамилия на получател</label>
        <input type="text" class="form-control" id="f-shippingLastname" value="{{ $shopOrder->shipping_lastname ?? $shippingAddress->lastname ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-shippingAddress1">Адрес</label>
        <input type="text" class="form-control" id="f-shippingAddress1" value="{{ $shopOrder->shipping_address_1 ?? $shippingAddress->address_1 ?? '-' }}" disabled/>
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-shippingAddress2">Допълнителни указания</label>
        <input type="text" class="form-control" id="f-shippingAddress2" value="{{ $shopOrder->shipping_address_2 ?? $shippingAddress->address_2 ?? '-' }}" disabled/>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Продукти</h2>
      </div>

      <div class="col-12">
        @if($errors->has('order_products'))
          <div class="alert alert-outline-danger my-2">{{ $errors->first('order_products') }}</div>
        @endif

        <div id="js-orderProductsList"></div>
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-orderProductPicker">Добавяне на продукт</label>
        <select class="form-select" id="f-orderProductPicker" data-role="product-picker"></select>
      </div>

      @if ($customer->group && $customer->group->discountPercent > 0)
        <div class="col-12">
          <div class="alert alert-outline-info mt-2">
            Клиентът е в група '{{ $customer->group->nameBg }}' и ползва {{ $customer->group->discountPercent }}% отстъпка. При добавяне на продукт тя се начислява автоматично към цената му.
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Доставка</h2>
      </div>

      <div class="col-12">
        @include('erp.shipments.speedy.partials.calculator')

        {{-- <span class="badge bg-warning-subtle text-warning" id="js-calcShippingBadge">Нужно е преизчисление</span>--}}

        <script type="module">
          window.calculateShippingGetProducts = function () {
            const products = [];
            $('[data-order-product-row]').each(function () {
              const $row = $(this);
              const id = $row.find('input[name*="[product_id]"]').val();
              const quantity = $row.find('input[name*="[quantity]"]').val();

              products.push({
                id,
                quantity,
              });
            });

            return products;
          }

          window.calculateShippingGetAddress = function () {
            return {
              citySpeedyId: '{{ $shopOrder->city_speedy_id ?? $shippingAddress->city_speedy_id ?? $customerAddress->citySpeedyId ?? null }}',
              streetSpeedyId: '{{ $shopOrder->street_speedy_id ?? $shippingAddress->street_speedy_id ?? $customerAddress->streetSpeedyId ?? null }}',
              // officeSpeedyId: null, // @todo
            };
          }

          window.calculateShippingHandleResponse = function (rs) {
            let total = rs.calculation?.calculations?.[0]?.price?.total ?? 0;
            let currency = rs.calculation?.calculations?.[0]?.price?.currency;

            if (currency === 'BGN') {
              total /= 1.95583;
            }

            $('#f-orderTotalShipping').val(total.toFixed(2));
          }
        </script>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Стойности</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderTotalSubTotal">Стойност</label>
        <div class="input-group">
          <input type="text" class="form-control @if($errors->has('order_total_sub_total')) is-invalid @endif" id="f-orderTotalSubTotal" name="order_total_sub_total" value="{{ $orderTotalSubTotalValue }}" readonly/>
          @if($errors->has('order_total_sub_total'))
            <div class="invalid-feedback">
              {{ $errors->first('order_total_sub_total') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderTotalShipping">Доставка</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="text" class="form-control @if($errors->has('order_total_shipping')) is-invalid @endif" id="f-orderTotalShipping" name="order_total_shipping" value="{{ $orderTotalShippingValue }}"/>
          @if($errors->has('order_total_shipping'))
            <div class="invalid-feedback">
              {{ $errors->first('order_total_shipping') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-orderTotalTotal">Общо</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="text" class="form-control @if($errors->has('order_total_total')) is-invalid @endif" id="f-orderTotalTotal" name="order_total_total" value="{{ $orderTotalTotalValue }}" readonly/>
          @if($errors->has('order_total_total'))
            <div class="invalid-feedback">
              {{ $errors->first('order_total_total') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Коментар към поръчката</h2>
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-orderComment">Коментар</label>
        <textarea class="form-control @if($errors->has('comment')) is-invalid @endif" id="f-orderComment" name="comment" rows="4" placeholder="Добавете важна информация за обработката...">{{ $shopOrder->comment ?? '' }}</textarea>
        @if($errors->has('comment'))
          <div class="invalid-feedback">
            {{ $errors->first('comment') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<script type="module">
  $(function () {
    class OrderProductsForm {
      constructor(options) {
        this.currencySymbol = options.currencySymbol ?? '';
        this.placeholderImage = options.placeholderImage ?? '';
        this.container = $('#js-orderProductsList');
        this.nextKey = 0;

        const existingRows = Array.isArray(options.existingRows) ? options.existingRows : [];
        existingRows.forEach(row => {
          const numericKey = parseInt(row?.formKey ?? row?.form_key ?? '', 10);
          if (!Number.isNaN(numericKey) && numericKey >= this.nextKey) {
            this.nextKey = numericKey + 1;
          }
        });
      }

      parseNumber(value) {
        if (value === null || value === undefined) {
          return null;
        }

        const normalized = String(value).replace(',', '.');
        const parsed = Number(normalized);

        return Number.isFinite(parsed) ? parsed : null;
      }

      formatNumber(value) {
        const numeric = Number(value ?? 0);

        if (!Number.isFinite(numeric)) {
          return '0.00';
        }

        return numeric.toFixed(2);
      }

      addRow(data = {}, errors = {}, options = {}) {
        const providedKey = data.formKey ?? data.form_key;
        const formKey = providedKey !== undefined && providedKey !== null && providedKey !== '' ? String(providedKey) : String(this.nextKey++);

        const numericKey = parseInt(formKey, 10);
        if (!Number.isNaN(numericKey) && numericKey >= this.nextKey) {
          this.nextKey = numericKey + 1;
        }

        const animate = options.animate !== false;

        const name = data.name ?? '';
        const sku = data.sku ?? '';
        const ean = data.ean ?? '';
        const quantityValue = data.quantity ?? '';
        const priceValue = data.price ?? '';
        const totalValue = data.total ?? '';
        const productIdValue = data.productId ?? '';
        const orderProductIdValue = data.orderProductId ?? data.order_product_id ?? '';
        const imageValue = data.image ?? '';
        const weightValue = data.weight ?? '';
        const widthValue = data.width ?? '';
        const heightValue = data.height ?? '';
        const lengthValue = data.length ?? '';
        const maxQuantityRaw = data.maxQuantity ?? null;
        const numericMax = Number(maxQuantityRaw);
        const hasNumericMax = Number.isFinite(numericMax);
        const isOutOfStock = Boolean(data.isOutOfStock) || (hasNumericMax && numericMax <= 0);
        const previewUrl = imageValue || this.placeholderImage || '';

        const quantityError = errors.quantity ?? null;
        const priceError = errors.price ?? null;
        const productError = errors.product_id ?? null;

        const infoParts = [];

        if (sku) {
          infoParts.push(`SKU: ${sku}`);
        }

        if (ean) {
          infoParts.push(`EAN: ${ean}`);
        }

        const availabilityParts = [];

        if (hasNumericMax) {
          availabilityParts.push(`<span class="text-body-secondary fs-9">Наличност: ${numericMax}</span>`);
        }

        if (isOutOfStock) {
          availabilityParts.unshift('<span class="badge bg-danger-subtle text-danger">Изчерпан продукт</span>');
        }

        const availabilityHtml = availabilityParts.length ? `<div class="mt-1 d-flex flex-wrap gap-2 align-items-center">${availabilityParts.join('')}</div>` : '';
        const productErrorHtml = productError ? `<div class="text-danger fs-9 mt-1">${productError}</div>` : '';

        const $row = $(`
          <div class="border border-dashed rounded-3 p-3 mb-3" data-order-product-row data-key="${formKey}">
            <div class="d-flex gap-3">
              <div class="flex-shrink-0">
                <div class="rounded-2 overflow-hidden bg-body-tertiary" style="width:72px;height:72px;">
                  <img src="${previewUrl}" alt="" class="w-100 h-100" style="object-fit: cover;"/>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">
                        ${name || 'Неизвестен продукт'}
                    </div>

                    ${infoParts.length ? `<div class="text-body-secondary fs-9">${infoParts.join(' | ')}</div>` : ''}
                    ${productErrorHtml}
                    ${availabilityHtml}
                  </div>
                  <div>
                    <ul class="list-unstyled fs-9">
                      <li>Тегло с опаковка: <strong>${weightValue ? `${weightValue} кг.` : '<span class="text-danger">Липсва</span>'}</strong></li>
                      <li>Широчина: <strong>${widthValue ? `${widthValue} см.` : '<span class="text-danger">Липсва</span>'}</strong></li>
                      <li>Височина: <strong>${heightValue ? `${heightValue} см.` : '<span class="text-danger">Липсва</span>'}</strong></li>
                      <li>Дълбочина: <strong>${lengthValue ? `${lengthValue} см.` : '<span class="text-danger">Липсва</span>'}</strong></li>
                    </ul>
                  </div>
                  <div class="text-md-end">
                    <button type="button" class="btn btn-sm btn-phoenix-danger" data-action="remove-order-product" data-key="${formKey}">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>
                <div class="row g-3 mt-1 align-items-end">
                  <div class="col-12 col-md-3 col-lg-2">
                    <label class="app-form-label" for="f-orderProducts-${formKey}-quantity">Количество</label>
                    <input type="number" min="1" step="1" class="form-control form-control-sm js-order-product-quantity${quantityError ? ' is-invalid' : ''}" id="f-orderProducts-${formKey}-quantity" name="order_products[${formKey}][quantity]" value="${quantityValue}"/>
                    ${quantityError ? `<div class="invalid-feedback d-block">${quantityError}</div>` : ''}
                  </div>
                  <div class="col-12 col-md-3 col-lg-2">
                    <label class="app-form-label" for="f-orderProducts-${formKey}-price">Единична цена</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">${this.currencySymbol}</span>
                      <input type="number" min="0" step="0.01" class="form-control js-order-product-price${priceError ? ' is-invalid' : ''}" id="f-orderProducts-${formKey}-price" name="order_products[${formKey}][price]" value="${priceValue}"/>
                    </div>
                    ${priceError ? `<div class="invalid-feedback d-block">${priceError}</div>` : ''}
                  </div>
                  <div class="col-12 col-md-3 col-lg-2">
                    <label class="app-form-label" for="f-orderProducts-${formKey}-total">Обща сума</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">${this.currencySymbol}</span>
                      <input type="text" class="form-control js-order-product-total" id="f-orderProducts-${formKey}-total" value="${totalValue}" readonly/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" name="order_products[${formKey}][product_id]" value="${productIdValue}"/>
            <input type="hidden" name="order_products[${formKey}][order_product_id]" value="${orderProductIdValue}"/>
            <input type="hidden" name="order_products[${formKey}][name]" value="${name}"/>
            <input type="hidden" name="order_products[${formKey}][sku]" value="${sku}"/>
            <input type="hidden" name="order_products[${formKey}][ean]" value="${ean}"/>
            <input type="hidden" name="order_products[${formKey}][image]" value="${imageValue}"/>
            <input type="hidden" name="order_products[${formKey}][weight]" value="${weightValue}"/>
            <input type="hidden" name="order_products[${formKey}][width]" value="${widthValue}"/>
            <input type="hidden" name="order_products[${formKey}][height]" value="${heightValue}"/>
            <input type="hidden" name="order_products[${formKey}][length]" value="${lengthValue}"/>
          </div>
        `);

        if (animate) {
          $row.hide();
        }

        this.container.append($row);
        this.updateRowTotal($row);
        this.updateTotals();

        if (animate) {
          $row.fadeIn(200);
        }
      }

      updateRowTotal($row) {
        const quantity = this.parseNumber($row.find('.js-order-product-quantity').val());
        const price = this.parseNumber($row.find('.js-order-product-price').val());

        if (quantity !== null && price !== null) {
          const total = quantity * price;
          $row.find('.js-order-product-total').val(this.formatNumber(total));
          $row.data('row-total', total);
        } else {
          $row.find('.js-order-product-total').val('');
          $row.data('row-total', null);
        }
      }

      updateTotals() {
        let subTotal = 0.0;

        this.container.find('[data-order-product-row]').each(function () {
          const value = $(this).data('row-total');
          if (typeof value === 'number' && Number.isFinite(value)) {
            subTotal += value;
          }
        });

        const shipping = this.parseNumber($('#f-orderTotalShipping').val()) ?? 0;

        $('#f-orderTotalSubTotal').val(this.formatNumber(subTotal));
        $('#f-orderTotalTotal').val(this.formatNumber(subTotal + shipping));
      }

      removeRow(key) {
        const $row = this.container.find(`[data-order-product-row][data-key="${key}"]`);
        if ($row.length) {
          $row.fadeOut(() => {
            $row.remove();
            this.updateTotals();
          });
        }
      }
    }

    const customerDiscountPercent = @json($customer?->group?->discountPercent);
    const existingRows = @json($orderProducts);
    const existingErrors = @json($orderProductErrors);
    const orderProductsForm = new OrderProductsForm({
      currencySymbol: @json($currencySymbol),
      placeholderImage: @json(asset('img/icons/file-placeholder.svg')),
      existingRows: existingRows,
    });

    existingRows.forEach(row => {
      const key = String(row?.formKey ?? row?.form_key ?? '');
      const rowErrors = existingErrors[key] || {};
      orderProductsForm.addRow(row, rowErrors, {animate: false});
    });

    orderProductsForm.updateTotals();

    $('#f-orderProductPicker').select2({
      placeholder: 'Изберете продукт...',
      minimumInputLength: 1,
      ajax: {
        url: '{{ url('/erp/products/') }}',
        dataType: 'json',
        delay: 250,
        data: params => ({
          filter: {q: params.term},
          page: params.page
        }),
        processResults: rs => {
          const items = rs?.products?.data ?? [];

          return {
            results: items.map(item => ({
              id: item.id,
              text: [item.mpn, item.ean, item.nameBg].filter(Boolean).join(' | '),
              product: item
            }))
          };
        },
        cache: true
      },
      templateSelection: item => item.text || item.id,
      templateResult: item => {
        if (item.loading) {
          return item.text;
        }

        const product = item.product || item;
        const preview = product?.uploads?.[0]?.urls?.tiny || orderProductsForm.placeholderImage;
        const info = [product?.mpn, product?.ean, product?.nameBg].filter(Boolean).join(' | ');
        const stock = Number(product?.quantity ?? NaN);
        const isOutOfStock = Number.isFinite(stock) && stock <= 0;

        const $wrapper = $('<div class="d-flex align-items-center gap-2"></div>');
        const $imageWrapper = $('<div style="width: 40px; height: 40px; flex-shrink: 0;" class="rounded-2 overflow-hidden bg-body-tertiary"></div>');
        if (preview) {
          $('<img>', {src: preview, alt: '', style: 'width: 40px; height: 40px; object-fit: cover;'}).appendTo($imageWrapper);
        }
        $wrapper.append($imageWrapper);

        const $infoWrapper = $('<div class="flex-grow-1"></div>');
        $infoWrapper.text(info);

        if (Number.isFinite(stock)) {
          $('<div class="text-body-secondary fs-9 mt-1"></div>').text(`Наличност: ${stock}`).appendTo($infoWrapper);
        }

        if (isOutOfStock) {
          $('<div class="mt-1"><span class="badge bg-danger-subtle text-danger">Изчерпан продукт</span></div>').appendTo($infoWrapper);
        }

        $wrapper.append($infoWrapper);

        return $wrapper;
      }
    }).on('select2:select', function (e) {
      const data = e.params.data?.product || null;
      if (data) {
        let price = Number(data.price ?? 0);

        if (customerDiscountPercent) {
          price -= (customerDiscountPercent / 100) * price;
        }

        orderProductsForm.addRow({
          productId: data.id,
          orderProductId: null,
          formKey: null,
          name: data.nameBg ?? '',
          sku: data.mpn ?? '',
          ean: data.ean ?? '',
          quantity: '1',
          price: orderProductsForm.formatNumber(price),
          total: orderProductsForm.formatNumber(price),
          maxQuantity: data.quantity ?? null,
          image: data.uploads?.[0]?.urls?.tiny || orderProductsForm.placeholderImage,
          isOutOfStock: Number(data.quantity ?? 0) <= 0,
          weight: data.weight ?? null,
          width: data.width ?? null,
          height: data.height ?? null,
          length: data.length ?? null,
        }, {});
      }

      $(this).val(null).trigger('change');
    });

    $(document).on('click', '[data-action="remove-order-product"]', function () {
      const key = $(this).data('key');
      orderProductsForm.removeRow(String(key ?? ''));
    });

    $(document).on('input', '.js-order-product-quantity, .js-order-product-price', function () {
      const $row = $(this).closest('[data-order-product-row]');
      const maxValue = $row.find('.js-order-product-quantity').attr('max');
      if (maxValue) {
        const maxNumeric = Number(maxValue);
        const current = Number($row.find('.js-order-product-quantity').val());
        if (Number.isFinite(maxNumeric) && Number.isFinite(current) && current > maxNumeric) {
          $row.find('.js-order-product-quantity').val(maxNumeric);
        }
      }

      const currentQuantity = Number($row.find('.js-order-product-quantity').val());
      if (!Number.isFinite(currentQuantity) || currentQuantity < 1) {
        $row.find('.js-order-product-quantity').val(1);
      }

      orderProductsForm.updateRowTotal($row);
      orderProductsForm.updateTotals();
    });

    $('#f-orderTotalShipping').on('input', function () {
      orderProductsForm.updateTotals();
    });
  });
</script>
