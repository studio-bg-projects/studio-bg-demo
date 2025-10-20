@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">
    Преглед на анализ
    {{ !empty($gptRequest->responseContent->summary) ? ' - ' . $gptRequest->responseContent->summary : '' }}
  </h1>

  <div class="row">
    @foreach($gptRequest->uploads as $uploads)
      <div class="col text-center mb-5">
        <a href="{{ $uploads->urls->analize }}" target="_blank" data-gallery="uploads-preview">
          <img src="{{ $uploads->urls->preview }}" alt="" style="height: 150px;"/>
        </a>
      </div>
    @endforeach
  </div>

  <hr/>

  @if ($gptRequest->progressStatus === 0)
    <pre class="alert alert-outline-danger" id="js-response-error" style="display: none;"></pre>
    <div class="alert alert-outline-primary">Заявката за анализ на изображенията беше пусната. Моля, изчакайте!</div>
    <script type="module">
      $.ajax({
        url: '{{ url('/vehicle-inspections/process/' . $gptRequest->id) }}',
        success: function () {
          setTimeout(() => document.location = document.location, 2000);
        },
        error: function (data) {
          alert('Възникна грешка!');
          $('#js-response-error').text(data.responseText).fadeIn();
        }
      });

      setTimeout(() => document.location = '?', 1000);
    </script>
  @elseif ($gptRequest->progressStatus === 1)
    <div class="alert alert-outline-primary">В момента се извършва визуален анализ. Моля, изчакайте ({{ app('request')->input('i') + 1 }})!</div>
    <script type="module">
      setTimeout(() => document.location = '?i=' + {{ app('request')->input('i') + 1 }}, 1000);
    </script>
  @elseif ($gptRequest->progressStatus === 2)
    <div class="card mb-5">
      <div class="card-body">
        <div class="row g-4 g-xl-1 g-xxl-3 justify-content-between">
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center ps-xl-5 border-translucent">
              <div class="d-flex bg-primary-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-truck-container"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Тип превозно средство</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($gptRequest->responseContent->vehicle->type) ? $gptRequest->responseContent->vehicle->type : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-success-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-input-numeric"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Регистрационен номер</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($gptRequest->responseContent->plate) ? $gptRequest->responseContent->plate : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-secondary-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-square-quote"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Модел превозно средство</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($gptRequest->responseContent->vehicle->model) ? $gptRequest->responseContent->vehicle->model : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-info-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-trailer"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Ремарке</p>
                <h4 class="fw-bolder text-nowrap">
                  {{ !empty($gptRequest->responseContent->trailer->type) ? $gptRequest->responseContent->trailer->type : '-' }}
                  {{ !empty($gptRequest->responseContent->trailer->model) ? $gptRequest->responseContent->trailer->model : '-' }}
                </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-md-6 d-flex align-items-stretch">
        <div class="card w-100">
          <div class="card-body">
            <h2 class="h4 mb-5">Проблеми по превозното средство ({{ !empty($gptRequest->responseContent->vehicle->type) ? $gptRequest->responseContent->vehicle->type : '-' }})</h2>

            @if (!empty($gptRequest->responseContent->vehicle->problems) || !empty($gptRequest->responseContent->vehicle->scratches))
              <table class="table fs-9">
                <thead>
                <tr>
                  <th class="white-space-nowrap align-middle text-uppercase">Част</th>
                  <th class="white-space-nowrap align-middle text-uppercase">Описание</th>
                  <th class="white-space-nowrap align-middle text-uppercase">Обеденост</th>
                  <th class="white-space-nowrap align-middle text-uppercase">Критичност</th>
                </tr>
                </thead>
                <tbody class="list">
                @foreach($gptRequest->responseContent->vehicle->problems ?? [] as $problem)
                  <tr class="btn-reveal-trigger position-static">
                    <td class="align-middle white-space-nowrap py-2">
                      {{ $problem->part ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      {{ $problem->summary ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      @if (($problem->confidence ?? '-') === 'high')
                        <i class="fa-regular fa-angle-up text-success"></i>
                      @elseif (($problem->confidence ?? '-') === 'middle')
                        <i class="fa-regular fa-angle-right text-warning"></i>
                      @elseif (($problem->confidence ?? '-') === 'low')
                        <i class="fa-regular fa-angle-down text-danger"></i>
                      @endif

                      {{ $problem->confidence ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      @if ($problem->criticality ?? null == 'critical' )
                        <span class="badge badge-phoenix badge-phoenix-danger">
                          Критично
                        </span>
                      @else
                        <span class="badge badge-phoenix badge-phoenix-warning">
                          Не критично
                        </span>
                      @endif
                    </td>
                  </tr>
                @endforeach
                @foreach($gptRequest->responseContent->vehicle->scratches ?? [] as $scratch)
                  <tr class="btn-reveal-trigger position-static">
                    <td class="align-middle white-space-nowrap py-2">
                      Драскотина / Пукнатина
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      {{ $scratch->summary ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      @if (($scratch->confidence ?? '-') === 'high')
                        <i class="fa-regular fa-angle-up text-success"></i>
                      @elseif (($scratch->confidence ?? '-') === 'middle')
                        <i class="fa-regular fa-angle-right text-warning"></i>
                      @elseif (($scratch->confidence ?? '-') === 'low')
                        <i class="fa-regular fa-angle-down text-danger"></i>
                      @endif

                      {{ $scratch->confidence ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      <span class="badge badge-phoenix badge-phoenix-secondary">
                        Некласифицирано
                      </span>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            @else
              <p>Няма открити проблеми.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-stretch">
        <div class="card w-100">
          <div class="card-body">
            <h2 class="h4 mb-5">Проблеми по ремаркето ({{ !empty($gptRequest->responseContent->trailer->type) ? 'Тип: ' . $gptRequest->responseContent->trailer->type : '-' }})</h2>

            @if (!empty($gptRequest->responseContent->trailer->problems))
              <table class="table fs-9">
                <thead>
                <tr>
                  <th class="white-space-nowrap align-middle text-uppercase">Описание</th>
                  <th class="white-space-nowrap align-middle text-uppercase">Обеденост</th>
                </tr>
                </thead>
                <tbody class="list">
                @foreach($gptRequest->responseContent->trailer->problems ?? [] as $problem)
                  <tr class="btn-reveal-trigger position-static">
                    <td class="align-middle white-space-nowrap py-2">
                      {{ $problem->summary ?? '-' }}
                    </td>
                    <td class="align-middle white-space-nowrap py-2">
                      @if (($problem->confidence ?? '-') === 'high')
                        <i class="fa-regular fa-angle-up text-success"></i>
                      @elseif (($problem->confidence ?? '-') === 'middle')
                        <i class="fa-regular fa-angle-right text-warning"></i>
                      @elseif (($problem->confidence ?? '-') === 'low')
                        <i class="fa-regular fa-angle-down text-danger"></i>
                      @endif

                      {{ $problem->confidence ?? '-' }}
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            @else
              <p>Няма открити проблеми.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif

  <hr/>

  <div class="fs-9 text-muted mb-3">
    @if (!empty($gptRequest->responseContent->version))
      Версия на модела:
      <code class="fs-9">studio-bg-{{ $gptRequest->responseContent->version ?? '-' }}</code>
    @endif

    @if ($gptRequest->response)
      |
      <a data-bs-toggle="collapse" href="#responseBlock" role="button" aria-expanded="false" aria-controls="responseBlock">
        Debug
      </a>
      <div class="collapse" id="responseBlock">
        @dump($gptRequest->response)
      </div>
    @endif
  </div>
@endsection
