@extends('layouts.pdf')

@php($documentTitle = ['bg' => 'Оферта', 'en' => 'Offer'][$lang ?? 'bg'])@php($hasDiscount = $offer->items->contains(fn($i) => $i->discountPercent > 0))

<style>
  #print-table {
    width: 100%;
  }

  @media screen {
    /* Make it closer to the print size on desktop */
    #print-table {
      max-width: 720px;
    }
  }

  @media print {
    @page {
      padding: 1cm;
    }
  }

  body {
    background-color: #ffffff !important;
  }
</style>

@section('content')
  <!-- LAYOUT TABLE (HEADER & CONTENT) -->
  <table class="mx-auto" id="print-table">
    <!-- HEADER -->
    <thead style="display: table-header-group;">
    <tr>
      <td>
        <div class="container">
          <div class="row">
            <div class="col-auto">
              <img src="data:image/svg+xml;base64,{{base64_encode(file_get_contents(public_path('img/logo.svg')))}}" alt="Inside Trading" style="height: 3rem;" class="mb-2"/>
            </div>
            <div class="col">
              <div class="text-end">
                <h1 class="text-uppercase m-0 mb-2 h3">{{ $documentTitle }} #{{ $offer->offerNumber }}</h1>

                @if ($offer->validUntil)
                  <p class="mb-2 fs-10 fw-bold">{{ ['bg' => 'Валидна до', 'en' => 'Valid Until'][$lang] }}: {{ $offer->validUntil }}</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </td>
    </tr>
    </thead>
    <!-- /HEADER -->

    <!-- CONTENT -->
    <tbody>
    <tr>
      <td>
        <div class="container">
          <div class="row mb-2">
            <div class="col-12">
              <h2 class="h5 mb-2 border-bottom pb-2">{{ $offer->companyName }}</h2>
              <ul class="list-unstyled fs-10">
                @if ($offer->companyPerson)
                  <li class="mb-1">{{ ['bg' => 'Лице за контакт', 'en' => 'Contact'][$lang] }}:
                    <strong>{{ $offer->companyPerson }}</strong>
                  </li>
                @endif
                @if ($offer->companyId)
                  <li class="mb-1">{{ ['bg' => 'ЕИК', 'en' => 'Company ID'][$lang] }}:
                    <strong>{{ $offer->companyId }}</strong>
                  </li>
                @endif
                @if ($offer->companyAddress)
                  <li class="mb-1">{{ ['bg' => 'Адрес', 'en' => 'Address'][$lang] }}:
                    <strong>{{ $offer->companyAddress }}</strong>
                  </li>
                @endif
                @if ($offer->companyPhone)
                  <li class="mb-1">{{ ['bg' => 'Телефон', 'en' => 'Phone'][$lang] }}:
                    <strong>{{ $offer->companyPhone }}</strong>
                  </li>
                @endif
                @if ($offer->companyEmail)
                  <li class="mb-1">{{ ['bg' => 'Email', 'en' => 'Email'][$lang] }}:
                    <strong>{{ $offer->companyEmail }}</strong>
                  </li>
                @endif
              </ul>
            </div>
          </div>

          <table class="table table-sm table-striped fs-10">
            <thead class="mt-3">
            <tr>
              <th class="text-uppercase align-middle white-space-nowrap">{{ ['bg' => 'Описание', 'en' => 'Description'][$lang] }}</th>
              <th class="text-uppercase align-middle white-space-nowrap">{{ ['bg' => 'MPN', 'en' => 'MPN'][$lang] }}</th>
              <th class="text-uppercase align-middle white-space-nowrap">{{ ['bg' => 'EAN', 'en' => 'EAN'][$lang] }}</th>
              <th class="text-uppercase align-middle text-end white-space-nowrap">{{ ['bg' => 'Бр.', 'en' => 'Qty'][$lang] }}</th>
              <th class="text-uppercase align-middle text-end white-space-nowrap">{{ ['bg' => 'Ед. Цена', 'en' => 'Price'][$lang] }}</th>
              @if ($hasDiscount)
                <th class="text-uppercase align-middle text-end white-space-nowrap">{{ ['bg' => 'Отстъпка %', 'en' => 'Discount %'][$lang] }}</th>
              @endif
              <th class="text-uppercase align-middle text-end white-space-nowrap">{{ ['bg' => 'Общо', 'en' => 'Total'][$lang] }}</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            @foreach ($offer->items as $item)
              <tr>
                <td class="px-2 align-middle">{{ $item->name }}</td>
                <td class="px-2 align-middle">{{ $item->mpn }}</td>
                <td class="px-2 align-middle">{{ $item->ean }}</td>
                <td class="px-2 text-end align-middle white-space-nowrap">{{ $item->quantity }}</td>
                <td class="px-2 text-end align-middle white-space-nowrap">{{ price($item->price) }}</td>
                @if ($hasDiscount)
                  <td class="px-2 text-end align-middle white-space-nowrap">
                    @if ($item->discountPercent > 0)
                      {{ $item->discountPercent }}%
                    @else
                      -
                    @endif
                  </td>
                @endif
                <td class="px-2 text-end align-middle white-space-nowrap">{{ price($item->totalPrice) }}</td>
              </tr>
            @endforeach
            <tr>
              <td class="px-2 text-end fw-bold align-middle" colspan="{{ $hasDiscount ? 6 : 5 }}">{{ ['bg' => 'Общо', 'en' => 'Total'][$lang] }}</td>
              <td class="px-2 text-end align-middle white-space-nowrap fw-bold">{{ price($offer->items->sum('totalPrice')) }}</td>
            </tr>
            </tbody>
          </table>

          @if ($offer->notesPublic)
            <h2 class="h5 mb-2 border-bottom pb-2">{{ ['bg' => 'Бележки', 'en' => 'Notes'][$lang] }}</h2>
            <p class="fs-10 mb-0">{{ $offer->notesPublic }}</p>
          @endif
        </div>
      </td>
    </tr>
    </tbody>
    <!-- /CONTENT -->
    <!-- FOOTER -->
    <tfoot style="display: table-footer-group;">
    <tr>
      <td>
        <div class="container">
          <hr class="my-2"/>
          <p class="text-center fs-10 mb-2">
            {{ ['bg' => 'При въпроси можете да се свържете с нас чрез контактната форма:',
               'en' => 'If you have any questions, feel free to reach out through our contact form:'][$lang] }}
            {!! [
              'bg' => '<a href="https://insidetrading.bg/kontakti/" target="blank">Контакти</a>',
              'en' => '<a href="https://insidetrading.bg/en/contact/" target="blank">Contacts</a>'
            ][$lang] !!}
          </p>
          <table class="w-100 fs-10">
            <tr class="text-center">
              <td class="p-2" style="width: 33%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded" style="width:50px;height:50px;background:#ef6b03;">
                  <svg aria-hidden="true" viewBox="0 0 512 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"></path>
                  </svg>
                </div>
                <div class="mt-1 fw-bold">+359 885 915515</div>
              </td>
              <td class="p-2" style="width: 33%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded" style="width:50px;height:50px;background:#ef6b03;">
                  <svg aria-hidden="true" viewBox="0 0 512 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
                  </svg>
                </div>
                <div class="mt-1 fw-bold">info@insidetrading.bg</div>
              </td>
              <td class="p-2" style="width: 33%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded" style="width:50px;height:50px;background:#ef6b03;">
                  <svg aria-hidden="true" viewBox="0 0 576 512" width="24" height="24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M560.02 32c-1.96 0-3.98.37-5.96 1.16L384.01 96H384L212 35.28A64.252 64.252 0 0 0 191.76 32c-6.69 0-13.37 1.05-19.81 3.14L20.12 87.95A32.006 32.006 0 0 0 0 117.66v346.32C0 473.17 7.53 480 15.99 480c1.96 0 3.97-.37 5.96-1.16L192 416l172 60.71a63.98 63.98 0 0 0 40.05.15l151.83-52.81A31.996 31.996 0 0 0 576 394.34V48.02c0-9.19-7.53-16.02-15.98-16.02zM224 90.42l128 45.19v285.97l-128-45.19V90.42zM48 418.05V129.07l128-44.53v286.2l-.64.23L48 418.05zm480-35.13l-128 44.53V141.26l.64-.24L528 93.95v288.97z"></path>
                  </svg>
                </div>
                <div class="mt-1 fw-bold">
                  {{ ['bg' => 'ул. Източна тангента № 102, София, България',
                     'en' => 'NPZ Iskar, ul. "Iztochna Tangenta" 102'][$lang] }}
                </div>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
    </tfoot>
    <!-- /FOOTER -->
  </table>
@endsection
