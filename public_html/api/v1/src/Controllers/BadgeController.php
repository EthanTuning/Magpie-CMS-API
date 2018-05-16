<?php

/* Contains the endpoint functions for Badges. */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\Mapper;

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class BadgeController
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
	
	public function getSingleBadge($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab hunt id */
		$huntid = $args['hunt_id'];
		
		// make Badge
		$badge = new Badge(null);
		$badge->setPrimaryKeyValue($args['badge_id']);
		$badge->setParentKeyValue($args['hunt_id']);
		
		try
		{
			/* Retreive the Badge from the mapper */
			$result = $mapper->get($badge);
			$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		catch (ResourceNotFoundException $e)
		{
			$response = $response->withStatus(404);
		}
		
		return $response;
	}
	
	
	/* Get all Badges associated with that hunt */
	public function getAllBadges($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		// make Badge
		$badge = new Badge(null);
		$badge->setParentKeyValue($args['hunt_id']);
		
		try
		{
			/* Retreive the Badge from the mapper */
			$result = $mapper->getAllChildren($badge);
			$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		catch (ResourceNotFoundException $e)
		{
			$response = $response->withStatus(404);
		}
		
		return $response;
	}
	

/************************************
 *				POST (Add)
 ***********************************/

	public function add($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		$parameters = $request->getParsedBody();
		
		$badge = new Badge($parameters);
		$badge->setParentKeyValue($args['hunt_id']);
		
		try
		{
			$result = $mapper->add($badge);
			$response->getBody()->write(json_encode($result));
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		
		return $response;
	}


	/************************************
	 *				PUT (Update)
	 ***********************************/

	public function update($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab hunt id from URL, shove it in assoc array w/rest of request */
		$parameters = $request->getParsedBody();
		
		$badge = new Badge($parameters);
		$badge->setPrimaryKeyValue($args['badge_id']);
		$badge->setParentKeyValue($args['hunt_id']);
		
		try
		{
			$result = $mapper->update($badge);
			$response->getBody()->write(json_encode($result));
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		catch (ResourceNotFoundException $e)
		{
			$response = $response->withStatus(404);
		}
		
		return $response;
	}


	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args) {
		
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Make blank Badge */
		$badge = new Badge(null);
		$badge->setPrimaryKeyValue($args['badge_id']);
		$badge->setParentKeyValue($args['hunt_id']);
		
		try
		{
			/* Use the Mapper to delete the hunt with that hunt_id */
			$temp = $mapper->delete($badge);
			$response->getBody()->write(json_encode($temp));		//add jsonSerialze() to interface?
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		catch (ResourceNotFoundException $e)
		{
			$response = $response->withStatus(404);
		}
		
		return $response;
		
	}


	/************************************
	 *				OPTIONS
	 ***********************************/

	public function options($request, $response, $args)
	{
		// Return response headers
		
		$response->getBody()->write(" HUNTS OPTIONS ROUTE ");
		return $response;
	}
		
		

	
}


?>
