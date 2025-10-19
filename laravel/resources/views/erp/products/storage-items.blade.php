@extends('layouts.app')

@section('content')
  @include('erp.products.partials.navbar')

  <h1 class="h4 mb-5">{{ $product->getOriginal('nameBg') }} - Артикули в склада</h1>

  @if (count($product->storageItems))
    <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
      <div class="table-responsive">
        <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
          <thead>
          <tr class="bg-body-highlight">
            <th class="nosort border-top border-translucent @if (request('sort') == 'productId') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              Артикули
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'product.mpn') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              MPN
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'product.ean') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              EAN
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'invoiceDate') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              Дата
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'purchasePrice') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              Цена
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'serialNumber') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              SN
            </th>
            <th class="nosort border-top border-translucent @if (request('sort') == 'note') {{ request('d') == 'asc' ? 'asc' : 'desc' }} @endif">
              Бележка
            </th>
            @isset($showWriteOffProtocolLink)
              <th class="nonosort border-top border-translucent">Отписване</th>
            @endisset
          </tr>
          </thead>
          <tbody>
          @foreach ($product->storageItems as $row)
            <tr>
              <td>
                <a href="{{ url('/erp/storage-items/view/' . $row->id) }}" class="@if ($row->isExited) text-decoration-line-through @endif">
                  {{ $row->product?->nameBg }} (ID: {{ $row->id }})
                </a>
              </td>
              <td>
                {{ $row->product?->mpn }}
              </td>
              <td>
                {{ $row->product?->ean }}
              </td>
              <td>
                {{ $row->invoiceDate }}
              </td>
              <td>
                {{ $row->purchasePrice }}
              </td>
              <td>
                {{ $row->serialNumber }}
              </td>
              <td>
                {{ $row->note }}
              </td>
              @isset($showWriteOffProtocolLink)
                <td>
                  @unless ($row->isExited)
                    <a href="{{ url('/erp/storage-items/writeoff-protocol/' . $row->id) }}">Протокол</a>
                  @endunless
                </td>
              @endisset
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    @include('shared.no-rs')
  @endif

@endsection
