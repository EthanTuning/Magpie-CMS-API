<?php

/* Contains the endpoint functions for Hunts. */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\Mapper;

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;
use MagpieAPI\Models\HuntFactory;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class HuntController
{
	protected $container;

	// constructor receives container instance
	public function __construct(/*Interop\Container\ContainerInterface*/ $container)
	{
		$this->container = $container;
	}


	/* Prepare the results array from the database for the user 
	 * 
	 * This adds in links to the sub-resources.
	 * This could also be used to remove unwanted fields from the results, like 'uid'.
	 * 
	 * */
	private function prepareResponse($response, $result)
	{
		$huntid = $result['hunt_id'];
		$result['badges'] = $this->container['base_url']."/hunts/".$huntid."/badges";
		$result['awards'] = $this->container['base_url']."/hunts/".$huntid."/awards";
		
		$response->getBody()->write(json_encode($result));		//add jsonSerialze() to interface?
		
		return $response;
	}
	
	
	/************************************
	 *				GET (Single Hunt identified by ID)
	 ***********************************/
	
	public function getSingleHunt($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab hunt id */
		$huntid = $args['hunt_id'];
		
		// make Hunt
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($huntid);
		
		$result = $mapper->get($hunt);
		
		$response->getBody()->write(json_encode($result));
			
		return $response;
	}
	
	/************************************
	 *				GET (Search With Parameters)
	 ***********************************/

	public function search($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab the query parameters */
		$params = $request->getQueryParams();
		
		/* If there are no parameters, just get Hunts belonging to the user */
		if ($params == null)
		{
			$hunt = new Hunt(null);
			$result = $mapper->getAll($hunt);
		}
		else
		{
			// make Hunt with search parameters
			$hunt = new Hunt($params);
			$result = $mapper->search($hunt);
		}
		$response->getBody()->write(json_encode($result));
		
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
		$imageController = new ImageController($this->container);	// make an ImageController to add images

		$parameters = $request->getParsedBody();
		
		$files = $request->getUploadedFiles();			// get array of uploaded files (if any)
		
		/* if the request contains images, process those images */
		if ( isset($files['super_badge']) )
		{
			// get file, send to /images controller
			$image = $files['super_badge'];
			$url = $imageController->addImage($image);
			
			// place URL in the $badge
			$parameters['super_badge'] = $url;
		}
		
		$hunt = new Hunt($parameters);
		$result = $mapper->add($hunt);
		$response->getBody()->write(json_encode($result));
		
		return $response;
	}


	/************************************
	 *				POST (Update)
	 ***********************************/

	public function update($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		$imageController = new ImageController($this->container);	// make an ImageController to add images
		
		/* Grab hunt id from URL, shove it in assoc array w/rest of request */
		$parameters = $request->getParsedBody();
		
		$files = $request->getUploadedFiles();			// get array of uploaded files (if any)
		
		/* if the request contains images, process those images */
		if ( isset($files['super_badge']) )
		{
			// get file, send to /images controller
			$image = $files['super_badge'];
			$url = $imageController->addImage($image);
			
			// place URL in the $badge
			$parameters['super_badge'] = $url;
		}
		
		$hunt = new Hunt($parameters);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		$result = $mapper->update($hunt);
		$response->getBody()->write(json_encode($result));

		return $response;
	}


	/************************************
	 *				PATCH (Submit)
	 ***********************************/

	// yea this isn't how patch is supposed to be used, oh well

	public function submit($request, $response, $args)
	{   
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container, $uid);
		
		/* Grab hunt id from URL, shove it in assoc array w/rest of request */
		$parameters = $request->getParsedBody();
		
		$hunt = new Hunt($parameters);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		
		$result = $mapper->submit($hunt);
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
		
		/* Make blank Hunt */
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		
		/* Use the Mapper to delete the hunt with that hunt_id */
		$temp = $mapper->delete($hunt);
		$response->getBody()->write(json_encode($temp));		//add jsonSerialze() to interface?
		
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
