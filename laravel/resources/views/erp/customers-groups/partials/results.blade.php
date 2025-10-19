@if (count($customersGroups))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">
            Име
          </th>
          <th class="nosort border-top border-translucent">
            Процент отстъпка
          </th>
          <th class="nosort border-top border-translucent">
            Брой клиенти
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($customersGroups as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/customers-groups/update/' . $row->id) }}">
                {{ $row->nameBg }}
              </a>
            </td>
            <td>
              <span class="badge text-bg-secondary">{{ $row->discountPercent }}%</span>
            </td>
            <td>
              <a href="{{ url('/erp/customers/?filter[groupId]=' . $row->id) }}&op[groupId]=eq">
                {{ number_format($row->customers->count()) }}
              </a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@else
  @include('shared.no-rs')
@endif
