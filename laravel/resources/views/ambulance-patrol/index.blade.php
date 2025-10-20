@extends('layouts.app')

@section('content')
  @include('ambulance-patrol.partials.navbar')
  @include('ambulance-patrol.partials.scripts')

  <script async src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&loading=async&callback=initMap"></script>

  <script>
    function initMap() {
      MyMap.init();
      AmbulancePatrol.init();
    }
  </script>

  <div id="start-intro">
    <div class="alert alert-subtle-info fs-9 py-2 px-3" role="alert">
      <p class="mb-1">This proof of concept demonstrates a system that tracks ambulance locations within a region and determines which unit can reach an incident fastest, taking into account current traffic conditions.</p>
      <p class="mb-1">You can adjust the route points and move the incident location using drag & drop.</p>
      <p class="mb-0">
        <b>Start by adding a few ambulances to begin the simulation.</b>
      </p>
    </div>

    <script>
      function addFew(total) {
        for (let i = 1; i <= total; i++) {
          AmbulancePatrol.createAmbulances();
        }
      }
    </script>

    <button class="btn btn-primary" id="first-add-btn" onclick="addFew(5);">
      <i class="fa-regular fa-plus"></i>
      Add 5 Ambulances
    </button>
  </div>

  <div id="ambulance-patrol">
    <ul class="map-items" style="display: none;">
      <li id="ambulanceTemplate" style="display: none;">
        <a style="float: right; cursor: pointer;" data-trigger="remove">
          <i class="fa-regular fa-trash"></i>
        </a>
        <span class="item-name" data-placeholder="name"></span>
        <span class="item-speed" data-placeholder="speed"></span>
        <span class="item-label" data-placeholder="label"></span>
        <button data-trigger="send" class="btn btn-primary">Send</button>
      </li>
    </ul>

    <div class="ambulance-patrol-map mx-n4 mx-lg-n6">
      <div id="map" style="height: 100%;"></div>

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
          <i data-id="icon"></i>
          <p>
            <i class="fa-regular fa-van-shuttle"></i>
            <strong data-id="name">-</strong>
            <span data-id="text">-</span>
          </p>
        </li>
      </ul>
    </div>
  </div>
@endsection
