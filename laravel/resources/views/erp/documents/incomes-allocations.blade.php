@extends('layouts.app')

@section('content')
  @include('erp.documents.partials.navbar')

  <h1 class="h4 mb-5">
    {{ \App\Services\MapService::documentTypes($document->getOriginal('type'))->labelBg }}
    #{{ $document->getOriginal('documentNumber') }}
    - Свързани плащания</h1>

  <div class="text-end mt-n8 mb-4">
    <a href="{{ url('/erp/incomes/create/?documentId=' . $document->id . '&customerId=' . $document->customerId . '&paidAmount=' . $document->leftAmount) }}" class="btn btn-sm btn-primary">
      <i class="fa-regular fa-money-bill-transfer me-2"></i>
      Добави плащане
    </a>
  </div>

  @include('erp.incomes.partials.results-incomes-allocations')
@endsection
