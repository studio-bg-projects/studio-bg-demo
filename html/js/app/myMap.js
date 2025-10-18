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

MyMap.isLoaded = false;
MyMap.waitingStack = [];
MyMap.proxies = [];

MyMap.init = function () {
  // Init map
  let mapScript = document.createElement('script');
  mapScript.type = 'text/javascript';
  mapScript.src = 'https://maps.googleapis.com/maps/api/js?key=' + Config.get('googleMapsKey') + '&callback=googleMapsApiIsLoaded';
  $('head').append(mapScript);

  // Set proxies
  let proxies = Config.get('googleDirectionProxies');

  if (proxies && proxies.length > 0) {
    for (let i = 0; i < proxies.length; i++) {
      $.ajax({
        url: proxies[i],
        data: {
          ping: true,
        },
        success: function (response) {
          if (response === 'pong') {
            MyMap.proxies.push(proxies[i]);
          }
        },
      });
    }
  }
};

MyMap.loaded = function () {
  MyMap.isLoaded = true;
  MyMap.directionsService = new google.maps.DirectionsService();
};

MyMap.ready = function (callback) {
  if (MyMap.isLoaded) {
    callback();
    return;
  }

  MyMap.waitingStack.push(callback);

  if (!MyMap._tryInterval) {
    MyMap._tryInterval = setInterval(function () {
      if (MyMap.isLoaded) {
        clearInterval(MyMap._tryInterval);

        for (let i = 0; i < MyMap.waitingStack.length; i++) {
          MyMap.waitingStack[i]();
        }

        MyMap.waitingStack = [];
      }
    }, 10);
  }
};

MyMap.create = function (place) {
  return new Promise(function (resolve) {
    MyMap.ready(function () {
      place = $(place).get(0);

      let map = new google.maps.Map(place, {
        zoom: 15,
        center: Config.get('defaultMapCenter'),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: MyMap.style,
      });

      resolve(map);
    });
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
      serviceUrl += '?key=' + Config.get('googleMapsKeyProxy');
      serviceUrl += '&origin=' + originLat + ',' + originLng;
      serviceUrl += '&destination=' + destinationLat + ',' + destinationLng;

      $.ajax({
        url: proxyUrl,
        dataType: 'json',
        method: 'post',
        data: {
          url: serviceUrl,
        },
        success: function (response) {
          if (response.status === 'OK') {
            for (let i = 0; i < response.routes.length; i++) {
              if (!response.routes[i].overview_path && response.routes[i].overview_polyline) {
                response.routes[i].overview_path = google.maps.geometry.encoding.decodePath(response.routes[i].overview_polyline.points);
              }
            }
          }

          resolve(response);
        },
        error: reject,
      });
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

App.ready(MyMap.init);

////

function googleMapsApiIsLoaded() {
  MyMap.loaded();
}
