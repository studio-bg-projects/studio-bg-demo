@include('erp.documents.templates.invoice', [
  'documentTitle' => [
    'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::PackingList)->labelBg,
    'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::PackingList)->labelEn,
  ][$lang ?? 'bg'],
])
