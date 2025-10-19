@extends('layouts.app')

@section('content')
  @include('erp.mails.partials.navbar')

  <h1 class="h4 mb-5">#{{ $mail->getOriginal('id') }} - {{ $mail->getOriginal('to') }}</h1>

  <hr class="my-3"/>

  <div class="card mb-5">
    <div class="card-body pb-1">
      <h1 class="h4 card-title mb-4">Основна инфоамция</h1>

      <div class="d-flex">
        <p class="text-body fw-semibold">ID:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->id }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">До:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->to }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Заглавие:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->subject }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Език:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->langId ?? '-' }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Датана на изпращане:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->sentDate ?? '-' }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Изпратено:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->isSent ? 'Да' : 'Не' }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Add HTML Wrapper:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->addHtmlWrapper }}</p>
      </div>

      <div class="d-flex">
        <p class="text-body fw-semibold">Created at:</p>
        <p class="text-body-emphasis fw-semibold ms-1">{{ $mail->createdAt }}</p>
      </div>
    </div>
  </div>

  <h1 class="h4 card-title mb-4">Съдържание</h1>

  <iframe id="preview-frame" class="border border-dashed mb-5" style="width: 100%; height: 800px;"></iframe>
  <script type="module">
    $(function () {
      $('#preview-frame').prop('srcdoc', @json($mail->content));
    });
  </script>
@endsection
