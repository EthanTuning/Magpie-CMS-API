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






/* Route used for testing */
$app->get('/test', function (Request $request, Response $response, array $args) {
    
    //GET
	$allGetVars = $request->getQueryParams();
	
	/*foreach($allGetVars as $key => $param){
	   //GET parameters list
	}*/
    
    $getParam = $allGetVars['name'];
    
    if ($getParam == null)
    {
		$response->getBody()->write(" Hello noname. ");
	}
    else
    {
		$response->getBody()->write(" Hello, $getParam ");
	}

    return $response;
});


require_once 'endpoints/hunts.php';
//require_once 'path_to_your_dir/admin_routes.php';
//require_once 'path_to_your_dir/some_other_routes.php';



$app->run();

?>




