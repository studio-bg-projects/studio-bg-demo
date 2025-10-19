@if (count($specifications))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($specifications->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $specifications->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'nameBg') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nameBg', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'valueType') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'valueType', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Вид
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'isActive') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'isActive', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Статус
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($specifications as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/specifications/update/' . $row->id) }}">
                {{ $row->nameBg }}
              </a>
            </td>
            <td>
              {{ \App\Services\MapService::specificationValueTypes($row->valueType)->label }}
              <span class="badge text-bg-light">{{ $row->valueType }}</span>
            </td>
            <td>
              @if ($row->isActive)
                <span class="badge badge-phoenix badge-phoenix-success">Активен</span>
              @else
                <span class="badge badge-phoenix badge-phoenix-secondary">Неактивен</span>
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($specifications->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $specifications->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
