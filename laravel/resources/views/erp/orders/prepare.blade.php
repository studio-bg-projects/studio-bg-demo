@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">Създаване на поръчка</h1>

  <form action="{{ url('/erp/orders/create') }}" method="get">
    <div class="card">
      <div class="card-body">
        <div class="row gy-2">
          <div class="col-12">
            {{-- no .mt-3 for 1st title --}}
            <h2 class="h5 pb-2 border-bottom border-dashed">Избор на свързан документ</h2>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label" for="f-customerId">Клиент</label>
            <select class="form-select" name="customerId" id="f-customerId">
              <option value="">-</option>
              @foreach ($customers as $row)
                <option value="{{ $row->id }}" {{ $customerId == $row->id ? 'selected' : '' }}>
                  {{ $row->companyName }} / {{ $row->companyId }} / {{ $row->firstName }} {{ $row->lastName }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-xl-6">
            <label class="app-form-label required" for="f-customerAddressId">Адрес</label>
            <select class="form-select" name="customerAddressId" id="f-customerAddressId" required disabled>
              <option value="">Изберете клиент, за да заредите адреси</option>
            </select>
          </div>

          <div class="col-12">
            <div class="text-end">
              <button class="btn btn-primary btn-lg mt-3" id="btn-continue" type="submit" disabled>Продължи</button>
            </div>
          </div>

          <div class="col-12">
            <div class="alert alert-outline-primary mt-2" id="addresses-message" style="display: none;"></div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script type="module">
    $(function () {
      const $customerSelect = $('#f-customerId');
      const $addressSelect = $('#f-customerAddressId');
      const $continueBtn = $('#btn-continue');
      const $addressesMessage = $('#addresses-message');
      const initialCustomerId = @json($customerId);
      const initialAddressId = @json(request()->input('customerAddressId'));
      const createAddressBaseUrl = "{{ url('/erp/customers/addresses/create') }}";

      $customerSelect.select2();

      const showNoAddressesAlert = (customerId) => {
        console.log('customerId', customerId);

        if (!customerId) {
          $addressesMessage.hide().empty();
          return;
        }

        const createUrl = `${createAddressBaseUrl}/${customerId}`;
        const linkHtml = `<a class="alert-link" href="${createUrl}">Клиенти -> Адреси -> Добавяне</a>`;
        $addressesMessage
          .html(`За да добавите нов адрес, отидете на ${linkHtml}.`);

        $addressesMessage.fadeIn();
      };

      const updateContinueState = () => {
        const hasCustomer = !!$customerSelect.val();
        const hasAddress = !!$addressSelect.val();
        $continueBtn.prop('disabled', !(hasCustomer && hasAddress));
      };

      const resetAddressSelect = (message, isDisabled = true) => {
        $addressSelect.empty();
        $addressSelect.append(new Option(message, '', true, true));
        $addressSelect.prop('disabled', isDisabled);
        updateContinueState();
      };

      const buildAddressLabel = (address) => {
        const parts = [];

        if (address?.city) {
          parts.push(address.city);
        }

        const street = [address?.street, address?.streetNo].filter(Boolean).join(' ');
        if (street) {
          parts.push(street);
        }

        if (address?.addressDetails) {
          parts.push(address.addressDetails);
        }

        return parts.length ? parts.join(', ') : `Адрес #${address?.id}`;
      };

      const setAddressOptions = (addresses, selectedId = null, customerId = null) => {
        let list = addresses;
        if (!Array.isArray(list)) {
          list = Object.values(list || {});
        }

        showNoAddressesAlert(customerId);

        if (!list.length) {
          resetAddressSelect('Няма налични адреси');
          return;
        }

        $addressSelect.empty();
        $addressSelect.append(new Option('Изберете адрес', '', true, true));

        list.forEach((address) => {
          $addressSelect.append(new Option(buildAddressLabel(address), address.id, false, false));
        });

        if (selectedId !== null && selectedId !== undefined && selectedId !== '') {
          $addressSelect.val(`${selectedId}`);
        } else {
          $addressSelect.val('');
        }

        $addressSelect.prop('disabled', false);
        updateContinueState();
      };

      const fetchAddresses = (customerId, selectedId = null) => {
        if (!customerId) {
          resetAddressSelect('Изберете клиент, за да заредите адреси');
          return;
        }

        resetAddressSelect('Зареждане...', true);

        $.ajax({
          url: `{{ url('/erp/customers/addresses') }}/${customerId}?page=all`,
          dataType: 'json',
          success: (response) => {
            const addresses = response?.addresses?.data ?? response?.addresses ?? [];
            setAddressOptions(addresses, selectedId, customerId);
          },
          error: () => {
            resetAddressSelect('Възникна грешка при зареждане на адресите');
          }
        });
      };

      $customerSelect.on('change', function () {
        const customerId = $(this).val();
        fetchAddresses(customerId);
      });

      $addressSelect.on('change', updateContinueState);

      if (initialCustomerId) {
        fetchAddresses(initialCustomerId, initialAddressId);
      } else {
        resetAddressSelect('Изберете клиент, за да заредите адреси');
      }

      updateContinueState();
    });
  </script>
@endsection
