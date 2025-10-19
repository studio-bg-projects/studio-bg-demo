@include('erp.documents.templates.invoice', [
  'documentTitle' => [
    'bg' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::ProformaInvoice)->labelBg,
    'en' => \App\Services\MapService::documentTypes(App\Enums\DocumentType::ProformaInvoice)->labelEn,
  ][$lang ?? 'bg'],
])
