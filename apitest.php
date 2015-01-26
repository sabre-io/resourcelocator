<?php

include 'vendor/autoload.php';
include '../http/vendor/autoload.php';

$request = Sabre\HTTP\Sapi::getRequest();
$request->setBaseUrl('/~evert/sabre/resourcelocator/apitest.php');

$locator = new Sabre\ResourceLocator\Locator();

// Our 'API'

class Resource extends Sabre\ResourceLocator\NullResource {

   function httpGet($request) {

       $response = new Sabre\HTTP\Response(200, ['Content-Type' => 'text/html'], '<h1>Hello</h1>');
       return $response;

   }

}

// Setting up the 'route'

$locator->mount('api', function() { return new Resource(); });

// Serving the request:

$resource = $locator->get( $request->getPath() );
$response = $resource->httpGet( $request) ;

Sabre\HTTP\Sapi::sendResponse($response);


