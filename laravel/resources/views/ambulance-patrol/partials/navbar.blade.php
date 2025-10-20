<div class="widgets-scrollspy-nav z-5 bg-body-emphasis border-bottom mt-n5 mx-n4 mx-lg-n6 mb-5 overflow-x-auto d-flex align-items-center" style="min-height: 3rem;">
  <ul class="nav nav-content flex-row">
    <li class="nav-item">
      <ol class="breadcrumb my-0 mx-3 white-space-nowrap flex-nowrap">
        <li class="breadcrumb-item">
          <a href="{{ url('/ambulance-patrol') }}" class="text-body-tertiary">
            <i class="fa-regular fa-truck-medical"></i>
            Ambulance Patrol
          </a>
        </li>
      </ol>
    </li>
  </ul>

  <ul class="nav nav-content ms-auto">
    <li class="nav-item" data-id="create-accident" style="display: none;">
      <a class="nav-link px-2 fw-bold pulse-btn-danger" onclick="AmbulancePatrol.createAccident(); this.classList.remove('pulse-btn-danger');" style="cursor: pointer;">
        <span class="text-danger">
          <i class="fa-regular fa-fire"></i>
          Create an incident
        </span>
      </a>
    </li>

    <li class="nav-item" data-id="simulation-play" style="display: none;">
      <a class="nav-link px-2 fw-bold" onclick="AmbulancePatrol.playPause();" style="cursor: pointer;">
        <i class="fa-regular fa-pause"></i>
        Pause simulation
      </a>
    </li>

    <li class="nav-item" data-id="simulation-pause" style="display: none;">
      <a class="nav-link px-2 fw-bold" onclick="AmbulancePatrol.playPause();" style="cursor: pointer;">
        <i class="fa-regular fa-play"></i>
        Run the simulation
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link px-2 fw-bold pulse-btn-primary" onclick="AmbulancePatrol.createAmbulances(); this.classList.remove('pulse-btn-primary');" style="cursor: pointer;">
        <i class="fa-regular fa-plus"></i>
        Add ambulance
      </a>
    </li>
  </ul>
</div>
