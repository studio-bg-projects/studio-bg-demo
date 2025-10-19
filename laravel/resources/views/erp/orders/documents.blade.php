@extends('layouts.app')

@section('content')
  @include('erp.orders.partials.navbar')

  <h1 class="h4 mb-5">Поръчка #{{ $order->id }} - Свързани документи</h1>

  <div class="text-end mt-n8 mb-4 dropdown">
    <a class="btn btn-sm btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Добави нов документ
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      @foreach (App\Enums\DocumentType::cases() as $type)
        <li>
          <a class="dropdown-item" href="{{ url('/erp/documents/prepare/' . $type->value) . '?orderId=' . $order->id }}">
            {{ \App\Services\MapService::documentTypes($type)->labelBg }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  @include('erp.documents.partials.results')
@endsection
