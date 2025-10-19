<div class="card mt-3">
  <div class="card-body">
    <h2 class="h5 pb-2 border-bottom border-dashed">Редове</h2>
    <div id="js-lines"></div>

    @if($errors->has('lines'))
      <div class="text-danger text-center fs-9 fw-bold">
        {{ $errors->first('lines') }}
      </div>
    @endif

    <div class="mt-3">
      <div class="dropdown">
        <button class="btn btn-sm btn-phoenix-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa fa-plus"></i>
          Добави нов ред
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="#!" data-action="add-product-line">
              <i class="fa-regular fa-box me-2"></i>
              Добави продукт
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#!" data-action="add-empty-line">
              <i class="fa-regular fa-file-lines me-2"></i>
              Добави празен ред
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@include('erp.documents.partials.form.partials.invoice-amounts')

<script type="module">
  class InvoiceLinesForm {
    constructor(useItems = true) {
      this.idx = 0;
      this.useItems = useItems;
    }

    addLine(data = {}) {
      const idx = this.idx++;
      const type = data.type || 'product';
      const itemsContainer = this.useItems && type === 'product' ? `<div class="mt-2" data-items-container id="items-${idx}"></div>` : '';
      const iconField = type === 'product'
        ? `<div class="col-auto d-flex align-items-center pt-4"><i class="fa-regular fa-box" data-bs-toggle="tooltip" data-bs-title="Продукт"></i></div>`
        : `<div class="col-auto d-flex align-items-center pt-4"><i class="fa-regular fa-file-lines" data-bs-toggle="tooltip" data-bs-title="Празен ред"></i></div>`;
      const mpnField = type === 'product' ? `
            <div class="col">
              <label class="form-label" for="f-lines-${idx}-mpn">MPN</label>
              <select class="form-select" id="f-lines-${idx}-mpn" data-autocomplete="mpn" name="lines[${idx}][mpn]">
                ${data.mpn ? `<option value="${data.mpn}" selected>${data.mpn}</option>` : ''}
              </select>
            </div>` : '';
      const eanField = type === 'product' ? `
            <div class="col">
              <label class="form-label" for="f-lines-${idx}-ean">EAN</label>
              <select class="form-select" id="f-lines-${idx}-ean" data-autocomplete="ean" name="lines[${idx}][ean]">
                ${data.ean ? `<option value="${data.ean}" selected>${data.ean}</option>` : ''}
              </select>
            </div>` : '';
      const $row = $(`
        <div class="col-12 border-bottom border-dashed pb-2 mb-2" data-row="${idx}">
          <div class="row g-2">
            ${iconField}
            ${mpnField}
            ${eanField}
            <div class="col">
              <label class="form-label required" for="f-lines-${idx}-name">Име</label>
              <input type="text" id="f-lines-${idx}-name" class="form-control" name="lines[${idx}][name]" value="${data.name ?? ''}" required/>
              <input type="hidden" name="lines[${idx}][productId]" value="${data.productId ?? ''}"/>
              <input type="hidden" name="lines[${idx}][type]" value="${type}"/>
            </div>
            <div class="col-auto" style="width:10rem;">
              <label class="form-label" for="f-lines-${idx}-po">PO</label>
              <input type="text" id="f-lines-${idx}-po" class="form-control" name="lines[${idx}][po]" value="${data.po ?? ''}"/>
            </div>
            <div class="col-auto" style="width:8rem;">
              <label class="form-label required" for="f-lines-${idx}-quantity">Количество</label>
              <input type="number" id="f-lines-${idx}-quantity" class="form-control js-quantity" min="1" step="1" name="lines[${idx}][quantity]" value="${parseInt(data.quantity) ?? 1}" required/>
            </div>
            <div class="col-auto" style="width:10rem;">
              <label class="form-label" for="f-lines-${idx}-price">Цена</label>
              <div class="input-group required">
                <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
                <input type="number" id="f-lines-${idx}-price" class="form-control" min="0.01" step="0.01" name="lines[${idx}][price]" value="${parseFloat(data.price).toFixed(2) ?? ''}" required/>
              </div>
            </div>
            <div class="col-auto" style="width:10rem;">
              <label class="form-label required" for="f-lines-${idx}-totalPrice">Общо</label>
              <div class="input-group">
                <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
                <input type="number" id="f-lines-${idx}-totalPrice" class="form-control" min="0.01" step="0.01" name="lines[${idx}][totalPrice]" value="${parseFloat(data.totalPrice).toFixed(2) ?? ''}" readonly required/>
              </div>
            </div>
            <div class="col-auto align-content-end">
              <button type="button" class="btn btn-sm btn-phoenix-danger" data-action="remove-line" data-id="${idx}">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>
          ${itemsContainer}
        </div>`
      );

      $('#js-lines').append($row);
      $row.find('[data-bs-toggle="tooltip"]').each((_, el) => new bootstrap.Tooltip(el));

      if (type === 'product') {
        $row.find('[data-autocomplete]').each((_, el) => {
          const $select = $(el);
          const kind = $select.data('autocomplete');
          $select.select2({
            tags: true,
            minimumInputLength: 1,
            ajax: {
              url: '{{ url('/erp/products/') }}',
              dataType: 'json',
              delay: 250,
              data: params => ({
                filter: {
                  [kind]: params.term,
                  usageStatus: @json(\App\Enums\ProductUsageStatus::InternalUse->value),
                },
                op: {
                  usageStatus: 'neq',
                },
                // page: 'all',
              }),
              processResults: rs => ({
                results: rs.products.data.map(item => ({
                  id: item[kind],
                  text: [item[kind], item.nameBg].filter(Boolean).join(' | '),
                  product: item,
                })),
                pagination: {more: rs.products.next_page_url !== null},
              }),
            },
            templateSelection: item => item.id || item.text,
            templateResult: item => item.text,
          }).on('select2:select', e => {
            const p = e.params.data.product;
            if (p) {
              $row.find(`input[name="lines[${idx}][productId]"]`).val(p.id);
              $row.find(`input[name="lines[${idx}][name]"]`).val(p.nameBg);
              $row.find(`#f-lines-${idx}-price`).val(p.price);
              const other = kind === 'mpn' ? 'ean' : 'mpn';
              const val = p[other];
              const $other = $row.find(`[data-autocomplete="${other}"]`);
              if (val) {
                if (!$other.find(`option[value="${val}"]`).length) {
                  const opt = new Option(val, val, true, true);
                  $other.append(opt).trigger('change');
                } else {
                  $other.val(val).trigger('change');
                }
              } else {
                $other.val(null).trigger('change');
              }
              if (this.useItems) {
                this.fetchItems(idx, p.id);
              }
              this.updateTotal(idx);
            }
          }).on('change', () => {
            if (!$select.val()) {
              const other = kind === 'mpn' ? 'ean' : 'mpn';
              if (!$row.find(`[data-autocomplete="${other}"]`).val()) {
                $row.find(`input[name="lines[${idx}][productId]"]`).val('');
                $row.find(`input[name="lines[${idx}][name]"]`).val('');
                $row.find(`#f-lines-${idx}-price`).val('');
                $row.find(`#f-lines-${idx}-totalPrice`).val('');
                $row.find(`#items-${idx}`).empty();
              }
            }
          });
        });
      }

      $row.find('.js-quantity').on('change', () => {
        if (this.useItems && type === 'product') {
          const $qty = $row.find('.js-quantity');
          const max = parseInt($qty.attr('max'));
          if (max) {
            this.updateMax(idx, max);
          }
          this.syncItems(idx);
        }
        this.updateTotal(idx);
      });

      $row.find(`#f-lines-${idx}-price`).on('input', () => this.updateTotal(idx));

      if (type === 'product' && data.productId) {
        if (this.useItems) {
          this.fetchItems(idx, data.productId, data.items || []);
        }
      }
      this.updateTotal(idx);
    }

    fetchItems(idx, productId, preset = []) {
      if (!this.useItems) {
        return;
      }
      const $container = $(`#items-${idx}`);
      $.get(`{{ url('/erp/storage-items') }}?page=all&filter[productId]=${productId}&op[groupId]=eq&filter[isExited]=0&op[isExited]=eq`, rs => {
        const items = rs?.storageItems?.data || [];

        if (!items.length) {
          $container.html(`
            <div class="alert alert-outline-danger p-3 fs-9 m-3">Този продукт няма заприходени артикули, съответно не може да бъде издаден документ към него. Моля, изтрийте реда или направете заприхождаване към съответния продукт!</div>
          `);
          return;
        }

        let html = `
        <div style="max-height: 360px; overflow-x: hidden; overflow-y: auto;">
          <div class="table-responsive">
            <table class="table table-hover table-sm fs-9">
            <thead>
              <tr>
                <th></th>
                <th>Дата</th>
                <th>Покупна цена</th>
                <th>SN</th>
                <th>Бележка</th>
              </tr>
            </thead>
            <tbody>
        `;
        items.forEach((it, i) => {
          const checked = preset.find(p => p.storageItemId === it.id && p.selected) ? 'checked' : '';
          html += `
          <tr>
            <td style="width: 10px;">
              <input type="checkbox" name="lines[${idx}][items][${i}][selected]" value="1" ${checked}/>
              <input type="hidden" name="lines[${idx}][items][${i}][storageItemId]" value="${it.id}"/>
            </td>
            <td>${it.invoiceDate.substring(0, 10)}</td>
            <td>${it.purchasePrice} {{ dbConfig('currency:symbol') }}</td>
            <td>${it.serialNumber ?? ''}</td>
            <td>${it.note ?? ''}</td>
          </tr>
          `;
        });
        html += `
              </tbody>
            </table>
            </div>
          </div>
        `;
        $container.html(html).toggle(items.length > 0);
        $container.off('change.items').on('change.items', 'input[type="checkbox"]', () => this.updateSelectionError(idx));
        this.updateMax(idx, items.length);
        this.syncItems(idx);
      });
    }

    updateMax(idx, max) {
      if (!this.useItems) {
        return;
      }
      const $qty = $(`[data-row="${idx}"] .js-quantity`);
      $qty.attr('max', max);
      const value = parseInt($qty.val());
      if (max && value > max) {
        $qty.addClass('is-invalid');
      } else {
        $qty.removeClass('is-invalid');
      }
      this.syncItems(idx);
    }

    syncItems(idx) {
      if (!this.useItems) {
        return;
      }
      const $row = $(`[data-row="${idx}"]`);
      const qty = parseInt($row.find('.js-quantity').val()) || 0;
      const $checks = $row.find('[data-items-container] input[type="checkbox"]');
      $checks.each((i, el) => $(el).prop('checked', i < qty));
      this.updateSelectionError(idx);
    }

    updateSelectionError(idx) {
      if (!this.useItems) {
        return;
      }
      const $row = $(`[data-row="${idx}"]`);
      const qty = parseInt($row.find('.js-quantity').val()) || 0;
      const selected = $row.find('[data-items-container] input[type="checkbox"]:checked').length;
      $row.find('.js-quantity').toggleClass('is-invalid', qty !== selected);
    }

    updateTotal(idx) {
      const $row = $(`[data-row="${idx}"]`);
      const qty = parseFloat($row.find('.js-quantity').val()) || 0;
      const price = parseFloat($row.find(`#f-lines-${idx}-price`).val()) || 0;
      const total = qty * price;
      $row.find(`#f-lines-${idx}-totalPrice`).val(total ? total.toFixed(2) : '');
      window.updateTotals();
    }
  }

  const invoiceLinesForm = new InvoiceLinesForm(@json($useItems ?? true));
  const existingLines = @json($lines ? array_values($lines) : []);
  existingLines.forEach(row => invoiceLinesForm.addLine(row));

  $(document).on('click', '[data-action="add-product-line"]', () => invoiceLinesForm.addLine({type: 'product'}));
  $(document).on('click', '[data-action="add-empty-line"]', () => invoiceLinesForm.addLine({type: 'empty'}));
  $(document).on('click', '[data-action="remove-line"]', function () {
    const id = $(this).data('id');
    $(`[data-row="${id}"]`).remove();
    window.updateTotals();
  });
</script>

