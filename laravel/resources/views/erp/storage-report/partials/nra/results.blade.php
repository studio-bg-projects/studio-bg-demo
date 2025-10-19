@if ($items->isNotEmpty())
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="d-flex justify-content-end gap-2 mb-3">
      <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-phoenix-secondary">
        <i class="fa-regular fa-file-excel"></i>
        Експорт в Excel
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent"></th>
          <th class="nosort border-top border-translucent">
            Артикул
          </th>
          <th class="nosort border-top border-translucent">
            Сериен номер
          </th>
          <th class="nosort border-top border-translucent">
            Посока
          </th>
          <th class="nosort border-top border-translucent">
            Тип документ
          </th>
          <th class="nosort border-top border-translucent">
            Документ
          </th>
          <th class="nosort border-top border-translucent">
            Дата
          </th>
          <th class="nosort border-top border-translucent">
            ИД номер
          </th>
          <th class="nosort border-top border-translucent">
            Контрагент
          </th>
          <th class="nosort border-top border-translucent">
            Данъчна основа
          </th>
          <th class="nosort border-top border-translucent">
            ДДС
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $productData)
          @php($product = (array)($productData['product'] ?? []))
          <tr>
            <td colspan="11">
              <div class="d-flex flex-column text-center">
                <span class="fw-semibold">{{ $product['name'] ?? 'Неизвестен продукт' }}</span>
                @if (!empty($product['details']))
                  <span class="text-body-secondary">{{ $product['details'] }}</span>
                @endif
              </div>
            </td>
          </tr>
          @foreach ($productData['items'] as $row)
            <tr>
              <td>
                @if (!empty($row['storageItemId']))
                  @include('erp.storage-report.partials.view-item-button', ['itemId' => $row['storageItemId']])
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <span>{{ $row['itemName'] ?? 'Неизвестен артикул' }}</span>
                </div>
                @if (!empty($row['itemLabel']))
                  <span class="text-body-secondary">{{ $row['itemLabel'] }}</span>
                @endif
              </td>
              <td>{{ $row['serialNumber'] ?? '' }}</td>
              <td>{{ $row['directionLabel'] ?? '' }}</td>
              <td>{{ $row['documentType'] ?? '' }}</td>
              <td>{{ $row['documentNumber'] ?? '' }}</td>
              <td>{{ $row['documentDate'] ?? '' }}</td>
              <td>{{ $row['partnerId'] ?? '' }}</td>
              <td>{{ $row['partnerName'] ?? '' }}</td>
              <td>{{ $row['formattedTaxBase'] ?? '' }}</td>
              <td>{{ $row['formattedTax'] ?? '' }}</td>
            </tr>
          @endforeach
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@else
  @include('shared.no-rs')
@endif

@include('erp.storage-report.partials.storage-item-quick-view')
