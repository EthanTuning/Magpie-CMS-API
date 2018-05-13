<?php


/* Composer Stuff */
require './vendor/autoload.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//use \Interop\Container\ContainerInterface as ContainerInterface;	//https://stackoverflow.com/questions/37906363/slim-controller-issue-must-be-an-instance-of-containerinterface-instance-of-s


use Firebase\Auth\Token\Exception\InvalidToken;

use MagpieAPI\UserManager;					// Checks if user is in database
use MagpieAPI\AuthenticationMiddleware;		// Firebase Token stuff
use MagpieAPI\AdminChecker;					// local admin checker

use MagpieAPI\Controllers\HuntController;
use MagpieAPI\Controllers\BadgeController;
use MagpieAPI\Controllers\AdminController;

require_once './src/Creds/creds.php';					// Configuration stuff

/*************************************
 *				SLIM
 *************************************/

/* Load the stuff from 'creds/creds.php' into Slim */
$app = new \Slim\App(['settings' => $config]);

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

/****************************************
 * 				URI Endpoints
 * ***********************************/


/* Hunts */
$app->post('/hunts', HuntController::class . ':add');
$app->get('/hunts/{hunt_id}', HuntController::class . ':getSingleHunt');
$app->get('/hunts', HuntController::class . ':search');
$app->put('/hunts/{hunt_id}', HuntController::class . ':update');
$app->delete('/hunts/{hunt_id}', HuntController::class . ':delete');
$app->patch('/hunts/{hunt_id}', HuntController::class . ':submit');

/* Badges */

$app->post('/hunts/{hunt_id}/badges', BadgeController::class . ':add');
$app->get('/hunts/{hunt_id}/badges/{badge_id}', BadgeController::class . ':getSingleBadge');
$app->get('/hunts/{hunt_id}/badges', BadgeController::class . ':getAllBadges');
$app->put('/hunts/{hunt_id}/badges/{badge_id}', BadgeController::class . ':update');
$app->delete('/hunts/{hunt_id}/badges/{badge_id}', BadgeController::class . ':delete');

/* Admin */
$app->group('/admin', function () {
	$this->get('/{hunt_id}', AdminController::class . ':getSingleHunt');
    $this->get('', AdminController::class . ':getNonApprovedList');
    $this->put('/{hunt_id}', AdminController::class . ':changeStatus');
    $this->delete('/{hunt_id}', AdminController::class . ':delete');
    $this->options('', AdminController::class . ':options');
})->add( new AdminChecker($container));


/* Images */
// sweet jesus I have no idea how authentication for images will work.


/* Add the additional Middleware Layers (! The last ond loaded runs first !) */

$app->add( new UserManager($container) );			// Checks if the current user is entered in the creators table
$app->add( new AuthenticationMiddleware() );		// Authentication with google and tokens and stuff


/* Start the Slim instance */
$app->run();



/* Route used for testing 
$app->get('/test', function (Request $request, Response $response, array $args) {
    
    //GET
	$allGetVars = $request->getQueryParams();
	
	/*foreach($allGetVars as $key => $param){
	   //GET parameters list
	}
    
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

*/

?>
