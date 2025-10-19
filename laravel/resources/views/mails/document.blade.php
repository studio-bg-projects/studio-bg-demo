@php($documentTitle = $documentTitle ?? [
  'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::Invoice)->labelBg,
  'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::Invoice)->labelEn,
][$lang ?? 'bg'])

@extends('layouts.mail')

@section('content')
  <h2 style="color: #000; text-align: center;">
    {{ [
      'bg' => 'Издаден документ',
      'en' => 'Issued document'
    ][$lang] }}
  </h2>

  <p>
    {{ [
      'bg' => 'Здравейте, ' . $customer->firstName . ' '. $customer->lastName . '!',
      'en' => 'Hello, ' . $customer->firstName . ' '. $customer->lastName . '!',
    ][$lang] }}
  </p>

  <p>
    {{ [
      'bg' => 'Има издаден документ адресиран към вас.',
      'en' => 'A document addressed to you has been issued.'
    ][$lang] }}
  </p>

  <p>
    <strong>{{ $documentTitle }} #{{ $document->documentNumber }}</strong>
  </p>

  @if ($document->issueDate)
    <p>
      <strong>{{ ['bg' => 'Дата', 'en' => 'Issue Date'][$lang] }}: {{ $document->issueDate }}</strong>
    </p>
  @endif

  <p style="text-align: center;">
    <a href="{{ env('SHOP_URL') }}/image/erp/{{ $document->uploads->first()->urls->path }}" style="text-decoration: none; color: #393185; text-align: center; text-transform: uppercase;" target="_blank">
      <div style="width: 50px; height: 50px; margin: 0 auto; background-color: #393185; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29.978 29.978" width="24" height="24" fill="#ffffff">
          <path d="M25.462,19.105v6.848H4.515v-6.848H0.489v8.861c0,1.111,0.9,2.012,2.016,2.012h24.967c1.115,0,2.016-0.9,2.016-2.012 v-8.861H25.462z"/>
          <path d="M14.62,18.426l-5.764-6.965c0,0-0.877-0.828,0.074-0.828s3.248,0,3.248,0s0-0.557,0-1.416c0-2.449,0-6.906,0-8.723 c0,0-0.129-0.494,0.615-0.494c0.75,0,4.035,0,4.572,0c0.536,0,0.524,0.416,0.524,0.416c0,1.762,0,6.373,0,8.742 c0,0.768,0,1.266,0,1.266s1.842,0,2.998,0c1.154,0,0.285,0.867,0.285,0.867s-4.904,6.51-5.588,7.193 C15.092,18.979,14.62,18.426,14.62,18.426z"/>
        </svg>
      </div>
      <div style="margin-top: 8px; font-weight: bold; color: #393185;">
        {{ ['bg' => 'Свали сега', 'en' => 'Download now'][$lang] }}
      </div>
    </a>
  </p>


  <hr style="border-top: 1px solid black; margin: 30px 0;"/>

  @if ($document->salesRepresentative)
    @include('mails.partials.sales-representative', [
      'title' => ['bg' => 'Отговорен служител', 'en' => 'Responsible Person'][$lang],
      'salesRepresentative' => $document->salesRepresentative,
    ])
  @endif
@endsection
