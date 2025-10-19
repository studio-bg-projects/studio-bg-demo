@extends('layouts.app')

@section('content')
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <script>
    const Config = {
      "googleMapsKey": "AIzaSyAwDzP7HofpNJAaKqW99-42OcFkvYSY2QQ",
      "googleMapsKeyProxy": "AIzaSyB0E9DnO1Z1QUcjBjgCJnbRoaiUFCXijbo",
      "defaultMapCenter": {
        "lat": 42.697713,
        "lng": 23.321844
      },
      "googleDirectionProxies": [
        @json(url('/ambulance-patrol/proxy/'))
      ],
      "ambulanceBlanks": [
        "#f26522",
        "#744be8",
        "#32caa1",
        "#6dcff2",
        "#ffca28",
        "#AD1457"
      ]
    };
  </script>

  @include('ambulance-patrol.partials.scripts')

  <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAwDzP7HofpNJAaKqW99-42OcFkvYSY2QQ&loading=async&callback=initMap"></script>

  <script>
    function initMap() {
      MyMap.init();
      AmbulancePatrol.init();
    }
  </script>


  <div id="ambulance-patrol">
    <div class="head">
      <h2>Ambulance Patrol - By Alex Gavazov 2015</h2>

      <ul class="controls">
        <li>
          <div data-id="create-accident" style="display: none;">
            <button class="btn btn-danger btn-pulse" onclick="AmbulancePatrol.createAccident();">
              <i class="material-icons">whatshot</i>
              Create an incident
            </button>
          </div>
        </li>

        <li>
          <div data-id="simulation-play" style="display: none;">
            <button class="btn btn-default" onclick="AmbulancePatrol.playPause();">
              <i class="material-icons">pause</i>
              Stop simulation
            </button>
          </div>

          <div data-id="simulation-pause" style="display: none;">
            <button class="btn btn-default" onclick="AmbulancePatrol.playPause();">
              <i class="material-icons">play_arrow</i>
              Run the simulation
            </button>
          </div>
        </li>

        <li>
          <div data-id="create-ambulance" style="display: none;">
            <button class="btn btn-default btn-pulse" onclick="AmbulancePatrol.createAmbulances();">
              <i class="material-icons">add</i>
              Add ambulance
            </button>
          </div>
        </li>
      </ul>
    </div>

    <ul class="map-items">
      <li class="notify-label">
        <p>This proof of concept demonstrates a system that tracks ambulance locations within a region and determines which unit can reach an incident fastest, taking into account current traffic conditions.</p>
        <p>You can adjust the route points and move the incident location using drag & drop.</p>
        <p>
          <b>Start by adding a few ambulances to begin the simulation.</b>
        </p>
      </li>
      <li id="ambulanceTemplate" style="display: none;">
        <a style="float: right; cursor: pointer;" data-trigger="remove">
          <i class="material-icons">delete</i>
        </a>
        <span class="item-name" data-placeholder="name"></span>
        <span class="item-speed" data-placeholder="speed"></span>
        <span class="item-label" data-placeholder="label"></span>
        <button data-trigger="send" class="btn btn-primary">Send</button>
      </li>
    </ul>

    <div class="ambulance-patrol-map">
      <div id="map" style="height: 500px;"></div>

      <div class="live-stats" data-id="live-stat" style="display: none;">
        <div class="head-data">
          <span>Current speed</span>
          <span data-info="speed" class="speed">--</span>
          <span>km/h</span>
        </div>
        <ul class="body-data">
          <li>
            <strong data-info="left-time">--:--</strong>
            minutes to arrival at location
          </li>
          <li>
            <strong data-info="speed" class="speed">--</strong>
            km/h speed
          </li>
          <li>
            <strong data-info="average-speed">--</strong>
            km/h average speed
          </li>
          <li>
            <strong data-info="street">--</strong>
            last location
          </li>
          <li>
            <strong data-info="plate">--</strong>
            plate
          </li>
          <li>
            <strong data-info="people">-</strong>
            person on board
          </li>
        </ul>
      </div>
      <ul class="notifications">
        <li class="directions" id="notificationTemplate" style="display: none;">
          <i class="material-icons" data-id="icon"></i>
          <p>
            <i class="material-icons">airport_shuttle</i>
            <strong data-id="name">-</strong>
            <span data-id="text">-</span>
          </p>
        </li>
      </ul>
    </div>
  </div>
@endsection
