<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firebase\Auth\Token\Exception\InvalidToken;



/* Configuration stuff */
require './creds/creds.php';

/* User Authentication code */
require 'authentication.php';

/* Composer stuff */
require './vendor/autoload.php';


/* Load the stuff from 'creds/creds.php' into Slim */
$app = new \Slim\App(['settings' => $config]);

/* Add the Authentication stuff */
$app->add( new AuthenticationMiddleware() );


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




