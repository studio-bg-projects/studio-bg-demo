<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Данни за демота</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-demoNumber">Номер на демо</label>
        <input type="text" class="form-control @if($errors->has('demoNumber')) is-invalid @endif" id="f-demoNumber" name="demoNumber" value="{{ $demo->demoNumber }}" placeholder="OFR-2025-00012..." required/>
        @if($errors->has('demoNumber'))
          <div class="invalid-feedback">
            {{ $errors->first('demoNumber') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-status">Статус</label>
        <select class="form-select @if($errors->has('status')) is-invalid @endif" id="f-status" name="status">
          @foreach (App\Enums\DemoStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ $demo->status && $demo->status->value == $status->value ? 'selected' : '' }}>
              {{ \App\Services\MapService::demoStatuses($status)->label }}
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
        <label class="app-form-label" for="f-addedDate">Валидна до</label>
        <input type="date" class="form-control @if($errors->has('addedDate')) is-invalid @endif" id="f-addedDate" name="addedDate" value="{{ $demo->addedDate }}" placeholder="2020-01-15..."/>
        @if($errors->has('addedDate'))
          <div class="invalid-feedback">
            {{ $errors->first('addedDate') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-addedDate');
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
            <option value="{{ $row->id }}" {{ $demo->customerId == $row->id ? 'selected' : '' }} data-client='@json($row)'>
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
                console.log('customerData', customerData);
              });
          });
        </script>
      </div>

      <div class="col-12 col-xl-12">
        <label class="app-form-label" for="f-companyName">Име на фирмата</label>
        <input type="text" class="form-control @if($errors->has('companyName')) is-invalid @endif" id="f-companyName" name="companyName" value="{{ $demo->companyName }}" placeholder="Фирма ЕООД..."/>
        @if($errors->has('companyName'))
          <div class="invalid-feedback">
            {{ $errors->first('companyName') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Бележки</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPrivate">Вътрешна бележка</label>
        <textarea type="text" class="form-control @if($errors->has('notesPrivate')) is-invalid @endif" id="f-notesPrivate" name="notesPrivate" rows="4" placeholder="...">{{ $demo->notesPrivate }}</textarea>
        @if($errors->has('notesPrivate'))
          <div class="invalid-feedback">
            {{ $errors->first('notesPrivate') }}
          </div>
        @endif
        <p class="text-body-tertiary fs-9 fw-semibold mb-0 mt-1">Този текст ще бъде достъпен само от операторите на системата.</p>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-notesPublic">Бележка към клиента</label>
        <textarea type="text" class="form-control @if($errors->has('notesPublic')) is-invalid @endif" id="f-notesPublic" name="notesPublic" rows="4" placeholder="...">{{ $demo->notesPublic }}</textarea>
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

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Прикачване на документ</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::Demo->value,
      'groupId' => $demo->fileGroupId,
      'fieldName' => 'fileGroupId',
    ])
  </div>
</div>
