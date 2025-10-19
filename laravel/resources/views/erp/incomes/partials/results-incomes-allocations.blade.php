@if (count($incomesAllocations))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($incomesAllocations->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $incomesAllocations->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'incomeId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'incomeId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Част от плащане
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'allocatedAmount') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'allocatedAmount', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Стойност на разпределението
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'documentId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'documentId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Чрез документ
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'customerId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'customerId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Клиент
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($incomesAllocations as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/incomes/update/' . $row->incomeId) }}">
                #{{ $row->incomeId }}
              </a>
            </td>
            <td>
              {{ price($row->allocatedAmount) }}
              <i>от обща стойност на плащане {{ price($row->income->paidAmount) }}</i>
            </td>
            <td>
              @if ($row->documentId)
                {{ \App\Services\MapService::documentTypes($row->document->getOriginal('type'))->labelBg }}
                #{{ $row->document->getOriginal('documentNumber') }}
              @else
                -
              @endif
            </td>
            <td>
              @if ($row->income->customer)
                {{ $row->income->customer->companyName }}
              @else
                -
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($incomesAllocations->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $incomesAllocations->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
