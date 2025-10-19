@if (count($orders))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($orders->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $orders->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent"></th>
          <th class="sort border-top border-translucent @if (request('sort') == 'id') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              ID
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'status') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'customerId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'customerId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Клиент
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'createdAt') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'createdAt', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дата
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($orders as $row)
          <tr>
            <td>
              <a class="dropdown-indicator-icon text-body-tertiary" href="#order-details-{{ $row->id }}" role="button" data-bs-toggle="collapse">
                <i class="fa-regular fa-angle-up"></i>
              </a>
            </td>
            <td>
              <a href="{{ url('/erp/orders/view/' . $row->id) }}">
                #{{ $row->id }}
              </a>
            </td>
            <td>
              <span style="color: {{ \App\Services\MapService::orderStatus($row->status)->color }}">
                {{ \App\Services\MapService::orderStatus($row->status)->labelBg }}
              </span>
            </td>
            <td>
              @if ($row->customerId)
                {{ $row->customer->email }}
              @endif
            </td>
            <td>
              {{ $row->createdAt }}
            </td>
          </tr>
          <tr class="collapse" id="order-details-{{ $row->id }}">
            <td colspan="5" class="py-3">
              @include('erp.orders.partials.process-flow', [
                'order' => $row
              ])
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($orders->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $orders->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
