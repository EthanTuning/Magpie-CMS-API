<?php

/* This is the starting point for the Magpie API.
 * 
 * User documentation for using the API is in the /documentation folder on the github. (will be moved to /public_html when finished)
 * Developer documentation is in the /documentation folder.
 */

/* Composer Stuff */
require './vendor/autoload.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//use \Interop\Container\ContainerInterface as ContainerInterface;	//https://stackoverflow.com/questions/37906363/slim-controller-issue-must-be-an-instance-of-containerinterface-instance-of-s


use Firebase\Auth\Token\Exception\InvalidToken;

use MagpieAPI\UserManager;					// Checks if user is in database
use MagpieAPI\AuthenticationMiddleware;		// Firebase Token stuff
use MagpieAPI\AdminChecker;					// local admin checker

use MagpieAPI\Exceptions\CustomHandler;		// Exception handler

use MagpieAPI\Controllers\HuntController;
use MagpieAPI\Controllers\BadgeController;
use MagpieAPI\Controllers\AdminController;
use MagpieAPI\Controllers\ImageController;
use MagpieAPI\Controllers\AwardController;

require_once './src/Creds/creds.php';					// Configuration stuff


/*************************************
 *				SLIM CONTAINER INITIALIZATION
 *************************************/

/* Load the stuff from 'creds/creds.php' into Slim */
$app = new \Slim\App(['settings' => $config]);

/* Get the Slim container array */
$container = $app->getContainer();

$container['upload_directory'] = __DIR__ . '/uploads';									// Add upload directory for image files
$container['base_url'] = 'http://localhost/magpie/magpie-php/public_html/api/v1';		// there's probably a dynamic way to do this.

/* Add the PDO container */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

/* Add Error Handler (of class CustomHandler) */
$container['errorHandler'] = function ($container) {
		return new CustomHandler();
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

/* Awards */
$app->post('/hunts/{hunt_id}/awards', AwardController::class . ':add');
$app->get('/hunts/{hunt_id}/awards/{award_id}', AwardController::class . ':getSingleAward');
$app->get('/hunts/{hunt_id}/awards', AwardController::class . ':getAllAwards');
$app->put('/hunts/{hunt_id}/awards/{award_id}', AwardController::class . ':update');
$app->delete('/hunts/{hunt_id}/awards/{award_id}', AwardController::class . ':delete');


/* Images */
$app->post('/images', ImageController::class . ':add');
//get() handled by apache (this could be added later to avoid using magpie as a public image host)
$app->delete('/images/{image_id}', ImageController::class . ':delete');		// not really needed, not implemented yet


/* Admin */
$app->group('/admin', function () {
	$this->get('/{hunt_id}', AdminController::class . ':getSingleHunt');
    $this->get('', AdminController::class . ':getNonApprovedList');
    $this->put('/{hunt_id}', AdminController::class . ':changeStatus');
    $this->delete('/{hunt_id}', AdminController::class . ':delete');
    $this->options('', AdminController::class . ':options');
})->add( new AdminChecker($container));		// grouping these endpoints allows us to use middleware on the entire group


/* Add the additional Middleware Layers (! The last ond loaded runs first !) */

$app->add( new UserManager($container) );			// Checks if the current user is entered in the creators table
$app->add( new AuthenticationMiddleware() );		// Authentication with google and tokens and stuff


/* Start the Slim instance */
$app->run();


?>
