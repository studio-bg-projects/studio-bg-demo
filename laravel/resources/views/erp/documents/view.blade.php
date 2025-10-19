@extends('layouts.app')

@section('content')
  @include('erp.documents.partials.navbar')

  <h1 class="h4 mb-5">{{ \App\Services\MapService::documentTypes($document->getOriginal('type'))->labelBg }} #{{ $document->getOriginal('documentNumber') }} - Преглед</h1>
  <div class="text-end mt-n8 mb-4">
    @if (count($createDocumentTypes))
      <div class="btn-group me-2">
        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa-regular fa-plus me-2"></i>
          Създай свързан документ
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          @foreach ($createDocumentTypes as $row)
            <li>
              <a class="dropdown-item" href="{{ url('/erp/documents/create/' . $row['type']) . '/' . $document->id }}">
                {{ $row['label'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    @endif
    @if ($document->customerId)
      <a href="{{ url('/erp/documents/notify/' . $document->id) }}" class="btn btn-sm btn-primary" onclick="return confirm('Сигурни ли сте, че искате да уведомите клиента за издадения документ?');">
        <i class="fa-regular fa-envelope me-2"></i>
        Изпрати копие до мейла на клиента
      </a>
    @endif
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div class="row gy-2">
        <div class="col-12">
          <h2 class="h5 pb-2 border-bottom border-dashed">Данни за документа</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Вид</p>
          <p class="text-body-emphasis fw-semibold">{{ \App\Services\MapService::documentTypes($document->type)->labelBg }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Номер</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->documentNumber }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Дата на издаване</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issueDate->format('Y-m-d') }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Падеж</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->dueDate->format('Y-m-d') ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Incoterms</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->incoterms }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Международна фактура</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->isForeignInvoice ? 'Да' : 'Не' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Език на документа</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->language }}</p>
        </div>

        <div class="col-12">
          <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Получател</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Име &mdash; Получател</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->recipientName ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ЕИК &mdash; Получател</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->recipientCompanyId ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ДДС номер &mdash; Получател</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->recipientVatId ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес &mdash; Получател</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->recipientAddress ?: '-' }}</p>
        </div>

        <div class="col-12">
          <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Доставка до / Ship To</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Име &mdash; Доставка до</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->shipToName ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ЕИК &mdash; Доставка до</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->shipToCompanyId ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ДДС номер &mdash; Доставка до</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->shipToVatId ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес &mdash; Доставка до</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->shipToAddress ?: '-' }}</p>
        </div>

        <div class="col-12">
          <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Издател</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Име на издателя [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerNameBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Име на издателя [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerNameEn ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес на издателя [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerAddressBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес на издателя [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerAddressEn ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ЕИК на издателя</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerCompanyId ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ДДС номер на издателя</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerVatId ?: '-' }}</p>
        </div>

        <div class="col-12">
          <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Информация за плащане на средствата</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Метод на плащане [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->incomeMethodBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Метод на плащане [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->incomeMethodEn ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Банка на издателя [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerBankNameBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Банка на издателя [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerBankNameEn ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес - банка на издателя [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerIBankAddressBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Адрес - банка на издателя [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerIBankAddressEn ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">IBAN на издателя</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerIban ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">SWIFT на издателя</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->issuerSwift ?: '-' }}</p>
        </div>

        <div class="col-12">
          <h2 class="h5 mt-3 pb-2 border-bottom border-dashed">Основна информация за документа</h2>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ДДС</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->vatRate }}%</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Общо без ДДС</p>
          <p class="text-body-emphasis fw-semibold">{{ price($document->totalAmountNoVat) }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">ДДС</p>
          <p class="text-body-emphasis fw-semibold">{{ price($document->totalVat) }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Общо с ДДС</p>
          <p class="text-body-emphasis fw-semibold">{{ price($document->totalAmount) }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Платена сума</p>
          <p class="text-body-emphasis fw-semibold">{{ price($document->paidAmount) }}</p>
        </div>

        <div class="col-12 col-xl-3">
          <p class="text-body fw-semibold mb-0">Оставаща сума</p>
          <p class="text-body-emphasis fw-semibold">{{ price($document->leftAmount) }}</p>
        </div>

        <div class="col-12 col-xl-6">
          <p class="text-body fw-semibold mb-0">Коментар към плащането [BG]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->incomeCommentBg ?: '-' }}</p>
        </div>

        <div class="col-12 col-xl-6">
          <p class="text-body fw-semibold mb-0">Коментар към плащането [EN]</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->incomeCommentEn ?: '-' }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Свързани данни</h2>
      <div class="row gy-2">
        <div class="col-12 col-xl-4">
          <p class="text-body fw-semibold mb-0">Клиент</p>
          <p class="text-body-emphasis fw-semibold">
            @if ($document->customer)
              <a href="{{ url('/erp/customers/update/' . $document->customer->id) }}">{{ $document->customer->companyName }}</a>
            @else
              -
            @endif
          </p>
        </div>

        <div class="col-12 col-xl-4">
          <p class="text-body fw-semibold mb-0">Поръчка</p>
          <p class="text-body-emphasis fw-semibold">
            @if ($document->order)
              <a href="{{ url('/erp/orders/view/' . $document->order->id) }}">#{{ $document->order->id }}</a>
            @else
              -
            @endif
          </p>
        </div>

        <div class="col-12 col-xl-4">
          <p class="text-body fw-semibold mb-0">Търговски представител</p>
          <p class="text-body-emphasis fw-semibold">{{ $document->salesRepresentative?->nameBg ?? '-' }}</p>
        </div>

        <div class="col-12">
          <p class="text-body fw-semibold mb-0">PDF</p>
          <p class="text-body-emphasis fw-semibold">
            @if ($document->uploads->count())
              <a href="{{ $document->uploads[0]->urls->main }}" target="_blank">{{ $document->uploads[0]->name }}</a>
            @else
              -
            @endif
          </p>
        </div>

        @if ($document->incomesAllocations->count())
          <div class="col-12">
            <p class="text-body fw-semibold mb-0">Плащания</p>
            <p class="text-body-emphasis fw-semibold">
              <a href="{{ url('/erp/documents/incomes-allocations/' . $document->id) }}">{{ $document->incomesAllocations->count() }} записа</a>
            </p>
          </div>
        @endif

        @if ($document->related->count())
          <div class="col-12">
            <p class="text-body fw-semibold mb-0">Свързани документи</p>
            <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
              <thead>
              <tr class="bg-body-highlight">
                <th class="nosort border-top border-translucent @if (request('sort') == 'documentNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
                  Номер на документа
                </th>
                <th class="nosort border-top border-translucent @if (request('sort') == 'name') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
                  Вид документ
                </th>
                <th class="nosort border-top border-translucent @if (request('sort') == 'totalAmount') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
                  Стойност
                </th>
              </tr>
              </thead>
              <tbody>
              @foreach ($document->related as $row)
                <tr class="@if (request()->related == $row->id) table-info @endif">
                  <td>
                    <a href="{{ url('/erp/documents/view/' . $row->id) }}">
                      #{{ $row->documentNumber }}
                    </a>
                  </td>
                  <td>
                    {{ \App\Services\MapService::documentTypes($row->type)->labelBg }}
                    <span class="badge text-bg-light">{{ $row->type }}</span>
                  </td>
                  <td>
                    {{ price($row->totalAmount) }}
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body pb-1">
      <h2 class="h4 card-title mb-4">Артикули</h2>

      <div class="table-responsive">
        <table class="table app-table-rs table-sm table-padding fs-9 align-middle">
          @if ($document->lines)
            @foreach ($document->lines as $line)
              <thead>
              <tr class="bg-body-highlight">
                <th class="nosort border-top border-translucent"></th>
                <th class="nosort border-top border-translucent">Име</th>
                <th class="nosort border-top border-translucent">MPN</th>
                <th class="nosort border-top border-translucent">EAN</th>
                <th class="nosort border-top border-translucent">PO</th>
                <th class="nosort border-top border-translucent text-end">Цена</th>
                <th class="nosort border-top border-translucent text-end">Количество</th>
                <th class="nosort border-top border-translucent text-end">Общо</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td style="width: 1px;">
                  @if ($line->productId)
                    <i class="fa-regular fa-box me-2" data-bs-toggle="tooltip" data-bs-title="Продукт"></i>
                  @else
                    <i class="fa-regular fa-file-lines me-2" data-bs-toggle="tooltip" data-bs-title="Празен ред"></i>
                  @endif
                </td>
                <td>
                  {{ $line->name }}
                </td>
                <td>
                  {{ $line->mpn }}
                </td>
                <td>
                  {{ $line->ean }}
                </td>
                <td>
                  {{ $line->po }}
                </td>
                <td class="text-end">
                  {{ price($line->price) }}
                </td>
                <td class="text-end">
                  {{ $line->quantity }}
                </td>
                <td class="text-end">
                  @if ($document->type === \App\Enums\DocumentType::OutcomeCreditMemo)
                    <span class="text-decoration-line-through">-{{ price($line->price * $line->quantity) }}</span>
                    <span class="fw-bold">-{{ price(($line->price * $line->quantity) - $line->totalPrice) }}</span>
                  @else
                    {{ price($line->totalPrice) }}
                  @endif
                </td>
              </tr>
              @if ($line->documentItems->count())
                <tr>
                  <td colspan="8" style="padding: 0 !important;">
                    <table class="table table-sm table-hover table-bordered mt-3 fs-9 bg-body-secondary" style="margin-top: 0 !important;">
                      <thead>
                      <tr>
                        <th>Сер. №</th>
                        <th>Бележка</th>
                        <th>Покупна цена</th>
                        @if ($document->type === \App\Enums\DocumentType::OutcomeCreditMemo)
                          <th>Кредитна стойност</th>
                        @else
                          <th>Продажна цена</th>
                        @endif
                      </tr>
                      </thead>
                      <tbody>
                      @foreach ($line->documentItems as $item)
                        <tr>
                          <td class="p-0" style="height: inherit;">
                            @if ($item->storageItem->successor)
                              <a href="{{ url('/erp/storage-items/view/' . $item->storageItem->id) }}">
                                {{ $item->storageItem->serialNumber ?? 'N/A' }}
                                <i>(стар запис)</i>
                              </a>
                              <i class="fa-regular fa-circle-arrow-right"></i>
                              <a href="{{ url('/erp/storage-items/view/' . $item->storageItem->successor->id) }}">
                                {{ $item->storageItem->successor->serialNumber ?? 'N/A' }}
                                <i>(нов запис)</i>
                              </a>
                            @else
                              <a href="{{ url('/erp/storage-items/view/' . $item->storageItem->id) }}">
                                {{ $item->storageItem->serialNumber ?? 'N/A' }}
                              </a>
                            @endif
                          </td>
                          <td class="p-0" style="height: inherit;">
                            {{ $item->storageItem->note ?? 'N/A' }}
                          </td>
                          <td class="p-0" style="height: inherit;">
                            {{ $item?->storageItem?->purchasePrice ? price($item->storageItem->purchasePrice) : 'N/A' }}
                          </td>
                          @if ($document->type === \App\Enums\DocumentType::OutcomeCreditMemo)
                            <td class="p-0" style="height: inherit;">
                              @if (isset($item?->storageItem?->exit?->originalPrice))
                                <span class="text-decoration-line-through">{{ price($item->storageItem->exit->originalPrice) }}</span>
                              @endif

                              @if (isset($item?->storageItem?->exit?->sellPrice))
                                <span class="fw-bold">{{ price($item->storageItem->exit->sellPrice) }}</span>
                              @endif
                            </td>
                          @else
                            <td class="p-0" style="height: inherit;">
                              {{ $item?->storageItem?->exit?->originalPrice ? price($item->storageItem->exit->originalPrice) : 'N/A' }}
                            </td>
                          @endif
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </td>
                </tr>
              @endif
              </tbody>
            @endforeach
          @endif
        </table>
      </div>
    </div>
  </div>
@endsection
