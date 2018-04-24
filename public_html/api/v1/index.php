<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firebase\Auth\Token\Exception\InvalidToken;


require './classes/creds/creds.php';		// Configuration stuff
require './classes/authentication.php';		// User Authentication code
require './classes/HuntMapper.php';			// Endpoint <-> Database Interfacer Class
require './classes/BadgeMapper.php';		// ^ same

require './vendor/autoload.php';			// Composer stuff //


/* Load the stuff from 'creds/creds.php' into Slim */
$app = new \Slim\App(['settings' => $config]);


/* Add the Authentication Layer */
$app->add( new AuthenticationMiddleware() );


/* Get the Slim container array */
$container = $app->getContainer();


/* Add the PDO container */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


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


require_once './endpoints/hunts.php';
//require_once './endpoints/badges.php';
//require_once 'path_to_your_dir/admin_routes.php';
//require_once 'path_to_your_dir/some_other_routes.php';


$app->run();

?>




