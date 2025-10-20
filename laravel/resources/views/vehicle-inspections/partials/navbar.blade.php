<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5">
  <div class="overflow-x-auto">
    <ul class="nav nav-content">
      <li class="nav-item">
        <ol class="breadcrumb m-3 white-space-nowrap flex-nowrap">
          <li class="breadcrumb-item">
            <a href="{{ url('/vehicle-inspections') }}">
              <i class="fa-regular fa-user"></i>
              Визуален анализ
            </a>
          </li>
          @if (!empty($gptRequest->id))
            <li class="breadcrumb-item active">
              <a href="{{ url('/vehicle-inspections/view/' . $gptRequest->id) }}" class="text-body-tertiary">
                {{ !empty($gptRequest->responseContent->summary) ? $gptRequest->responseContent->summary : '#' . $gptRequest->getOriginal('id') }}
              </a>
            </li>
          @elseif (Request::is('vehicle-inspections/create'))
            <li class="breadcrumb-item active">
              <a href="{{ url('/vehicle-inspections/create') }}" class="text-body-tertiary">
                Добавяне на запис
              </a>
            </li>
          @endif
        </ol>
      </li>

      @if (empty($gptRequest->id))
        @if (!Request::is('vehicle-inspections/create'))
          <li class="nav-item ms-auto">
            <a href="{{ url('/vehicle-inspections/create') }}" class="btn btn-sm btn-primary">
              <i class="fas fa-plus"></i>
              Добави запис
            </a>
          </li>
        @endif
      @else
        <li class="nav-item ms-auto">
          <a href="{{ url('/vehicle-inspections/view/' . $gptRequest->id) }}" class="btn btn-sm {{ Request::is('vehicle-inspections/view/*') ? 'btn-primary' : 'btn-phoenix-primary' }}">
            <i class="fa-regular fa-magnifying-glass"></i>
            Преглед
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/vehicle-inspections/reset/' . $gptRequest->id) }}" class="btn btn-sm {{ Request::is('vehicle-inspections/process/*') ? 'btn-primary' : 'btn-phoenix-primary' }}"  onclick="return confirm('Сигурни ли сте, че искате да стартирате нов анализ?')">
            <i class="fa-regular fa-microchip-ai"></i>
            Анализ (ресет)
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/vehicle-inspections/delete/' . $gptRequest->id) }}" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Сигурни ли сте, че искате да изтриете този ВИЗУАЛЕН АНАЛИЗ?')">
            <span class="text-danger">
              <i class="fa-regular fa-trash-can"></i>
              Изтрий записа
            </span>
          </a>
        </li>
      @endif
    </ul>
  </div>
</div>
