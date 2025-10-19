@if (count($incomes))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($incomes->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $incomes->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'id') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Номер на плащането
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'customerId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'customerId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Клиент
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'paidAmount') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'paidAmount', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Стойност
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($incomes as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/incomes/update/' . $row->id) }}">
                #{{ $row->id }}
              </a>
            </td>
            <td>
              @if ($row->customer)
                {{ $row->customer->companyName }}
              @else
                -
              @endif
            </td>
            <td>
              {{ price($row->paidAmount) }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($incomes->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $incomes->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
