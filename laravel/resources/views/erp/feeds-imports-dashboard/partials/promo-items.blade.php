<div class="card h-100 border-0 bg-transparent">
  <div class="card-header border-bottom-0 pb-0 d-flex flex-row">
    <h3 class="text-body-emphasis">Спрени от синхронизация</h3>
  </div>
  <div class="card-body py-0 scrollbar" style="height: 400px;">
    <div class="py-5">
      @if(count($promoItems))
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th class="nosort border-top border-translucent">Доставчик</th>
            <th class="nosort border-top border-translucent">Име</th>
            <th class="nosort border-top border-translucent">EAN</th>
          </tr>
          </thead>
          <tbody>
          @foreach($promoItems as $row)
            <tr>
              <td>
                {{ $row->feedImport->providerName ?? '-' }}
              </td>
              <td>
                {{ $row->itemName }}
              </td>
              <td>
                {{ $row->itemEan }}
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
