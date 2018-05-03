<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firebase\Auth\Token\Exception\InvalidToken;


require './classes/creds/creds.php';					// Configuration stuff
require './classes/AuthenticationMiddleware.php';		// User Authentication code

/* Interfaces and Basic classes */
require './classes/CustomExceptions.php';
require './classes/Interfaces.php';
require './classes/Hunt.php';
//require './classes/Badge.php';

/* Mapper classes (Endpoint <-> Database Interfacers) */
require './classes/State.php';				// 
require './classes/Mapper.php';				// Mapper holds a State

/* Composer Stuff */
require './vendor/autoload.php';


/*************************************
 *				SLIM
 *************************************/

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
    
    //$getParam = $allGetVars['name'];
    
    $hunt = new Hunt(array('name'=>"BobTheHunter", 'hunt_id'=>1));
	$uid = $request->getAttribute('uid');
	$huntMapper = new HuntMapper($this->db, $uid);
	try
	{
		$resultingHunt = $huntMapper->get(666);
		$response->getBody()->write(json_encode($resultingHunt->jsonSerialize()));
	}
	catch (IllegalAccessException $e)
	{
		$response = $response->withStatus(403);
	}

	

    return $response;
});


/****************************************
 * 				URI Endpoints
 * ***********************************/

//require_once './endpoints/badges.php';
require_once './endpoints/hunts.php';

//require_once 'path_to_your_dir/admin_routes.php';
//require_once 'path_to_your_dir/some_other_routes.php';


$app->run();

?>




