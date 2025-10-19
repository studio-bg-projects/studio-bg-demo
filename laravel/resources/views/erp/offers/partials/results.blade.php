@if (count($offers))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($offers->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $offers->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'offerNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'offerNumber', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Заглавие
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'status') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'companyName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'companyName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Фирма
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'validUntil') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'validUntil', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              До дата
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($offers as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/offers/update/' . $row->id) }}">
                {{ $row->offerNumber }}
              </a>
            </td>
            <td>
              {{ \App\Services\MapService::offerStatuses($row->status)->label }}
            </td>
            <td>
              {{ $row->companyName }}
            </td>
            <td>
              {{ $row->validUntil }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($offers->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $offers->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
