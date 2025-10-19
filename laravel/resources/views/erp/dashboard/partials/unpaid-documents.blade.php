<div class="card h-100">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Неплатени документи</h3>

    <a href="{{ url('/erp/documents?filter[type]=' . $unpaidDocumentsTypes . '&op[type]=in&filter[leftAmount]=0&op[leftAmount]=neq') }}" class="ms-auto btn btn-phoenix-secondary">
      <i class="fa-regular fa-file-lines"></i>
      Всички неплатени
    </a>
  </div>
  <div class="card-body py-0">
    <div class="card-body py-0 scrollbar" style="height: 400px;">
      <div class="py-5">
        @if (count($pendingOrdersList))
          <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
            <thead>
            <tr class="bg-body-highlight">
              <th class="nosort border-top border-translucent">
                Номер на документа
              </th>
              <th class="nosort border-top border-translucent">
                Вид документ
              </th>
              <th class="nosort border-top border-translucent">
                Свързани
              </th>
              <th class="nosort border-top border-translucent">
                Стойност
              </th>
              <th class="nosort border-top border-translucent">
                Дължимо
              </th>
              <th class="nosort border-top border-translucent">
                Клиент
              </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($unpaidDocumentsList as $row)
              <tr class="@if (request()->related == $row->id) table-info @endif">
                <td>
                  <a href="{{ url('/erp/documents/update/' . $row->id) }}">
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
              </tr>
            @endforeach
            </tbody>
          </table>
        @else
          <p class="text-body-tertiary fs-5">Нямате неплатени документи :)</p>
        @endif
      </div>
    </div>
  </div>
</div>
