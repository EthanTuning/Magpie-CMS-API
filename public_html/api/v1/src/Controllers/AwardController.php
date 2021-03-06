<?php

/* Contains the endpoint functions for Badges. */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\Mapper;

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Award;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class AwardController
{
	protected $container;

	// constructor receives container instance
	public function __construct(/*Interop\Container\ContainerInterface*/ $container)
	{
		$this->container = $container;
	}

	
	/************************************
	 *				GET
	 ***********************************/
	
	public function getSingleAward($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab hunt id */
		$huntid = $args['hunt_id'];
		
		// make Award
		$award = new Award(null);
		$award->setPrimaryKeyValue($args['award_id']);
		$award->setParentKeyValue($args['hunt_id']);
	
		/* Retreive the Award from the mapper */
		$result = $mapper->get($award);
		$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		
		return $response;
	}
	
	
	/* Get all Awards associated with that hunt */
	public function getAllAwards($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		// make Award
		$award = new Award(null);
		$award->setParentKeyValue($args['hunt_id']);
		
		/* Retreive the Award from the mapper */
		$result = $mapper->getAllChildren($award);
		$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		
		return $response;
	}
	

/************************************
 *				POST (Add or Update)
 ***********************************/

	public function addOrUpdate($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		$parameters = $request->getParsedBody();
		
		$award = new Award($parameters);
		$award->setParentKeyValue($args['hunt_id']);
		
		// If the URL contains an award ID then we're updating an existing award
		if (isset($args['award_id']))
		{
			$award->setPrimaryKeyValue($args['award_id']);
			$result = $mapper->update($award);
		}
		// Otherwise add it
		else
		{
			$result = $mapper->add($award);
		}
		
		$response->getBody()->write(json_encode($result));
		return $response;
	}


	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args) {
		
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Make blank Award */
		$award = new Award(null);
		$award->setPrimaryKeyValue($args['award_id']);
		$award->setParentKeyValue($args['hunt_id']);
		

		/* Use the Mapper to delete the award with that award_id */
		$temp = $mapper->delete($award);
		$response->getBody()->write(json_encode($temp));		//add jsonSerialze() to interface?
		
		return $response;
	}

	
}


?>
