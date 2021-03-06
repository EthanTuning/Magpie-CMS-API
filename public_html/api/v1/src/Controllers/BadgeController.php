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
		
		/* Retreive the Badge from the mapper */
		$result = $mapper->get($badge);
		$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		
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

		/* Retreive the Badge from the mapper */
		$result = $mapper->getAllChildren($badge);
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
		$imageController = new ImageController($this->container);	// make an ImageController to add images
		
		$parameters = $request->getParsedBody();		// get the text from the request
		
		$files = $request->getUploadedFiles();			// get array of uploaded files (if any)
		
		/* if the request contains images, process those images */
		if ( isset($files['icon']) )
		{
			// get file, send to /images controller
			$url = $imageController->addImage($files['icon']);
			
			// place URL in the $badge
			$parameters['icon'] = $url;
		}
		if ( isset($files['image']) )
		{
			// get file, send to /images controller
			$url = $imageController->addImage($files['image']);			
			
			// place URL in the $badge
			$parameters['image'] = $url;
		}
		
		$badge = new Badge($parameters);				// make a Badge to hold the values
		$badge->setParentKeyValue($args['hunt_id']);
		
		// If the URL contains a badge ID then we're updating an existing badge
		if (isset($args['badge_id']))
		{
			$badge->setPrimaryKeyValue($args['badge_id']);
			$result = $mapper->update($badge);
		}
		// Otherwise add it
		else
		{
			$result = $mapper->add($badge);
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
		
		/* Make blank Badge */
		$badge = new Badge(null);
		$badge->setPrimaryKeyValue($args['badge_id']);
		$badge->setParentKeyValue($args['hunt_id']);
		
		/* Use the Mapper to delete the object */
		$temp = $mapper->delete($badge);
		$response->getBody()->write(json_encode($temp));		//add jsonSerialze() to interface?
		
		return $response;
	}

	
}


?>
