@if (count($addresses))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">
            Адрес
          </th>
          <th class="nosort border-top border-translucent">
            ПК/ZIP
          </th>
          <th class="nosort border-top border-translucent">
            Държава
          </th>
          <th class="nosort border-top border-translucent">
            Град
          </th>
          <th class="nosort border-top border-translucent"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($addresses as $row)
          <tr @if ($row->isDeleted) class="text-decoration-line-through bg-danger-lighter" @endif>
            <td>
              <a href="{{ url('/erp/customers/addresses/update/' . $row->id) }}" title="{{ $row->address }}" class="text-truncate d-inline-block" style="max-width: 25rem;">
                #{{ $row->id }}

                @if ($row->isDeleted)
                  <span class="badge badge-phoenix badge-phoenix-danger">Изтрит</span>
                @endif
                {{ $row->street }}
                {{ $row->addressDetails }}
              </a>
            </td>
            <td>
              {{ $row->zipCode }}
            </td>
            <td>
              {{ $row->country->name }}
              [{{ $row->country->isoCode3 }}]
            </td>
            <td>
              {{ $row->city }}
            </td>
            <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
              <div class="btn-reveal-trigger position-static">
                <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                  <i class="fa-regular fa-ellipsis-h fs-10"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end py-2">
                  <a class="dropdown-item" href="{{ url('/erp/customers/addresses/update/' . $row->id) }}">Преглед/Редакция</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-danger" href="{{ url('/erp/customers/addresses/delete/' . $row->id) }}" onclick="return confirm('Сигурни ли сте, че искате да изтриете този АДРЕС?')">
                    Изтрий адреса
                  </a>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@else
  @include('shared.no-rs')

  <div class="text-center">
    <a href="{{ url('/erp/customers/addresses/create/' . $customer->id) }}" class="btn btn-lg btn-phoenix-primary">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Добави нов адрес
    </a>
  </div>
@endif
