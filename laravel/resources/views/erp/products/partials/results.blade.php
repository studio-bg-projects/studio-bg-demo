@if (count($products))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($products->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          @if (!empty(request()->tool['showNumber']))
            <th class="nosort border-top border-translucent @if (request('sort') == 'id') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif" style="width: 10px;">
              #
            </th>
          @endif
          <th style="width: 5rem;" class="nosort border-top border-translucent @if (request('sort') == 'nameBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif"></th>
          <th class="sort border-top border-translucent @if (request('sort') == 'mpn') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'mpn', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              MPN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'ean') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'ean', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              EAN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'nameBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nameBg', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'price') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Цена (продажна)
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'purchasePrice') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'purchasePrice', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Доставна цена
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'quantity') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Бр.
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'onStock') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'onStock', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              На склад
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'usageStatus') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'usageStatus', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $row)
          <tr @if (!$row->categories->count()) class="bg-danger-lighter" @endif>
            @if (!empty(request()->tool['showNumber']))
              <td>
                {{ !empty(request()->page) ? request()->page : 1 }}.{{ $i = isset($i) ? $i + 1 : 1 }}
              </td>
            @endif
            <td>
              <div class="d-block border border-translucent rounded-2 table-preview">
                <a href="{{ url('/erp/products/update/' . $row->id) }}">
                  @if ($row->uploads->isNotEmpty())
                    <img src="{{ $row->uploads->first()->urls->tiny }}" alt=""/>
                  @else
                    <img src="{{ asset('img/icons/file-placeholder.svg') }}" alt=""/>
                  @endif
                </a>
              </div>
            </td>
            <td>
              <a href="{{ url('/erp/products/update/' . $row->id) }}">
                {{ $row->mpn }}
              </a>

              @if (!$row->categories->count())
                <span class="badge badge-phoenix badge-phoenix-danger ms-1">Няма категории</span>
              @endif
            </td>
            <td>
              {{ $row->ean }}
            </td>
            <td>
              {{ $row->nameBg }}
            </td>
            <td>
              {{ price($row->price) }}
            </td>
            <td>
              {{ price($row->purchasePrice) }}
            </td>
            <td>
              {{ (int)$row->quantity }} бр.
            </td>
            <td>
              @if ($row->onStock)
                <span class="badge badge-phoenix badge-phoenix-success">На склад</span>
              @else
                <span class="badge badge-phoenix badge-phoenix-warning">Изисква доставка</span>
              @endif
            </td>
            <td>
              <span class="badge badge-phoenix badge-phoenix-{{ \App\Services\MapService::productUsageStatus($row->usageStatus)->color }}">
                {{ \App\Services\MapService::productUsageStatus($row->usageStatus)->short }}
              </span>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($products->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
