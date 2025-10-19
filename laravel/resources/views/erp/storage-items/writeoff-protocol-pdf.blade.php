@extends('layouts.pdf')

<style>
  #print-table {
    width: 100%;
  }

  @media screen {
    /* Make it closer to the print size on desktop */
    #print-table {
      max-width: 720px;
    }
  }

  @media print {
    @page {
      padding: 1cm;
    }
  }

  body {
    background-color: #ffffff !important;
  }
</style>

@section('content')
  <!-- LAYOUT TABLE (HEADER & CONTENT) -->
  <table class="mx-auto" id="print-table">
    <!-- HEADER -->
    <thead style="display: table-header-group;">
    <tr>
      <td>
        <div class="container">
          <img src="data:image/svg+xml;base64,{{base64_encode(file_get_contents(public_path('img/logo.svg')))}}" alt="Inside Trading" style="height: 3rem;" class="mb-2"/>
        </div>
      </td>
    </tr>
    </thead>
    <!-- /HEADER -->

    <!-- CONTENT -->
    <tbody>
    <tr>
      <td>
        <div class="container">
          <h1 class="text-center h4 mb-4">Протокол за отписване #{{ $writeOffProtocol->documentNumber }}</h1>

          <p class="mb-1">
            <strong>Продукт:</strong> {{ $writeOffProtocol->item->product?->nameBg ?: '-' }}
          </p>

          <p class="mb-1">
            <strong>SN:</strong> {{ $writeOffProtocol->item->serialNumber ?: '-' }}
          </p>

          <p class="mb-1">
            <strong>MPN:</strong> {{ $writeOffProtocol->item->product?->mpn ?: '-' }}
          </p>

          <p class="mb-1">
            <strong>EAN:</strong> {{ $writeOffProtocol->item->product?->ean ?: '-' }}
          </p>

          <p class="mb-1">
            <strong>Дата на събитието:</strong> {{ $writeOffProtocol?->date?->format('Y-m-d') }}
          </p>

          <p class="mb-1">
            <strong>Причина за отписване:</strong>
          </p>

          <p>
            {{ $writeOffProtocol->reason }}
          </p>
        </div>
      </td>
    </tr>
    </tbody>
    <!-- /CONTENT -->
  </table>
@endsection

