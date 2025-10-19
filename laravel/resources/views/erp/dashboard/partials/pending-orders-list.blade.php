<div class="card h-100 border-0 bg-transparent">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Чакащи поръчки</h3>

    <a href="{{ url('/erp/orders?filter[status]=' . $pendingOrdersListStatuses . '&op[status]=in') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-cart-shopping"></i>
      Всички чакащи
    </a>
  </div>
  <div class="card-body py-0 scrollbar" style="height: 400px;">
    <div class="py-5">
      @if (count($pendingOrdersList))
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th class="nosort border-top border-translucent">
              ID
            </th>
            <th class="nosort border-top border-translucent">
              Статус
            </th>
            <th class="nosort border-top border-translucent">
              Клиент
            </th>
            <th class="nosort border-top border-translucent">
              Търговец
            </th>
          </tr>
          </thead>
          <tbody>
          @foreach ($pendingOrdersList as $row)
            <tr>
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
                @if ($row->customer)
                  {{ $row->customer->companyName }}
                @else
                  <span class="badge badge-phoenix badge-phoenix-warning">Липсва</span>
                @endif
              </td>
              <td>
                @if ($row->customer && $row->customer->salesRepresentative)
                  {{ $row->customer->salesRepresentative->nameBg }}
                @else
                  <span class="badge badge-phoenix badge-phoenix-warning">Липсва</span>
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @else
        <p class="text-body-tertiary fs-5">Нямате необработени поръчки :)</p>
      @endif
    </div>
  </div>
</div>
