@extends('layouts.app')

@section('content')
  @include('vehicle-inspections.partials.navbar')

  <h1 class="h4 mb-5">
    Inspection Overview
    {{ !empty($vehicleInspection->responseContent->summary) ? ' - ' . $vehicleInspection->responseContent->summary : '' }}
  </h1>

  <div class="row">
    @foreach($vehicleInspection->files as $file)
      <div class="col text-center mb-5">
        <a href="{{ Storage::url('uploads/vehicle-inspections/' . $vehicleInspection->id . '/' . $file) }}" target="_blank" data-gallery="uploads-preview">
          <img src="{{ Storage::url('uploads/vehicle-inspections/' . $vehicleInspection->id . '/' . $file) }}" alt="" style="height: 150px;"/>
        </a>
      </div>
    @endforeach
  </div>

  <hr/>

  @if (!$vehicleInspection->progressStatus)
    <pre class="alert alert-outline-danger" id="js-response-error" style="display: none;"></pre>
    <div class="alert alert-outline-primary">The image inspection request has been submitted. Please wait!</div>
    <script type="module">
      $.ajax({
        url: '{{ url('/vehicle-inspections/process/' . $vehicleInspection->id) }}',
        success: function () {
          setTimeout(() => document.location = document.location, 2000);
        },
        error: function (data) {
          alert('An error occurred!');
          $('#js-response-error').text(data.responseText).fadeIn();
        }
      });

      setTimeout(() => document.location = '?', 1000);
    </script>
  @elseif ($vehicleInspection->progressStatus === 1)
    <div class="alert alert-outline-primary">
      The analysis is currently in progress.
      <br/>
      The average analysis time is between 3 to 5 minutes.
      <br/>
      In a production environment without warm-up, the process may be reduced to just a few seconds.
      <br/>
    </div>

    <div class="loader ms-auto me-auto my-5">
      <style>
        /* HTML: <div class="loader"></div> */
        .loader {
          width: 40px;
          aspect-ratio: 1;
          --c: linear-gradient(#3874ff 0 0);
          --r1: radial-gradient(farthest-side at bottom, #3874ff 93%, #3874ff);
          --r2: radial-gradient(farthest-side at top, #3874ff 93%, #3874ff);
          background: var(--c), var(--r1), var(--r2),
          var(--c), var(--r1), var(--r2),
          var(--c), var(--r1), var(--r2);
          background-repeat: no-repeat;
          animation: l2 1s infinite alternate;
        }

        @keyframes l2 {
          0%, 25% {
            background-size: 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
            background-position: 0 50%, 0 calc(50% - 2px), 0 calc(50% + 2px), 50% 50%, 50% calc(50% - 2px), 50% calc(50% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
          }
          50% {
            background-size: 8px 100%, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
            background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(50% - 2px), 50% calc(50% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
          }
          75% {
            background-size: 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
            background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(0% - 2px), 50% calc(100% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
          }
          95%, 100% {
            background-size: 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px;
            background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(0% - 2px), 50% calc(100% + 2px), 100% 50%, 100% calc(0% - 2px), 100% calc(100% + 2px);
          }
        }
      </style>
    </div>


    <script type="module">
      const checkFn = () => {
        $.ajax({
          url: '?',
          success: function (data) {
            const progressStatus = data?.vehicleInspection?.progressStatus;

            if (progressStatus !== 1) {
              setTimeout(() => document.location = '?i=' + {{ app('request')->input('i') + 1 }}, 1000);
            } else {
              setTimeout(checkFn, 1000);
            }
          }
        });
      };

      checkFn();
    </script>
  @elseif ($vehicleInspection->progressStatus === 2)
    <div class="card mb-5">
      <div class="card-body">
        <div class="row g-4 g-xl-1 g-xxl-3 justify-content-between">
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center ps-xl-5 border-translucent">
              <div class="d-flex bg-primary-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-truck-container"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Vehicle Type</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($vehicleInspection->responseContent->vehicle->type) ? $vehicleInspection->responseContent->vehicle->type : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-success-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-input-numeric"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">License Plate</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($vehicleInspection->responseContent->plate) ? $vehicleInspection->responseContent->plate : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-secondary-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-square-quote"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Vehicle Model</p>
                <h4 class="fw-bolder text-nowrap">{{ !empty($vehicleInspection->responseContent->vehicle->model) ? $vehicleInspection->responseContent->vehicle->model : '-' }}</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl">
            <div class="d-flex align-items-center border-start-xl ps-xl-5 border-translucent">
              <div class="d-flex bg-info-subtle rounded flex-center me-3" style="width:32px; height:32px">
                <i class="fa-regular fa-trailer"></i>
              </div>
              <div>
                <p class="fw-bold mb-1">Trailer</p>
                <h4 class="fw-bolder text-nowrap">
                  {{ !empty($vehicleInspection->responseContent->trailer->type) ? $vehicleInspection->responseContent->trailer->type : '-' }}
                  {{ !empty($vehicleInspection->responseContent->trailer->model) ? $vehicleInspection->responseContent->trailer->model : '-' }}
                </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-5">
      <div class="card-body">
        <h2 class="h4 mb-5">Vehicle Issues ({{ !empty($vehicleInspection->responseContent->vehicle->type) ? $vehicleInspection->responseContent->vehicle->type : '-' }})</h2>

        @if (!empty($vehicleInspection->responseContent->vehicle->problems) || !empty($vehicleInspection->responseContent->vehicle->scratches))
          <table class="table fs-9">
            <thead>
            <tr>
              <th class="white-space-nowrap align-middle text-uppercase">Part</th>
              <th class="white-space-nowrap align-middle text-uppercase">Description</th>
              <th class="white-space-nowrap align-middle text-uppercase">Confidence</th>
              <th class="white-space-nowrap align-middle text-uppercase">Criticality</th>
            </tr>
            </thead>
            <tbody class="list">
            @foreach($vehicleInspection->responseContent->vehicle->problems ?? [] as $problem)
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
                      Critical
                    </span>
                  @else
                    <span class="badge badge-phoenix badge-phoenix-warning">
                      Not Critical
                    </span>
                  @endif
                </td>
              </tr>
            @endforeach
            @foreach($vehicleInspection->responseContent->vehicle->scratches ?? [] as $scratch)
              <tr class="btn-reveal-trigger position-static">
                <td class="align-middle white-space-nowrap py-2">
                  Scratch / Crack
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
                    Unclassified
                  </span>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        @else
          <p>No issues found.</p>
        @endif
      </div>
    </div>

    <div class="card mb-5">
      <div class="card-body">
        <h2 class="h4 mb-5">Trailer Issues ({{ !empty($vehicleInspection->responseContent->trailer->type) ? 'Type: ' . $vehicleInspection->responseContent->trailer->type : '-' }})</h2>

        @if (!empty($vehicleInspection->responseContent->trailer->problems))
          <table class="table fs-9">
            <thead>
            <tr>
              <th class="white-space-nowrap align-middle text-uppercase">Description</th>
              <th class="white-space-nowrap align-middle text-uppercase">Confidence</th>
            </tr>
            </thead>
            <tbody class="list">
            @foreach($vehicleInspection->responseContent->trailer->problems ?? [] as $problem)
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
          <p>No issues found.</p>
        @endif
      </div>
    </div>
  @endif
@endsection
