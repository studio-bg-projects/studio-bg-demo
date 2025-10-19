@extends('layouts.app')

@section('content')
  @include('erp.api-keys.partials.navbar')

  <h1 class="h4 mb-5">Външни API интеграции</h1>


  <div class="bg-body-emphasis border-y mx-n4 mx-lg-n6 px-4 px-lg-6 py-5 mb-5">
    <div class="table-responsive">
      <table class="table app-table-rs table-sm table-padding fs-9 my-0 align-middle">
        <thead>
        <tr class="bg-body-highlight">
          <th class="nosort border-top border-translucent">
            Описание
          </th>
          <th class="nosort border-top border-translucent">
            Case
          </th>
          <th class="nosort border-top border-translucent">
            Ключ
          </th>
          <th class="nosort border-top border-translucent">
            Заявки
          </th>
          <th class="nosort border-top border-translucent">
            Последно използван
          </th>
          <th class="nosort border-top border-translucent">
            Лог
          </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($apiKeys as $iKey => $row)
          <tr>
            <td>
              {{ $row->description }}
            </td>
            <td>
              {{ $row->case }}
            </td>
            <td>
              {{ $row->key }}
            </td>
            <td>
              {{ number_format($row->requestsCount) }}
            </td>
            <td>
              @if (!$row->latestRequest)
                Няма заявки
              @else
                {{ $row->latestRequest }}
              @endif
            </td>
            <td>
              <button class="btn btn-sm btn-phoenix-primary" data-bs-toggle="modal" data-bs-target="#infoModal-{{ $iKey }}">
                <i class="fa-regular fa-circle-info"></i>
                Лог и интеграция
              </button>

              <div class="modal fade" id="infoModal-{{ $iKey }}" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="infoModalLabel">Лог и интеграция</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Затвори"></button>
                    </div>
                    <div class="modal-body">
                      <h5 class="h5 mb-3">Заявки от клиента</h5>
                      <pre style="font-size: 14px">@dump($row->requestsLog)</pre>

                      <hr/>

                      <h5 class="h5 mb-3">URLs</h5>
                      @foreach([
                        'products-xml' =>  url('/api/feed/products/bg/xml?api-key=' . $row->key . '&case=' . $row->case),
                        'available-xml' => url('/api/feed/available/bg/xml?api-key=' . $row->key . '&case=' . $row->case),
                        'products-json' => url('/api/feed/products/bg/json?api-key=' . $row->key . '&case=' . $row->case),
                        'available-json' => url('/api/feed/available/bg/json?api-key=' . $row->key . '&case=' . $row->case),
                      ] as $urlType => $url)
                        <div class="mb-3">
                          <label class="app-form-label" for="f-{{$iKey}}-{{ $urlType }}">{{ $urlType }}</label>
                          <input type="text" class="form-control" id="f-{{$iKey}}-{{ $urlType }}" value="{{ $url }}" readonly/>
                        </div>
                      @endforeach
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Затвори</button>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
