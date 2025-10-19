@if(count($feeds))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'providerName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'providerName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Доставчик
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'markupPercent') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'markupPercent', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              % надценка
            </a>
          </th>
          <th class="nosort border-top border-translucent">
            Брой записи
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'lastSync') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'lastSync', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Последна синхронизация
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($feeds as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/feeds-imports/update/' . $row->id) }}">{{ $row->providerName }}</a>
            </td>
            <td>
              {{ $row->markupPercent }} %
            </td>
            <td>
              <a href="{{ url('/erp/feeds-imports/items/' . $row->id) }}">
                {{ $row->items ? $row->items->count() : 0 }}
              </a>
            </td>
            <td>
              {{ $row->lastSync }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $feeds->links('pagination::bootstrap-5') }}
  </div>
@else
  @include('shared.no-rs')
@endif
