<thead style="display: table-header-group;">
<tr>
  <td>
    <div class="container">
      <div class="row">
        <div class="col-auto">
          <img src="data:image/svg+xml;base64,{{base64_encode(file_get_contents(public_path('img/logo.svg')))}}" alt="Inside Trading" style="height: 3rem;" class="mb-2"/>

          <h2 class="h5 mb-3">{{ ['bg' => $document->issuerNameBg, 'en' => $document->issuerNameEn][$lang] }}</h2>
        </div>
        <div class="col">
          <div class="text-end">
            <h1 class="text-uppercase m-0 mb-2 h3">{{ $documentTitle }} #{{ $document->documentNumber }}</h1>

            @if ($document->issueDate)
              <p class="mb-2 fs-10 fw-bold">{{ ['bg' => 'Дата', 'en' => 'Issue Date'][$lang] }}: {{ $document->issueDate }}</p>
            @endif

            {!! $document->barcode !!}
          </div>
        </div>
      </div>
    </div>
  </td>
</tr>
</thead>
