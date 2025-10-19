@include('erp.documents.templates.invoice', [
  'showSerials' => true,
  'documentTitle' => [
    'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::DeliveryNote)->labelBg,
    'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::DeliveryNote)->labelEn,
  ][$lang ?? 'bg'],
])
