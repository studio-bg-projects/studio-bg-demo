@if (count($shipments))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($shipments->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $shipments->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'parcelId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'parcelId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Товарителница
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'createdAt') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'createdAt', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дата на създаване
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($shipments as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/shipments/speedy/view/' . $row->id) }}">
                {{ $row->parcelId }}
              </a>
            </td>
            <td>
              {{ $row->createdAt }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($shipments->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $shipments->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
