@php($documentPrefix = \App\Services\MapService::documentTypes($document->type)->prefix)
<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за документа</h2>
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-documentNumber">Уникален номер на документа</label>
        <input @if($document->id) disabled @endif type="text" class="form-control @if($errors->has('documentNumber')) is-invalid @endif" id="f-documentNumber" name="documentNumber" value="{{ $document->documentNumber }}" placeholder="{{ $documentPrefix }}-123..." required/>
        @if($errors->has('documentNumber'))
          <div class="invalid-feedback">
            {{ $errors->first('documentNumber') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="form-label required" for="f-isForeignInvoice">Международна фактура</label>
        <select class="form-select @if($errors->has('isForeignInvoice')) is-invalid @endif" id="f-isForeignInvoice" name="isForeignInvoice" required>
          <option value="0" @if(!$document->isForeignInvoice) selected @endif>Фактурата е за Българаия</option>
          <option value="1" @if($document->isForeignInvoice) selected @endif>Междунардона без ДДС</option>
        </select>
        @if($errors->has('isForeignInvoice'))
          <div class="invalid-feedback">
            {{ $errors->first('isForeignInvoice') }}
          </div>
        @endif

        <script type="module">
          $(function () {
            const $isForeignInvoice = $('#f-isForeignInvoice');
            const $issuerIban = $('#f-issuerIban');
            const $incomeCommentBg = $('#f-incomeCommentBg');
            const $incomeCommentEn = $('#f-incomeCommentEn');
            const $documentNumber = $('#f-documentNumber');

            const msgBg = 'Фактурата е освободена от ДДС поради доставка извън България съгласно чл. 86 от ЗДДС.';
            const msgEn = 'The invoice is exempt from VAT due to delivery outside Bulgaria in accordance with Article 86 of the VAT Act.';
            const ibanWorld = '{{ dbConfig('default:issuerIbanWorld') }}';
            const ibanLocal = '{{ dbConfig('default:issuerIbanLocal') }}';
            const refDocumentNumber = @json($refDocument?->documentNumber);
            const documentType = @json($document->type->value);
            const documentPrefix = @json($documentPrefix);
            const isEdit = @json((bool)$document->id);

            function updateDocumentNumber() {
              let setDocumentNumber = documentPrefix + '-';
              if (documentType === 'invoice') {
                const isForeign = $isForeignInvoice.val() === '1';
                const seq = isForeign ? @json(dbConfig('document:seq:world')) : @json(dbConfig('document:seq:bg'));
                setDocumentNumber = documentPrefix + '-' + seq;
              } else if (documentType === 'proformaInvoice') {
                setDocumentNumber = documentPrefix + '-' + @json(dbConfig('document:seq:proforma'));
              } else if (refDocumentNumber) {
                const relatedNumber = refDocumentNumber.includes('-') ? refDocumentNumber.split('-').slice(1).join('-') : refDocumentNumber;
                setDocumentNumber = documentPrefix + '-' + relatedNumber;
              }

              $documentNumber.val(setDocumentNumber);
            }

            if (!isEdit) {
              $isForeignInvoice.on('change', function () {
                const isForeignInvoice = $(this).val() === '1';

                if (isForeignInvoice) {
                  $incomeCommentBg.val(msgBg);
                  $incomeCommentEn.val(msgEn);

                  if ($issuerIban.val() === ibanLocal || !$issuerIban.val()) {
                    $issuerIban.val(ibanWorld);
                  }
                } else {
                  if ($incomeCommentBg.val() === msgBg) {
                    $incomeCommentBg.val('');
                  }

                  if ($incomeCommentEn.val() === msgEn) {
                    $incomeCommentEn.val('');
                  }

                  if ($issuerIban.val() === ibanWorld || !$issuerIban.val()) {
                    $issuerIban.val(ibanLocal);
                  }
                }

                updateDocumentNumber();
              }).change();

              updateDocumentNumber();
            }
          });
        </script>
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-issueDate">Дата на издаване</label>
        <input type="date" class="form-control @if($errors->has('issueDate')) is-invalid @endif" id="f-issueDate" name="issueDate" value="{{ $document->issueDate->format('Y-m-d') ?? $document->issueDate ?? '' }}" placeholder="2020-01-01..." required/>
        @if($errors->has('issueDate'))
          <div class="invalid-feedback">
            {{ $errors->first('issueDate') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-issueDate');
        </script>
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label" for="f-dueDate">Срок за плащане</label>
        <input type="date" class="form-control @if($errors->has('dueDate')) is-invalid @endif" id="f-dueDate" name="dueDate" value="{{ $document->dueDate->format('Y-m-d') ?? $document->dueDate ?? '' }}" placeholder="2020-01-15..."/>
        @if($errors->has('dueDate'))
          <div class="invalid-feedback">
            {{ $errors->first('dueDate') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-dueDate');
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-language">Език на документа</label>
        <select class="form-select @if($errors->has('language')) is-invalid @endif" id="f-language" name="language">
          <option value="bg" {{ $document->language == 'bg' ? 'selected' : '' }}>Български</option>
          <option value="en" {{ $document->language == 'en' ? 'selected' : '' }}>Английски</option>
        </select>
        @if($errors->has('language'))
          <div class="invalid-feedback">
            {{ $errors->first('language') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-incoterms">Incoterms</label>
        <select class="form-select @if($errors->has('incoterms')) is-invalid @endif" id="f-incoterms" name="incoterms" required>
          @foreach (dbConfig('incoterms:list') as $incotermsId => $value)
            <option value="{{ $incotermsId }}" {{ $document->incoterms === $incotermsId ? 'selected' : '' }}>{{ $incotermsId }} - {{ $value->info }}</option>
          @endforeach
        </select>
        @if($errors->has('incoterms'))
          <div class="invalid-feedback">
            {{ $errors->first('incoterms') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-salesRepresentativeId">Търговец издал фактурата</label>
        <select class="form-select @if($errors->has('salesRepresentativeId')) is-invalid @endif" id="f-salesRepresentativeId" name="salesRepresentativeId">
          <option value="">-</option>
          @foreach ($salesRepresentatives as $row)
            <option value="{{ $row->id }}" {{ $document->salesRepresentativeId == $row->id ? 'selected' : '' }}>
              {{ $row->nameBg }} ({{ $row->titleBg }})
            </option>
          @endforeach
        </select>
        @if($errors->has('salesRepresentativeId'))
          <div class="invalid-feedback">
            {{ $errors->first('salesRepresentativeId') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Получател</h2>
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-recipientName">Име &mdash; Получател</label>
        <input type="text" class="form-control @if($errors->has('recipientName')) is-invalid @endif" id="f-recipientName" name="recipientName" value="{{ $document->recipientName }}" placeholder="Фирма ЕООД..." required/>
        @if($errors->has('recipientName'))
          <div class="invalid-feedback">
            {{ $errors->first('recipientName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-recipientCompanyId">ЕИК &mdash; Получател</label>
        <input type="text" class="form-control @if($errors->has('recipientCompanyId')) is-invalid @endif" id="f-recipientCompanyId" name="recipientCompanyId" value="{{ $document->recipientCompanyId }}" placeholder="123456789..." required/>
        @if($errors->has('recipientCompanyId'))
          <div class="invalid-feedback">
            {{ $errors->first('recipientCompanyId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label" for="f-recipientVatId">ДДС номер &mdash; Получател</label>
        <input type="text" class="form-control @if($errors->has('recipientVatId')) is-invalid @endif" id="f-recipientVatId" name="recipientVatId" value="{{ $document->recipientVatId }}" placeholder="BG123456789..."/>
        @if($errors->has('recipientVatId'))
          <div class="invalid-feedback">
            {{ $errors->first('recipientVatId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-recipientAddress">Адрес &mdash; Получател</label>
        <input type="text" class="form-control @if($errors->has('recipientAddress')) is-invalid @endif" id="f-recipientAddress" name="recipientAddress" value="{{ $document->recipientAddress }}" placeholder="София, Васил Левски 12..." required/>
        @if($errors->has('recipientAddress'))
          <div class="invalid-feedback">
            {{ $errors->first('recipientAddress') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Доставка до / Ship To</h2>
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-shipToName">Име &mdash; Доставка до</label>
        <input type="text" class="form-control @if($errors->has('shipToName')) is-invalid @endif" id="f-shipToName" name="shipToName" value="{{ $document->shipToName }}" placeholder="Фирма ЕООД..." required/>
        @if($errors->has('shipToName'))
          <div class="invalid-feedback">
            {{ $errors->first('shipToName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-shipToCompanyId">ЕИК &mdash; Доставка до</label>
        <input type="text" class="form-control @if($errors->has('shipToCompanyId')) is-invalid @endif" id="f-shipToCompanyId" name="shipToCompanyId" value="{{ $document->shipToCompanyId }}" placeholder="123456789..." required/>
        @if($errors->has('shipToCompanyId'))
          <div class="invalid-feedback">
            {{ $errors->first('shipToCompanyId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-shipToVatId">ДДС номер &mdash; Доставка до</label>
        <input type="text" class="form-control @if($errors->has('shipToVatId')) is-invalid @endif" id="f-shipToVatId" name="shipToVatId" value="{{ $document->shipToVatId }}" placeholder="BG123456789..." required/>
        @if($errors->has('shipToVatId'))
          <div class="invalid-feedback">
            {{ $errors->first('shipToVatId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-3">
        <label class="app-form-label required" for="f-shipToAddress">Адрес &mdash; Доставка до</label>
        <input type="text" class="form-control @if($errors->has('shipToAddress')) is-invalid @endif" id="f-shipToAddress" name="shipToAddress" value="{{ $document->shipToAddress }}" placeholder="София, Васил Левски 12..." required/>
        @if($errors->has('shipToAddress'))
          <div class="invalid-feedback">
            {{ $errors->first('shipToAddress') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <hr/>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-issuer-info">
          + Покажи данните за издателя
        </button>

        <script type="module">
          $(function () {
            const $btn = $('#toggle-issuer-info');
            const $info = $('#issuer-info');
            $btn.on('click', function () {
              $info.toggleClass('d-none');
              $btn.text($info.hasClass('d-none') ? '+ Покажи данните за издателя' : '- Скрии данните за издателя');
            });
          });
        </script>
      </div>

      <div id="issuer-info" class="col-12 d-none">
        <div class="row gy-2">
          <div class="col-12">
            <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Издател</h2>
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-issuerNameBg">Име на издателя [BG]</label>
            <input type="text" class="form-control @if($errors->has('issuerNameBg')) is-invalid @endif" id="f-issuerNameBg" name="issuerNameBg" value="{{ $document->issuerNameBg }}" placeholder="Инсайд Трейдинг ЕООД..." required/>
            @if($errors->has('issuerNameBg'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerNameBg') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-issuerNameEn">Име на издателя [EN]</label>
            <input type="text" class="form-control @if($errors->has('issuerNameEn')) is-invalid @endif" id="f-issuerNameEn" name="issuerNameEn" value="{{ $document->issuerNameEn }}" placeholder="Inside Trading Bulgaria LTD..." required/>
            @if($errors->has('issuerNameEn'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerNameEn') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-issuerCompanyId">ЕИК на издателя</label>
            <input type="text" class="form-control @if($errors->has('issuerCompanyId')) is-invalid @endif" id="f-issuerCompanyId" name="issuerCompanyId" value="{{ $document->issuerCompanyId }}" placeholder="123456789..." required/>
            @if($errors->has('issuerCompanyId'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerCompanyId') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label" for="f-issuerVatId">ДДС номер на издателя</label>
            <input type="text" class="form-control @if($errors->has('issuerVatId')) is-invalid @endif" id="f-issuerVatId" name="issuerVatId" value="{{ $document->issuerVatId }}" placeholder="BG123456789..."/>
            @if($errors->has('issuerVatId'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerVatId') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-issuerAddressBg">Адрес на издателя [BG]</label>
            <input type="text" class="form-control @if($errors->has('issuerAddressBg')) is-invalid @endif" id="f-issuerAddressBg" name="issuerAddressBg" value="{{ $document->issuerAddressBg }}" placeholder="София, Васил Левски 12..." required/>
            @if($errors->has('issuerAddressBg'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerAddressBg') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-issuerAddressEn">Адрес на издателя [EN]</label>
            <input type="text" class="form-control @if($errors->has('issuerAddressEn')) is-invalid @endif" id="f-issuerAddressEn" name="issuerAddressEn" value="{{ $document->issuerAddressEn }}" placeholder="Sofia, Vasil Levski 12..." required/>
            @if($errors->has('issuerAddressEn'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerAddressEn') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label" for="f-incomeMethodBg">Метод на плащане [BG]</label>
            <input type="text" class="form-control @if($errors->has('incomeMethodBg')) is-invalid @endif" id="f-incomeMethodBg" name="incomeMethodBg" value="{{ $document->incomeMethodBg }}" placeholder="Банков превод, Кредитна карта..."/>
            @if($errors->has('incomeMethodBg'))
              <div class="invalid-feedback">
                {{ $errors->first('incomeMethodBg') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label" for="f-incomeMethodEn">Метод на плащане [EN]</label>
            <input type="text" class="form-control @if($errors->has('incomeMethodEn')) is-invalid @endif" id="f-incomeMethodEn" name="incomeMethodEn" value="{{ $document->incomeMethodEn }}" placeholder="Bank Transfer, Credit Card..."/>
            @if($errors->has('incomeMethodEn'))
              <div class="invalid-feedback">
                {{ $errors->first('incomeMethodEn') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerBankNameBg">Банка на издателя [BG]</label>
            <input type="text" class="form-control @if($errors->has('issuerBankNameBg')) is-invalid @endif" id="f-issuerBankNameBg" name="issuerBankNameBg" value="{{ $document->issuerBankNameBg }}" placeholder="Уникредит Булбанк..." required/>
            @if($errors->has('issuerBankNameBg'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerBankNameBg') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerBankNameEn">Банка на издателя [EN]</label>
            <input type="text" class="form-control @if($errors->has('issuerBankNameEn')) is-invalid @endif" id="f-issuerBankNameEn" name="issuerBankNameEn" value="{{ $document->issuerBankNameEn }}" placeholder="Unicredit Bulbank..." required/>
            @if($errors->has('issuerBankNameEn'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerBankNameEn') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerIBankAddressBg">Адрес - банка на издателя [BG]</label>
            <input type="text" class="form-control @if($errors->has('issuerIBankAddressBg')) is-invalid @endif" id="f-issuerIBankAddressBg" name="issuerIBankAddressBg" value="{{ $document->issuerIBankAddressBg }}" placeholder="София, Васил Левски 12..." required/>
            @if($errors->has('issuerIBankAddressBg'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerIBankAddressBg') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerIBankAddressEn">Адрес - банка на издателя [EN]</label>
            <input type="text" class="form-control @if($errors->has('issuerIBankAddressEn')) is-invalid @endif" id="f-issuerIBankAddressEn" name="issuerIBankAddressEn" value="{{ $document->issuerIBankAddressEn }}" placeholder="Sofia, Vasil Levski 12..." required/>
            @if($errors->has('issuerIBankAddressEn'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerIBankAddressEn') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerIban">IBAN на издателя</label>
            <input type="text" class="form-control @if($errors->has('issuerIban')) is-invalid @endif" id="f-issuerIban" name="issuerIban" value="{{ $document->issuerIban }}" placeholder="BG12UBBS123456789..." required/>
            @if($errors->has('issuerIban'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerIban') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-3">
            <label class="app-form-label required" for="f-issuerSwift">SWIFT на издателя</label>
            <input type="text" class="form-control @if($errors->has('issuerSwift')) is-invalid @endif" id="f-issuerSwift" name="issuerSwift" value="{{ $document->issuerSwift }}" placeholder="BG12UBBS123456789..." required/>
            @if($errors->has('issuerSwift'))
              <div class="invalid-feedback">
                {{ $errors->first('issuerSwift') }}
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12">
        <hr/>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-incomeCommentBg">Коментар към плащането [BG]</label>
        <input type="text" class="form-control @if($errors->has('incomeCommentBg')) is-invalid @endif" id="f-incomeCommentBg" name="incomeCommentBg" value="{{ $document->incomeCommentBg }}" placeholder="Добавете коментар за плащането..."/>
        @if($errors->has('incomeCommentBg'))
          <div class="invalid-feedback">
            {{ $errors->first('incomeCommentBg') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-incomeCommentEn">Коментар към плащането [EN]</label>
        <input type="text" class="form-control @if($errors->has('incomeCommentEn')) is-invalid @endif" id="f-incomeCommentEn" name="incomeCommentEn" value="{{ $document->incomeCommentEn }}" placeholder="Add a comment about the income..."/>
        @if($errors->has('incomeCommentEn'))
          <div class="invalid-feedback">
            {{ $errors->first('incomeCommentEn') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('erp.documents.partials.form.' . $document->type->value)
