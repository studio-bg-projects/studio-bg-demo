@if (count($salesRepresentatives))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($salesRepresentatives->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $salesRepresentatives->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'nameBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nameBg', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'titleBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'titleBg', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Позиция
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'phone1') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'phone1', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Телефон
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'email1') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email1', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Email
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($salesRepresentatives as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/sales-representatives/update/' . $row->id) }}">
                {{ $row->nameBg }}
              </a>
            </td>
            <td>
              {{ $row->titleBg }}
            </td>
            <td>
              {{ $row->phone1 }}
            </td>
            <td>
              {{ $row->email1 }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($salesRepresentatives->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $salesRepresentatives->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
