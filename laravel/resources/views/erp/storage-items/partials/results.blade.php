@if (count($storageItems))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($storageItems->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $storageItems->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'productId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'productId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Артикули
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'product.mpn') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'product.mpn', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              MPN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'product.ean') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'product.ean', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              EAN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'invoiceDate') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoiceDate', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дата
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'purchasePrice') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'purchasePrice', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Цена
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'serialNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'serialNumber', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              SN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'note') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'note', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Бележка
            </a>
          </th>
          @isset($showWriteOffProtocolLink)
            <th class="nosort border-top border-translucent">Отписване</th>
          @endisset
        </tr>
        </thead>
        <tbody>
        @foreach ($storageItems as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/storage-items/view/' . $row->id) }}" class="@if ($row->isExited) text-decoration-line-through @endif">
                {{ $row->product?->nameBg }} (ID: {{ $row->id }})
              </a>

              @if ($row->product->usageStatus === \App\Enums\ProductUsageStatus::InternalUse)
                <span class="badge badge-phoenix badge-phoenix-{{ \App\Services\MapService::productUsageStatus($row->product->usageStatus)->color }}">
                  {{ \App\Services\MapService::productUsageStatus($row->product->usageStatus)->short }}
                </span>
              @endif
            </td>
            <td>
              {{ $row->product?->mpn }}
            </td>
            <td>
              {{ $row->product?->ean }}
            </td>
            <td>
              {{ $row->invoiceDate }}
            </td>
            <td>
              {{ $row->purchasePrice }}
            </td>
            <td>
              {{ $row->serialNumber }}
            </td>
            <td>
              {{ $row->note }}
            </td>
            @isset($showWriteOffProtocolLink)
              <td>
                @unless ($row->isExited)
                  <a href="{{ url('/erp/storage-items/writeoff-protocol/' . $row->id) }}">Протокол</a>
                @endunless
              </td>
            @endisset
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($storageItems->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $storageItems->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
