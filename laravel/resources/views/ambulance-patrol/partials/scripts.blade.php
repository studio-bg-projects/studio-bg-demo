<script>
  let MyMap = {};

  MyMap.style = [
    {
      'featureType': 'administrative',
      'elementType': 'geometry',
      'stylers': [
        {
          'visibility': 'off',
        },
      ],
    },
    {
      'featureType': 'poi',
      'stylers': [
        {
          'visibility': 'off',
        },
      ],
    },
    {
      'featureType': 'road',
      'elementType': 'labels.icon',
      'stylers': [
        {
          'visibility': 'off',
        },
      ],
    },
    {
      'featureType': 'transit',
      'stylers': [
        {
          'visibility': 'off',
        },
      ],
    },
  ];

  MyMap.proxies = [];

  MyMap.init = function () {
    // Set proxies
    let proxies = Config.googleDirectionProxies;

    if (proxies && proxies.length > 0) {
      for (let i = 0; i < proxies.length; i++) {
        let proxyUrl;

        try {
          proxyUrl = new URL(proxies[i], window.location.href);
        } catch (error) {
          continue;
        }

        proxyUrl.searchParams.set('ping', 'true');

        fetch(proxyUrl.toString(), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
        }).then(function (response) {
          return response.text();
        }).then(function (text) {
          if (text === 'pong') {
            MyMap.proxies.push(proxies[i]);
          }
        }).catch(function () {
          // Ignore failing proxies
        });
      }
    }

    // Init map
    MyMap.directionsService = new google.maps.DirectionsService();
  };

  MyMap.create = function (place) {
    let element = place;

    if (typeof place === 'string') {
      element = document.querySelector(place);
    }

    if (!element) {
      throw new Error('Map container not found');
    }

    return new google.maps.Map(element, {
      zoom: 15,
      center: Config.defaultMapCenter,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      styles: MyMap.style,
    });
  };

  MyMap.directionRequest = function (originLat, originLng, destinationLat, destinationLng) {
    // Count requests
    if (!MyMap.directionRequest.startTime) {
      MyMap.directionRequest.startTime = new Date();
    }

    if (!MyMap.directionRequest.count) {
      MyMap.directionRequest.count = 0;
    }

    MyMap.directionRequest.count++;

    if ((MyMap.directionRequest.count % 50) === 0) {
      console.info('MyMap.directionRequest: ' + MyMap.directionRequest.count + ' requests for ' + Math.round(((new Date()) - MyMap.directionRequest.startTime) / 1000) + ' sec.');
    }

    // Make request
    if (MyMap.proxies.length > 0) {
      return new Promise(function (resolve, reject) {
        let proxyUrl = MyMap.proxies[0];
        let serviceUrl = 'https://maps.googleapis.com/maps/api/directions/json';
        serviceUrl += '?key=' + Config.googleMapsKeyProxy;
        serviceUrl += '&origin=' + originLat + ',' + originLng;
        serviceUrl += '&destination=' + destinationLat + ',' + destinationLng;

        let body = new URLSearchParams();
        body.append('url', serviceUrl);

        fetch(proxyUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          },
          body: body.toString(),
        }).then(function (response) {
          if (!response.ok) {
            throw new Error('Proxy request failed');
          }

          return response.json();
        }).then(function (data) {
          if (data.status === 'OK') {
            for (let i = 0; i < data.routes.length; i++) {
              if (!data.routes[i].overview_path && data.routes[i].overview_polyline) {
                data.routes[i].overview_path = google.maps.geometry.encoding.decodePath(data.routes[i].overview_polyline.points);
              }
            }
          }

          resolve(data);
        }).catch(reject);
      });
    } else {
      // Make request via google service
      return new Promise(function (resolve, reject) {
        MyMap.directionsService.route({
          origin: new google.maps.LatLng(originLat, originLng),
          destination: new google.maps.LatLng(destinationLat, destinationLng),
          travelMode: google.maps.TravelMode.DRIVING,
          language: 'bg',
        }, function (response, status) {
          if (status === 'OK') {
            resolve(response);
          } else {
            reject(status);
          }
        });
      });
    }
  };

  MyMap.calcDistanceFrom = function (lat1, lon1, lat2, lon2) {
    let EarthRadiusMeters = 6378137.0; // meters
    let dLat = (lat2 - lat1) * Math.PI / 180;
    let dLon = (lon2 - lon1) * Math.PI / 180;
    let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return EarthRadiusMeters * c;
  };

  MyMap.pathToArray = function (path) {
    let newPath = [];

    path.forEach(function (position) {
      newPath.push({
        lat: position.lat(),
        lng: position.lng(),
      });
    });

    return newPath;
  };

  MyMap.calcDistance = function (path) {
    let dist = 0;
    for (let i = 1; i < path.length; i++) {
      dist += MyMap.calcDistanceFrom(path[i].lat, path[i].lng, path[i - 1].lat, path[i - 1].lng);
    }
    return dist;
  };

  MyMap.calcPointAtDistance = function (path, metres) {
    if (metres === 0) {
      return path[0];
    }

    if (metres < 0) {
      return null;
    }

    if (path.length < 2) {
      return null;
    }

    let dist = 0;
    let oldDist = 0;
    let i;
    for (i = 1; (i < path.length && dist < metres); i++) {
      oldDist = dist;
      dist += MyMap.calcDistanceFrom(path[i].lat, path[i].lng, path[i - 1].lat, path[i - 1].lng);
    }

    if (dist < metres) {
      return null;
    }

    let point1 = path[i - 2];
    let point2 = path[i - 1];
    let m = (metres - oldDist) / (dist - oldDist);

    return new google.maps.LatLng(point1.lat + (point2.lat - point1.lat) * m, point1.lng + (point2.lng - point1.lng) * m);
  };

  ////

  function googleMapsApiIsLoaded() {
    MyMap.loaded();
  }

</script>

<script>
  let TimePolygon = function (map, color) {
    this.map = map;
    this.color = color || '#ff0000';

    this.centerLat = null;
    this.centerLng = null;

    this.requiredTime = 60 * 3; // sec
    this.pointsDistance = 200; // meters
    this.pointsRepeates = 3;
    this.rotateAngleStep = 20; // (10, 20, 30, 40, 60)

    this.points = [];
    this.poligonPath = {};
    this.currentDegrees = 0;

    // Create polygon
    this.polygon = new google.maps.Polygon({
      map: this.map,
      zIndex: 1,
      paths: [],
      strokeColor: this.color,
      strokeOpacity: 0,
      strokeWeight: 0,
      fillColor: this.color,
      fillOpacity: 0.5
    });

    // Create test points
    for (let i = 1; i <= this.pointsRepeates; i++) {
      this.points.push({
        distance: this.pointsDistance * i,
        marker: new google.maps.Marker({
          map: this.map,
          zIndex: 2,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            strokeOpacity: 0.5,
            strokeWeight: 1,
            strokeColor: this.color,
            fillColor: this.color,
            fillOpacity: 0,
            scale: 3
          }
        }),
        road: new google.maps.Polyline({
          map: this.map,
          strokeOpacity: 0.3,
          strokeWeight: 1.4,
          strokeColor: this.color
        })
      });
    }

    // Start syncinc
    this.syncPoints();
  };

  TimePolygon.prototype.updatePosition = function (lat, lng) {
    this.centerLat = lat;
    this.centerLng = lng;
  };

  TimePolygon.prototype.syncPoints = function () {
    // if (this.syncPoints.timer) {
    //     clearTimeout(this.syncPoints.timer);
    // }

    if (!this.centerLat || !this.centerLng) {
      this.syncPoints.timer = setTimeout(this.syncPoints.bind(this), 500);

      return;
    }

    if (!AmbulancePatrol.isPlayed) {
      this.syncPoints.timer = setTimeout(this.syncPoints.bind(this), 500);

      return;
    }

    this.currentDegrees = (this.currentDegrees + this.rotateAngleStep) % 360;

    // Set position by degrees
    for (let i = 0; i < this.points.length; i++) {
      let rLat = (this.points[i].distance / 6378135) * (180 / Math.PI);
      let rLng = rLat / Math.cos(this.centerLat * (Math.PI / 180));
      let pointRadians = this.currentDegrees * (Math.PI / 180);

      let lat = parseFloat(this.centerLat + (rLat * Math.sin(pointRadians)));
      let lng = parseFloat(this.centerLng + (rLng * Math.cos(pointRadians)));

      this.points[i].marker.setPosition({lat: lat, lng: lng});
    }

    // Request points
    let promises = [];

    for (let i = 0; i < this.points.length; i++) {
      let lat = this.points[i].marker.getPosition().lat();
      let lng = this.points[i].marker.getPosition().lng();

      // promises.push(Promise.resolve({status: 'test'}));
      promises.push(MyMap.directionRequest(this.centerLat, this.centerLng, lat, lng));
    }

    Promise.all(promises).then(function (values) {
      // Set position
      let tmp = [];

      for (let i = 0; i < values.length; i++) {
        if (values[i].status === 'OK') {
          let path = values[i].routes[0].overview_path;

          this.points[i].marker.setPosition(path[path.length - 1]);
          this.points[i].road.setPath(path);

          tmp.push({
            position: path[path.length - 1],
            duration: values[i].routes[0].legs[0].duration.value
          });
        }
      }

      // Update polygon
      if (tmp.length) {
        tmp.sort(function (a, b) {
          let durationA = Math.abs(this.requiredTime - a.duration);
          let durationB = Math.abs(this.requiredTime - b.duration);

          if (durationA > durationB) {
            return 1;
          } else if (durationA < durationB) {
            return -1;
          } else {
            return 0;
          }
        }.bind(this));

        this.setPolygonPoint(this.currentDegrees, tmp[0].position);
      }

      this.syncPoints.timer = setTimeout(this.syncPoints.bind(this), 50);
    }.bind(this)).catch(function (error) {
      console.log('Error', error);
      this.syncPoints.timer = setTimeout(this.syncPoints.bind(this), 500);
    }.bind(this));
  };

  TimePolygon.prototype.setPolygonPoint = function (index, position) {
    let center = new google.maps.LatLng(this.centerLat, this.centerLng);

    this.poligonPath[index] = position;
    this.poligonPath[index].angle = google.maps.geometry.spherical.computeHeading(center, position);

    let path = Object.values(this.poligonPath);

    path.sort(function (a, b) {
      return a.angle - b.angle;
    });

    this.polygon.setPath(path);
  };

  TimePolygon.prototype.destroy = function () {
    for (let i = 0; i < this.points.length; i++) {
      if (this.points[i].marker) {
        this.points[i].marker.setMap(null);
      }

      if (this.points[i].road) {
        this.points[i].road.setMap(null);
      }

      if (this.polygon) {
        this.polygon.setMap(null);
      }

      if (this.syncPoints.timer) {
        clearTimeout(this.syncPoints.timer);
      }

      this.syncPoints = function () {
        return false;
      };
    }
  };
</script>

<script>
  let AmbulancePatrol = {};

  AmbulancePatrol.map = null;

  AmbulancePatrol.ambulanceBlanks = [];
  AmbulancePatrol.ambulanceTemplate = null;

  AmbulancePatrol.accidentMarker = null;
  AmbulancePatrol.accidentAmbulanceId = null;

  AmbulancePatrol.ambulances = {};

  AmbulancePatrol.speedHumanMultiplier = 50;

  AmbulancePatrol.isPlayed = true;

  function showElement(element, displayValue) {
    if (!element) {
      return;
    }

    if (displayValue) {
      element.style.display = displayValue;
    } else {
      element.style.removeProperty('display');
    }
  }

  function hideElement(element) {
    if (!element) {
      return;
    }

    element.style.display = 'none';
  }

  function toggleElement(element, visible, displayValue) {
    if (!element) {
      return;
    }

    if (visible) {
      showElement(element, displayValue);
    } else {
      hideElement(element);
    }
  }

  function toggleElements(selector, visible, displayValue) {
    document.querySelectorAll(selector).forEach(function (element) {
      toggleElement(element, visible, displayValue);
    });
  }

  function forEachAmbulance(callback) {
    Object.keys(AmbulancePatrol.ambulances).forEach(function (key) {
      callback(AmbulancePatrol.ambulances[key]);
    });
  }

  AmbulancePatrol.init = function () {
    AmbulancePatrol.map = MyMap.create('#map');

    AmbulancePatrol.ambulanceTemplate = document.getElementById('ambulanceTemplate');

    if (AmbulancePatrol.ambulanceTemplate && AmbulancePatrol.ambulanceTemplate.parentNode) {
      AmbulancePatrol.ambulanceTemplate.parentNode.removeChild(AmbulancePatrol.ambulanceTemplate);
    }

    AmbulancePatrol.notificationsTemplate = document.getElementById('notificationTemplate');

    if (AmbulancePatrol.notificationsTemplate && AmbulancePatrol.notificationsTemplate.parentNode) {
      AmbulancePatrol.notificationsTemplate.parentNode.removeChild(AmbulancePatrol.notificationsTemplate);
    }

    AmbulancePatrol.ambulanceBlanks = Config.ambulanceBlanks.slice();

    setInterval(AmbulancePatrol.moveAmbulances, 100);
    setInterval(AmbulancePatrol.setAmbulanceSpeed, 1000);

    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.updateAccidentLines = function () {
    if (AmbulancePatrol.updateAccidentLines.timer) {
      clearTimeout(AmbulancePatrol.updateAccidentLines.timer);
    }

    AmbulancePatrol.updateAccidentLines.timer = setTimeout(AmbulancePatrol.updateAccidentLines, 5000);

    if (!AmbulancePatrol.accidentMarker || !AmbulancePatrol.accidentMarker.getPosition()) {
      return;
    }

    if (!AmbulancePatrol.isPlayed) {
      return;
    }

    forEachAmbulance(function (ambulance) {
      if (ambulance.stopUpdate === 2) {
        return;
      } else if (ambulance.stopUpdate === 1) {
        AmbulancePatrol.notify(ambulance.plate, 'thumb_up', 'arrived at the incident');

        ambulance.stopUpdate = 2;
      }

      if (!ambulance.ambulanceMarker || !ambulance.ambulanceMarker.getPosition()) {
        return;
      }

      let directionPromise = MyMap.directionRequest(
        ambulance.ambulanceMarker.getPosition().lat(),
        ambulance.ambulanceMarker.getPosition().lng(),
        AmbulancePatrol.accidentMarker.getPosition().lat(),
        AmbulancePatrol.accidentMarker.getPosition().lng()
      );

      directionPromise.then(function (response) {
        if (response.status === 'OK' && response.routes.length) {
          if (AmbulancePatrol.accidentAmbulanceId !== ambulance.id) {
            let path = response.routes[0].overview_path;

            if (ambulance.accidentLine) {
              ambulance.accidentLine.setPath(path);
            } else {
              ambulance.accidentLine = new google.maps.Polyline({
                path: path,
                strokeOpacity: 0.4,
                strokeWeight: 3,
                strokeColor: ambulance.color,
                map: AmbulancePatrol.map
              });
            }
          }

          ambulance.accidentDistance = response.routes[0].legs[0].distance.value;
          ambulance.accidentDuration = response.routes[0].legs[0].duration.value;
          ambulance.accidentCurrentAddress = response.routes[0].legs[0].start_address;
          ambulance.accidentCurrentAddress = ambulance.accidentCurrentAddress.replace(/,.*/g, '');

          AmbulancePatrol.ambulanceInfo(ambulance.id);
        }
      }).catch(function (error) {
        console.error(error);
      });
    });
  };

  AmbulancePatrol.moveAmbulances = function () {
    if (!AmbulancePatrol.isPlayed) {
      return;
    }

    forEachAmbulance(function (ambulance) {
      if (ambulance.waitReverse) {
        return;
      }

      if (!ambulance.ambulanceMarker.getMap()) {
        ambulance.ambulanceMarker.setMap(AmbulancePatrol.map);
      }

      if (!ambulance.speedStep) {
        ambulance.speedStep = +1;
      }

      if (ambulance.travelDistance === null) {
        ambulance.travelDistance = 0;
      } else {
        ambulance.travelDistance += ambulance.speed;
      }

      let movePosition;

      if (AmbulancePatrol.accidentAmbulanceId === ambulance.id) {
        let totalDistance = MyMap.calcDistance(MyMap.pathToArray(ambulance.accidentLine.getPath()));

        if (ambulance.travelDistance >= totalDistance) {
          ambulance.travelDistance = totalDistance;

          if (!ambulance.stopUpdate) {
            ambulance.stopUpdate = 1;
          }
        }

        movePosition = MyMap.calcPointAtDistance(MyMap.pathToArray(ambulance.accidentLine.getPath()), ambulance.travelDistance);
      } else {
        if (ambulance.patrolLine && ambulance.patrolLine.getPath()) {
          let totalDistance = MyMap.calcDistance(MyMap.pathToArray(ambulance.patrolLine.getPath()));

          if (ambulance.travelDistance > totalDistance) {
            ambulance.travelDistance = totalDistance;
          }

          movePosition = MyMap.calcPointAtDistance(MyMap.pathToArray(ambulance.patrolLine.getPath()), ambulance.travelDistance);

          if (ambulance.travelDistance >= totalDistance) {
            let startPosition = ambulance.endMarker.getPosition();
            let endPosition = ambulance.startMarker.getPosition();

            ambulance.startMarker.setPosition(startPosition);
            ambulance.endMarker.setPosition(endPosition);
            AmbulancePatrol.relocatePoints(ambulance.id);

            ambulance.waitReverse = true;
            ambulance.travelDistance = null;
          }
        }
      }

      if (movePosition) {
        ambulance.ambulanceMarker.setPosition(movePosition);
        ambulance.timePolygon.updatePosition(movePosition.lat(), movePosition.lng());
      }

      AmbulancePatrol.ambulanceInfo(ambulance.id);
    });

    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.setAmbulanceSpeed = function () {
    let from = 0.5;
    let to = 2.5;

    forEachAmbulance(function (ambulance) {
      if (ambulance.stopUpdate) {
        return;
      }

      let time = new Date();

      if (!ambulance.nextSpeedChange || ambulance.nextSpeedChange <= time) {
        ambulance.nextSpeedChange = new Date();
        ambulance.nextSpeedChange.setSeconds(ambulance.nextSpeedChange.getSeconds() + AmbulancePatrol.rand(4, 8));

        ambulance.speed = AmbulancePatrol.rand(from * 10, to * 10) / 10;
      }
    });
  };

  AmbulancePatrol.createAccident = function () {
    if (AmbulancePatrol.accidentMarker) {
      return;
    }

    AmbulancePatrol.accidentMarker = new google.maps.Marker({
      position: {
        lat: AmbulancePatrol.map.getCenter().lat(),
        lng: AmbulancePatrol.map.getCenter().lng()
      },
      animation: google.maps.Animation.DROP,
      draggable: true,
      map: AmbulancePatrol.map
    });

    google.maps.event.addListener(AmbulancePatrol.accidentMarker, 'dragend', function () {
      if (AmbulancePatrol.accidentAmbulanceId) {
        AmbulancePatrol.ambulances[AmbulancePatrol.accidentAmbulanceId].travelDistance = null;
      }
    });

    AmbulancePatrol.updateAccidentLines();

    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.createAmbulances = function () {
    if (!AmbulancePatrol.ambulanceTemplate || !AmbulancePatrol.ambulanceBlanks.length) {
      return;
    }

    document.querySelector('.notify-label').style.display = 'none';

    let ambulance = {};
    ambulance.color = AmbulancePatrol.ambulanceBlanks.shift();
    ambulance.plate = AmbulancePatrol.randomPlate();
    ambulance.id = ambulance.plate.replace(/ /gi, '');
    ambulance.people = AmbulancePatrol.rand(3, 6);
    ambulance.travelDistance = null;
    ambulance.speed = 1;

    ambulance.timePolygon = new TimePolygon(AmbulancePatrol.map, ambulance.color);

    ambulance.html = AmbulancePatrol.ambulanceTemplate.cloneNode(true);
    ambulance.html.removeAttribute('id');
    ambulance.html.removeAttribute('style');

    let mapItems = document.querySelector('.map-items');

    if (mapItems) {
      mapItems.insertBefore(ambulance.html, mapItems.firstChild);
    }

    ambulance.html.id = 'ambulance-' + ambulance.id;

    let style = document.createElement('style');
    style.textContent = '#ambulance-' + ambulance.id + ':after { background-color: ' + ambulance.color + '; }';
    document.head.appendChild(style);

    ambulance.nodeRemove = ambulance.html.querySelector('[data-trigger="remove"]');
    ambulance.nodeName = ambulance.html.querySelector('[data-placeholder="name"]');
    ambulance.nodeSpeed = ambulance.html.querySelector('[data-placeholder="speed"]');
    ambulance.nodeLabel = ambulance.html.querySelector('[data-placeholder="label"]');
    ambulance.nodeSend = ambulance.html.querySelector('[data-trigger="send"]');

    if (ambulance.nodeRemove) {
      console.log('ambulance.nodeRemove', ambulance.nodeRemove);
      ambulance.nodeRemove.addEventListener('click', AmbulancePatrol.removeAmbulance.bind(null, ambulance.id));
    }

    if (ambulance.nodeName) {
      ambulance.nodeName.textContent = ambulance.plate;
    }

    if (ambulance.nodeLabel) {
      ambulance.nodeLabel.innerHTML = '&nbsp;';
    }

    if (ambulance.nodeSend) {
      ambulance.nodeSend.addEventListener('click', AmbulancePatrol.sendAmbulanceToAccident.bind(null, ambulance.id));
      hideElement(ambulance.nodeSend);
    }

    ambulance.ambulanceMarker = new google.maps.Marker({
      zIndex: 3,
      icon: {
        path: google.maps.SymbolPath.CIRCLE,
        strokeOpacity: 1,
        strokeWeight: 1.5,
        strokeColor: ambulance.color,
        fillColor: ambulance.color,
        fillOpacity: 0.3,
        scale: 17
      }
    });

    let zones = AmbulancePatrol.generateZones(2, 3);
    let zone = zones[Object.keys(AmbulancePatrol.ambulances).length % zones.length];
    let halfX = (zone.endX - zone.startX) / 2;
    let halfY = (zone.endY - zone.startY) / 2;

    let startEndMarkerBase = {
      map: AmbulancePatrol.map,
      draggable: true,
      animation: google.maps.Animation.DROP,
      zIndex: 4,
      icon: {
        path: google.maps.SymbolPath.CIRCLE,
        strokeOpacity: 0,
        fillColor: ambulance.color,
        fillOpacity: 1,
        scale: 5
      }
    };

    function createMarker(position) {
      return new google.maps.Marker({
        map: startEndMarkerBase.map,
        draggable: startEndMarkerBase.draggable,
        animation: startEndMarkerBase.animation,
        zIndex: startEndMarkerBase.zIndex,
        icon: Object.assign({}, startEndMarkerBase.icon),
        position: position
      });
    }

    ambulance.startMarker = createMarker({
      lat: AmbulancePatrol.rand(zone.startX * 100000, (zone.endX - halfX) * 100000) / 100000,
      lng: AmbulancePatrol.rand(zone.startY * 100000, (zone.endY - halfY) * 100000) / 100000
    });
    google.maps.event.addListener(ambulance.startMarker, 'dragend', AmbulancePatrol.relocatePoints.bind({}, ambulance.id));

    ambulance.endMarker = createMarker({
      lat: AmbulancePatrol.rand((zone.startX + halfX) * 100000, zone.endX * 100000) / 100000,
      lng: AmbulancePatrol.rand((zone.startY + halfY) * 100000, zone.endY * 100000) / 100000
    });
    google.maps.event.addListener(ambulance.endMarker, 'dragend', AmbulancePatrol.relocatePoints.bind({}, ambulance.id));

    AmbulancePatrol.ambulances[ambulance.id] = ambulance;

    AmbulancePatrol.relocatePoints(ambulance.id);

    AmbulancePatrol.controlVisibility();

    AmbulancePatrol.notify(ambulance.plate, 'repeat', 'started patrolling');
  };

  AmbulancePatrol.sendAmbulanceToAccident = function (ambulanceId) {
    if (!AmbulancePatrol.accidentMarker) {
      return;
    }

    if (!AmbulancePatrol.ambulances[ambulanceId]) {
      return;
    }

    AmbulancePatrol.accidentAmbulanceId = ambulanceId;

    let ambulance = AmbulancePatrol.ambulances[ambulanceId];

    ambulance.accidentLine.setOptions({
      strokeOpacity: 1,
      strokeWeight: 4
    });

    ambulance.travelDistance = null;

    AmbulancePatrol.accidentMarker.setDraggable(false);

    AmbulancePatrol.notify(ambulance.plate, 'trending_up', 'is on its way to the incident');

    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.generateZones = function (totalX, totalY) {
    let startX = AmbulancePatrol.map.getBounds().getNorthEast().lat();
    let startY = AmbulancePatrol.map.getBounds().getNorthEast().lng();
    let endX = AmbulancePatrol.map.getBounds().getSouthWest().lat();
    let endY = AmbulancePatrol.map.getBounds().getSouthWest().lng();

    let stepX = (endX - startX) / totalX;
    let stepY = (endY - startY) / totalY;

    let zones = [];
    for (let x = 1; x <= totalX; x++) {
      for (let y = 1; y <= totalY; y++) {
        let endX = startX + (x * stepX);
        let endY = startY + (y * stepY);

        zones.push({
          startX: endX - stepX,
          startY: endY - stepY,
          endX: endX,
          endY: endY
        });
      }
    }

    return zones;
  };

  AmbulancePatrol.relocatePoints = function (ambulanceId) {
    if (!AmbulancePatrol.ambulances[ambulanceId]) {
      return;
    }

    let ambulance = AmbulancePatrol.ambulances[ambulanceId];

    let startPosition = ambulance.startMarker.getPosition();
    let endPosition = ambulance.endMarker.getPosition();

    let directionPromise = MyMap.directionRequest(startPosition.lat(), startPosition.lng(), endPosition.lat(), endPosition.lng());
    directionPromise.then(function (response) {
      if (response.status === 'OK' && response.routes.length) {
        let path = response.routes[0].overview_path;

        // Draw road
        if (ambulance.patrolLine) {
          ambulance.patrolLine.setPath(path);
        } else {
          ambulance.patrolLine = new google.maps.Polyline({
            path: path,
            strokeOpacity: 0,
            icons: [{
              icon: {
                path: 'M 0,-1 0,1',
                strokeOpacity: 1,
                strokeColor: ambulance.color,
                scale: 1.1
              },
              repeat: '9px'
            }],
            map: AmbulancePatrol.map
          });
        }

        // Mark ambulance as reversed
        setTimeout(function () {
          ambulance.waitReverse = false;
        }, 200);

        // Snap markers
        ambulance.startMarker.setPosition(path[0]);
        ambulance.endMarker.setPosition(path[path.length - 1]);
      }
    }).catch(function (error) {
      console.error(error);
    });
  };

  AmbulancePatrol.removeAmbulance = function (ambulanceId) {
    if (!AmbulancePatrol.ambulances[ambulanceId]) {
      return;
    }

    let ambulance = AmbulancePatrol.ambulances[ambulanceId];

    ambulance.html.remove();

    if (ambulance.ambulanceMarker) {
      ambulance.ambulanceMarker.setMap(null);
    }

    if (ambulance.startMarker) {
      ambulance.startMarker.setMap(null);
    }

    if (ambulance.endMarker) {
      ambulance.endMarker.setMap(null);
    }

    if (ambulance.patrolLine) {
      ambulance.patrolLine.setMap(null);
    }

    if (ambulance.accidentLine) {
      ambulance.accidentLine.setMap(null);
    }

    if (ambulance.timePolygon) {
      ambulance.timePolygon.destroy();
    }

    AmbulancePatrol.ambulanceBlanks.push(ambulance.color);

    if (AmbulancePatrol.accidentAmbulanceId === ambulanceId) {
      AmbulancePatrol.accidentAmbulanceId = null;
    }

    delete AmbulancePatrol.ambulances[ambulanceId];

    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.ambulanceInfo = function (ambulanceId) {
    if (!AmbulancePatrol.ambulances[ambulanceId]) {
      return;
    }

    let ambulance = AmbulancePatrol.ambulances[ambulanceId];
    let leftMin = AmbulancePatrol.secToHumanMin(ambulance.accidentDuration);
    let distance = (ambulance.accidentDistance / 1000).toFixed(2);
    let speed = parseInt(ambulance.speed * AmbulancePatrol.speedHumanMultiplier);

    // Average speed
    if (!ambulance.speedRegistry) {
      ambulance.speedRegistry = [];
      ambulance.speedRegistry.push(speed);
    } else if (ambulance.speedRegistry[ambulance.speedRegistry.length - 1] !== speed) {
      ambulance.speedRegistry.push(speed);
    }

    let averageSpeed = 0;
    for (let i = 0; i < ambulance.speedRegistry.length; i++) {
      averageSpeed += ambulance.speedRegistry[i];
    }
    averageSpeed = averageSpeed / ambulance.speedRegistry.length;

    // Ambulance speed
    if (ambulance.nodeSpeed) {
      ambulance.nodeSpeed.innerHTML = speed + ' km/h.';
    }

    // Ambulance label
    if (!isNaN(distance) && ambulance.nodeLabel) {
      let label = leftMin + ' <small>min.</small>';
      label += ' <span class="distance">/ ' + distance + ' km.</span>';
      ambulance.nodeLabel.innerHTML = label;
    }

    // Live stats
    if (AmbulancePatrol.accidentAmbulanceId === ambulance.id) {
      document.querySelectorAll('[data-info="speed"]').forEach(function (element) {
        element.textContent = speed;
      });

      document.querySelectorAll('[data-info="average-speed"]').forEach(function (element) {
        element.textContent = parseInt(averageSpeed, 10);
      });

      document.querySelectorAll('[data-info="left-time"]').forEach(function (element) {
        element.textContent = leftMin;
      });

      document.querySelectorAll('[data-info="street"]').forEach(function (element) {
        element.textContent = ambulance.accidentCurrentAddress;
      });

      document.querySelectorAll('[data-info="plate"]').forEach(function (element) {
        element.textContent = ambulance.plate;
      });

      document.querySelectorAll('[data-info="people"]').forEach(function (element) {
        element.textContent = ambulance.people;
      });
    }
  };

  AmbulancePatrol.controlVisibility = function () {
    // Create new ambulance
    toggleElements('[data-id="create-ambulance"]', AmbulancePatrol.ambulanceBlanks.length > 0);

    if (AmbulancePatrol.accidentMarker) {
      toggleElements('[data-id="create-accident"]', false);
    } else if (Object.keys(AmbulancePatrol.ambulances).length) {
      toggleElements('[data-id="create-accident"]', true);
    } else {
      toggleElements('[data-id="create-accident"]', false);
    }

    if (!Object.keys(AmbulancePatrol.ambulances).length) {
      toggleElements('[data-id="simulation-pause"]', false);
      toggleElements('[data-id="simulation-play"]', false);
    } else if (AmbulancePatrol.isPlayed) {
      toggleElements('[data-id="simulation-pause"]', false);
      toggleElements('[data-id="simulation-play"]', true);
    } else {
      toggleElements('[data-id="simulation-pause"]', true);
      toggleElements('[data-id="simulation-play"]', false);
    }

    forEachAmbulance(function (ambulance) {
      let shouldShowSend = Boolean(AmbulancePatrol.accidentMarker && !AmbulancePatrol.accidentAmbulanceId && ambulance.accidentLine);
      toggleElement(ambulance.nodeSend, shouldShowSend, 'inline-block');
    });

    toggleElements('[data-id="live-stat"]', Boolean(AmbulancePatrol.accidentAmbulanceId));

    if (AmbulancePatrol.accidentMarker) {
      let closestAmbulance = null;

      forEachAmbulance(function (ambulance) {
        if (ambulance.html) {
          ambulance.html.classList.remove('closest');
        }

        if (typeof ambulance.accidentDuration === 'undefined') {
          return;
        }

        if (closestAmbulance === null || closestAmbulance.accidentDuration > ambulance.accidentDuration) {
          closestAmbulance = ambulance;
        }
      });

      if (closestAmbulance && closestAmbulance.html) {
        closestAmbulance.html.classList.add('closest');
      }
    } else {
      forEachAmbulance(function (ambulance) {
        if (ambulance.html) {
          ambulance.html.classList.remove('closest');
        }
      });
    }
  };

  AmbulancePatrol.playPause = function () {
    AmbulancePatrol.isPlayed = !AmbulancePatrol.isPlayed;
    AmbulancePatrol.controlVisibility();
  };

  AmbulancePatrol.notify = function (name, icon, text) {
    if (!AmbulancePatrol.notificationsTemplate) {
      return;
    }

    let html = AmbulancePatrol.notificationsTemplate.cloneNode(true);
    html.removeAttribute('id');
    html.removeAttribute('style');

    let nameNode = html.querySelector('[data-id="name"]');
    let iconNode = html.querySelector('[data-id="icon"]');
    let textNode = html.querySelector('[data-id="text"]');

    if (nameNode) {
      nameNode.textContent = name;
    }

    if (iconNode) {
      iconNode.textContent = icon;
    }

    if (textNode) {
      textNode.textContent = text;
    }

    let container = document.querySelector('.notifications');

    if (container) {
      container.appendChild(html);
      showElement(html);

      setTimeout(function () {
        html.remove();
      }, 5000);
    }
  };

  AmbulancePatrol.randomPlate = function () {
    if (!AmbulancePatrol.randomPlate._plates) {
      AmbulancePatrol.randomPlate._plates = {};
    }

    while (true) {
      let plate = 'C';
      plate += ['', 'A', 'B'][AmbulancePatrol.rand(0, 2)];
      plate += ' ';
      plate += AmbulancePatrol.rand(0, 9);
      plate += AmbulancePatrol.rand(0, 9);
      plate += AmbulancePatrol.rand(0, 9);
      plate += AmbulancePatrol.rand(0, 9);
      plate += ' ';
      plate += ['E', 'T', 'O', 'P', 'A', 'H', 'K', 'X', 'B', 'M'][AmbulancePatrol.rand(0, 9)];
      plate += ['E', 'T', 'O', 'P', 'A', 'H', 'K', 'X', 'B', 'M'][AmbulancePatrol.rand(0, 9)];

      if (!AmbulancePatrol.randomPlate._plates[plate]) {
        AmbulancePatrol.randomPlate._plates[plate] = true;
        return plate;
      }
    }
  };

  AmbulancePatrol.rand = function (min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
  };

  AmbulancePatrol.secToHumanMin = function (time) {
    function strPadLeft(string, pad, length) {
      return (new Array(length + 1).join(pad) + string).slice(-length);
    }

    let minutes = Math.floor(time / 60);
    let seconds = time - minutes * 60;

    return strPadLeft(minutes, '0', 2) + ':' + strPadLeft(seconds, '0', 2);
  };
</script>
