@extends('layouts.app')

@section('content')
  @include('erp.shipments.speedy.partials.navbar')

  <h1 class="h4 mb-5">Пратки - DPD/Speedy - Преглед</h1>

  <div class="d-flex align-items-center">
    <p class="mb-0 me-2">Външни линкове към DPD/Speedy:</p>

    <a href="https://myspeedy.speedy.bg/consignments/view?bol=63094603145" target="_blank" class="me-2 btn btn-phoenix-primary btn-sm">
      <i class="fa-regular fa-memo"></i>
      Преглед
    </a>

    <a href="https://myspeedy.speedy.bg/consignments/print?id=63094603145&printType=CONSIGNMENT_LABELS" target="_blank" class="me-2 btn btn-phoenix-primary btn-sm">
      <i class="fa-regular fa-print"></i>
      Етикет
    </a>

    <a href="https://myspeedy.speedy.bg/consignments/update?id=63094603145" target="_blank" class="me-2 btn btn-phoenix-primary btn-sm">
      <i class="fa-regular fa-pen-to-square"></i>
      Редакция
    </a>
  </div>

  @if ($trackingData->parcels)
    @foreach ($trackingData->parcels as $parcel)
      <hr/>
      <h1 class="h5 mb-3">Проследяване на пратка #{{ $parcel->parcelId }}</h1>

      @if (isset($parcel->error->message))
        <div class="alert alert-subtle-danger">{{ $parcel->error->message }}</div>
      @endif

      @if (!empty($parcel->externalCarrierParcelNumbers) && count($parcel->externalCarrierParcelNumbers) > 0)
        <p>Външен номер за проследяване: {{ join(',', $parcel->externalCarrierParcelNumbers) }}</p>
      @endif

      @if ($parcel->operations)
        <table class="table table-hover table-sm fs-9">
          <thead>
          <tr>
            <th>Дата</th>
            <th>Код</th>
            <th>Място</th>
            <th>Описание</th>
            <th>Друго</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($parcel->operations as $operation)
            <tr>
              <td>{{ $operation->dateTime }}</td>
              <td>{{ $operation->operationCode }}</td>
              <td>{{ $operation->place ?? '-' }}</td>
              <td>{{ $operation->description }}</td>
              <td>
                @if (!empty($operation->comment))
                  Коментар: {{ $operation->comment }};
                @endif
                @if (!empty($operation->consignee))
                  Получател: {{ $operation->consignee }};
                @endif
                @if (!empty($operation->podImageURL))
                  <a href="{{ $operation->podImageURL }}" target="_blank">Подпис</a>
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @endif
    @endforeach
  @endif
@endsection
