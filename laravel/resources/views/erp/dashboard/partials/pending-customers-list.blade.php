<div class="card h-100 border-0 bg-transparent">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Чакащи клиенти</h3>

    <a href="{{ url('/erp/customers') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-person"></i>
      Всички чакащи
    </a>
  </div>
  <div class="card-body py-0 scrollbar" style="height: 400px;">
    <div class="py-5">
      @if (count($waitingCustomersList))
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th class="nosort border-top border-translucent">
              Клиент
            </th>
            <th class="nosort border-top border-translucent">
              Действие
            </th>
            <th class="nosort border-top border-translucent">
              Дата
            </th>
          </tr>
          </thead>
          <tbody>
          @foreach ($waitingCustomersList as $row)
            <tr>
              <td>
                <a href="{{ url('/erp/customers/update/' . $row->id) }}">
                  {{ $row->email }}
                </a>
              </td>
              <td>
                @if ($row->statusType->value == \App\Enums\CustomerStatusType::WaitingApproval->value)
                  <span class="badge badge-phoenix badge-phoenix-danger">Одобрение</span>
                @endif

                @if ($row->creditLineRequested)
                  <span class="badge badge-phoenix badge-phoenix-warning">
                    <abbr title="Кредитна линия">КЛ</abbr>
                  </span>
                @endif
              </td>
              <td>
                {{ $row->createdAt }}
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @else
        <p class="text-body-tertiary fs-5">Нямате неодобрени клиенти :)</p>
      @endif
    </div>
  </div>
</div>
