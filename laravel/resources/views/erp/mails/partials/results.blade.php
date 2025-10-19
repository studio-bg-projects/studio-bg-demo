@if (count($mails))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($mails->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $mails->links('pagination::bootstrap-5') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">
            До
          </th>
          <th class="nosort border-top border-translucent">
            Заглавие
          </th>
          <th class="nosort border-top border-translucent">
            Съдържание
          </th>
          <th class="nosort border-top border-translucent">
            Изпратен
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($mails as $row)
          <tr>
            <td>
              <a href="{{ url('/erp/mails/view/' . $row->id) }}">
                #{{ $row->id }}
                - {{ $row->to }}
              </a>
            </td>
            <td>
              {{ $row->subject }}
            </td>
            <td>
              {{ substr(strip_tags($row->content), 0, 100) }}...
            </td>
            <td>
              {{ $row->sentDate ?? '-' }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @if ($mails->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $mails->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif
