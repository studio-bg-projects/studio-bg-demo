@if (count($documents))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($documents->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="sort border-top border-translucent @if (request('sort') == 'documentNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'documentNumber', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Номер на документа
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'name') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Вид документ
            </a>
          </th>
          <th class="nosort border-top border-translucent">
            Свързани
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'totalAmount') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'totalAmount', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Стойност
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'leftAmount') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'leftAmount', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Дължимо
            </a>
          </th>
          <th class="sort border-top border-translucent @if (request('sort') == 'customerId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'customerId', 'd' => request('d') == 'asc' ? 'desc' : 'asc', 'page' => 1]) }}">
              Клиент
            </a>
          </th>
          <th class="nosort border-top border-translucent"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($documents as $row)
          <tr class="@if (request()->related == $row->id) table-info @endif">
            <td>
              <a href="{{ url('/erp/documents/view/' . $row->id) }}">
                #{{ $row->documentNumber }}
              </a>
            </td>
            <td>
              {{ \App\Services\MapService::documentTypes($row->type)->labelBg }}
              <span class="badge text-bg-light">{{ $row->type }}</span>
            </td>
            <td>
              @if ($row->related()->count())
                <a class="text-decoration-none" href="?related={{ $row->id }}" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Покажи свързаните документи">
                  <span class="badge badge-phoenix @if (request()->related == $row->id) badge-phoenix-primary @else badge-phoenix-info @endif fs-10">
                    {{ $row->related()->count() }} бр.
                  </span>
                </a>
              @else
                <span class="badge badge-phoenix badge-phoenix-secondary">
                  Няма
                </span>
              @endif
            </td>
            <td>
              {{ price($row->totalAmount) }}
            </td>
            <td>
              @if (\App\Services\MapService::documentTypes($row->type)->isPayable)
                <span class="badge badge-phoenix @if ($row->leftAmount != 0) badge-phoenix-danger @else badge-phoenix-success @endif">
                  {{ price($row->leftAmount) }}
                </span>
              @else
                <span class="badge badge-phoenix badge-phoenix-secondary">
                  Не се плаща
                </span>
              @endif
            </td>
            <td>
              @if ($row->customerId)
                {{ $row->customer->companyName }}
                / {{ $row->customer->companyId }}
                / {{ $row->customer->firstName }} {{ $row->customer->lastName }}
              @else
                -
              @endif
            </td>
            <td style="width: 1px;">
              <a href="{{ url('/erp/documents/view/' . $row->id) }}">
                {!! $row->barcode !!}
              </a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($documents->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
