<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');

// Headers
if (!empty($_REQUEST['ping'])) {
    die('pong');
}

if (empty($_REQUEST['url'])) {
    die('Please provide "url" POST');
}

// Curl
$url = $_REQUEST['url'];

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

print $body;
