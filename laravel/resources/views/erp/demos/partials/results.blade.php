@if (count($demos))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($demos->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $demos->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'demoNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'demoNumber', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Заглавие
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'status') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'companyName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'companyName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Фирма
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'addedDate') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'addedDate', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              До дата
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($demos as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/demos/update/' . $row->id) }}">
                {{ $row->demoNumber }}
              </a>
            </td>
            <td>
              {{ \App\Services\MapService::demoStatuses($row->status)->label }}
            </td>
            <td>
              {{ $row->companyName }}
            </td>
            <td>
              {{ $row->addedDate }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($demos->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $demos->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
