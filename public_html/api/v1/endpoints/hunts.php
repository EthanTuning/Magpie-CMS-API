<?php

/* Contains the endpoint functions for Hunts. */

/************************************
 *				GET
 ***********************************/

$app->get('/hunts', function ($request, $response, $args) {
    // Show book identified by $args['id']
    
    /* Create the Mappers used */
    $uid = $request->getAttribute('uid');
	$huntMapper = new Mapper($this->db, $uid);
    
    /* Get parameters from request */
    $params = $request->getQueryParams();
    
    /* Create reference for response data to go in the response*/
    $data;
    
    /* These represent the different options on the API flow chart */
    if ( isset($params['id']) )
    {
		/* If an 'id' is present, return that specific Hunt w/Badges (Approved Only) */
		
		$huntobj = $huntMapper->get($params['id']);					// get the Hunt
		$badgearray = $badgeMapper->fromHunt($params['id']);			// get all the badges assoc with hunt
		$data = array($huntobj, $badgearray);
	}
	elseif ( isset($params['currentuser']) )
	{
		/* Get all the Hunts belonging to the current user (Approved and Unapproved) */
		
		
	}
    elseif ( /* array of params != null */ false )
    {
		/* Search for a Hunt (Approved Only) */
	}
	
    else
    {
		/* Get all Hunts (Approved Only) */
		$huntMapper->getall();
	}
    
    
    $response->getBody()->write(json_encode($data));
    
    return $response;
});


/************************************
 *				POST (Add)
 ***********************************/

$app->post('/hunts', function ($request, $response, $args) {
    // Create new book
    
    $response->getBody()->write(" HUNTS POST ROUTE ");
    return $response;
});


/************************************
 *				PUT (Update)
 ***********************************/

$app->put('/hunts', function ($request, $response, $args) {
    // Update book identified by $args['id'] (not anymore...)
    
    $response->getBody()->write(" HUNTS PUT ROUTE ");
    return $response;
});


/************************************
 *				PATCH (Submit)
 ***********************************/

// yea this isn't how patch is supposed to be used, oh well

$app->patch('/hunts', function ($request, $response, $args) {
    // Update book identified by $args['id'] (not anymore...)
    
    $response->getBody()->write(" HUNTS PATCH ROUTE (used for submitting)");
    return $response;
});

/************************************
 *				DELETE
 ***********************************/

$app->delete('/hunts', function ($request, $response, $args) {
    // Delete book identified by $args['id']
    
    $response->getBody()->write(" HUNTS DELETE ROUTE ");
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
