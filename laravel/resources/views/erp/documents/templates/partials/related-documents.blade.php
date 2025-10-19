@if ($document->related->count())
  <h2 class="h5 border-bottom pb-2">{{ ['bg' => 'Свързани документи', 'en' => 'Related Documents'][$lang] }}</h2>

  <table class="table table-sm table-striped fs-10">
    <thead>
    <tr>
      <th style="width: 1px;">{{ ['bg' => 'Баркод', 'en' => 'Barcode'][$lang] }}</th>
      <th>{{ ['bg' => 'Номер', 'en' => 'Document ID'][$lang] }}</th>
      <th>{{ ['bg' => 'Вид', 'en' => 'Type'][$lang] }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($document->related as $related)
      <tr class="align-middle">
        <td class="px-2 barcode-td" style="width: 1px;">
          {!! $related->barcode !!}
        </td>
        <td class="px-2 pe-5" style="white-space: nowrap; width: 1px;">
          #{{ $related->documentNumber }}
        </td>
        <td class="px-2">
          {{ [
            'bg' => \App\Services\MapService::documentTypes($related->type)->labelBg,
            'en' => \App\Services\MapService::documentTypes($related->type)->labelEn,
          ][$lang ] }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endif
