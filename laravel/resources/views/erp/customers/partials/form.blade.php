@if ($customer->isDeleted)
  <div class="alert alert-subtle-danger mb-5" role="alert">Този клиент е маркиран като изтрит и при синхронизация ще бъде премахнат от ERP системата и онлайн магазина.</div>
@endif

<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        {{-- no .mt-3 for 1st title --}}
        <h2 class="h5 pb-2 border-bottom border-dashed">Фирмени данни</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-companyName">Име на фирмата</label>
        <input type="text" class="form-control @if($errors->has('companyName')) is-invalid @endif" id="f-companyName" name="companyName" value="{{ $customer->companyName }}" placeholder="Име на фирма ООД..." required/>
        @if($errors->has('companyName'))
          <div class="invalid-feedback">
            {{ $errors->first('companyName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label w-100" for="f-companyId">
          <div class="d-flex justify-content-between pe-3">
            <span>ЕИК</span>
            <a href="#" target="_blank" class="ms-auto" id="js-brra-link" style="display: none;">
              <i class="fa-regular fa-arrow-up-right-from-square"></i>
              Справка ТР -
              <span id="js-brra-link-id">...</span>
            </a>
          </div>
        </label>
        <input type="text" class="form-control @if($errors->has('companyId')) is-invalid @endif" id="f-companyId" name="companyId" value="{{ $customer->companyId }}" placeholder="200300400..."/>
        @if($errors->has('companyId'))
          <div class="invalid-feedback">
            {{ $errors->first('companyId') }}
          </div>
        @endif

        <script type="module">
          $('#f-companyId').keyup(function () {
            const $link = $('#js-brra-link');
            const val = $(this).val();
            $link.attr('href', 'https://portal.registryagency.bg/CR/Reports/VerificationPersonOrg?selectedSearchFilter=1&ident=' + val);
            $('#js-brra-link-id').html(val);
            if (val) {
              $link.fadeIn();
            } else {
              $link.fadeOut();
            }
          })
            .keyup();
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-companyVatNumber">ДДС номер</label>
        <input type="text" class="form-control @if($errors->has('companyVatNumber')) is-invalid @endif" id="f-companyVatNumber" name="companyVatNumber" value="{{ $customer->companyVatNumber }}" placeholder="BG200300400..."/>
        @if($errors->has('companyVatNumber'))
          <div class="invalid-feedback">
            {{ $errors->first('companyVatNumber') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-companyCountryId">Държава</label>
        <select class="form-select @if($errors->has('companyCountryId')) is-invalid @endif" id="f-companyCountryId" name="companyCountryId">
          <option value="">--- Изберете държава ---</option>
          @foreach ($countries as $row)
            <option value="{{ $row->id }}" {{ $customer->companyCountryId == $row->id ? 'selected' : '' }}>
              {{ $row->name }}
              [{{ $row->isoCode3 }}]
            </option>
          @endforeach
        </select>
        @if($errors->has('companyCountryId'))
          <div class="invalid-feedback">
            {{ $errors->first('companyCountryId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-companyCity">Град</label>
        <input type="text" class="form-control @if($errors->has('companyCity')) is-invalid @endif" id="f-companyCity" name="companyCity" value="{{ $customer->companyCity }}" placeholder="София..."/>
        @if($errors->has('companyCity'))
          <div class="invalid-feedback">
            {{ $errors->first('companyCity') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-companyZipCode">ПК/ZIP</label>
        <input type="text" class="form-control @if($errors->has('companyZipCode')) is-invalid @endif" id="f-companyZipCode" name="companyZipCode" value="{{ $customer->companyZipCode }}" placeholder="1000..."/>
        @if($errors->has('companyZipCode'))
          <div class="invalid-feedback">
            {{ $errors->first('companyZipCode') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-companyAddress">Адрес по регистрация</label>
        <input type="text" class="form-control @if($errors->has('companyAddress')) is-invalid @endif" id="f-companyAddress" name="companyAddress" value="{{ $customer->companyAddress }}" placeholder="Бул. Черни Връх 1000..."/>
        @if($errors->has('companyAddress'))
          <div class="invalid-feedback">
            {{ $errors->first('companyAddress') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Информация за потребителя</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-statusType">Статус</label>
        <select class="form-select @if($errors->has('statusType')) is-invalid @endif" id="f-statusType" name="statusType">
          <option value="">-</option>
          @foreach(App\Enums\CustomerStatusType::cases() as $statusType)
            <option value="{{ $statusType->value }}" {{ ($customer->statusType && $customer->statusType->value === $statusType->value) ? 'selected' : '' }}>
              {{ \App\Services\MapService::customerStatusType($statusType)->label }}
            </option>
          @endforeach
        </select>
        @if($errors->has('statusType'))
          <div class="invalid-feedback">{{ $errors->first('statusType') }}</div>
        @endif

        <script type="module">
          const $statusType = $('#f-statusType');

          function setClientCardStyle() {
            const action = $statusType.val() === '{{ \App\Enums\CustomerStatusType::WaitingApproval->value }}' ? 'addClass' : 'removeClass';
            $statusType[action]('pulse-red');
          }

          setClientCardStyle();
          $statusType.change(setClientCardStyle);
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-email">Имейл (основен и за магазина)</label>
        <input type="email" class="form-control @if($errors->has('email')) is-invalid @endif" id="f-email" name="email" value="{{ $customer->email }}" placeholder="email@insidetrading.bg" required/>
        @if($errors->has('email'))
          <div class="invalid-feedback">
            {{ $errors->first('email') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-groupId">Група</label>
        <select class="form-select @if($errors->has('groupId')) is-invalid @endif" id="f-groupId" name="groupId">
          @foreach ($customersGroups as $row)
            <option value="{{ $row->id }}" {{ $customer->groupId == $row->id ? 'selected' : '' }}>
              {{ $row->nameBg }}
              [{{ $row->discountPercent }}%]
            </option>
          @endforeach
        </select>
        @if($errors->has('groupId'))
          <div class="invalid-feedback">
            {{ $errors->first('groupId') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-firstName">Име</label>
        <input type="text" class="form-control @if($errors->has('firstName')) is-invalid @endif" id="f-firstName" name="firstName" value="{{ $customer->firstName }}" placeholder="Иван..." required/>
        @if($errors->has('firstName'))
          <div class="invalid-feedback">
            {{ $errors->first('firstName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label required" for="f-lastName">Фамилия</label>
        <input type="text" class="form-control @if($errors->has('lastName')) is-invalid @endif" id="f-lastName" name="lastName" value="{{ $customer->lastName }}" placeholder="Иванов..." required/>
        @if($errors->has('lastName'))
          <div class="invalid-feedback">
            {{ $errors->first('lastName') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-salesRepresentativeId">Търговски представител</label>
        <select class="form-select @if($errors->has('salesRepresentativeId')) is-invalid @endif" id="f-salesRepresentativeId" name="salesRepresentativeId">
          <option value="">Без търговски представител</option>
          @foreach ($salesRepresentatives as $row)
            <option value="{{ $row->id }}" {{ $customer->salesRepresentativeId == $row->id ? 'selected' : '' }}>
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
        <label class="app-form-label" for="f-note">Вътрешна бележка</label>
        <textarea class="form-control @if($errors->has('note')) is-invalid @endif" id="f-note" name="note" rows="4">{{ $customer->note }}</textarea>
        @if($errors->has('note'))
          <div class="invalid-feedback">{{ $errors->first('note') }}</div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Финансов контакт</h2>
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-financialContactPhone">Телефон</label>
        <input type="text" class="form-control @if($errors->has('financialContactPhone')) is-invalid @endif" id="f-financialContactPhone" name="financialContactPhone" value="{{ $customer->financialContactPhone }}" placeholder="+1 1234 1234..."/>
        @if($errors->has('financialContactPhone'))
          <div class="invalid-feedback">
            {{ $errors->first('financialContactPhone') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-6">
        <label class="app-form-label" for="f-financialContactEmail">Имейл</label>
        <input type="text" class="form-control @if($errors->has('financialContactEmail')) is-invalid @endif" id="f-financialContactEmail" name="financialContactEmail" value="{{ $customer->financialContactEmail }}" placeholder="finances@email.com..."/>
        @if($errors->has('financialContactEmail'))
          <div class="invalid-feedback">
            {{ $errors->first('financialContactEmail') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Търговски представител</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-contactSales">Търговски контакт</label>
        <input type="text" class="form-control @if($errors->has('contactSales')) is-invalid @endif" id="f-contactSales" name="contactSales" value="{{ $customer->contactSales }}" placeholder="John Doe..."/>
        @if($errors->has('contactSales'))
          <div class="invalid-feedback">
            {{ $errors->first('contactSales') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-contactPhone">Телефон</label>
        <input type="text" class="form-control @if($errors->has('contactPhone')) is-invalid @endif" id="f-contactPhone" name="contactPhone" value="{{ $customer->contactPhone }}" placeholder="+1 1234 1234..."/>
        @if($errors->has('contactPhone'))
          <div class="invalid-feedback">
            {{ $errors->first('contactPhone') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-contactEmail">Имейл</label>
        <input type="text" class="form-control @if($errors->has('contactEmail')) is-invalid @endif" id="f-contactEmail" name="contactEmail" value="{{ $customer->contactEmail }}" placeholder="office@email.com..."/>
        @if($errors->has('contactEmail'))
          <div class="invalid-feedback">
            {{ $errors->first('contactEmail') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Настройки документи</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-preferredLang">Предпочитан език (документи и магазин)</label>
        <select class="form-select @if($errors->has('preferredLang')) is-invalid @endif" id="f-preferredLang" name="preferredLang">
          <option value="bg" {{ $customer->preferredLang == 'bg' ? 'selected' : '' }}>Български</option>
          <option value="en" {{ $customer->preferredLang == 'en' ? 'selected' : '' }}>Английски</option>
        </select>
        @if($errors->has('preferredLang'))
          <div class="invalid-feedback">
            {{ $errors->first('preferredLang') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-preferredIncoterms">Предпочитан Incoterm</label>
        <select class="form-select @if($errors->has('preferredIncoterms')) is-invalid @endif" id="f-preferredIncoterms" name="preferredIncoterms">
          <option value="">--- Изберете ---</option>
          @foreach (dbConfig('incoterms:list') as $incotermsId => $value)
            <option value="{{ $incotermsId }}" {{ $customer->preferredIncoterms === $incotermsId ? 'selected' : '' }}>{{ $incotermsId }} - {{ $value->info }}</option>
          @endforeach
        </select>
        @if($errors->has('preferredIncoterms'))
          <div class="invalid-feedback">
            {{ $errors->first('preferredIncoterms') }}
          </div>
        @endif
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-paymentTerm">Срок за плащане в дни</label>
        <input type="text" class="form-control @if($errors->has('paymentTerm')) is-invalid @endif" id="f-paymentTerm" name="paymentTerm" value="{{ $customer->paymentTerm }}" placeholder="14..."/>
        @if($errors->has('paymentTerm'))
          <div class="invalid-feedback">
            {{ $errors->first('paymentTerm') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Кредитна линия</h2>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-creditLineValue">Стойност на кредитната линия на клиента</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="number" min="0" step="1" class="form-control @if($errors->has('creditLineValue')) is-invalid @endif" id="f-creditLineValue" name="creditLineValue" value="{{ $customer->creditLineValue }}" placeholder="1000..."/>
          @if($errors->has('creditLineValue'))
            <div class="invalid-feedback">
              {{ $errors->first('creditLineValue') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-creditLineUsed">Използвана стойност</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="number" min="0" step="1" class="form-control @if($errors->has('creditLineUsed')) is-invalid @endif" id="f-creditLineUsed" name="creditLineUsed" value="{{ $customer->creditLineUsed }}" placeholder="300..."/>
          @if($errors->has('creditLineUsed'))
            <div class="invalid-feedback">
              {{ $errors->first('creditLineUsed') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-creditLineLeft">Остатъчна стойност</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="number" min="0" step="1" class="form-control @if($errors->has('creditLineLeft')) is-invalid @endif" id="f-creditLineLeft" name="creditLineLeft" value="{{ $customer->creditLineLeft }}" placeholder="700..."/>
          @if($errors->has('creditLineLeft'))
            <div class="invalid-feedback">
              {{ $errors->first('creditLineLeft') }}
            </div>
          @endif
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-creditLineRequested">Поискана нова кредитна линия</label>
        <select class="form-select @if($errors->has('creditLineRequested')) is-invalid @endif" id="f-creditLineRequested" name="creditLineRequested">
          <option value="0" @if (!$customer->creditLineRequested) selected @endif>Клиента не чака одобрение за КЛ</option>
          <option value="1" @if ($customer->creditLineRequested) selected @endif>Клиента чака одобрение за КЛ</option>
        </select>
        @if($errors->has('creditLineRequested'))
          <div class="invalid-feedback">
            {{ $errors->first('creditLineRequested') }}
          </div>
        @endif

        <script type="module">
          const $creditLineRequested = $('#f-creditLineRequested');

          function setClientCardStyle() {
            const action = $creditLineRequested.val() === '1' ? 'addClass' : 'removeClass';
            $creditLineRequested[action]('pulse-red');
          }

          setClientCardStyle();
          $creditLineRequested.change(setClientCardStyle);
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="app-form-label" for="f-creditLineRequestValue">Поискана стойност на кредитната линия</label>
        <div class="input-group">
          <span class="input-group-text">{{ dbConfig('currency:symbol') }}</span>
          <input type="number" min="0" step="1" class="form-control @if($errors->has('creditLineRequestValue')) is-invalid @endif" id="f-creditLineRequestValue" name="creditLineRequestValue" value="{{ $customer->creditLineRequestValue }}" placeholder="500..."/>
          @if($errors->has('creditLineRequestValue'))
            <div class="invalid-feedback">
              {{ $errors->first('creditLineRequestValue') }}
            </div>
          @endif
        </div>

        <script type="module">
          function setCreditLineCardStyle() {
            const action = $('#f-creditLineRequested').prop('checked') ? 'addClass' : 'removeClass';
            $('#js-credit-line-card')[action]('bg-danger-subtle');
          }

          setCreditLineCardStyle();
          $('#f-creditLineRequested').change(setCreditLineCardStyle);
        </script>
      </div>

      <div class="col-12 col-xl-4">
        <label class="form-label" for="f-clientNotifyCreditLine">Уведоми клиента за стойността на кредитната му линия</label>
        <select class="form-select @if($errors->has('clientNotifyCreditLine')) is-invalid @endif" id="f-clientNotifyCreditLine" name="clientNotifyCreditLine">
          <option value="0" @if (!request()->clientNotifyCreditLine) selected @endif>Не изпращай мейл</option>
          <option value="1" @if (request()->clientNotifyCreditLine) selected @endif>Изпрати мейл с параметрите на КЛ</option>
        </select>
        @if($errors->has('clientNotifyCreditLine'))
          <div class="invalid-feedback">
            {{ $errors->first('clientNotifyCreditLine') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">
          @if (Request::is('erp/customers/update/*'))
            Промяна на паролата на клиента
          @else
            Парола на клиента
          @endif
        </h2>
      </div>

      <div class="col-12">
        <div class="row">
          <div class="col-12 col-xl-6">
            @php($allowInShop = $customer->statusType && \App\Services\MapService::customerStatusType($customer->statusType)->allowInShop)

            <label id="f-password-label" class="app-form-label {{ Request::is('erp/customers/create') && $allowInShop ? 'required' : '' }}" for="f-password">Парола в онлайн магазина</label>
            <div class="position-relative" data-app-password>
              <input class="form-control form-icon-input pe-6 @if($errors->has('password')) is-invalid @endif" id="f-password" type="password" name="password" value="{{ $customer->password }}" placeholder="Парола" data-app-password-input/>
              @if($errors->has('password'))
                <div class="invalid-feedback">
                  {{ $errors->first('password') }}
                </div>
              @endif
              <div class="btn px-3 py-0 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-app-password-toggle>
                <i class="fa-regular fa-eye show"></i>
                <i class="fa-regular fa-eye-slash hide"></i>
              </div>
            </div>
          </div>

          <div class="col-12 col-xl-6">
            <label class="form-label" for="f-clientNotifyPassword">Уведоми клиента за новата му парола</label>
            <select class="form-select @if($errors->has('clientNotifyPassword')) is-invalid @endif" id="f-clientNotifyPassword" name="clientNotifyPassword">
              <option value="0" @if (!request()->clientNotifyPassword) selected @endif>Не изпращай мейл</option>
              <option value="1" @if (request()->clientNotifyPassword) selected @endif>Изпрати welcome мейл с новата му парола</option>
            </select>
            @if($errors->has('clientNotifyPassword'))
              <div class="invalid-feedback">
                {{ $errors->first('clientNotifyPassword') }}
              </div>
            @endif
          </div>

          @if (Request::is('erp/customers/update/*'))
            <p class="text-body-tertiary fs-9 mt-2">
              <i class="fa-regular fa-circle-info"></i>
              Въведете нова парола, ако искате да смените текущата. Ако полето остане празно, паролата на клиента няма да бъде променена.
            </p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
