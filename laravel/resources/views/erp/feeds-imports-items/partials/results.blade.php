@if (count($items))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($items->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $items->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'itemMpn') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemMpn', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              MPN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemEan') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemEan', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              EAN
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'parentId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'parentId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemPrice') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemPrice', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Цена
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'itemQuantity') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'itemQuantity', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Количество
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $row)
          <tr>
            <td>
              {{ $row->itemMpn }}
            </td>
            <td>
              {{ $row->itemEan }}
            </td>
            <td>
              {{ $row->itemName }}
            </td>
            <td>
              <i>{{ price($row->itemPrice) }} + {{ $row->feedImport->markupPercent }}% =</i>
              <b>{{ price($row->itemPrice + ($row->itemPrice * $row->feedImport->markupPercent / 100)) }}</b>
            </td>
            <td>
              {{ $row->itemQuantity }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($items->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $items->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
