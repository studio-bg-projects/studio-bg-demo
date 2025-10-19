@if (count($users))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($users->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $users->links('pagination::bootstrap-5') }}
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
          <th class="sort border-top border-translucent @if (request('sort') == 'fullName') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'fullName', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Име
            </a>
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/users/update/' . $row->id) }}">
                {{ $row->email }}
              </a>
            </td>
            <td>
              {{ $row->fullName }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($users->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
