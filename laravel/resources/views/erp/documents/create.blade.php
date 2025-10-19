@extends('layouts.app')

@section('content')
  @include('erp.documents.partials.navbar')

  <h1 class="h4 mb-5">
    Добавяне на документ - {{ \App\Services\MapService::documentTypes($document->type)->labelBg }}
  </h1>

  <form method="post" action="?" class="mb-5" onsubmit="return confirm('Сигурни ли сте, че искате да продължите?')" data-disable-on-submit>
    @csrf
    <input type="hidden" name="customerId" value="{{ $document->customerId }}"/>
    <input type="hidden" name="orderId" value="{{ $document->orderId }}"/>

    @include('erp.documents.partials.form')

    <div class="text-end">
      <button class="btn btn-primary btn-lg mt-3" type="submit">
        <i class="fa-regular fa-plus me-2"></i>
        Създай документа
      </button>
    </div>
  </form>
@endsection
