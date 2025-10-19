@if (count($products))
  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    @if ($products->lastPage() > 1)
      <div class="mt-n3 mb-1">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif

    @foreach ($products as $product)
      @php($rows = $items[$product->productId])

      <div class="border rounded p-3 mb-4">
        <h5 class="mb-3">
          <a href="{{ url('/erp/products/update/' . $rows->first()->product->id) }}" target="_blank">
            {{ $rows->first()->product->nameBg }}
          </a>
        </h5>
        <div class="mb-2">
          Цена: {{ price($rows->first()->product->price) }}
        </div>
        <div class="mb-2">
          Наличност: {{ $rows->first()->product->quantity }}
        </div>
        <div class="mb-2">
          MPN: {{ $rows->first()->product->mapn }}
        </div>
        <div class="mb-2">
          EAN: {{ $rows->first()->product->ean }}
        </div>
        <div class="mb-3">
          Спрян от синхронизация:
          @if ($rows->first()->product->nonSyncStatus)
            <strong class="text-warning">{{ \App\Services\MapService::productNonSyncStatus($rows->first()->product->nonSyncStatus)->label }}</strong>
          @else
            Не е спрян
          @endif
        </div>
        <table class="table table-sm @if (!$product->leadCount) table-danger @endif" data-table="{{ $product->productId }}">
          <thead>
          <tr>
            <th>Доставчик</th>
            <th>Име</th>
            <th>MPN</th>
            <th>EAN</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Водещ</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($rows as $row)
            <tr>
              <td>
                {{ $row->feedImport->providerName ?? '-' }}
              </td>
              <td>
                {{ $row->itemName }}
              </td>
              <td>
                {{ $row->itemMpn }}
              </td>
              <td>
                {{ $row->itemEan }}
              </td>
              <td>
                <i>{{ price($row->itemPrice) }} + {{ $row->feedImport->markupPercent }}% =</i>
                <b>{{ price($row->itemPrice + ($row->itemPrice * $row->feedImport->markupPercent / 100)) }}</b>
              </td>
              <td>
                {{ $row->itemQuantity }}
              </td>
              <td>
                <input type="radio" name="lead_{{ $product->productId }}" class="form-check-input js-set-lead" data-id="{{ $row->id }}" data-productId="{{ $product->productId }}" {{ $row->isLeadRecord ? 'checked' : '' }}>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endforeach

    @if ($products->lastPage() > 1)
      <div class="mt-3 mb-n5">
        {{ $products->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@else
  @include('shared.no-rs')
@endif

<script type="module">
  $(document).on('change', '.js-set-lead', function () {
    const itemId = $(this).data('id');

    const productId = $(this).data('productid');
    $(`[data-table="${productId}"]`).removeClass('table-danger');

    $.ajax({
      url: '{{ url('/erp/feeds-imports-items/conflicts/set-lead') }}/' + itemId,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: () => {
        window.appToast({
          body: 'Записът е актуализиран.',
          type: 'success',
          icon: 'fa-check'
        });
      }
    });
  });
</script>
