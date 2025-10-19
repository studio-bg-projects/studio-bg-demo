@if ($items->isEmpty())
  <p class="text-body-secondary fs-9 mb-0">Няма налични артикули към {{ $inventoryDate->format('d.m.Y') }} г.</p>
@else
  <div class="table-responsive">
    <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle text-nowrap table-hover">
      <thead>
      <tr class="bg-body-highlight">
        <th class="nosort border-top border-translucent"></th>
        <th class="nosort border-top border-translucent">ID</th>
        <th class="nosort border-top border-translucent">Сериен номер</th>
        <th class="nosort border-top border-translucent">Заприхождаване</th>
        <th class="nosort border-top border-translucent">Изписване</th>
        <th class="nosort border-top border-translucent">Цена на закупуване</th>
        <th class="nosort border-top border-translucent">Цена на продажба</th>
        <th class="nosort border-top border-translucent">Бележка</th>
      </tr>
      </thead>
      <tbody>
      @foreach ($items as $item)
        <tr>
          <td style="width: 70px;">
            @include('erp.storage-report.partials.view-item-button', ['itemId' => $item->id])
          </td>
          <td style="width: 70px;">
            #{{ $item->id }}
          </td>
          <td style="width: 200px;">{{ $item->serialNumber }}</td>
          <td style="width: 350px;">
            {{ $item->invoiceDate?->format('Y-m-d') }}

            @if ($item->storageEntriesIncomeInvoiceId)
              чрез Ф-ра: #{{ $item->storageEntriesIncomeInvoice?->documentNumber }}
            @elseif ($item->outcomeCreditMemoDocumentId)
              чрез КИ: #{{ $item->outcomeCreditMemo?->documentNumber }}
            @endif
          </td>
          <td style="width: 350px;">
            @if ($item->isExited)
              {{ $item->exitDate?->format('Y-m-d') }}

              @if ($item->exit)
                {{ \App\Services\MapService::storageExit($item->exit->type)->title }}

                @if ($item->exit->outcomeInvoiceId)
                  чрез #{{ $item->exit->outcomeInvoice?->documentNumber }}
                @elseif ($item->exit->writeOffProtocolId)
                  чрез #{{ $item->exit->writeOffProtocol?->id }}
                @elseif ($item->exit->incomeCreditMemoId)
                  чрез #{{ $item->exit->incomeCreditMemo?->incomeInvoice?->documentNumber }}
                @endif
              @endif
            @else
              -
            @endif
          </td>
          <td style="width: 150px;">{{ price($item->purchasePrice) }}</td>
          <td style="width: 150px;">
            @if ($item->exit)
              @if ($item->exit->originalPrice != $item->exit->sellPrice)
                <span class="text-decoration-line-through">{{ price($item->exit->originalPrice) }}</span>
                {{ price($item->exit->sellPrice) }}
              @else
                {{ price($item->exit->sellPrice) }}
              @endif
            @else
              -
            @endif
          </td>
          <td>{{ $item->note }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif
