@if ($address->isDeleted)
  <div class="alert alert-subtle-danger mb-5" role="alert">Този адрес е маркиран като изтрит и при синхронизация ще бъде премахнат от ERP системата и онлайн магазина.</div>
@endif

<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-firstName">Име</label>
        <input type="text" class="form-control @if($errors->has('firstName')) is-invalid @endif" id="f-firstName" name="firstName" value="{{ $address->firstName }}" placeholder="Петър..." required/>
        @if($errors->has('firstName'))
          <div class="invalid-feedback">
            {{ $errors->first('firstName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-lastName">Фамилия</label>
        <input type="text" class="form-control @if($errors->has('lastName')) is-invalid @endif" id="f-lastName" name="lastName" value="{{ $address->lastName }}" placeholder="Петров..." required/>
        @if($errors->has('lastName'))
          <div class="invalid-feedback">
            {{ $errors->first('lastName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-companyName">Фирма</label>
        <input type="text" class="form-control @if($errors->has('companyName')) is-invalid @endif" id="f-companyName" name="companyName" value="{{ $address->companyName }}" placeholder="Фирма ЕООД..." required/>
        @if($errors->has('companyName'))
          <div class="invalid-feedback">
            {{ $errors->first('companyName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-zipCode">ПК/ZIP</label>
        <input type="text" class="form-control @if($errors->has('zipCode')) is-invalid @endif" id="f-zipCode" name="zipCode" value="{{ $address->zipCode }}" placeholder="1234..."/>
        @if($errors->has('zipCode'))
          <div class="invalid-feedback">
            {{ $errors->first('zipCode') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label required" for="f-countryId">Държава</label>
        <select class="form-select @if($errors->has('countryId')) is-invalid @endif" id="f-countryId" name="countryId" required>
          <option value="">--- Изберете държава ---</option>
          @foreach ($countries as $row)
            <option value="{{ $row->id }}" {{ $address->countryId == $row->id ? 'selected' : '' }}>
              {{ $row->name }}
              [{{ $row->isoCode3 }}]
            </option>
          @endforeach
        </select>
        @if($errors->has('countryId'))
          <div class="text-danger fs-9 mt-n3">
            {{ $errors->first('countryId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <div class="position-relative z-2">
          <label class="app-form-label required" for="f-city-search">Град</label>
          <input type="text" class="form-control @if($errors->has('city')) is-invalid @endif" data-kind="site" id="f-city-search" name="auto-complete-off" value="{{ $address->city }}" placeholder="София..." autocomplete="off" required/>
          @if($errors->has('city'))
            <div class="invalid-feedback">
              {{ $errors->first('city') }}
            </div>
          @endif

          <input type="hidden" id="f-city" name="city" value="{{ $address->city }}"/>
          <input type="hidden" id="f-city-speedy-id" name="citySpeedyId" value="{{ $address->citySpeedyId }}"/>

          <ul id="js-city-list" class="position-absolute w-100 gap-0 bg-body list-unstyled fs-9" style="max-height: 10rem; overflow-y: scroll; box-shadow: 0 0 3px rgba(0, 0, 0, .2); margin-top: 3px; border-radius: 3px;"></ul>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="position-relative z-1">
          <label class="app-form-label required" for="f-street-search">Ул.</label>
          <input type="text" class="form-control @if($errors->has('street')) is-invalid @endif" data-kind="street" id="f-street-search" name="auto-complete-off" value="{{ $address->street }}" placeholder="Александър Стамболийски..." autocomplete="off" required/>
          @if($errors->has('street'))
            <div class="invalid-feedback">
              {{ $errors->first('street') }}
            </div>
          @endif

          <input type="hidden" id="f-street" name="street" value="{{ $address->street }}"/>
          <input type="hidden" id="f-street-speedy-id" name="streetSpeedyId" value="{{ $address->streetSpeedyId }}"/>

          <ul id="js-street-list" class="position-absolute w-100 gap-0 bg-body list-unstyled fs-9" style="max-height: 10rem; overflow-y: scroll; box-shadow: 0 0 3px rgba(0, 0, 0, .2); margin-top: 3px; border-radius: 3px;"></ul>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="row">
          <div class="col">
            <label class="app-form-label" for="f-streetNo">Ул. №</label>
            <input type="text" class="form-control @if($errors->has('streetNo')) is-invalid @endif" id="f-streetNo" name="streetNo" value="{{ $address->streetNo }}" placeholder="1234..."/>
            @if($errors->has('streetNo'))
              <div class="invalid-feedback">
                {{ $errors->first('streetNo') }}
              </div>
            @endif
          </div>

          <div class="col">
            <label class="app-form-label" for="f-blockNo">Бл.</label>
            <input type="text" class="form-control @if($errors->has('blockNo')) is-invalid @endif" id="f-blockNo" name="blockNo" value="{{ $address->blockNo }}" placeholder="1234..."/>
            @if($errors->has('blockNo'))
              <div class="invalid-feedback">
                {{ $errors->first('blockNo') }}
              </div>
            @endif
          </div>

          <div class="col">
            <label class="app-form-label" for="f-entranceNo">Вх.</label>
            <input type="text" class="form-control @if($errors->has('entranceNo')) is-invalid @endif" id="f-entranceNo" name="entranceNo" value="{{ $address->entranceNo }}" placeholder="1234..."/>
            @if($errors->has('entranceNo'))
              <div class="invalid-feedback">
                {{ $errors->first('entranceNo') }}
              </div>
            @endif
          </div>

          <div class="col">
            <label class="app-form-label" for="f-floor">Ет.</label>
            <input type="text" class="form-control @if($errors->has('floor')) is-invalid @endif" id="f-floor" name="floor" value="{{ $address->floor }}" placeholder="3..."/>
            @if($errors->has('floor'))
              <div class="invalid-feedback">
                {{ $errors->first('floor') }}
              </div>
            @endif
          </div>

          <div class="col">
            <label class="app-form-label" for="f-apartmentNo">Ап.</label>
            <input type="text" class="form-control @if($errors->has('apartmentNo')) is-invalid @endif" id="f-apartmentNo" name="apartmentNo" value="{{ $address->apartmentNo }}" placeholder="12..."/>
            @if($errors->has('apartmentNo'))
              <div class="invalid-feedback">
                {{ $errors->first('apartmentNo') }}
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-addressDetails">Уточнения към адреса</label>
        <input type="text" class="form-control @if($errors->has('addressDetails')) is-invalid @endif" id="f-addressDetails" name="addressDetails" value="{{ $address->addressDetails }}" placeholder="Входът е зад сградата..."/>
        @if($errors->has('addressDetails'))
          <div class="invalid-feedback">
            {{ $errors->first('addressDetails') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-phone">Телефон</label>
        <input type="text" class="form-control @if($errors->has('phone')) is-invalid @endif" id="f-phone" name="phone" value="{{ $address->phone }}" placeholder="+1 1234 1234..."/>
        @if($errors->has('phone'))
          <div class="invalid-feedback">
            {{ $errors->first('phone') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-email">Имейл</label>
        <input type="text" class="form-control @if($errors->has('email')) is-invalid @endif" id="f-email" name="email" value="{{ $address->email }}" placeholder="delivery@email.com..."/>
        @if($errors->has('email'))
          <div class="invalid-feedback">
            {{ $errors->first('email') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-operatingHours">Работно време склад</label>
        <input type="text" class="form-control @if($errors->has('operatingHours')) is-invalid @endif" id="f-operatingHours" name="operatingHours" value="{{ $address->operatingHours }}" placeholder="9h до 18h..."/>
        @if($errors->has('operatingHours'))
          <div class="invalid-feedback">
            {{ $errors->first('operatingHours') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<script type="module">
  let timer = null;
  let show;

  // Autocomplete and fill
  $('#f-city-search, #f-street-search').on('change keyup focus blur', function (e) {
    show = true;

    const kind = $(this).data('kind');
    const isBulgaria = $('#f-countryId').val() === '33';

    let $list;
    let $inputSearch;
    let $inputTitle;
    let $inputSpeedyId;
    let $resetFields;

    if (kind === 'site') {
      $list = $('#js-city-list');
      $inputSearch = $('#f-city-search');
      $inputTitle = $('#f-city');
      $inputSpeedyId = $('#f-city-speedy-id');
      $resetFields = $inputSpeedyId;

      // Street fields
      $resetFields = $resetFields.add($('#f-street-search'));
      $resetFields = $resetFields.add($('#f-street'));
      $resetFields = $resetFields.add($('#f-street-speedy-id'));
    } else if (kind === 'street') {
      $list = $('#js-street-list');
      $inputSearch = $('#f-street-search');
      $inputTitle = $('#f-street');
      $inputSpeedyId = $('#f-street-speedy-id');
      $resetFields = $inputSpeedyId;
    } else {
      throw new Error(`Unknown kind: ${kind}`);
    }

    console.log('isBulgaria', isBulgaria);

    // Clear & fill for non Bg city
    if (e.type === 'change' || e.type === 'keyup') {
      if (!isBulgaria) {
        $inputTitle.val($inputSearch.val());
      } else {
        $inputTitle.val('');
      }

      $resetFields.val('');
    }

    if (timer) {
      clearTimeout(timer);
    }

    if (!isBulgaria) {
      show = false;
    }

    if (e.type === 'blur') {
      show = false;
    }

    timer = setTimeout(() => {
      const speedyCountryId = 100;
      const speedySiteId = $('#f-city-speedy-id').val();

      if (!show) {
        $list.hide();
        return;
      }

      $.ajax({
        url: '{{ url('/erp/shipments/speedy/search/') }}',
        data: {
          kind: kind,
          q: $inputSearch.val(),
          countryId: speedyCountryId,
          siteId: speedySiteId,
        },
        method: 'GET',
        dataType: 'json',
        success: (rs) => {
          $list.html('');

          if (!show) {
            return;
          }

          if (!rs?.data?.length) {
            $list.html(`
              <li class="p-3 m-0">
                Няма намерени резултати!
              </li>
            `);
          } else {
            rs?.data?.forEach(row => {
              let $node = $(`
                <li class="p-0 m-0">
                  <a href="#!" class="d-block px-3 py-1 fs-sm fw-medium text-truncate animate-underline">
                    <span class="animate-target">
                        ${row.title}
                        ${kind === 'site' ? `<i class="text-muted">(${row?.item?.municipality} / ${row?.item?.region})</i>` : ''}
                    </span>
                  </a>
                </li>
              `);

              $node.click(function (e) {
                e.preventDefault();

                $inputSearch.val(row?.title);
                $inputTitle.val(row?.title);
                $inputSpeedyId.val(row?.id);

                if (row?.item?.postCode) {
                  $('#f-postcode').val(row?.item?.postCode);
                }
              });

              $list.append($node);
            });
          }

          $list.fadeIn();
        }
      });
    }, 250);
  });
</script>
