<div class="card">
  <div class="card-body">
    <div class="row gy-2">
      <div class="col-12">
        <label class="app-form-label required" for="f-date">Дата</label>
        <input type="date" class="form-control @if($errors->has('date')) is-invalid @endif" id="f-date" name="date" value="{{ $creditMemo->date }}" required/>
        @if($errors->has('date'))
          <div class="invalid-feedback">
            {{ $errors->first('date') }}
          </div>
        @endif

        <script type="module">
          flatpickr('#f-date');
        </script>
      </div>

      <div class="col-12">
        <label class="app-form-label" for="f-note">Бележка</label>
        <textarea class="form-control @if($errors->has('note')) is-invalid @endif" id="f-note" name="note" rows="3">{{ $creditMemo->note }}</textarea>
        @if($errors->has('note'))
          <div class="invalid-feedback">
            {{ $errors->first('note') }}
          </div>
        @endif
      </div>

      <div class="col-12">
        <hr class="my-3"/>
      </div>
      <div class="col-12">
        @php($entryProducts = $document->products()->with(['product', 'items'])->orderBy('arrangementSeq')->get())

        @if($entryProducts->count())
          <div class="table-responsive">
            <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
              <thead>
              <tr class="bg-body-highlight">
                <th class="border-top border-translucent">№</th>
                <th class="border-top border-translucent">MPN</th>
                <th class="border-top border-translucent">EAN</th>
                <th class="border-top border-translucent">Име</th>
                <th class="border-top border-translucent text-end">Покупна цена</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($entryProducts as $entryProduct)
                <tr>
                  <td>{{ $entryProduct->arrangementSeq }}</td>
                  <td>{{ $entryProduct->product?->mpn }}</td>
                  <td>{{ $entryProduct->product?->ean }}</td>
                  <td>{{ $entryProduct->product?->nameBg }}</td>
                  <td class="text-end">{{ price($entryProduct->purchasePrice) }}</td>
                </tr>
                <tr>
                  <td colspan="5">
                    <div style="max-height: 360px; overflow-x: hidden; overflow-y: auto;">
                      <div class="table-responsive">
                        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
                          <thead>
                          <tr class="bg-body-highlight">
                            <th class="nosort border-top border-translucent">-</th>
                            <th class="nosort border-top border-translucent">Отписан</th>
                            <th class="nosort border-top border-translucent">Сериен номер</th>
                            <th class="nosort border-top border-translucent">Оригинална цена</th>
                            <th class="nosort border-top border-translucent">Покупна цена</th>
                            <th class="nosort border-top border-translucent">Кредитна стойност</th>
                            <th class="nosort border-top border-translucent">Дата на отписване</th>
                            <th class="nosort border-top border-translucent">Бележка</th>
                          </tr>
                          </thead>
                          <tbody>
                          @forelse ($entryProduct->items as $item)
                            @php($locked = $item->isExited || $item->priceCorrectionIncomeCreditMemoId)
                            @php($creditValue = old('items.' . $item->id . '.creditValue', max($item->originalPrice - $item->purchasePrice, 0)))
                            @php($creditValue = old('items.' . $item->id . '.creditValue', max($item->originalPrice - $item->purchasePrice, 0)))
                            @php($rowClass = $locked ? 'bg-body-secondary' : ($creditValue == $item->originalPrice ? 'bg-warning-subtle' : ''))

                            <tr class="item-row {{ $rowClass }}" data-original-price="{{ $item->originalPrice }}" data-item-id="{{ $item->id }}">
                              <td>
                                @if ($locked)
                                  <i class="fa-regular fa-lock me-2" data-bs-toggle="tooltip" data-bs-title="Този артикул е вече изписан или има кредитно известие към него"></i>
                                @endif
                              </td>
                              <td class="item-is-exited">{!! $creditValue == $item->originalPrice ? '<span class="badge badge-phoenix badge-phoenix-warning">Да</span>' : '<span class="badge badge-phoenix badge-phoenix-secondary">Не</span>' !!}</td>
                              <td>{{ $item->serialNumber }}</td>
                              <td>{{ price($item->originalPrice) }}</td>
                              <td class="item-purchase-price">{{ price($item->originalPrice - $creditValue) }}</td>
                              <td>
                                <div class="input-group input-group-sm">
                                  <span class="input-group-text text-warning">
                                    <i class="fa-regular fa-minus"></i>
                                  </span>
                                  <input type="number" step="0.01" min="0" max="{{ $item->originalPrice }}" class="form-control credit-input" name="items[{{ $item->id }}][creditValue]" value="{{ $creditValue }}" {{ $locked ? 'disabled' : '' }} data-bs-toggle="tooltip" data-bs-trigger="focus" data-bs-placement="top" title="Ако въведете пълната стойност, артикулът ще бъде изписан от склада"/>
                                </div>
                              </td>
                              <td class="item-exit-date">{{ $item->exitDate?->format('Y-m-d') }}</td>
                              <td>{{ $item->note ?: '-' }}</td>
                            </tr>
                          @empty
                            <tr>
                              <td colspan="7" class="text-center">Няма артикули</td>
                            </tr>
                          @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center">Няма продукти</div>
        @endif
      </div>
    </div>
  </div>
</div>

<script type="module">
  const currency = '{{ dbConfig('currency:symbol') }}';
  $('.credit-input').on('input', function () {
    const $row = $(this).closest('.item-row');
    const original = parseFloat($row.data('original-price'));
    let credit = parseFloat($(this).val()) || 0;

    if (credit < 0) {
      credit = 0;
    }

    if (credit > original) {
      credit = original;
    }

    $row.find('.item-purchase-price').text(`${(original - credit).toFixed(2)} ${currency}`);

    const $exitedCell = $row.find('.item-is-exited');
    if (credit === original) {
      $row.addClass('bg-warning-subtle');
      $exitedCell.html('<span class="badge badge-phoenix badge-phoenix-warning">Да</span>');
    } else {
      $row.removeClass('bg-warning-subtle');
      $exitedCell.html('<span class="badge badge-phoenix badge-phoenix-secondary">Не</span>');
    }
  });
</script>

<hr class="my-3"/>

<div class="card">
  <div class="card-body pb-1">
    <h2 class="h4 card-title mb-4">Добавяне на файлове</h2>
    @include('erp.uploads.uploader', [
      'groupType' => \App\Enums\UploadGroupType::IncomeCreditMemo->value,
      'groupId' => $creditMemo->fileGroupId,
      'fieldName' => 'fileGroupId',
    ])
  </div>
</div>
