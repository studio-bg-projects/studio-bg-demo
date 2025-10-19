<div class="row mb-2 mt-3">
  <div class="col-6">
    <h2 class="h5 mb-2 border-bottom pb-2">{{ ['bg' => 'Получател', 'en' => 'Bill To'][$lang] }}</h2>
    <ul class="list-unstyled fs-10">
      @if ($document->recipientName)
        <li class="mb-1">
          <strong>{{ $document->recipientName }}</strong>
        </li>
      @endif
      @if ($document->recipientCompanyId)
        <li class="mb-1">
          {{ ['bg' => 'ЕИК', 'en' => 'Company ID'][$lang] }}:
          <strong>{{ $document->recipientCompanyId }}</strong>
        </li>
      @endif
      @if ($document->recipientVatId)
        <li class="mb-1">
          {{ ['bg' => 'ДДС', 'en' => 'VAT ID'][$lang] }}:
          <strong>{{ $document->recipientVatId }}</strong>
        </li>
      @endif
      @if ($document->recipientAddress)
        <li class="mb-1">
          {{ ['bg' => 'Адрес', 'en' => 'Address'][$lang] }}:
          <strong>{{ $document->recipientAddress }}</strong>
        </li>
      @endif
    </ul>
  </div>
  <div class="col-6">
    <h2 class="h5 mb-2 border-bottom pb-2">{{ ['bg' => 'Доставка до', 'en' => 'Ship To'][$lang] }}</h2>
    <ul class="list-unstyled fs-10">
      @if ($document->shipToName)
        <li class="mb-1">
          <strong>{{ $document->shipToName }}</strong>
        </li>
      @endif
      @if ($document->shipToCompanyId)
        <li class="mb-1">
          {{ ['bg' => 'ЕИК', 'en' => 'Company ID'][$lang] }}:
          <strong>{{ $document->shipToCompanyId }}</strong>
        </li>
      @endif
      @if ($document->shipToVatId)
        <li class="mb-1">
          {{ ['bg' => 'ДДС', 'en' => 'VAT ID'][$lang] }}:
          <strong>{{ $document->shipToVatId }}</strong>
        </li>
      @endif
      @if ($document->shipToAddress)
        <li class="mb-1">
          {{ ['bg' => 'Адрес', 'en' => 'Address'][$lang] }}:
          <strong>{{ $document->shipToAddress }}</strong>
        </li>
      @endif
    </ul>
  </div>
</div>
