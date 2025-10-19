<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за плащането</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-customerId">Клиент</label>
        <select class="form-select @if($errors->has('customerId')) is-invalid @endif" id="f-customerId" name="customerId" required>
          <option value="">-</option>
          @foreach ($customers as $row)
            <option value="{{ $row->id }}" {{ $income->customerId == $row->id ? 'selected' : '' }}>
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
          $(function () {
            $('#f-customerId')
              .select2()
              .change(() => erpIncome.loadCustomer($('#f-customerId').val()))
              .change();
          });
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-paymentDate">Дата на плащането</label>
        <input type="date" class="form-control @if($errors->has('paymentDate')) is-invalid @endif" id="f-paymentDate" name="paymentDate" value="{{ $income->paymentDate }}" placeholder="2020-12-01" required/>
        @if($errors->has('paymentDate'))
          <div class="invalid-feedback">
            {{ $errors->first('paymentDate') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-paymentDate');
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-paidAmount">Постъпила сума</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="number" min="0" step="0.01" class="form-control @if($errors->has('paidAmount')) is-invalid @endif" id="f-paidAmount" name="paidAmount" value="{{ $income->paidAmount }}" placeholder="2400..." required/>
          @if($errors->has('paidAmount'))
            <div class="invalid-feedback">
              {{ $errors->first('paidAmount') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Разпределение на плащането</h2>
      </div>

      <div class="col-12">
        <div id="js-allocations"></div>

        @if($errors->has('items'))
          <div class="alert alert-phoenix-danger fs-9 m-4 mt-0 p-3">
            {{ $errors->first('items') }}
          </div>
        @endif

        <button type="button" class="btn btn-sm btn-phoenix-info text-primary w-100 mb-4" onclick="erpIncome.addAllocation();">
          <i class="fa-regular fa-plus"></i>
          Добави разпределение
        </button>

        <div class="alert alert-outline-warning" id="js-allocation-warning" style="display: none;">
          <div class="d-flex flex-row align-items-center">
            <div class="d-flex bg-warning-subtle rounded flex-center me-3" style="width: 32px; height: 32px">
              <i class="fa-regular fa-hexagon-exclamation text-warning-dark fs-7"></i>
            </div>
            <div>
              <p class="fw-bold mb-0 fs-9" id="js-allocation-warning-text"></p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Бележки</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPrivate">Вътрешна бележка</label>
        <textarea type="text" class="form-control @if($errors->has('notesPrivate')) is-invalid @endif" id="f-notesPrivate" name="notesPrivate" rows="4" placeholder="...">{{ $income->notesPrivate }}</textarea>
        @if($errors->has('notesPrivate'))
          <div class="invalid-feedback">
            {{ $errors->first('notesPrivate') }}
          </div>
        @endif
        <p class="text-body-tertiary fs-9 fw-semibold mb-0 mt-1">Този текст ще бъде достъпен само от операторите на системата.</p>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPublic">Бележка към клиента</label>
        <textarea type="text" class="form-control @if($errors->has('notesPublic')) is-invalid @endif" id="f-notesPublic" name="notesPublic" rows="4" placeholder="...">{{ $income->notesPublic }}</textarea>
        @if($errors->has('notesPublic'))
          <div class="invalid-feedback">
            {{ $errors->first('notesPublic') }}
          </div>
        @endif
        <p class="text-body-tertiary fs-9 fw-semibold mb-0 mt-1">Този текст ще бъде видим от клиента.</p>
      </div>
    </div>
  </div>
</div>

<script type="module">
  class ErpIncome {
    allocationIdx = 0;
    allocationsData = {};
    customerDocuments = null; // must be null to when there are no loaded customer documents

    constructor() {
      this.refillData();
      this.checkForAmountAlerts();

      $('#f-paidAmount').change(() => {
        this.checkForAmountAlerts();
      });
    }

    refillData(allocationIdx) {
      this.allocationsData[allocationIdx] = {
        id: parseFloat($(`#erp-form-id-${allocationIdx}`).val()) ?? null,
        documentId: parseFloat($(`#erp-form-documentId-${allocationIdx}`).val()) ?? null,
        description: $(`#erp-form-description-${allocationIdx}`).val() ?? null,
        allocatedAmount: parseFloat($(`#erp-form-allocatedAmount-${allocationIdx}`).val()) ?? null,
        error: parseFloat($(`#erp-form-error-${allocationIdx}`).val()) ?? null,
      };
    }

    removeAllocation(allocationIdx) {
      if (!confirm('Сигурни ли сте, че искате да премахнете това разпределение?')) {
        return;
      }

      $(`#allocation-${allocationIdx}`).fadeOut(() => {
        $(`#allocation-${allocationIdx}`).remove();

        if (this.allocationsData[allocationIdx]) {
          delete this.allocationsData[allocationIdx];
        }

        this.checkForAmountAlerts();
      });
    }

    putLeftAmount(allocationIdx) {
      const leftAmount = this.getLeftAmount() + this.allocationsData[allocationIdx].allocatedAmount;
      $(`#erp-form-allocatedAmount-${allocationIdx}`)
        .val(parseFloat(leftAmount.toFixed(2)))
        .change();

      this.checkForAmountAlerts();
    }

    addAllocation(allocation) {
      const allocationIdx = this.allocationIdx++;

      if (typeof allocation !== 'object') {
        allocation = {};
      }

      allocation = {
        id: allocation.id ?? null,
        documentId: allocation.documentId ?? null,
        description: allocation.description ?? null,
        allocatedAmount: allocation.allocatedAmount ? parseFloat(allocation.allocatedAmount) : null,
        error: allocation.error ?? null,
        attribute: allocation.attribute ?? '',
      };

      this.allocationsData[allocationIdx] = allocation;

      const $html = $(`
        <div class="row g-2 border-1 border-bottom border-dashed pb-4 mb-3 position-relative" id="allocation-${allocationIdx}" ${allocation.attribute}>
          <input type="hidden" name="allocations[${allocationIdx}][id]" value="${allocation.id ?? ''}" />

          <div class="col-6 col-md">
            <label class="form-label" for="erp-form-documentId-${allocationIdx}">
              Документ
            </label>
            <select class="form-select" id="erp-form-documentId-${allocationIdx}" name="allocations[${allocationIdx}][documentId]">
              <option value="">Избор на документ</option>
            </select>
          </div>

          <div class="col-6 col-md">
            <label class="form-label required" for="erp-form-description-${allocationIdx}">
              Описание
            </label>
            <input type="text" class="form-control" id="erp-form-description-${allocationIdx}" name="allocations[${allocationIdx}][description]" value="${allocation.description ?? ''}" placeholder="Плащане на разход..." required/>
          </div>

          <div class="col">
            <label class="form-label required" for="erp-form-allocatedAmount-${allocationIdx}">
              Сума
            </label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control" id="erp-form-allocatedAmount-${allocationIdx}" name="allocations[${allocationIdx}][allocatedAmount]" value="${allocation.allocatedAmount ?? 0}" placeholder="160..." required/>
            </div>
          </div>

          <div class="col-auto align-content-end">
            <button type="button" class="btn btn-phoenix-secondary bg-body-emphasis bg-body-hover" data-bs-toggle="tooltip" data-bs-title="Въведи оставащата сума" onclick="erpIncome.putLeftAmount(${allocationIdx})">
              <i class="fa-regular fa-circle-arrow-down-left"></i>
            </button>
            <button type="button" class="btn btn-phoenix-secondary bg-body-emphasis bg-body-hover" data-bs-toggle="tooltip" data-bs-title="Изтрий това разпределение" onclick="erpIncome.removeAllocation(${allocationIdx})">
              <i class="fa-regular fa-trash text-danger"></i>
            </button>
          </div>

          ${allocation.error ? `<div class="text-warning fw-bold fs-9 mt-1">${allocation.error}</div>` : ''}
          <div class="text-warning fw-bold fs-9 mt-1" id="erp-form-alert-${allocationIdx}" style="display: none;"></div>
        </div>
      `);

      $html
        .hide()
        .appendTo('#js-allocations')
        .fadeIn();

      // Add temporary option with current documentId
      if (allocation.documentId) {
        const $select = $html.find('select[name*="documentId]')
        $select.append(`<option selected data-tmp value="${allocation.documentId}">ID: ${allocation.documentId}...</option>`);
      }

      // Tooltip init
      $html.find('[data-bs-toggle="tooltip"]').each(function () {
        new bootstrap.Tooltip(this);
      });

      // Onchange
      const that = this;
      $html.find('input,select,textarea').each(function () {
        $(this).change(function () {
          that.refillData(allocationIdx);
          that.checkForAmountAlerts();
        });
      });

      // Set description
      $html.find('select[name*="documentId]').change(function () {
        const documentId = parseInt($(this).val());
        let setDescription = '';

        that.customerDocuments?.forEach(doc => {
          if (doc.id === documentId) {
            setDescription = `Плащане по ${doc?.additionals?.typeTitle} #${doc?.documentNumber}`;
          }
        });

        $(`#erp-form-description-${allocationIdx}`)
          .val(setDescription)
          .change();
      });

      // Calculations
      const $allocatedAmount = $html.find(`#erp-form-allocatedAmount-${allocationIdx}`);
      $allocatedAmount.on('change keyup', this.checkForAmountAlerts.bind(this));

      // Refresh
      this.checkForAmountAlerts();

      // Append options
      if (this.customerDocuments?.length) {
        this.buildDocumentsOptions();
      }
    }

    checkForAmountAlerts() {
      if (this._checkForAmountAlertsTimer) {
        clearTimeout(this._checkForAmountAlertsTimer);
      }

      this._checkForAmountAlertsTimer = setTimeout(() => {
        // Total amount
        {
          const leftAMount = parseFloat(this.getLeftAmount().toFixed(2));
          const $allocationWarning = $('#js-allocation-warning');
          const $allocationWarningText = $('#js-allocation-warning-text');

          if (leftAMount > 0) {
            $allocationWarning.fadeIn();
            $allocationWarningText.html(`Трябва да разпределите оставащите ${leftAMount} {{ dbConfig('currency:symbol') }}`);
          } else if (leftAMount < 0) {
            $allocationWarning.fadeIn();
            $allocationWarningText.html(`Надвиши ли сте разпределението на средства с ${Math.abs(leftAMount)} {{ dbConfig('currency:symbol') }}`);
          } else {
            $allocationWarningText.html('');
            $allocationWarning.fadeOut();
          }
        }
      }, 300);
    }

    getLeftAmount() {
      const paidAmount = parseFloat($('#f-paidAmount').val()) || 0;
      let totalAllocatedAmount = 0;
      $('[id^="erp-form-allocatedAmount-"]').each(function () {
        totalAllocatedAmount += parseFloat($(this).val()) || 0;
      });

      return paidAmount - totalAllocatedAmount;
    }

    loadCustomer(customerId) {
      this.customerDocuments = null;

      $.ajax({
        url: `{{ url('/erp/documents') }}?filter[customerId]=${customerId}&page=all`,
        dataType: 'json',
        success: (response) => {
          this.customerDocuments = response?.documents?.data.filter(doc => !!doc.additionals.isPayable) || [];
          this.buildDocumentsOptions();
          this.checkForAmountAlerts();
        }
      });
    }

    buildDocumentsOptions() {
      if (this.customerDocuments) {
        // Map documents
        const docsMap = {};
        this.customerDocuments.forEach(doc => {
          docsMap[doc.id] = doc;
        });

        // Get all selects
        const that = this;
        let removedCount = 0;
        $('[id^="erp-form-documentId-"]').each(function () {
          const $select = $(this);
          const idx = $select.attr('id').replace('erp-form-documentId-', '');
          const selectValue = parseInt($select.val());

          // Remove non match options
          {
            $select.find('option').each(function () {
              const $option = $(this);

              if ($option.val() && !docsMap[$option.val()]) {
                $option.remove();

                Object.entries(erpIncome.allocationsData).forEach(([idxRm, value]) => {
                  if (value.id === parseInt($option.val())) {
                    erpIncome.removeAllocation(idxRm);
                    removedCount++;
                  }
                });
              }
            });
          }

          // Add non added options
          {
            // Remove tmp option
            $select.find('option[data-tmp]').remove();

            // Check what is not added
            const addList = {...docsMap};
            $select.find('option').each(function () {
              const $option = $(this);

              if (docsMap[$option.val()]) {
                delete addList[$option.val()];
              }
            });

            Object.values(addList).forEach(doc => {
              const $option = $(`<option value="${doc.id}">${doc.additionals.typeTitle} #${doc.documentNumber} / ${doc.additionals.price}</option>`);
              $select.append($option);
            });

            if (selectValue) {
              $select.val(selectValue);

              // Fill empty description
              if (!that.allocationsData?.[idx]?.description) {
                const doc = addList?.[selectValue];

                $(`#erp-form-description-${idx}`)
                  .val(`Плащане по ${doc?.additionals?.typeTitle} #${doc?.documentNumber}`)
                  .change();
              }
              if (!that.allocationsData?.[idx]?.allocatedAmount) {
                that.putLeftAmount(idx);
              }
            }
          }
        });

        // Alert for removed
        if (removedCount) {
          alert(`Бяха премахнати ${removedCount} документа, които не са свързани с текущия клиент!`);
        }
      }
    }
  }

  const erpIncome = new ErpIncome();
  window.erpIncome = erpIncome;

  const errors = @json($errors);
  const allocations = @json($allocations);
  allocations.forEach((allocation, allocationIdx) => {
    allocation.error = '';
    for (const [key, value] of Object.entries(errors)) {
      if (key.startsWith(`allocations.${allocationIdx}.`)) {
        allocation.error += value.join('; ') + '; ';
      }
    }

    erpIncome.addAllocation(allocation)
  });

  // Add at least one item
  if (!Object.keys(erpIncome.allocationsData).length) {
    erpIncome.addAllocation();
  }
</script>
