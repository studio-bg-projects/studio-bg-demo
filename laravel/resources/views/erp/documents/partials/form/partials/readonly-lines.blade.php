<div class="card mt-3">
  <div class="card-body pb-1">
    <h2 class="h5 card-title mb-4">Артикули</h2>

    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent"></th>
          <th class="nosort border-top border-translucent">Име</th>
          <th class="nosort border-top border-translucent">MPN</th>
          <th class="nosort border-top border-translucent">EAN</th>
          <th class="nosort border-top border-translucent">PO</th>
          <th class="nosort border-top border-translucent text-end">Цена</th>
          <th class="nosort border-top border-translucent text-end">Количество</th>
          <th class="nosort border-top border-translucent text-end">Общо</th>
        </tr>
        </thead>
        <tbody>
        @foreach($lines ?? [] as $line)
          <tr>
            <td style="width: 1px;">
              @if ($line['productId'])
                <i class="fa-regular fa-box me-2" data-bs-toggle="tooltip" data-bs-title="Продукт"></i>
              @else
                <i class="fa-regular fa-file-lines me-2" data-bs-toggle="tooltip" data-bs-title="Празен ред"></i>
              @endif
            </td>
            <td>{{ $line['name'] ?? '' }}</td>
            <td>{{ $line['mpn'] ?? '' }}</td>
            <td>{{ $line['ean'] ?? '' }}</td>
            <td>{{ $line['po'] ?? '' }}</td>
            <td class="text-end">{{ price($line['price'] ?? 0) }}</td>
            <td class="text-end">{{ $line['quantity'] ?? '' }}</td>
            <td class="text-end">{{ price($line['totalPrice'] ?? 0) }}</td>
          </tr>
          @if(!empty($line['items']))
            <tr>
              <td colspan="8" style="padding: 0 !important;">
                <table class="table table-sm table-hover table-bordered mt-3 fs-9 bg-body-secondary" style="margin-top: 0 !important;">
                  <thead>
                  <tr>
                    <th>Сер. №</th>
                    <th>Бележка</th>
                    <th>Покупна цена</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($line['items'] as $item)
                    <tr>
                      <td class="p-0" style="height: inherit;">
                        {{ $item['serialNumber'] ?? 'N/A' }}
                      </td>
                      <td class="p-0" style="height: inherit;">
                        {{ $item['note'] ?? 'N/A' }}
                      </td>
                      <td class="p-0" style="height: inherit;">
                        {{ price($item['purchasePrice']) }}
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </td>
            </tr>
          @endif
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
