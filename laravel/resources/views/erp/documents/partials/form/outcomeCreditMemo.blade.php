<div class="card mt-3">
  <div class="card-body pb-1">
    <h2 class="h5 card-title mb-4">Артикули</h2>

    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding align-middle">
        @foreach($lines ?? [] as $idx => $line)
          <thead>
          <tr class="bg-body-highlight fs-9">
            <th class="nosort border-top border-translucent"></th>
            <th class="nosort border-top border-translucent">Име</th>
            <th class="nosort border-top border-translucent">MPN</th>
            <th class="nosort border-top border-translucent">EAN</th>
            <th class="nosort border-top border-translucent">PO</th>
            <th class="nosort border-top border-translucent text-end">Количество</th>
            <th class="nosort border-top border-translucent text-end">Цена</th>
            <th class="nosort border-top border-translucent text-end">Кредитна сума</th>
            <th class="nosort border-top border-translucent text-end">Общо</th>
          </tr>
          </thead>
          <tbody>
          <tr data-line-row data-index="{{ $idx }}" data-price="{{ $line['price'] }}" data-qty="{{ $line['quantity'] }}">
            <td style="width: 1px;">
              @if ($line['productId'])
                <i class="fa-regular fa-box me-2"></i>
              @else
                <i class="fa-regular fa-file-lines me-2"></i>
              @endif
              <input type="hidden" name="lines[{{ $idx }}][type]" value="{{ $line['type'] ?? ($line['productId'] ? 'product' : 'empty') }}"/>
              <input type="hidden" name="lines[{{ $idx }}][productId]" value="{{ $line['productId'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][name]" value="{{ $line['name'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][mpn]" value="{{ $line['mpn'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][ean]" value="{{ $line['ean'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][po]" value="{{ $line['po'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][quantity]" value="{{ $line['quantity'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][price]" value="{{ $line['price'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][totalPrice]" data-line-total-hidden data-line="{{ $idx }}" value="{{ $line['totalPrice'] }}"/>
              <input type="hidden" name="lines[{{ $idx }}][creditAmount]" data-line-credit-hidden data-line="{{ $idx }}" value="{{ $line['creditAmount'] ?? '' }}"/>
            </td>
            <td>{{ $line['name'] ?? '' }}</td>
            <td>{{ $line['mpn'] ?? '' }}</td>
            <td>{{ $line['ean'] ?? '' }}</td>
            <td>{{ $line['po'] ?? '' }}</td>
            <td class="text-end">{{ $line['quantity'] ?? '' }}</td>
            <td class="text-end" style="width: 10rem;">{{ price($line['price'] ?? 0) }}</td>
            <td class="text-end" style="width: 12rem;">
              @if (!$line['productId'])
                <div class="input-group">
                  <span class="input-group-text text-danger">
                    <i class="fa-regular fa-minus"></i>
                  </span>
                  <input type="number" class="form-control form-control-sm text-end js-line-credit" data-credit-amount data-line="{{ $idx }}" name="lines[{{ $idx }}][creditAmount]" min="0" step="0.01" max="{{ $line['price'] * $line['quantity'] }}" value="{{ $line['creditAmount'] ?? '' }}"/>
                  <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
                </div>
              @else
                <div data-line-credit-sum data-line="{{ $idx }}">{{ ($line['creditAmount'] ?? 0) ? price($line['creditAmount']) : '-' }}</div>
              @endif
            </td>
            <td class="text-end" style="width: 10rem;" data-line-total data-line="{{ $idx }}">{{ price($line['totalPrice'] ?? 0) }}</td>
          </tr>
          @if(!empty($line['items']))
            <tr>
              <td colspan="9" style="padding: 0 !important;">
                <div style="max-height: 360px; overflow-x: hidden; overflow-y: auto;">
                  <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mt-3 fs-9 bg-body-secondary" style="margin-top: 0 !important;">
                      <thead>
                      <tr>
                        <th>Сер. №</th>
                        <th>Бележка</th>
                        <th class="text-end">Фактурирана цена</th>
                        <th class="text-end">Кредитна сума</th>
                        <th class="text-end">Коригирана цена</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($line['items'] as $iIdx => $item)
                        <tr data-item-row data-line="{{ $idx }}" data-sell-price="{{ $item['sellPrice'] }}">
                          <td>
                            {{ $item['serialNumber'] ?? 'N/A' }}
                            <input type="hidden" name="lines[{{ $idx }}][items][{{ $iIdx }}][serialNumber]" value="{{ $item['serialNumber'] ?? '' }}"/>
                            <input type="hidden" name="lines[{{ $idx }}][items][{{ $iIdx }}][storageItemId]" value="{{ $item['id'] ?? $item['storageItemId'] ?? '' }}"/>
                          </td>
                          <td>
                            {{ $item['note'] ?? 'N/A' }}
                            <input type="hidden" name="lines[{{ $idx }}][items][{{ $iIdx }}][note]" value="{{ $item['note'] ?? '' }}"/>
                          </td>
                          <td style="width: 10rem;" class="text-end">
                            {{ price($item['sellPrice']) }}
                            <input type="hidden" name="lines[{{ $idx }}][items][{{ $iIdx }}][sellPrice]" value="{{ $item['sellPrice'] }}"/>
                            <input type="hidden" name="lines[{{ $idx }}][items][{{ $iIdx }}][purchasePrice]" value="{{ $item['purchasePrice'] }}"/>
                          </td>
                          <td style="width: 12rem;">
                            <div class="input-group">
                              <span class="input-group-text text-danger">
                                <i class="fa-regular fa-minus"></i>
                              </span>
                              <input type="number" class="form-control form-control-sm text-end js-item-credit" data-credit-amount data-line="{{ $idx }}" data-item="{{ $iIdx }}" name="lines[{{ $idx }}][items][{{ $iIdx }}][creditAmount]" min="0" step="0.01" max="{{ $item['sellPrice'] }}" value="{{ $item['creditAmount'] ?? '' }}" data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top" title="Ако въведете пълната стойност, артикулът ще бъде върнат в склада"/>
                              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
                            </div>
                          </td>
                          <td style="width: 10rem;" class="text-end" data-item-corrected-price>
                            {{ price($item['sellPrice'] - ($item['creditAmount'] ?? 0)) }}
                          </td>
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </td>
            </tr>
          @endif
          </tbody>
        @endforeach
      </table>

      <div class="alert alert-outline-warning py-2 px-3 fs-9">
        В кредитното известие ще се включат само записите, за които има корекции. Останалите няма да бъдат включени.
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <h2 class="h5 pb-2 border-bottom border-dashed">Стойности</h2>

    <div class="col-12">
      <div class="row">
        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-vatRate">Ставка на ДДС</label>
          <div class="input-group">
            <input type="number" min="0" max="100" step="0.01" class="form-control" id="f-vatRate" name="vatRate" value="{{ $document->vatRate }}" readonly/>
            <span class="input-group-text">
              <i class="fa-regular fa-percent"></i>
            </span>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalAmountNoVat">Общо без ДДС</label>
          <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="f-totalAmountNoVat" name="totalAmountNoVat" value="" readonly/>
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalVat">Общо ДДС</label>
          <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="f-totalVat" name="totalVat" value="" readonly/>
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalAmount">Общо с ДДС</label>
          <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="f-totalAmount" name="totalAmount" value="" readonly/>
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="module">
  const currency = @json(dbConfig('currency:symbol'));

  function updateTotals() {
    let totalCredit = 0;
    $('.js-item-credit').each((_, el) => {
      totalCredit += parseFloat($(el).val()) || 0;
    });
    totalCredit *= -1;

    $('#f-totalAmountNoVat')
      .val(totalCredit ? totalCredit.toFixed(2) : '')
      .trigger('change');

    const vatRate = parseFloat($('#f-vatRate').val()) || 0;
    const totalVat = totalCredit * vatRate / 100;
    $('#f-totalVat')
      .val(totalVat ? totalVat.toFixed(2) : '')
      .trigger('change');

    const totalAmount = totalCredit + totalVat;
    $('#f-totalAmount')
      .val(totalAmount ? totalAmount.toFixed(2) : '')
      .trigger('change');
  }

  function updateLine(idx) {
    const $line = $(`[data-line-row][data-index="${idx}"]`);
    const price = parseFloat($line.data('price')) || 0;
    const qty = parseFloat($line.data('qty')) || 0;

    let credit = 0;
    $(`[data-credit-amount][data-line="${idx}"]`).each((_, el) => {
      credit += parseFloat($(el).val()) || 0;
    });

    $(`[data-line-credit-hidden][data-line="${idx}"]`).val(credit ? credit.toFixed(2) : '');

    const $display = $(`[data-line-credit-sum][data-line="${idx}"]`);
    if ($display.length) {
      $display.text(credit ? credit.toFixed(2) + ' ' + currency : '-');
    }

    const total = price * qty - credit;
    $(`[data-line-total-hidden][data-line="${idx}"]`).val(total ? total.toFixed(2) : '');
    const $total = $(`[data-line-total][data-line="${idx}"]`);
    if (total <= 0) {
      $total.html('<span class="text-danger">Ще бъде сторнирано</span>');
    } else {
      $total.text(total.toFixed(2) + ' ' + currency);
    }

    updateTotals();
  }

  function updateItem($input) {
    const credit = parseFloat($input.val()) || 0;
    const $row = $input.closest('[data-item-row]');
    const sellPrice = parseFloat($row.data('sell-price')) || 0;
    const max = parseFloat($input.attr('max')) || 0;
    const corrected = sellPrice - credit;
    const $cell = $row.find('[data-item-corrected-price]');
    if (credit >= max) {
      $cell.html('<span class="text-danger">Ще бъде сторнирано</span>');
    } else {
      $cell.text(corrected ? corrected.toFixed(2) + ' ' + currency : '');
    }
    const idx = $input.data('line');
    updateLine(idx);
  }

  $(function () {
    $('.js-item-credit').each(function () {
      updateItem($(this));
    }).on('input', function () {
      updateItem($(this));
    });
    $('.js-line-credit').each(function () {
      const idx = $(this).data('line');
      updateLine(idx);
    }).on('input', function () {
      const idx = $(this).data('line');
      updateLine(idx);
    });
    $('[data-line-row]').each((_, el) => {
      updateLine($(el).data('index'));
    });
  });
</script>

