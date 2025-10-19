@extends('layouts.app')

@section('content')
  @include('erp.storage-entries.partials.navbar')

  <h1 class="h4 mb-4">{{ $document->getOriginal('documentNumber') }} - Преглед на кредитно известие</h1>

  <div class="card mb-3">
    <div class="card-body">
      <div class="row gy-2">
        <div class="col-12 col-md-6 d-flex">
          <p class="text-body fw-semibold">Дата:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $creditMemo->date }}</p>
        </div>
        <div class="col-12 d-flex">
          <p class="text-body fw-semibold">Бележка:</p>
          <p class="text-body-emphasis fw-semibold ms-1">{{ $creditMemo->note }}</p>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-3"/>

  @php($rows = collect($items)->map(fn($i) => ['item' => $i, 'type' => 'price']))
  @php($rows = $rows->merge(collect($exitItems)->map(fn($e) => ['item' => $e->storageItem, 'type' => 'exit'])->filter(fn($r) => $r['item'])))

  @if($rows->count())
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">Статус</th>
          <th class="nosort border-top border-translucent">Сериен номер</th>
          <th class="nosort border-top border-translucent">Бележка</th>
          <th class="nosort border-top border-translucent text-end">Кредитна стойност</th>
          <th class="nosort border-top border-translucent text-end">Покупна цена</th>
          <th class="nosort border-top border-translucent text-end">Оригинална цена</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
          @php($item = $row['item'])
          <tr>
            <td>
              @if($row['type'] === 'price')
                <span class="badge badge-phoenix badge-phoenix-info">Корекция на цена</span>
              @else
                <span class="badge badge-phoenix badge-phoenix-warning">Изписан</span>
              @endif
            </td>
            <td>
              <a href="{{ url('/erp/storage-items/view/' . $item->id) }}">{{ $item->serialNumber ?: 'N/A' }}</a>
            </td>
            <td>{{ $item->note }}</td>
            <td class="text-end">{{ price($item->originalPrice - $item->purchasePrice) }}</td>
            <td class="text-end">{{ price($item->purchasePrice) }}</td>
            <td class="text-end">{{ price($item->originalPrice) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="text-center">Няма артикули</div>
  @endif
@endsection

