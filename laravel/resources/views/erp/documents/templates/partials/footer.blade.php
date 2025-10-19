<div class="row mb-2">
  <div class="col">
    <h2 class="h5 mb-2 border-bottom pb-2">{{ ['bg' => 'Данни за плащане', 'en' => 'Income Details'][$lang] }}</h2>

    <ul class="list-unstyled fs-10">
      <li class="mb-1">
        {{ ['bg' => 'Метод на плащане', 'en' => 'Income Method'][$lang] }}:
        <strong>{{ ['bg' => $document->incomeMethodBg, 'en' => $document->incomeMethodEn][$lang] }}</strong>
      </li>
      <li class="mb-1">
        {{ ['bg' => 'Банка', 'en' => 'Bank'][$lang] }}:
        <strong>{{ ['bg' => $document->issuerBankNameBg, 'en' => $document->issuerBankNameEn][$lang] }}</strong>
      </li>
      <li class="mb-1">
        {{ ['bg' => 'Адрес', 'en' => 'Address'][$lang] }}:
        <strong>{{ ['bg' => $document->issuerIBankAddressBg, 'en' => $document->issuerIBankAddressEn][$lang] }}</strong>
      </li>
      <li class="mb-1">
        {{ ['bg' => 'IBAN', 'en' => 'IBAN'][$lang] }}:
        <strong>{{ $document->issuerIban }}</strong>
      </li>
      <li class="mb-1">
        {{ ['bg' => 'SWIFT', 'en' => 'SWIFT'][$lang] }}:
        <strong>{{ $document->issuerSwift }}</strong>
      </li>
    </ul>
  </div>
  @if ($document->salesRepresentative)
    <div class="col">
      <h2 class="h5 mb-2 border-bottom pb-2">{{ ['bg' => 'Отговорен служител', 'en' => 'Responsible Person'][$lang] }}</h2>

      <ul class="list-unstyled fs-10">
        <li class="mb-1">
          <strong>
            {{ ['bg' => $document->salesRepresentative->nameBg, 'en' => $document->salesRepresentative->nameEn][$lang] }}
            ({{ ['bg' => $document->salesRepresentative->titleBg, 'en' => $document->salesRepresentative->titleEn][$lang] }})
          </strong>
        </li>
        @if ($document->salesRepresentative->phone1)
          <li class="mb-1">
            {{ ['bg' => 'Телефон', 'en' => 'Phone'][$lang] }}:
            <strong>{{ $document->salesRepresentative->phone1 }}</strong>
          </li>
        @endif
        @if ($document->salesRepresentative->phone2)
          <li class="mb-1">
            {{ ['bg' => 'Телефон', 'en' => 'Phone'][$lang] }}:
            <strong>{{ $document->salesRepresentative->phone2 }}</strong>
          </li>
        @endif
        @if ($document->salesRepresentative->email1)
          <li class="mb-1">
            {{ ['bg' => 'Email', 'en' => 'Email'][$lang] }}:
            <strong>{{ $document->salesRepresentative->email1 }}</strong>
          </li>
        @endif
        @if ($document->salesRepresentative->email2)
          <li class="mb-1">
            {{ ['bg' => 'Email', 'en' => 'Email'][$lang] }}:
            <strong>{{ $document->salesRepresentative->email2 }}</strong>
          </li>
        @endif
      </ul>
    </div>
  @endif
</div>
