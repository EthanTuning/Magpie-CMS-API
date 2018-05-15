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
 * Or it does some direct SQL via PDO.
 * 
 * */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\StateBypass;		// We're using this as a bypass to the state-checking

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class AdminController
{
	protected $container;

	// constructor receives container instance
	public function __construct($container)
	{
		$this->container = $container;
	}	
	
	
	/************************************
	 *				GET (Single Hunt identified by ID)
	 ***********************************/
	
	public function getSingleHunt($request, $response, $args)
	{
		// Bypassing functionality of the mapper by making a State object directly
		$mapperBypass = new StateBypass($this->container->db, $request->getAttribute('uid'));
		
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);
		
		$result = $mapperBypass->get($hunt);
		
		$response->getBody()->write(json_encode($result));
		return $response;
	}
	
	/************************************
	 *				GET
	 * 
	 * GET A LIST OF NON-APPROVED HUNTS FOR REVIEW
	 ***********************************/

	public function getNonApprovedList($request, $response, $args)
	{
		//TODO: add in query functionality
		
		//direct SQL query
		$sql = 'SELECT * from `hunts` WHERE `approval_status` = "submitted"';
		$stmt = $this->container->db->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll();
		
		$response->getBody()->write(json_encode($result));
		
		return $response;
	}


	/************************************
	 *				PUT (Update)
	 * 
	 * CHANGE THE STATUS OF A HUNT (APPROVE OR DISAPPROVE)
	 * 
	 * The hunt should be in the "submitted" state, but this doesn't check that.
	 * If you're an admin you can do whatever you want.
	 * (See Hunt state diagram)
	 ***********************************/

	public function changeStatus($request, $response, $args)
	{
		// direct SQL query
		$huntid = $args['hunt_id'];			// get hunt ID from URI
		
		$parameters = $request->getParsedBody();
		
		$status = $parameters['approval_status'];
		
		if ($status == 'approved' || $status == 'non-approved')
		{
			$sql = 'UPDATE `hunts` SET `approval_status` =? WHERE `hunt_id`=?';
			$stmt = $this->container->db->prepare($sql);
			$stmt->execute([$status, $huntid]);
			$result = $stmt->rowCount();
		
			if ($result < 1)
			{
				$result = "Nothing changed.";
			}
			
			$response->getBody()->write(json_encode($result));
		
			return $response;
		}

		throw new \InvalidArgumentException("'approval_status' not found.");
	}

	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args)
	{
		// Bypassing functionality of the mapper by making a State object directly
		$mapperBypass = new StateBypass($this->container->db, $request->getAttribute('uid'));
		
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);
		
		$result = $mapperBypass->delete($hunt);
		
		$response->getBody()->write(json_encode($result));
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
