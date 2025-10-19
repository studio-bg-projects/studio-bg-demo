<div class="navbar-vertical-content">
  <ul class="navbar-nav flex-column">
    <li class="nav-item">
      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('dashboard') || Request::is('dashboard/*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-house"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Начало</span>
            </span>
          </div>
        </a>
      </div>
    </li>

    <li class="nav-item">
      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('ambulance-patrol') || Request::is('ambulance-patrol/*') ? 'active' : '' }}" href="{{ url('/ambulance-patrol') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-truck-medical"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">Ambulance Patrol</span>
            </span>
          </div>
        </a>
      </div>
    </li>

    <li class="nav-item">
      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('virtual-project-manager') || Request::is('virtual-project-manager/*') ? 'active' : '' }}" href="{{ url('/virtual-project-manager') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-user-microphone"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">AI Virtual Project Manager</span>
            </span>
          </div>
        </a>
      </div>
    </li>

    <li class="nav-item">
      <div class="nav-item-wrapper">
        <a class="nav-link label-1 {{ Request::is('vehicles-inspection') || Request::is('vehicles-inspection/*') ? 'active' : '' }}" href="{{ url('/vehicles-inspection') }}">
          <div class="d-flex align-items-center">
            <span class="nav-link-icon fs-8">
              <i class="fa-regular fa-car-burst"></i>
            </span>
            <span class="nav-link-text-wrapper">
              <span class="nav-link-text">AI Vehicles Inspection</span>
            </span>
          </div>
        </a>
      </div>
    </li>
  </ul>
</div>
