@extends('layouts.app')

@section('content')
  @include('erp.storage-items.partials.navbar')

  <h1 class="h4 mb-5">
    {{ $storageItem->product?->nameBg }} (ID: {{ $storageItem->id }})
    @if ($storageItem->product->usageStatus === \App\Enums\ProductUsageStatus::InternalUse)
      <span class="badge badge-phoenix badge-phoenix-{{ \App\Services\MapService::productUsageStatus($storageItem->product->usageStatus)->color }}">
        {{ \App\Services\MapService::productUsageStatus($storageItem->product->usageStatus)->short }}
      </span>
    @endif
    - Преглед
  </h1>

  @if (!$storageItem->isExited)
    <div class="text-end mt-n8 mb-4">
      <a href="{{ url('/erp/storage-items/writeoff-protocol/' . $storageItem->id) }}" class="btn btn-sm btn-primary">
        <i class="fa-regular fa-wine-glass-crack me-2"></i>
        Протокол за отписване
      </a>
    </div>
  @else
    <div class="alert alert-subtle-warning" role="alert">
      Този артикул е отписан от склада, посредтсвом
      <b>
        @if ($storageItem?->exit?->type?->value)
          <i class="fa-regular {{ \App\Services\MapService::storageExit($storageItem->exit->type)->icon }}"></i>
          {{ \App\Services\MapService::storageExit($storageItem->exit->type)->title }}
        @else
          ----
        @endif
      </b>
      на
      <b>{{ $storageItem?->exitDate?->format('Y-m-d') ?: '-----' }}</b>
      .
    </div>
  @endif

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Данни за артикула</h2>

      <div class="row gy-2">
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Покупна цена:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            @if ($storageItem->originalPrice != $storageItem->purchasePrice)
              <span class="text-decoration-line-through">{{ price($storageItem->originalPrice) }}</span>
              {{ price($storageItem->purchasePrice) }}
            @else
              {{ price($storageItem->purchasePrice) }}
            @endif
          </p>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Номер на фактура:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->invoiceNumber ?: '-' }}</p>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Дата на фактура:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->invoiceDate->format('Y-m-d') }}</p>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Сериен номер:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->serialNumber ?: '-' }}</p>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Изписан:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->isExited ? 'Да' : 'Не' }}</p>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Дата на изписване:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem?->exitDate?->format('Y-m-d') ?: '-' }}</p>
        </div>
        <div class="col-12 d-flex">
          <p class="text-body fw-semibold">Бележка:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->note ?: '-' }}</p>
        </div>
        <div class="col-12">
          <h2 class="h4 card-title mb-4">История</h2>
          @if (!empty($history))
            <table class="table table-hover table-sm fs-9">
              <thead>
              <tr>
                <th>Дата</th>
                <th>Действие</th>
                <th>Бележка</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($history as $row)
                <tr>
                  <td>{{ $row['date'] ?? '-' }}</td>
                  <td>{{ $row['action'] ?? '-' }}</td>
                  <td>{{ $row['note'] ?? '-' }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @else
            <p class="text-body-emphasis fw-semibold ms-1">-</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <hr class="my-3"/>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Изписване на артикула</h2>

      @if ($storageItem->exit)
        <div class="d-flex">
          <p class="text-body fw-semibold">Дата на изписване:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            {{ $storageItem?->exitDate?->format('Y-m-d') ?: '-' }}
          </p>
        </div>

        <div class="d-flex">
          <p class="text-body fw-semibold">Изписано посредством:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            @if ($storageItem?->exit?->type?->value)
              <i class="fa-regular {{ \App\Services\MapService::storageExit($storageItem->exit->type)->icon }}"></i>
              {{ \App\Services\MapService::storageExit($storageItem->exit->type)->title }}
            @else
              ----
            @endif
          </p>
        </div>

        @if ($storageItem->successor)
          <div class="d-flex">
            <p class="text-body fw-semibold">Наследник:</p>
            <p class="text-body-emphasis fw-semibold ms-1">
              <a href="{{ url('/erp/storage-items/view/' . $storageItem?->successor->id) }}">
                Артикул #{{ $storageItem?->successor?->id }}
              </a>
            </p>
          </div>
        @endif

        @if ($storageItem->exit->type === \App\Enums\StorageItemExitType::WriteOffProtocol)
          @if ($storageItem?->exit?->writeOffProtocol?->id)
            <div class="d-flex">
              <p class="text-body fw-semibold">Бележка в протокола:</p>
              <p class="text-body-emphasis fw-semibold ms-1">
                @if ($storageItem->exit?->writeOffProtocol?->reason)
                  {{ $storageItem->exit->writeOffProtocol->reason }}
                @else
                  ----
                @endif
              </p>
            </div>

            <a href="{{ url('/erp/storage-items/writeoff-protocol/' . $storageItem->exit->writeOffProtocol->id . '/pdf') }}" class="btn btn-sm btn-primary">
              <i class="fa-regular fa-file-pdf me-2"></i>
              Свали протокола
            </a>
          @else
            <p class="text-danger">
              <i>Липсва релация</i>
            </p>
          @endif

        @elseif ($storageItem->exit->type === \App\Enums\StorageItemExitType::IncomeCreditMemo)
          @if ($storageItem?->exit?->incomeCreditMemo?->id)
            <div class="d-flex">
              <p class="text-body fw-semibold">Бележка в кредитното известие:</p>
              <p class="text-body-emphasis fw-semibold ms-1">
                @if ($storageItem?->exit?->incomeCreditMemo?->note)
                  {{ $storageItem?->exit?->incomeCreditMemo?->note }}
                @else
                  ----
                @endif
              </p>
            </div>

            <a href="{{ url('/erp/storage-entries/income-credit-memos/view/' . $storageItem->exit->incomeCreditMemo->id) }}" class="btn btn-sm btn-primary">
              <i class="fa-regular fa-arrow-right me-2"></i>
              Преглед на кредитното известие
            </a>
          @else
            <p class="text-danger">
              <i>Липсва релация</i>
            </p>
          @endif
        @elseif ($storageItem->exit->type === \App\Enums\StorageItemExitType::Invoice)
          @if ($storageItem?->exit?->outcomeInvoice?->id)
            <div class="d-flex">
              <p class="text-body fw-semibold">Продажна цена:</p>
              <p class="text-body-emphasis fw-semibold ms-1">
                @if ($storageItem?->exit?->originalPrice != $storageItem?->exit?->sellPrice)
                  <span class="text-decoration-line-through">{{ price($storageItem?->exit?->originalPrice) }}</span>
                  {{ price($storageItem?->exit?->sellPrice) }}
                @else
                  {{ price($storageItem?->exit?->sellPrice) }}
                @endif
              </p>
            </div>

            <a href="{{ url('/erp/documents/view/' . $storageItem->exit->outcomeInvoice->id) }}" class="btn btn-sm btn-primary">
              <i class="fa-regular fa-arrow-right me-2"></i>
              Преглед на фактурата
            </a>
          @else
            <p class="text-danger">
              <i>Липсва релация</i>
            </p>
          @endif
        @endif
      @else
        <p class="text-body-emphasis fw-semibold">
          <i>Този артикул не е изписан и все още е на склад.</i>
        </p>
      @endif
    </div>
  </div>

  <hr class="my-3"/>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Продукт</h2>
      <div class="d-flex">
        <p class="text-body fw-semibold">Име:</p>
        <p class="text-body-emphasis fw-semibold ms-1">
          @if ($storageItem->product)
            <a href="{{ url('/erp/products/update/' . $storageItem->product->id) }}">{{ $storageItem->product->nameBg }}</a>
        @else
          <div class="text-body-emphasis fw-semibold">
            <i>Този артикул не е свързан с нито един продукт.</i>
          </div>
          @endif</p>
      </div>
      <div class="d-flex">
        <p class="text-body fw-semibold">MPN:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->product->mpn ?? '-' }}</p>
      </div>
      <div class="d-flex">
        <p class="text-body fw-semibold">EAN:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->product->ean ?? '-' }}</p>
      </div>
    </div>
  </div>

  <hr class="my-3"/>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Документ за заприхождаване</h2>
      @if ($storageItem->storageEntriesIncomeInvoiceId)
        <div class="d-flex">
          <p class="text-body fw-semibold">Номер:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <a href="{{ url('/erp/storage-entries/update/' . $storageItem->storageEntriesIncomeInvoice->id) }}">
              #{{ $storageItem->storageEntriesIncomeInvoice->documentNumber }} -
              {{ $storageItem->storageEntriesIncomeInvoice->supplier->companyName ?? '-' }}
            </a>
          </p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Дата:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->storageEntriesIncomeInvoice->documentDate->format('Y-m-d') }}</p>
        </div>
      @elseif ($storageItem->outcomeCreditMemoDocumentId)
        <div class="d-flex">
          <p class="text-body fw-semibold">Номер:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <a href="{{ url('/erp/documents/view/' . $storageItem->outcomeCreditMemo->id) }}">
              #{{ $storageItem->outcomeCreditMemo->documentNumber }} -
              {{ $storageItem->outcomeCreditMemo->customer->companyName ?? '-' }}
            </a>
          </p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Дата:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem?->outcomeCreditMemo?->issueDate?->format('Y-m-d') ?: '-' }}</p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Предшественик:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <a href="{{ url('/erp/storage-items/view/' . $storageItem?->predecessorId) }}">
              Артикул #{{ $storageItem?->predecessor?->id }}
            </a>
          </p>
        </div>
      @else
        <p class="text-body-emphasis fw-semibold">
          <i>Няма открита връзка със заприхождаване.</i>
        </p>
      @endif
    </div>
  </div>

  <hr class="my-3"/>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Доставчик</h2>
      @if ($storageItem->supplier)
        <div class="d-flex">
          <p class="text-body fw-semibold">Име:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <a href="{{ url('/erp/customers/update/' . $storageItem->supplier->id) }}">{{ $storageItem->supplier->companyName ?: 'N/A' }}</a>
          </p>
        </div>
      @else
        <p class="text-body-emphasis fw-semibold">-</p>
      @endif
    </div>
  </div>

  <hr class="my-3"/>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h4 card-title mb-4">Корекция на цена посредством Входящо Кредитно Известие</h2>
      @if ($storageItem->priceCorrectionIncomeCreditMemo)
        <div class="d-flex">
          <p class="text-body fw-semibold">ID:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            <a href="{{ url('/erp/storage-entries/income-credit-memos/view/' . $storageItem->priceCorrectionIncomeCreditMemo->id) }}">{{ $storageItem->priceCorrectionIncomeCreditMemo->id }}</a>
            @if ($storageItem->priceCorrectionIncomeCreditMemo->note)
              <i>({{ $storageItem->priceCorrectionIncomeCreditMemo->note }})</i>
            @endif
          </p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Дата:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $storageItem->priceCorrectionIncomeCreditMemo->date->format('Y-m-d') }}</p>
        </div>
        <div class="d-flex">
          <p class="text-body fw-semibold">Цена:</p>
          <p class="text-body-emphasis fw-semibold ms-1">
            @if ($storageItem->originalPrice != $storageItem->purchasePrice)
              <span class="text-decoration-line-through">{{ price($storageItem->originalPrice) }}</span>
              {{ price($storageItem->purchasePrice) }}
            @else
              {{ price($storageItem->purchasePrice) }}
            @endif
          </p>
        </div>
      @else
        <p class="text-body-emphasis fw-semibold">
          <i>Този артикул няма релация с всходящо кредитно известие, което да е коригирало цената му.</i>
        </p>
      @endif
    </div>
  </div>
@endsection
