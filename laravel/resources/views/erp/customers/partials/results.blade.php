@if (count($customers))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($customers->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $customers->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'email') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Имейл
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'groupId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'groupId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Група
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'companyId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'companyId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              ЕИК
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'companyName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'companyName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Фирма
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'statusType') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'statusType', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'creditLineRequested') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'creditLineRequested', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Чака
              <abbr title="Кредитна линия">КЛ</abbr>
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($customers as $row)
          <tr @if ($row->isDeleted) class="text-decoration-line-through bg-danger-lighter" @endif
          @if ($row->statusType->value == \App\Enums\CustomerStatusType::WaitingApproval->value || $row->creditLineRequested) class="bg-danger-lighter" @endif
          >
            <td>
              <a href="{{ url('/erp/customers/update/' . $row->id) }}">
                @if ($row->isDeleted)
                  <span class="badge badge-phoenix badge-phoenix-danger">Изтрит</span>
                @endif
                {{ $row->email }}
              </a>
            </td>
            <td>
              @if ($row->groupId)
                {{ $row->group->nameBg }}
                <span class="badge text-bg-secondary">{{ $row->group->discountPercent }}%</span>
              @else
                <span class="badge badge-phoenix badge-phoenix-danger">Липсва!</span>
              @endif
            </td>
            <td>
              {{ $row->companyId }}
            </td>
            <td>
              {{ $row->companyName }}
            </td>
            <td>
              <span class="badge badge-phoenix badge-phoenix-{{ \App\Services\MapService::customerStatusType($row->statusType)->color }}">
                {{ \App\Services\MapService::customerStatusType($row->statusType)->label }}
              </span>
            </td>
            <td>
              @if ($row->creditLineRequested)
                <span class="badge badge-phoenix badge-phoenix-warning">Чака
                  <abbr title="Кредитна линия">КЛ</abbr>
                </span>
              @else
                <span class="badge badge-phoenix badge-phoenix-success">Ок</span>
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($customers->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $customers->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
