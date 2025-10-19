@if (count($documents))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($documents->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'documentNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'documentNumber', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Номер
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'supplierId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'supplierId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Доставчик
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'documentDate') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'documentDate', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дата
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($documents as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/storage-entries/update/' . $row->id) }}">
                {{ $row->documentNumber }}
              </a>
            </td>
            <td>
              @if ($row->supplier)
                {{ $row->supplier->companyName }}
                [{{ $row->supplier->companyId }}]
              @else
                -
              @endif
            </td>
            <td>
              {{ $row->documentDate->format('Y-m-d') }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($documents->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
