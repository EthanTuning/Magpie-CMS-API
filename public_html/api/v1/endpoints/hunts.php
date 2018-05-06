<?php

/* Contains the endpoint functions for Hunts. */




$app->post('/hunts', HuntController::class . ':add');
$app->get('/hunts/{hunt_id}', HuntController::class . ':getSingleHunt');
$app->put('/hunts/{hunt_id}', HuntController::class . ':update');
$app->delete('/hunts/{hunt_id}', HuntController::class . ':delete');
$app->patch('/hunts/{hunt_id}', HuntController::class . ':submit');





class HuntController
{
	protected $container;

	// constructor receives container instance
	public function __construct(Interop\Container\ContainerInterface $container)
	{
		$this->container = $container;
	}

	
	/************************************
	 *				GET
	 ***********************************/
	
	public function getSingleHunt($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container->db, $uid);
		
		/* Grab hunt id */
		$huntid = $args['hunt_id'];
		
		// make Hunt
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($huntid);
		
		try
		{
			/* Retreive the Hunt from the mapper */
			$result = $mapper->get($hunt);
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
		$mapper = new Mapper($this->container->db, $uid);
		
		$parameters = $request->getParsedBody();
		
		$hunt = new Hunt($parameters);
		
		try
		{
			$result = $mapper->add($hunt);
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
		$mapper = new Mapper($this->container->db, $uid);
		
		/* Grab hunt id from URL, shove it in assoc array w/rest of request */
		$parameters = $request->getParsedBody();
		
		$hunt = new Hunt($parameters);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		
		try
		{
			$result = $mapper->update($hunt);
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
	 *				PATCH (Submit)
	 ***********************************/

	// yea this isn't how patch is supposed to be used, oh well

	public function submit($request, $response, $args)
	{    
		$response->getBody()->write(" HUNTS PATCH ROUTE (used for submitting, not implemented yet)");
		return $response;
	}

	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args) {
		
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container->db, $uid);
		
		/* Make blank Hunt */
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		
		try
		{
			/* Use the Mapper to delete the hunt with that hunt_id */
			$temp = $mapper->delete($hunt);
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
