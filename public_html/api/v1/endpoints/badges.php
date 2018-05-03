<?php

/* Contains the endpoint functions for Hunts. */

/************************************
 *				GET
 ***********************************/

$app->get('/badges/{badge_id}', function ($request, $response, $args)
{
    /* Create the Mappers used */
    $uid = $request->getAttribute('uid');
	$mapper = new Mapper($this->db, $uid);
    
    /* Grab hunt id */
    $huntid = $args['hunt_id'];
    
    // make Hunt
    
    $temp = new Badge(array('hunt_id' => $huntid));
    
    
	try
	{
		/* Retreive the Hunt from the mapper */
		$hunt = $mapper->get($temp);
		$response->getBody()->write(json_encode($hunt->jsonSerialize()));		//add jsonSerialze() to interface?
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
});


/************************************
 *				POST (Add)
 ***********************************/

$app->post('/hunts', function ($request, $response, $args) {
    
    /* Create the Mappers used */
    $uid = $request->getAttribute('uid');
	$mapper = new Mapper($this->db, $uid);
	
	$hunt = new Hunt($request->getParsedBody());
	
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
});


/************************************
 *				PUT (Update)
 ***********************************/

$app->put('/hunts/{hunt_id}', function ($request, $response, $args)
{
    /* Create the Mappers used */
    $uid = $request->getAttribute('uid');
	$mapper = new Mapper($this->db, $uid);
    
    /* Grab hunt id from URL, shove it in assoc array w/rest of request */
    $huntid = $args['hunt_id'];
    $parameters = $request->getParsedBody()
    $parameters['hunt_id'] = $huntid;
    
    $hunt = new Hunt($parameters);
	
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
});


/************************************
 *				PATCH (Submit)
 ***********************************/

// yea this isn't how patch is supposed to be used, oh well

$app->patch('/hunts/{hunt_id}', function ($request, $response, $args) {
    // Update book identified by $args['id'] (not anymore...)
    
    $response->getBody()->write(" HUNTS PATCH ROUTE (used for submitting, not implemented yet)");
    return $response;
});

/************************************
 *				DELETE
 ***********************************/

$app->delete('/hunts/{hunt_id}', function ($request, $response, $args) {
    
    /* Create the Mappers used */
    $uid = $request->getAttribute('uid');
	$mapper = new Mapper($this->db, $uid);
    
    /* Grab hunt id */
    $huntid = $args['hunt_id'];
    
    // make Hunt
    
    $temp = new Hunt(array('hunt_id' => $huntid));
    
	try
	{
		/* Use the Mapper to delete the hunt with that hunt_id */
		$hunt = $mapper->delete($temp);
		$response->getBody()->write(json_encode($hunt->jsonSerialize()));		//add jsonSerialze() to interface?
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
    
});


/************************************
 *				OPTIONS
 ***********************************/

$app->options('/hunts', function ($request, $response, $args) {
    // Return response headers
    
    $response->getBody()->write(" HUNTS OPTIONS ROUTE ");
    return $response;
});


?>
