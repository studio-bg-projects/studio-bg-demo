@extends('layouts.app')

@section('content')
  @include('erp.storage-entries.partials.navbar')

  <h1 class="h4 mb-5">{{ $document->getOriginal('documentNumber') }} - Кредитни известия</h1>

  <div class="text-end mt-n8 mb-4">
    <a href="{{ url('/erp/storage-entries/income-credit-memos/create/' . $document->id) }}" class="btn btn-sm btn-primary">
      <i class="fa-regular fa-circle-plus me-2"></i>
      Добави ново кредитно известие
    </a>
  </div>

  @include('erp.storage-entries-income-credit-memos.partials.results')
@endsection
