@php($showSerials = !empty($showSerials))@php($documentTitle = $documentTitle ?? [
  'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::Invoice)->labelBg,
  'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::Invoice)->labelEn,
][$lang ?? 'bg'])

@extends('layouts.pdf')

@include('erp.documents.templates.partials.style')

@section('content')
  <!-- LAYOUT TABLE (HEADER & CONTENT) -->
  <table class="mx-auto" id="print-table">
    <!-- HEADER -->
    @include('erp.documents.templates.partials.header')
    <!-- /HEADER -->

    <!-- CONTENT -->
    <tbody>
    <tr>
      <td>
        <div class="container">
          <ul class="list-unstyled my-0 fs-10">
            <li class="mb-1">
              {{ ['bg' => 'ЕИК', 'en' => 'Company ID'][$lang] }}:
              <strong>{{ $document->issuerCompanyId }}</strong>
            </li>
            <li class="mb-1">
              {{ ['bg' => 'ДДС', 'en' => 'VAT ID'][$lang] }}:
              <strong>{{ $document->issuerVatId }}</strong>
            </li>
            <li class="mb-1">
              {{ ['bg' => 'Адрес', 'en' => 'Address'][$lang] }}:
              <strong>{{ ['bg' => $document->issuerAddressBg, 'en' => $document->issuerAddressEn][$lang] }}</strong>
            </li>
          </ul>

          @include('erp.documents.templates.partials.parties')

          <ul class="list-unstyled row fs-10 py-2 fw-bold" style="background-color: #ffd8bd;">
            @if ($document->incoterms)
              <li class="col">
                {{ ['bg' => 'Incoterms', 'en' => 'Incoterms'][$lang] }}:
                <strong>{{ $document->incoterms }}</strong>
              </li>
            @endif
            @if ($document->dueDate)
              <li class="col">
                {{ ['bg' => 'Плащане до', 'en' => 'Due Date'][$lang] }}:
                <strong>{{ $document->dueDate }}</strong>
              </li>
            @endif
          </ul>

          <div class="row mb-2">
            <div class="col-12">
              <table class="table table-sm table-striped fs-10">
                <thead class="mt-3">
                <tr>
                  <th scope="col" class="text-uppercase align-middle">{{ ['bg' => 'MPN', 'en' => 'MPN'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle">{{ ['bg' => 'EAN', 'en' => 'EAN'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle">{{ ['bg' => 'PO', 'en' => 'PO'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle">{{ ['bg' => 'Описание', 'en' => 'Description'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle text-end">{{ ['bg' => 'Бр.', 'en' => 'Qty'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle text-end">{{ ['bg' => 'Ед. Цена', 'en' => 'Price'][$lang] }}</th>
                  <th scope="col" class="text-uppercase align-middle text-end">{{ ['bg' => 'Общо', 'en' => 'Total'][$lang] }}</th>
                </tr>
                </thead>
                <tbody class="table-group-divider">
                @foreach ($document->lines as $line)
                  <tr>
                    <td class="px-2 align-middle">
                      {{ $line->mpn }}
                    </td>
                    <td class="px-2 align-middle">
                      {{ $line->ean }}
                    </td>
                    <td class="px-2 align-middle">
                      {{ $line->po }}
                    </td>
                    <td class="px-2 align-middle">
                      {{ $line->name }}
                    </td>
                    <td class="px-2 text-end align-middle white-space-nowrap">
                      {{ $line->quantity }}
                    </td>
                    <td class="px-2 text-end align-middle white-space-nowrap">
                      {{ price($line->price) }}
                      @if (!$document->isForeignInvoice && $lang === 'bg')
                        <br/>{{ price($line->price * 1.95583, 'лв.') }}
                      @endif
                    </td>
                    <td class="px-2 text-end align-middle white-space-nowrap">
                      {{ price($line->totalPrice) }}
                      @if (!$document->isForeignInvoice && $lang === 'bg')
                        <br/>{{ price($line->totalPrice * 1.95583, 'лв.') }}
                      @endif
                    </td>
                  </tr>
                  @if ($showSerials && $line->documentItems->count())
                    <tr>
                      <td colspan="8">
                        <i>
                          {{ ['bg' => 'Серийни номера: ', 'en' => 'Serial numbers: '][$lang] }}
                          @foreach($line->documentItems as $item)
                            {{ $item->storageItem->serialNumber }};
                          @endforeach
                        </i>
                      </td>
                    </tr>
                  @endif
                @endforeach
                <tr>
                  <td class="px-2 text-end align-middle" colspan="6">
                    {{ ['bg' => 'Данъчна основа', 'en' => 'Tax base'][$lang] }}:
                  </td>
                  <td class="px-2 text-end align-middle white-space-nowrap">
                    {{ price($document->totalAmountNoVat) }}
                    @if (!$document->isForeignInvoice && $lang === 'bg')
                      <br/>{{ price($document->totalAmountNoVat * 1.95583, 'лв.') }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="px-2 text-end align-middle" colspan="6">
                    {{ ['bg' => 'Данъчна ставка', 'en' => 'VAT rate'][$lang] }} ({{ $document->vatRate }} %):
                  </td>
                  <td class="px-2 text-end align-middle white-space-nowrap">
                    {{ price($document->totalVat) }}
                    @if (!$document->isForeignInvoice && $lang === 'bg')
                      <br/>{{ price($document->totalVat * 1.95583, 'лв.') }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="px-2 text-end fw-bold align-middle" colspan="6">
                    {{ ['bg' => 'Сума за плащане', 'en' => 'Income amount'][$lang] }}
                  </td>
                  <td class="px-2 text-end align-middle white-space-nowrap fw-bold">
                    {{ price($document->totalAmount) }}
                    @if (!$document->isForeignInvoice && $lang === 'bg')
                      <br/>{{ price($document->totalAmount  * 1.95583, 'лв.') }}
                    @endif
                  </td>
                </tr>
                </tbody>
              </table>
            </div>

            @if ($document->incomeCommentBg && $lang === 'bg')
              <p class="text-end fs-10 mb-2">
                <i>{{ $document->incomeCommentBg }}</i>
              </p>
            @endif
            @if ($document->incomeCommentEn && $lang === 'en')
              <p class="text-end fs-10 mb-2">
                <i>{{ $document->incomeCommentEn }}</i>
              </p>
            @endif
          </div>

          @include('erp.documents.templates.partials.related-documents')

          @include('erp.documents.templates.partials.footer')
        </div>
      </td>
    </tr>
    </tbody>
    <!-- /CONTENT -->
  </table>
@endsection
