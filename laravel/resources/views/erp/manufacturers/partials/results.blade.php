@if (count($manufacturers))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($manufacturers->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $manufacturers->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th style="width: 5rem;" class="nosort border-top border-translucent @if (request('sort') == 'name') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif"></th>
          <th class="sort border-top border-translucent @if (request('sort') == 'name') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'sortOrder') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sortOrder', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Позиция
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
        @foreach ($manufacturers as $row)
          <tr>
            <td>
              <div class="d-block border border-translucent rounded-2 table-preview">
                @if ($row->uploads->isNotEmpty())
                  <img src="{{ $row->uploads->first()->urls->tiny }}" alt=""/>
                @else
                  <img src="{{ asset('img/icons/image-placeholder.svg') }}" alt=""/>
                @endif
              </div>
            </td>
            <td>
              <a href="{{ url('/erp/manufacturers/update/' . $row->id) }}">
                {{ $row->name }}
              </a>
            </td>
            <td>
              {{ $row->sortOrder }}
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
    @if ($manufacturers->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $manufacturers->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
