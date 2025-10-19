@if (count($searches))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($searches->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $searches->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'keyword') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'keyword', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дума
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'language') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'language', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Език
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'categoryId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'categoryId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Категория
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'subCategoryId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'subCategoryId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Под категория
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'customerId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'customerId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Клиент
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'results') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'results', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Резултати
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
        @foreach ($searches as $row)
          <tr>
            <td>
              {{ $row->keyword }}
            </td>
            <td>
              {{ $row->language }}
            </td>
            <td>
              @if ($row->category)
                {{ $row->category->nameBg }}
              @else
                -
              @endif
            </td>
            <td>
              @if ($row->subCategory)
                {{ $row->subCategory->nameBg }}
              @else
                -
              @endif
            </td>
            <td>
              @if ($row->customer)
                <a href="{{ url('/erp/customers/update/' . $row->customerId) }}">
                  {{ $row->customer->email }}
                </a>
              @else
                -
              @endif
            </td>
            <td>
              {{ $row->results }}
            </td>
            <td>
              {{ $row->createdAt }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($searches->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $searches->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
