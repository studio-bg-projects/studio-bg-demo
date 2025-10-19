@php
  $productErrors = [];
  foreach ($errors->getMessages() as $key => $messages) {
    if (str_starts_with($key, 'products.')) {
      if (preg_match('/^products\.(\d+)\.(.+)$/', $key, $m)) {
        $productErrors[$m[1]][$m[2]] = $messages[0];
        if ($m[2] === 'ean') {
          $productErrors[$m[1]]['mpn'] = $messages[0];
        }
      }
    }
  }
@endphp

<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за документа</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-documentNumber">Номер на документа</label>
        <input type="text" class="form-control @if($errors->has('documentNumber')) is-invalid @endif" id="f-documentNumber" name="documentNumber" value="{{ $document->documentNumber }}" placeholder="INV-2025-0001..." required/>
        @if($errors->has('documentNumber'))
          <div class="invalid-feedback">
            {{ $errors->first('documentNumber') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-documentDate">Дата на издаване</label>
        <input type="date" class="form-control @if($errors->has('documentDate')) is-invalid @endif" id="f-documentDate" name="documentDate" value="{{ $document?->documentDate?->format('Y-m-d') ?: $document?->documentDate  }}" placeholder="2024-01-15..." required/>
        @if($errors->has('documentDate'))
          <div class="invalid-feedback">
            {{ $errors->first('documentDate') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-documentDate');
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-supplierId">Доставчик</label>
        <select class="form-select @if($errors->has('supplierId')) is-invalid @endif" id="f-supplierId" name="supplierId" required>
          <option value="">-</option>
          @foreach ($suppliers as $row)
            <option value="{{ $row->id }}" {{ $document->supplierId == $row->id ? 'selected' : '' }}>
              {{ $row->companyName }}
              / {{ $row->companyId }}
              / {{ $row->firstName }} {{ $row->lastName }}
            </option>
          @endforeach
        </select>
        @if($errors->has('supplierId'))
          <div class="invalid-feedback">
            {{ $errors->first('supplierId') }}
          </div>
        @endif

        <script type="module">
          $(function () {
            $('#f-supplierId').select2();
          });
        </script>
      </div>
    </div>

    <div class="col-12">
      <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Артикули</h2>
    </div>

    <style>
      .timeline-basic .timeline-item:last-child .timeline-bar {
        display: none;
      }

      ._main-timeline-item-bar.hidden-line .timeline-bar {
        display: none;
      }
    </style>

    @if($errors->has('products'))
      <div class="alert alert-outline-danger my-2">{{ $errors->first('products') }}</div>
    @endif

    <div id="js-products"></div>

    <div class="text-end fw-bold border-bottom border-dashed pt-2 pb-3 mb-3">
      Общо:
      <span id="js-total"></span> {{ dbConfig('currency:symbol') }}
    </div>

    <button type="button" class="btn btn-sm btn-phoenix-primary w-100" data-action="add-row">
      <i class="fa fa-plus"></i>
      Добави нов ред
    </button>
  </div>
</div>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Прикачване на документи</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::StorageEntriesIncomeInvoices->value,
      'groupId' => $document->fileGroupId,
      'fieldName' => 'fileGroupId',
    ])
  </div>
</div>

<script type="module">
  class StorageEntriesForm {
    constructor() {
      this.idx = 0;
    }

    addRow(data = {}, errors = {}) {
      const idx = this.idx++;
      const $row = $(`
        <div class="col-12 border-bottom border-dashed pb-2 mb-2" data-row="${idx}">
          <div class="timeline-basic">
            <div class="timeline-item">
              <div class="row g-3">
                <div class="col-auto pt-4">
                  <div class="timeline-item-bar hidden-line position-relative _main-timeline-item-bar">
                    <div data-action="toggle-sub" data-id="${idx}" class="icon-item icon-item-md rounded-7 border border-translucent cursor-pointer text-primary">
                      <i class="fa-regular fa-plus-large"></i>
                    </div>
                    <span class="timeline-bar border-end border-dashed"></span>
                  </div>
                </div>
                <div class="col pb-3">
                  <div class="row g-2">
                    <div class="col-4 col-xxl">
                      <label class="form-label ps-0" for="f-products-${idx}-mpn">MPN</label>
                      <select class="form-select${errors.mpn ? ' is-invalid' : ''}" id="f-products-${idx}-mpn" data-autocomplete="mpn" name="products[${idx}][mpn]">
                        ${data.mpn ? `<option value="${data.mpn}" selected>${data.mpn}</option>` : ''}
                      </select>
                      ${errors.mpn ? `<div class="invalid-feedback">${errors.mpn}</div>` : ''}
                    </div>
                    <div class="col-4 col-xxl">
                      <label class="form-label ps-0" for="f-products-${idx}-ean">EAN</label>
                      <select class="form-select${errors.ean ? ' is-invalid' : ''}" id="f-products-${idx}-ean" data-autocomplete="ean" name="products[${idx}][ean]">
                        ${data.ean ? `<option value="${data.ean}" selected>${data.ean}</option>` : ''}
                      </select>
                      ${errors.ean ? `<div class="invalid-feedback">${errors.ean}</div>` : ''}
                    </div>
                    <div class="col-4 col-xxl">
                      <label class="form-label ps-0" for="f-products-${idx}-name">Име</label>
                      <input type="text" id="f-products-${idx}-name" class="form-control${errors.name ? ' is-invalid' : ''}" name="products[${idx}][name]" value="${data.name ?? ''}"/>
                      ${errors.name ? `<div class="invalid-feedback">${errors.name}</div>` : ''}
                      <input type="hidden" name="products[${idx}][productId]" value="${data.productId ?? ''}"/>
                      <input type="hidden" name="products[${idx}][entryProductId]" value="${data.entryProductId ?? ''}"/>
                    </div>
                    <div class="col-auto" style="width: 9rem;">
                      <label class="form-label ps-0" for="f-products-${idx}-quantity">Количество</label>
                      <input type="number" id="f-products-${idx}-quantity" min="1" step="1" class="form-control js-quantity${errors.quantity ? ' is-invalid' : ''}" name="products[${idx}][quantity]" value="${data.quantity ?? 1}"/>
                      ${errors.quantity ? `<div class="invalid-feedback">${errors.quantity}</div>` : ''}
                    </div>
                    <div class="col-auto" style="width: 9rem;">
                      <label class="form-label ps-0 required" for="f-products-${idx}-purchasePrice">Покупна цена {{ dbConfig('currency:symbol') }}</label>
                      <input type="number" min="0.01" step="0.01" id="f-products-${idx}-purchasePrice" class="form-control${errors.purchasePrice ? ' is-invalid' : ''}" name="products[${idx}][purchasePrice]" value="${data.purchasePrice ?? ''}" required/>
                      ${errors.purchasePrice ? `<div class="invalid-feedback">${errors.purchasePrice}</div>` : ''}
                    </div>
                    <div class="col-auto" style="width: 9rem;">
                      <label class="form-label ps-0" for="f-products-${idx}-total">Общо {{ dbConfig('currency:symbol') }}</label>
                      <input type="number" min="0.01" step="0.01" id="f-products-${idx}-total" class="form-control${errors.total ? ' is-invalid' : ''}" name="products[${idx}][total]" value="${data.total ?? ''}" disabled/>
                    </div>
                    <div class="col-auto js-internalUseCol" style="width: 9rem;">
                      <label class="form-label ps-0" for="f-products-${idx}-ean">Фирмено ползване</label>
                      <select class="form-select" id="f-products-${idx}-internalUse" name="products[${idx}][internalUse]">
                        <option value="0" ${!parseInt(data?.internalUse) ? 'selected' : ''}>Не</option>
                        <option value="1" ${parseInt(data?.internalUse) ? 'selected' : ''}>Да</option>
                      </select>
                    </div>
                    <div class="col-auto pt-4">
                      <button type="button" class="btn btn-sm btn-phoenix-danger" data-action="remove-row" data-id="${idx}">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="sub-${idx}" style="max-height: 360px; overflow-x: hidden; overflow-y: auto; display: none;"></div>
          </div>
        </div>
      `);

      $row.find('input[name*="[quantity]"],input[name*="[purchasePrice]"]').on('change keyup', this.updateTotals.bind(this));

      $('#js-products').append($row);

      // Remove internal use on edit
      @if(Request::is('erp/storage-entries/update/*'))
      $row.find('.js-internalUseCol').remove();
      @endif

      $row.find('[data-autocomplete]').each(function () {
        const $select = $(this);
        const kind = $select.data('autocomplete');

        $select.select2({
          tags: true,
          minimumInputLength: 1,
          ajax: {
            url: '{{ url('/erp/products/') }}',
            dataType: 'json',
            delay: 250,
            data: params => ({
              filter: {[kind]: params.term},
              page: params.page
            }),
            processResults: rs => ({
              results: rs.products.data.map(item => ({
                id: item[kind],
                text: [item[kind], item.nameBg].filter(Boolean).join(' | '),
                product: item
              }))
            }),
            cache: true
          },
          templateSelection: item => item.id || item.text,
          templateResult: item => item.text
        }).on('select2:select', function (e) {
          const data = e.params.data.product;
          if (data) {
            $row.find(`input[name="products[${idx}][productId]"]`).val(data.id);
            $row.find(`input[name="products[${idx}][name]"]`).val(data.nameBg);

            const eanSelect = $row.find('[data-autocomplete="ean"]');
            if (eanSelect.val() !== data.ean) {
              const opt = new Option(data.ean, data.ean, true, true);
              eanSelect.append(opt).trigger('change');
            }

            const mpnSelect = $row.find('[data-autocomplete="mpn"]');
            if (mpnSelect.val() !== data.mpn) {
              const opt = new Option(data.mpn, data.mpn, true, true);
              mpnSelect.append(opt).trigger('change');
            }
          } else {
            $row.find(`input[name="products[${idx}][productId]"]`).val('');
          }
        }).on('change', function () {
          if (!$select.val()) {
            $row.find(`input[name="products[${idx}][productId]"]`).val('');
          }
        });
      });

      this.refreshSub(idx);

      if (data.items) {
        Array.from(data.items).forEach((sub, i) => {
          const $sub = $row.find(`#sub-${idx} [data-sub]`).eq(i);
          $sub.find(`input[name="products[${idx}][items][${i}][serialNumber]"]`).val(sub.serialNumber || '');
          $sub.find(`input[name="products[${idx}][items][${i}][note]"]`).val(sub.note || '');
          $sub.find(`input[name="products[${idx}][items][${i}][itemId]"]`).val(sub.itemId || '');
          $sub.find('[data-action="remove-sub"]').data('is-exited', sub.isExited ? 1 : 0);
          $sub.find('[data-action="remove-sub"]').data('has-correction', sub.priceCorrectionIncomeCreditMemoId ? 1 : 0);

          if (sub.isExited) {
            $sub.find('[data-seq]').addClass('bg-primary-lighter');
          }

          if (sub.priceCorrectionIncomeCreditMemoId) {
            $sub.find('[data-seq]').addClass('bg-primary-lighter');
          }
        });
      }
    }

    refreshSub(idx) {
      const $row = $(`[data-row="${idx}"]`);
      const qty = parseInt($row.find('.js-quantity').val()) || 1;
      const $container = $row.find(`#sub-${idx}`);
      const current = $container.find('[data-sub]').length;

      if (current < qty) {
        for (let i = current; i < qty; i++) {
          $container.append(`
            <div class="timeline-item" data-sub>
              <div class="row g-3">
                <div class="col-auto">
                  <div class="timeline-item-bar position-relative">
                    <div class="icon-item icon-item-md rounded-7 border border-translucent" data-seq="${idx}">
                      <span class="fs-9 fw-bolder">${i + 1}</span>
                    </div>
                    <span class="timeline-bar border-end border-dashed"></span>
                  </div>
                </div>
                <div class="col">
                  <div class="row g-2 align-items-center pb-3">
                    <div class="col">
                      <input type="text" class="form-control" name="products[${idx}][items][${i}][serialNumber]" placeholder="Сериен номер"/>
                      <input type="hidden" name="products[${idx}][items][${i}][itemId]" value=""/>
                    </div>
                    <div class="col">
                      <input type="text" class="form-control" name="products[${idx}][items][${i}][note]" placeholder="Бележка"/>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-sm" data-action="remove-sub" data-row="${idx}">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `);
        }
      } else if (current > qty) {
        $container.find('[data-sub]').slice(qty).fadeOut(function () {
          $(this).remove();
        });
      }
    }

    updateTotals() {
      let total = 0;

      $('#js-products [data-row]').each(function () {
        const $row = $(this);
        const quantity = parseFloat($row.find('input[name*="[quantity]"]').val());
        const purchasePrice = parseFloat($row.find('input[name*="[purchasePrice]"]').val());

        let subTotal = purchasePrice * quantity || 0;
        total += subTotal;

        const $total = $row.find('input[name*="[total]"]');
        $total.val(subTotal.toFixed(2));
      })

      $('#js-total').text(total.toFixed(2));
    }
  }

  const storageEntriesForm = new StorageEntriesForm();

  const existingProducts = @json($products ?? []);
  const existingProductErrors = @json($productErrors ?? []);
  existingProducts.forEach((row, index) => storageEntriesForm.addRow(row, existingProductErrors[index] || {}));

  storageEntriesForm.updateTotals();

  $(document).on('click', '[data-action="add-row"]', function () {
    storageEntriesForm.addRow();
  });

  $(document).on('click', '[data-action="remove-row"]', function () {
    const id = $(this).data('id');
    $(`[data-row="${id}"]`).fadeOut(function () {
      $(this).remove();
    });
  });

  $(document).on('click', '[data-action="toggle-sub"]', function () {
    const $btn = $(this);
    const $btnParent = $btn.parent();
    const id = $btn.data('id');
    const $container = $(`#sub-${id}`);
    $container.fadeToggle(function () {
      const isVisible = $container.is(':visible');
      $btn.html(isVisible ? '<i class="fa-regular fa-minus-large"></i>' : '<i class="fa-regular fa-plus-large"></i>');

      if (isVisible) {
        $btnParent.removeClass('hidden-line');
      } else {
        $btnParent.addClass('hidden-line');
      }
    });
  });

  $(document).on('change', '.js-quantity', function () {
    const id = $(this).closest('[data-row]').data('row');
    storageEntriesForm.refreshSub(id);
  });

  $(document).on('click', '[data-action="remove-sub"]', function () {
    const id = $(this).data('row');

    const isExited = !!parseInt($(this).data('is-exited'));
    if (isExited) {
      alert('Изтриването на записа не е възможно, защото е изписан от склада (продаден или отписан по друг начин).');
      return;
    }

    const hasCorrection = !!parseInt($(this).data('has-correction'));
    if (hasCorrection) {
      alert('Изтриването на записа не е възможно, защото е има входящо кредитно известие към него.');
      return;
    }

    const $row = $(`[data-row="${id}"]`);
    $(this).closest('[data-sub]').remove();
    $row.find('.js-quantity').val($row.find('[data-sub]').length);
  });
</script>
