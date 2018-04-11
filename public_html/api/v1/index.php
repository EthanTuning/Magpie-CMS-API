<?php

/* Configuration stuff */
require 'creds.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';

$app = new \Slim\App;

$app->get('/test', function (Request $request, Response $response, array $args) {
    
    //GET
	$allGetVars = $request->getQueryParams();
	
	/*foreach($allGetVars as $key => $param){
	   //GET parameters list
	}*/
    
    $getParam = $allGetVars['name'];
    
    $response->getBody()->write("Hello, $getParam");

    return $response;
});

$app->run();

?>
