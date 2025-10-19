<div class="card h-100">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Всички фийдове</h3>
  </div>
  <div class="card-body py-0">
    <div class="py-5">
      @if(count($feeds))
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th class="nosort border-top border-translucent">Име</th>
            <th class="nosort border-top border-translucent">Записи</th>
            <th class="nosort border-top border-translucent">Последна синхронизация</th>
          </tr>
          </thead>
          <tbody>
          @foreach($feeds as $feed)
            <tr>
              <td>
                <a href="{{ url('/erp/feeds-imports/update/' . $feed->id) }}">
                  {{ $feed->providerName }}
                </a>
              </td>
              <td>
                <a href="{{ url('/erp/feeds-imports/items/' . $feed->id) }}">
                  {{ $feed->items ? $feed->items->count() : 0 }}
                </a>
              </td>
              <td>
                {{ $feed->lastSync ?: 'Няма' }}
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @else
        <p class="text-body-tertiary fs-5">Няма записи :)</p>
      @endif
    </div>
  </div>
</div>
