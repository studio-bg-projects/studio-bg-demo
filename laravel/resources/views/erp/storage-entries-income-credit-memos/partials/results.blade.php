@if (count($notes))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">Дата</th>
          <th class="nosort border-top border-translucent">Бележка</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($notes as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/storage-entries/income-credit-memos/view/' . $row->id) }}" class="text-truncate d-inline-block" style="max-width: 25rem;">
                {{ $row->date->format('Y-m-d') }}
              </a>
            </td>
            <td class="text-truncate" style="max-width: 25rem;">
              {{ $row->note }}
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
    <a href="{{ url('/erp/storage-entries/income-credit-memos/create/' . $document->id) }}" class="btn btn-lg btn-phoenix-primary">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Добави ново кредитно известие
    </a>
  </div>
@endif
