@include('erp.documents.templates.invoice', [
  'documentTitle' => [
    'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::OrderConfirmation)->labelBg,
    'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::OrderConfirmation)->labelEn,
  ][$lang ?? 'bg'],
])
