@extends('layouts.app')

@section('content')
  @include('erp.shipments.speedy.partials.navbar')

  <h1 class="h4 mb-5">Създаване на пратка</h1>

  <form method="post" action="?" class="mb-5" data-disable-on-submit id="js-shipment-form">
    @csrf
    <input type="hidden" name="orderId" value="{{ $order->id ?? null }}"/>
    <input type="hidden" name="customerId" value="{{ $customer->id ?? null }}"/>

    <div class="card">
      <div class="card-body">
        <div class="row gy-2">
          <div class="col-12">
            {{-- no .mt-3 for 1st title --}}
            <h2 class="h5 pb-2 border-bottom border-dashed">Данни за подателя</h2>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-senderClientIdId">Системен клиент (изпращач)</label>
            <select class="form-select @if($errors->has('senderClientIdId')) is-invalid @endif" id="f-senderClientIdId" name="senderClientIdId" required>
              @if(!empty($shipmentData->senderClientIdId))
                <option value="{{ $shipmentData->senderClientIdId }}" selected>{{ $shipmentData->senderClientIdTitle ?? null }}</option>
              @endif
            </select>
            <input type="hidden" id="f-senderClientIdTitle" name="senderClientIdTitle" value="{{ $shipmentData->senderClientIdTitle ?? null }}"/>
            @if($errors->has('senderClientIdId'))
              <div class="text-danger fs-9">
                {{ $errors->first('senderClientIdId') }}
              </div>
            @endif

            <script type="module">
              $(function () {
                window.ajaxSelect($('#f-senderClientIdId'), $('#f-senderClientIdTitle'), '{{ url('/erp/shipments/speedy/search/?kind=clientContract') }}', 'Избери клиент...');
              });
            </script>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-senderEmail">E-mail</label>
            <input type="email" class="form-control @if($errors->has('senderEmail')) is-invalid @endif" id="f-senderEmail" name="senderEmail" value="{{ $shipmentData->senderEmail ?? null }}" placeholder="office@insidetrading.bg..." required/>
            @if($errors->has('senderEmail'))
              <div class="invalid-feedback">
                {{ $errors->first('senderEmail') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-senderPhone1">Телефон</label>
            <input type="text" class="form-control @if($errors->has('senderPhone1')) is-invalid @endif" id="f-senderPhone1" name="senderPhone1" value="{{ $shipmentData->senderPhone1 ?? null }}" placeholder="0888777666..." required/>
            @if($errors->has('senderPhone1'))
              <div class="invalid-feedback">
                {{ $errors->first('senderPhone1') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-senderPhone2">Телефон 2</label>
            <input type="text" class="form-control @if($errors->has('senderPhone2')) is-invalid @endif" id="f-senderPhone2" name="senderPhone2" value="{{ $shipmentData->senderPhone2 ?? null }}" placeholder="0888777666..."/>
            @if($errors->has('senderPhone2'))
              <div class="invalid-feedback">
                {{ $errors->first('senderPhone2') }}
              </div>
            @endif
          </div>

          <div class="col-12">
            <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Данни за получателя</h2>
          </div>

          @if ($order)
            <div class="col-12">
              <div style="display: none;">
                <label class="app-form-label" for="f-recipientInfo">Бързи данни за контакт</label>
                <select class="form-select mb-3" id="f-recipientInfo" name="recipientInfo" data-recipient-selected="{{ $shipmentData->recipientInfo ?? null }}">
                  <option value="">-- Ибзери --</option>
                </select>
              </div>

              <ul style="display: none;" class="fs-9 list-unstyled row gy-3" data-recipient-info-id="order" data-recipient-info-title="Данни от поръчката">
                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Телефон 1:</span>
                  <span data-addr="#f-recipientPhone1">{{ $customer->deliveryInfoTelephone ?: '-' }}</span>
                </li>

                {{-- <li class="h6 col-md-6 col-xxl-3">--}}
                {{--   <span class="text-body-tertiary fw-semibold ms-2">Телефон 2:</span>--}}
                {{--   <span data-addr="#f-recipientPhone2">xxx recipientPhone2</span>--}}
                {{-- </li>--}}

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Фирма получател:</span>
                  <span data-addr="#f-recipientClientName">{{ $order->shopData->order->company ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Лице за контакт:</span>
                  <span data-addr="#f-recipientContactName">{{ $order->shopData->order->shipping_firstname ?? '-' }} {{ $order->shopData->order->shipping_lastname ?? '-' }}</span>
                </li>

                {{-- <li class="h6 col-md-6 col-xxl-3">--}}
                {{--   <span class="text-body-tertiary fw-semibold ms-2">Обект:</span>--}}
                {{--   <span data-addr="#f-recipientObjectName">xxx recipientObjectName</span>--}}
                {{-- </li>--}}

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">E-mail:</span>
                  <span data-addr="#f-recipientEmail">{{ $order->shopData->order->email ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Държава:</span>
                  <span data-addr="#f-recipientCountryTitle">БЪЛГАРИЯ</span>
                  <span data-addr="#f-recipientCountryId" data-title="БЪЛГАРИЯ" style="display: none;">{{ $order->shopData->shipping_address->country_id ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Град:</span>
                  <span data-addr="#f-recipientSiteTitle">{{ $order->shopData->shipping_address->city ?? '-' }}</span>
                  <span data-addr="#f-recipientSiteId" data-title="{{ $order->shopData->shipping_address->city ?? '-' }}" style="display: none;">{{ $order->shopData->shipping_address->city_speedy_id ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Улица:</span>
                  <span data-addr="#f-recipientStreetTitle">{{ $order->shopData->shipping_address->address_1 ?? '-' }}</span>
                  <span data-addr="#f-recipientStreetId" data-title="{{ $order->shopData->shipping_address->address_1 ?? '-' }}" style="display: none;">{{ $order->shopData->shipping_address->street_speedy_id ?? '-' }}</span>
                </li>

                {{-- <li class="h6 col-md-6 col-xxl-3">--}}
                {{--   <span class="text-body-tertiary fw-semibold ms-2">Квартал:</span>--}}
                {{--   <span data-addr="#f-recipientComplexId">xxx recipientComplexId</span>--}}
                {{-- </li>--}}

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Ул. №:</span>
                  <span data-addr="#f-recipientStreetNo">{{ $order->shopData->shipping_address->street_no ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Бл.:</span>
                  <span data-addr="#f-recipientBlockNo">{{ $order->shopData->shipping_address->block_no ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Вх.:</span>
                  <span data-addr="#f-recipientEntranceNo">{{ $order->shopData->shipping_address->entrance_no ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Ет.:</span>
                  <span data-addr="#f-recipientFloorNo">{{ $order->shopData->shipping_address->floor ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Ап.:</span>
                  <span data-addr="#f-recipientApartmentNo">{{ $order->shopData->shipping_address->apartment_no ?? '-' }}</span>
                </li>

                {{-- <li class="h6 col-md-6 col-xxl-3">--}}
                {{--   <span class="text-body-tertiary fw-semibold ms-2">Офис/Автомат.:</span>--}}
                {{--   <span data-addr="#f-recipientOfficeId">xxx recipientOfficeId</span>--}}
                {{-- </li>--}}

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">ПК/ZIP:</span>
                  <span>{{ $order->shopData->order->shipping_postcode ?? '-' }}</span>
                </li>

                <li class="h6 col-md-6 col-xxl-3">
                  <span class="text-body-tertiary fw-semibold ms-2">Уточнение към адреса:</span>
                  <span>{{ $order->shopData->order->shipping_address_2 ?? '-' }}</span>
                </li>
              </ul>

              <a href="#!" class="btn btn-phoenix-primary" id="js-transfer-addr" onclick="window.transferAddress();" style="display: none;">Пренеси данните</a>

              <script type="module">
                $(function () {
                  const $select = $('#f-recipientInfo')
                  const selected = $select.data('recipient-selected');
                  const $infos = $('[data-recipient-info-id]');

                  $infos.each(function () {
                    const $this = $(this);
                    const id = $this.data('recipient-info-id');
                    const title = $this.data('recipient-info-title');
                    $select.append(`<option value="${id}">${title}</option>`);
                  });

                  $select.change(function () {
                    const value = $(this).val();
                    $infos.hide();

                    $('#js-transfer-addr').hide();

                    const $target = $(`[data-recipient-info-id="${value}"]`);
                    $target.fadeIn();
                    if ($target.length) {
                      $('#js-transfer-addr').fadeIn();
                    }
                  });

                  $select.val(selected);
                  $select.change();

                  //

                  window.transferAddress = function () {
                    const $wrapper = $('[data-recipient-info-id]:visible').first();

                    $wrapper.find('[data-addr]').each(function () {
                      const $this = $(this);
                      const $target = $($(this).data('addr'));
                      const value = $this.text().trim();
                      const title = ($this.data('title') ?? ' - ').trim();

                      if ($target.get(0)?.type === 'select-one') {
                        const newOption = new Option(title, value, false, false);
                        $($target).append(newOption).trigger('change');
                      }

                      if (!$target.val()) {
                        $target.val(value !== '-' ? value : '');
                      }
                    });
                  };

                  // When the form is not submit, transfer the 1st data
                  @if (!request()->isMethod('post'))
                  window.transferAddress();
                  @endif
                });
              </script>

              <hr/>
            </div>
          @endif

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-recipientPhone1">Телефон</label>
            <input type="text" class="form-control @if($errors->has('recipientPhone1')) is-invalid @endif" id="f-recipientPhone1" name="recipientPhone1" value="{{ $shipmentData->recipientPhone1 ?? null }}" placeholder="0888777666..." required/>
            @if($errors->has('recipientPhone1'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientPhone1') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-recipientPhone2">Телефон 2</label>
            <input type="text" class="form-control @if($errors->has('recipientPhone2')) is-invalid @endif" id="f-recipientPhone2" name="recipientPhone2" value="{{ $shipmentData->recipientPhone2 ?? null }}" placeholder="0888777666..."/>
            @if($errors->has('recipientPhone2'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientPhone2') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-recipientClientName">Фирма получател</label>
            <input type="text" class="form-control @if($errors->has('recipientClientName')) is-invalid @endif" id="f-recipientClientName" name="recipientClientName" value="{{ $shipmentData->recipientClientName ?? null }}" placeholder="Получател..." required/>
            @if($errors->has('recipientClientName'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientClientName') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-recipientContactName">Лице за контакт</label>
            <input type="text" class="form-control @if($errors->has('recipientContactName')) is-invalid @endif" id="f-recipientContactName" name="recipientContactName" value="{{ $shipmentData->recipientContactName ?? null }}" placeholder="Петър Петров..."/>
            @if($errors->has('recipientContactName'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientContactName') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-recipientObjectName">Обект</label>
            <input type="text" class="form-control @if($errors->has('recipientObjectName')) is-invalid @endif" id="f-recipientObjectName" name="recipientObjectName" value="{{ $shipmentData->recipientObjectName ?? null }}" placeholder="..."/>
            @if($errors->has('recipientObjectName'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientObjectName') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-recipientEmail">E-mail</label>
            <input type="email" class="form-control @if($errors->has('recipientEmail')) is-invalid @endif" id="f-recipientEmail" name="recipientEmail" value="{{ $shipmentData->recipientEmail ?? null }}" placeholder="cpompany@mail.com..." required/>
            @if($errors->has('recipientEmail'))
              <div class="invalid-feedback">
                {{ $errors->first('recipientEmail') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label" for="f-recipientCountryId">Държава</label>
            <select class="form-select @if($errors->has('recipientCountryId')) is-invalid @endif" id="f-recipientCountryId" name="recipientCountryId" data-location="countryId">
              @if(!empty($shipmentData->recipientCountryId))
                <option value="{{ $shipmentData->recipientCountryId }}" selected>{{ $shipmentData->recipientCountryTitle ?? null }}</option>
              @endif
            </select>
            <input type="hidden" id="f-recipientCountryTitle" name="recipientCountryTitle" value="{{ $shipmentData->recipientCountryTitle ?? null }}"/>
            @if($errors->has('recipientCountryId'))
              <div class="text-danger fs-9">
                {{ $errors->first('recipientCountryId') }}
              </div>
            @endif

            <script type="module">
              $(function () {
                window.ajaxSelect($('#f-recipientCountryId'), $('#f-recipientCountryTitle'), '{{ url('/erp/shipments/speedy/search/?kind=country') }}', 'Избери държава...');
              });
            </script>
          </div>

          <div class="col-12 col-xl-4">
            <label class="app-form-label required" for="f-recipientSiteId">Населено място</label>
            <select class="form-select @if($errors->has('recipientSiteId')) is-invalid @endif" id="f-recipientSiteId" name="recipientSiteId" data-location="siteId" required>
              @if(!empty($shipmentData->recipientSiteId))
                <option value="{{ $shipmentData->recipientSiteId }}" selected>{{ $shipmentData->recipientSiteTitle ?? null }}</option>
              @endif
            </select>
            <input type="hidden" id="f-recipientSiteTitle" name="recipientSiteTitle" value="{{ $shipmentData->recipientSiteTitle ?? null }}"/>
            @if($errors->has('recipientSiteId'))
              <div class="text-danger fs-9">
                {{ $errors->first('recipientSiteId') }}
              </div>
            @endif

            <script type="module">
              $(function () {
                window.ajaxSelect($('#f-recipientSiteId'), $('#f-recipientSiteTitle'), '{{ url('/erp/shipments/speedy/search/?kind=site') }}', 'Избери населено място...');
              });
            </script>
          </div>

          <div class="col-12 col-xl-4">
            <label class="form-label" for="f-officeShipment">Адрес или офис</label>
            <select class="form-select @if($errors->has('officeShipment')) is-invalid @endif" id="f-officeShipment" name="officeShipment">
              <option value="0" @if (empty($shipmentData->officeShipment)) selected @endif>До адрес</option>
              <option value="1" @if (!empty($shipmentData->officeShipment)) selected @endif>До поискване в офис/автомат</option>
            </select>
            @if($errors->has('officeShipment'))
              <div class="invalid-feedback">
                {{ $errors->first('officeShipment') }}
              </div>
            @endif

            <script type="module">
              $('#f-officeShipment').change(function () {
                const $officeShipmentYes = $('[data-office-shipment="yes"]');
                const $officeShipmentNo = $('[data-office-shipment="no"]');

                if ($(this).val() === '1') {
                  $officeShipmentYes.fadeIn();
                  $officeShipmentNo.hide();
                } else {
                  $officeShipmentYes.hide();
                  $officeShipmentNo.fadeIn();
                }
              });

              // Timeout because of select2
              setTimeout(() => {
                $('#f-officeShipment').change()
              }, 300);
            </script>
          </div>

          <div class="col-12" data-office-shipment="no">
            <div class="row gy-2">
              <div class="col">
                <label class="app-form-label" for="f-recipientComplexId">Квартал</label>
                <select class="form-select @if($errors->has('recipientComplexId')) is-invalid @endif" id="f-recipientComplexId" name="recipientComplexId" data-location="complexId">
                  @if(!empty($shipmentData->recipientComplexId))
                    <option value="{{ $shipmentData->recipientComplexId }}" selected>{{ $shipmentData->recipientComplexTitle ?? null }}</option>
                  @endif
                </select>
                <input type="hidden" id="f-recipientComplexTitle" name="recipientComplexTitle" value="{{ $shipmentData->recipientComplexTitle ?? null }}"/>
                @if($errors->has('recipientComplexId'))
                  <div class="text-danger fs-9">
                    {{ $errors->first('recipientComplexId') }}
                  </div>
                @endif

                <script type="module">
                  $(function () {
                    window.ajaxSelect($('#f-recipientComplexId'), $('#f-recipientComplexTitle'), '{{ url('/erp/shipments/speedy/search/?kind=complex') }}', 'Избери квартал...');
                  });
                </script>
              </div>
              <div class="col">
                <label class="app-form-label" for="f-recipientStreetId">Улица</label>
                <select class="form-select @if($errors->has('recipientStreetId')) is-invalid @endif" id="f-recipientStreetId" name="recipientStreetId" data-location="streetId">
                  @if(!empty($shipmentData->recipientStreetId))
                    <option value="{{ $shipmentData->recipientStreetId }}" selected>{{ $shipmentData->recipientStreetTitle ?? null }}</option>
                  @endif
                </select>
                <input type="hidden" id="f-recipientStreetTitle" name="recipientStreetTitle" value="{{ $shipmentData->recipientStreetTitle ?? null }}"/>
                @if($errors->has('recipientStreetId'))
                  <div class="text-danger fs-9">
                    {{ $errors->first('recipientStreetId') }}
                  </div>
                @endif

                <script type="module">
                  $(function () {
                    window.ajaxSelect($('#f-recipientStreetId'), $('#f-recipientStreetTitle'), '{{ url('/erp/shipments/speedy/search/?kind=street') }}', 'Избери улица...');
                  });
                </script>
              </div>
            </div>
          </div>

          <div class="col-12" data-office-shipment="no">
            <div class="row gy-2">
              <div class="col-4 col-md">
                <label class="app-form-label" for="f-recipientStreetNo">Ул. №</label>
                <input type="text" maxlength="10" class="form-control @if($errors->has('recipientStreetNo')) is-invalid @endif" id="f-recipientStreetNo" name="recipientStreetNo" value="{{ $shipmentData->recipientStreetNo ?? null }}" placeholder="10A..."/>
                @if($errors->has('recipientStreetNo'))
                  <div class="invalid-feedback">
                    {{ $errors->first('recipientStreetNo') }}
                  </div>
                @endif
              </div>
              <div class="col-4 col-md">
                <label class="app-form-label" for="f-recipientBlockNo">Бл.</label>
                <input type="text" maxlength="40" class="form-control @if($errors->has('recipientBlockNo')) is-invalid @endif" id="f-recipientBlockNo" name="recipientBlockNo" value="{{ $shipmentData->recipientBlockNo ?? null }}" placeholder="..."/>
                @if($errors->has('recipientBlockNo'))
                  <div class="invalid-feedback">
                    {{ $errors->first('recipientBlockNo') }}
                  </div>
                @endif
              </div>
              <div class="col-4 col-md">
                <label class="app-form-label" for="f-recipientEntranceNo">Вх.</label>
                <input type="text" maxlength="10" class="form-control @if($errors->has('recipientEntranceNo')) is-invalid @endif" id="f-recipientEntranceNo" name="recipientEntranceNo" value="{{ $shipmentData->recipientEntranceNo ?? null }}" placeholder="B..."/>
                @if($errors->has('recipientEntranceNo'))
                  <div class="invalid-feedback">
                    {{ $errors->first('recipientEntranceNo') }}
                  </div>
                @endif
              </div>
              <div class="col-4 col-md">
                <label class="app-form-label" for="f-recipientFloorNo">Ет.</label>
                <input type="text" maxlength="10" class="form-control @if($errors->has('recipientFloorNo')) is-invalid @endif" id="f-recipientFloorNo" name="recipientFloorNo" value="{{ $shipmentData->recipientFloorNo ?? null }}" placeholder="3.."/>
                @if($errors->has('recipientFloorNo'))
                  <div class="invalid-feedback">
                    {{ $errors->first('recipientFloorNo') }}
                  </div>
                @endif
              </div>
              <div class="col-4 col-md">
                <label class="app-form-label" for="f-recipientApartmentNo">Ап.</label>
                <input type="text" maxlength="10" class="form-control @if($errors->has('recipientApartmentNo')) is-invalid @endif" id="f-recipientApartmentNo" name="recipientApartmentNo" value="{{ $shipmentData->recipientApartmentNo ?? null }}" placeholder="12.."/>
                @if($errors->has('recipientApartmentNo'))
                  <div class="invalid-feedback">
                    {{ $errors->first('recipientApartmentNo') }}
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-12" data-office-shipment="yes">
            <label class="app-form-label" for="f-recipientOfficeId">Офис/Автомат</label>
            <select class="form-select @if($errors->has('recipientOfficeId')) is-invalid @endif" id="f-recipientOfficeId" name="recipientOfficeId">
              @if(!empty($shipmentData->recipientOfficeId))
                <option value="{{ $shipmentData->recipientOfficeId }}" selected>{{ $shipmentData->recipientOfficeTitle ?? null }}</option>
              @endif
            </select>
            <input type="hidden" id="f-recipientOfficeTitle" name="recipientOfficeTitle" value="{{ $shipmentData->recipientOfficeTitle ?? null }}"/>
            @if($errors->has('recipientOfficeId'))
              <div class="text-danger fs-9">
                {{ $errors->first('recipientOfficeId') }}
              </div>
            @endif

            <script type="module">
              $(function () {
                window.ajaxSelect($('#f-recipientOfficeId'), $('#f-recipientOfficeTitle'), '{{ url('/erp/shipments/speedy/search/?kind=office') }}', 'Избери офис/автомат...');
              });
            </script>
          </div>

          <div class="col-12">
            <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Параметри на пратката</h2>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-serviceId">Услуга</label>
            <select class="form-select @if($errors->has('serviceId')) is-invalid @endif" id="f-serviceId" name="serviceId">
              <option value="505" @if (($shipmentData->serviceId ?? null) == '505') selected @endif>505 - Стандарт 24 часа</option>
              <option value="515" @if (($shipmentData->serviceId ?? null) == '515') selected @endif>515 - Стандарт 24 часа пакет</option>
              <option value="412" @if (($shipmentData->serviceId ?? null) == '412') selected @endif>412 - Pallet One BG - Premium</option>
              <option value="413" @if (($shipmentData->serviceId ?? null) == '413') selected @endif>413 - Pallet One BG - Economy</option>
              <option value="704" @if (($shipmentData->serviceId ?? null) == '704') selected @endif>704 - Гуми</option>
            </select>
            @if($errors->has('serviceId'))
              <div class="invalid-feedback">
                {{ $errors->first('serviceId') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-serviceCodAmount">Наложен платеж</label>
            <div class="input-group">
              <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
              <input type="number" step="0.01" min="0" class="form-control @if($errors->has('serviceCodAmount')) is-invalid @endif" id="f-serviceCodAmount" name="serviceCodAmount" value="{{ $document->serviceCodAmount ?? null }}" placeholder="123.45..."/>
              <span class="input-group-text" id="serviceCodAmount-bgn"></span>
              @if($errors->has('serviceCodAmount'))
                <div class="invalid-feedback">
                  {{ $errors->first('serviceCodAmount') }}
                </div>
              @endif
            </div>

            <script type="module">
              $('#f-serviceCodAmount').on('change keyup', function () {
                const currentValue = parseFloat($('#f-serviceCodAmount').val()) || 0;
                $('#serviceCodAmount-bgn').html((currentValue * 1.95583) + ' лв.');
              })
                .change();
            </script>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-servicePickupDate">Изпращане на</label>
            <input type="date" class="form-control @if($errors->has('servicePickupDate')) is-invalid @endif" id="f-servicePickupDate" name="servicePickupDate" value="{{ $shipmentData->servicePickupDate ?? null }}" placeholder="2020-01-01..."/>
            @if($errors->has('servicePickupDate'))
              <div class="invalid-feedback">
                {{ $errors->first('servicePickupDate') }}
              </div>
            @endif

            <script type="module">
              flatpickr('#f-servicePickupDate');
            </script>
          </div>

          <div class="col-12 col-xl-6">
            <label class="form-label" for="f-serviceAutoAdjustPickupDate">Намери първата налична дата за вземане</label>
            <select class="form-select @if($errors->has('serviceAutoAdjustPickupDate')) is-invalid @endif" id="f-serviceAutoAdjustPickupDate" name="serviceAutoAdjustPickupDate">
              <option value="1" @if ($shipmentData->serviceAutoAdjustPickupDate) selected @endif>Да</option>
              <option value="0" @if (!$shipmentData->serviceAutoAdjustPickupDate) selected @endif>Използвай само въведената дата</option>
            </select>
            @if($errors->has('serviceAutoAdjustPickupDate'))
              <div class="invalid-feedback">
                {{ $errors->first('serviceAutoAdjustPickupDate') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-paymentCourierServicePayer">Разходите за доставка се поемат от</label>
            <select class="form-select @if($errors->has('paymentCourierServicePayer')) is-invalid @endif" id="f-paymentCourierServicePayer" name="paymentCourierServicePayer">
              <option value="SENDER" @if (($shipmentData->paymentCourierServicePayer ?? null) == 'SENDER') selected @endif>Подател</option>
              <option value="RECIPIENT" @if (($shipmentData->paymentCourierServicePayer ?? null) == 'RECIPIENT') selected @endif>Получател</option>
            </select>
            @if($errors->has('paymentCourierServicePayer'))
              <div class="invalid-feedback">
                {{ $errors->first('paymentCourierServicePayer') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-paymentDeclaredValuePayer">Разходите за наложен платеж се поемат от</label>
            <select class="form-select @if($errors->has('paymentDeclaredValuePayer')) is-invalid @endif" id="f-paymentDeclaredValuePayer" name="paymentDeclaredValuePayer">
              <option value="SENDER" @if (($shipmentData->paymentDeclaredValuePayer ?? null) == 'SENDER') selected @endif>Подател</option>
              <option value="RECIPIENT" @if (($shipmentData->paymentDeclaredValuePayer ?? null) == 'RECIPIENT') selected @endif>Получател</option>
            </select>
            @if($errors->has('paymentDeclaredValuePayer'))
              <div class="invalid-feedback">
                {{ $errors->first('paymentDeclaredValuePayer') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-serviceObpdOption">Опции преди плащане/получаване</label>
            <select class="form-select @if($errors->has('serviceObpdOption')) is-invalid @endif" id="f-serviceObpdOption" name="serviceObpdOption">
              <option value="">- Избери -</option>
              <option value="OPEN" @if (($shipmentData->serviceObpdOption ?? null) == 'OPEN') selected @endif>Отвори преди плащане/получаване</option>
              <option value="TEST" @if (($shipmentData->serviceObpdOption ?? null) == 'TEST') selected @endif>Тествай преди плащане/получаване</option>
            </select>
            @if($errors->has('serviceObpdOption'))
              <div class="invalid-feedback">
                {{ $errors->first('serviceObpdOption') }}
              </div>
            @endif
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-serviceObpdReturnShipmentPayer">Плащане при отказ</label>
            <select class="form-select @if($errors->has('serviceObpdReturnShipmentPayer')) is-invalid @endif" id="f-serviceObpdReturnShipmentPayer" name="serviceObpdReturnShipmentPayer">
              <option value="SENDER" @if (($shipmentData->serviceObpdReturnShipmentPayer ?? null) == 'SENDER') selected @endif>Подател</option>
              <option value="RECIPIENT" @if (($shipmentData->serviceObpdReturnShipmentPayer ?? null) == 'RECIPIENT') selected @endif>Получател</option>
            </select>
            @if($errors->has('serviceObpdReturnShipmentPayer'))
              <div class="invalid-feedback">
                {{ $errors->first('serviceObpdReturnShipmentPayer') }}
              </div>
            @endif
            <div class="form-text">Изпращачът на връщащата се пратка е получателят на основната пратка</div>
          </div>

          <div class="col-12">
            <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Съдържание на пратката</h2>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-contentContents">Описание на съдържанието</label>
            <input type="text" class="form-control @if($errors->has('contentContents')) is-invalid @endif" id="f-contentContents" name="contentContents" value="{{ $shipmentData->contentContents ?? null }}" placeholder="Техника..." required/>
            @if($errors->has('contentContents'))
              <div class="invalid-feedback">
                {{ $errors->first('contentContents') }}
              </div>
            @endif</div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-contentPackage">Начин на опаковане</label>
            <input type="text" class="form-control @if($errors->has('contentPackage')) is-invalid @endif" id="f-contentPackage" name="contentPackage" value="{{ $shipmentData->contentPackage ?? null }}" placeholder="Опаковка..." required/>
            @if($errors->has('contentPackage'))
              <div class="invalid-feedback">
                {{ $errors->first('contentPackage') }}
              </div>
            @endif
          </div>

          <div class="col-12">
            <div id="js-parcels"></div>

            @if($errors->has('parcels'))
              <div class="alert alert-phoenix-danger fs-9 m-4 mt-0 p-3">
                {{ $errors->first('parcels') }}
              </div>
            @endif

            <button type="button" class="btn btn-sm btn-phoenix-info text-primary " onclick="shipment.addParcel();">
              <i class="fa-regular fa-plus"></i>
              Добави нов ред
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-body">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2">Изчисляване на цената за доставка</h2>

        @include('erp.shipments.speedy.partials.calculator')

        {{-- <span class="badge bg-warning-subtle text-warning" id="js-calcShippingBadge">Нужно е преизчисление</span>--}}

        <script type="module">
          window.calculateShippingGetProducts = function () {
            return window.shipment.parcelsData
          }

          window.calculateShippingGetAddress = function () {
            return {
              citySpeedyId: $('#f-recipientSiteId').val(),
              streetSpeedyId: $('#f-recipientStreetId').val(),
              officeSpeedyId: $('#f-recipientOfficeId').val(),
            };
          }

          window.calculateShippingHandleResponse = function (rs) {
            console.log('rs', rs);
          }
        </script>
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-plus me-2"></i>
        Създай поръчката
      </button>
    </div>
  </form>

  <script type="module">
    window.ajaxSelect = ($select, $title, url, placeholder) => {
      $select.select2({
        placeholder: placeholder,
        ajax: {
          url,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            const addParams = {};
            $('[data-location]').each(function () {
              const $this = $(this);
              const param = $this.data('location');
              addParams[param] = $this.val();
            });

            return {
              q: params.term,
              ...addParams,
            };
          },
          processResults: (rs) => {
            return {
              results: rs.data
            };
          },
          cache: true
        },
        templateSelection: (item) => (item.text || item.title),
        templateResult: (item) => {
          if (item.loading) {
            return item.text;
          }

          return $(`<div>${item.title}</div>`);
        }
      }).on('select2:select', function (e) {
        const itemData = e.params.data;
        $($title).val(itemData.title);
      });
    };
  </script>

  <script type="module">
    class Shipment {
      parcelIdx = 0;
      parcelsData = {};

      refillData(parcelIdx) {
        this.parcelsData[parcelIdx] = {
          ref1: $(`#erp-form-ref1-${parcelIdx}`).val() ?? null,
          ref2: $(`#erp-form-ref2-${parcelIdx}`).val() ?? null,
          weight: parseFloat($(`#erp-form-weight-${parcelIdx}`).val()) ?? null,
          height: parseFloat($(`#erp-form-height-${parcelIdx}`).val()) ?? null,
          depth: parseFloat($(`#erp-form-depth-${parcelIdx}`).val()) ?? null,
          width: parseFloat($(`#erp-form-width-${parcelIdx}`).val()) ?? null,
          error: parseFloat($(`#erp-form-error-${parcelIdx}`).val()) ?? null,
        };
      }

      removeParcel(parcelIdx) {
        if (!confirm('Сигурни ли сте, че искате да премахнете този артикул?')) {
          return;
        }

        $(`#parcel-${parcelIdx}`).fadeOut(() => {
          $(`#parcel-${parcelIdx}`).remove();

          if (this.parcelsData[parcelIdx]) {
            delete this.parcelsData[parcelIdx];
          }
        });
      }

      duplicate(parcelIdx) {
        this.addParcel({
          ...this.parcelsData[parcelIdx],
          id: null,
          error: null,
        });
      }

      addParcel(parcel) {
        const parcelIdx = this.parcelIdx++;

        if (typeof parcel !== 'object') {
          parcel = {};
        }

        parcel = {
          ref1: parcel.ref1 ?? null,
          ref2: parcel.ref2 ?? null,
          weight: parcel.weight ? parseFloat(parcel.weight) : null,
          height: parcel.height ? parseFloat(parcel.height) : null,
          depth: parcel.depth ? parseFloat(parcel.depth) : null,
          width: parcel.width ? parseFloat(parcel.width) : null,
          error: parcel.error ?? null,
        };

        this.parcelsData[parcelIdx] = parcel;

        const $html = $(`
        <div class="row g-2 border-1 border-bottom border-dashed pb-4 mb-3 position-relative" id="parcel-${parcelIdx}">
          <input type="hidden" id="erp-form-ref1-${parcelIdx}" name="parcels[${parcelIdx}][ref1]" value="${parcel.ref1 ?? ''}"/>

          <div class="col-auto align-content-end order-last">
            <div class="btn-reveal-trigger position-absolute top-0 end-0">
              <button type="button" class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                <i class="fa-regular fa-ellipsis-h fs-10"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end py-2">
                <button type="button" class="dropdown-item" onclick="shipment.duplicate(${parcelIdx})">Дублирай</button>
                <div class="dropdown-divider"></div>
                <button type="button" class="dropdown-item text-danger" onclick="shipment.removeParcel(${parcelIdx})">Изтрий</button>
              </div>
            </div>
          </div>

          <div class="col align-content-end">
            <label class="form-label" for="erp-form-ref2-${parcelIdx}">Описание</label>
            <input type="text" maxlength="20" class="form-control" id="erp-form-ref2-${parcelIdx}" name="parcels[${parcelIdx}][ref2]" value="${parcel.ref2 ?? ''}" placeholder="Телевизор Samsung..."/>
          </div>
          <div class="col align-content-end">
            <label class="form-label" for="erp-form-width-${parcelIdx}">Дължина (см.)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="erp-form-width-${parcelIdx}" name="parcels[${parcelIdx}][width]" value="${parcel.width ?? ''}" placeholder="125..."/>
          </div>
          <div class="col align-content-end">
            <label class="form-label" for="erp-form-depth-${parcelIdx}">Дълбочина (см.)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="erp-form-depth-${parcelIdx}" name="parcels[${parcelIdx}][depth]" value="${parcel.depth ?? ''}" placeholder="30..."/>
          </div>
          <div class="col align-content-end">
            <label class="form-label" for="erp-form-height-${parcelIdx}">Височина (см.)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="erp-form-height-${parcelIdx}" name="parcels[${parcelIdx}][height]" value="${parcel.height ?? ''}" placeholder="15..."/>
          </div>
          <div class="col align-content-end">
            <label class="form-label" for="erp-form-weight-${parcelIdx}">Тегло (кг.)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="erp-form-weight-${parcelIdx}" name="parcels[${parcelIdx}][weight]" value="${parcel.weight ?? ''}" placeholder="2..."/>
          </div>

          ${parcel.error ? `<div class="text-danger fw-bold fs-9 mt-1">${parcel.error}</div>` : ''}
        </div>
      `);

        $html
          .hide()
          .appendTo('#js-parcels')
          .fadeIn();

        const that = this;
        $html.find('input,select,textarea').each(function () {
          const $input = $(this);
          $input.change(that.refillData.bind(that, parcelIdx));
        });
      }
    }

    const shipment = new Shipment();
    window.shipment = shipment;

    const errors = @json($errors);
    const parcels = @json(array_values($shipmentData->parcels ?? []));
    parcels.forEach((parcel, parcelIdx) => {
      parcel.error = '';
      for (const [key, value] of Object.entries(errors)) {
        if (key.startsWith(`parcels.${parcelIdx}.`)) {
          parcel.error += value.join('; ') + '; ';
        }
      }

      shipment.addParcel(parcel);
    });
  </script>
@endsection
