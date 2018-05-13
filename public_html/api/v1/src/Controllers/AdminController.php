<?php

/* Contains the endpoint functions for Administrative Tasks. 
 * 
 * The AdminController is very monolithic.  
 * 
 * This class DOES NOT check if the user is an administrator - the
 * AdminChecker class needs to be run before this.  With Slim that means loading
 * the AdminChecker class as middleware on the '/admin' route group (done!).
 * 
 * This does not use a Mapper, it makes a StateApproved object and uses that directly.
 * 
 * */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\StateApproved;		// We're using this as a bypass to the state-checking

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class AdminController
{
	protected $container;

	// constructor receives container instance
	public function __construct(/*Interop\Container\ContainerInterface*/ $container)
	{
		$this->container = $container;
	}	
	
	
	/************************************
	 *				GET (Single Hunt identified by ID)
	 ***********************************/
	
	public function getSingleHunt($request, $response, $args)
	{
		// Bypassing functionality of the mapper by making a State object directly
		$mapperBypass = new StateApproved($this->container->db, $request->getAttribute('uid'));
		
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);
		
		$result = $mapperBypass->get($hunt);
		
		$response->getBody()->write(json_encode($result));
		return $response;
	}
	
	/************************************
	 *				GET (Search With Parameters)
	 ***********************************/

	public function getNonApprovedList($request, $response, $args)
	{
		$response->write("GET A LIST OF NON-APPROVED HUNTS FOR REVIEW");
		
		return $response;
	}


	/************************************
	 *				PUT (Update)
	 ***********************************/

	public function changeStatus($request, $response, $args)
	{
		$response->write("CHANGE THE STATUS OF A HUNT (YEA OR NAY)");
		
		return $response;
	}

	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args)
	{
		$response->write("DELETE A HUNT");
		
		return $response;
		
	}


	/************************************
	 *				OPTIONS
	 ***********************************/

	public function options($request, $response, $args)
	{
		// Return response headers
		
		$response->getBody()->write("ADMIN OPTIONS ROUTE ");
		return $response;
	}
		
		
	
	
}


?>
