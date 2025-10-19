<div class="card mt-3">
  <div class="card-body">
    <h2 class="h5 pb-2 border-bottom border-dashed">Стойности</h2>

    <div class="col-12">
      <div class="row">
        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-vatRate">Ставка на ДДС</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="fa-regular fa-percent"></i>
            </span>
            <input type="number" min="0" max="100" step="0.01" class="form-control" id="f-vatRate" name="vatRate" value="{{ $document->vatRate }}" readonly/>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalAmountNoVat">Обща сума без ДДС</label>
          <div class="input-group">
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
            <input type="number" min="0.01" step="0.01" class="form-control" id="f-totalAmountNoVat" name="totalAmountNoVat" value="{{ $document->totalAmountNoVat }}" readonly/>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalVat">Общо ДДС</label>
          <div class="input-group">
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
            <input type="number" min="0" step="0.01" class="form-control" id="f-totalVat" name="totalVat" value="{{ $document->totalVat }}" readonly/>
          </div>
        </div>

        <div class="col-6 col-xxl-3">
          <label class="app-form-label" for="f-totalAmount">Обща сума с ДДС</label>
          <div class="input-group">
            <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
            <input type="number" min="0.01" step="0.01" class="form-control" id="f-totalAmount" name="totalAmount" value="{{ $document->totalAmount }}" readonly/>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="module">
  window.updateTotals = function () {
    let totalNoVat = 0;
    $('[id^="f-lines-"][id$="-totalPrice"]').each((_, el) => {
      totalNoVat += parseFloat($(el).val()) || 0;
    });

    $('#f-totalAmountNoVat')
      .val(totalNoVat ? totalNoVat.toFixed(2) : '')
      .trigger('change');

    const vatRate = parseFloat($('#f-vatRate').val()) || 0;
    const totalVat = (totalNoVat * vatRate / 100);
    $('#f-totalVat')
      .val(totalVat ? totalVat.toFixed(2) : '0')
      .trigger('change');

    const totalAmount = totalNoVat + totalVat;
    $('#f-totalAmount')
      .val(totalAmount ? totalAmount.toFixed(2) : '')
      .trigger('change');
  }

  $(function () {
    // Change vat rate
    $('#f-isForeignInvoice').change(() => {
      const isForeignInvoice = $('#f-isForeignInvoice').val() === '1';

      let setRate = @json($document->vatRate ?: dbConfig('default:vatRate'));

      if (isForeignInvoice) {
        setRate = 0;
      }

      $('#f-vatRate').val(setRate);
      window.updateTotals();
    })
      .change();

    // Update totals
    window.updateTotals();
  });
</script>
