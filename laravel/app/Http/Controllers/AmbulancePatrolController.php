<?php

namespace App\Http\Controllers;

class AmbulancePatrolController extends Controller
{
  protected $googleMapsKey = 'AIzaSyAwDzP7HofpNJAaKqW99-42OcFkvYSY2QQ';
  protected $googleMapsKeyProxy = 'AIzaSyB0E9DnO1Z1QUcjBjgCJnbRoaiUFCXijbo';

  public function index()
  {
    return view('ambulance-patrol.index', [
      'googleMapsKey' => $this->googleMapsKey
    ]);
  }

  public function proxy()
  {
    // Headers
    if (!empty($_REQUEST['ping'])) {
      die('pong');
    }

    // Curl
    $url = 'https://maps.googleapis.com/maps/api/directions/json';
    $url .= '?key=' . $this->googleMapsKeyProxy;
    $url .= '&origin=' . request()->input('originLat') . ',' . request()->input('originLng');
    $url .= '&destination=' . request()->input('destinationLat') . ',' . request()->input('destinationLng');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    if ($_SERVER['REQUEST_METHOD'] != 'get') {
      curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
    }

    if (!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strrpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
      curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $responseHeaders = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    curl_close($ch);

    // Output
    //foreach (explode("\n", $responseHeaders) as $header) {
    //    header($header);
    //}

    return $body;
  }
}
