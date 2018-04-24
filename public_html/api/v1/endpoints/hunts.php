<?php

/* Contains the endpoint functions for Hunts. */

/******  GET  ******/
$app->get('/hunts', function ($request, $response, $args) {
    // Show book identified by $args['id']
    
    $uid = $request->getAttribute('uid');
	$mapper = new HuntMapper($this->db, $uid);
    
    $response->getBody()->write($mapper->get("foo"));
    return $response;
});


/******  POST (Add) ******/

$app->post('/hunts', function ($request, $response, $args) {
    // Create new book
    
    $response->getBody()->write(" HUNTS POST ROUTE ");
    return $response;
});


/******  PUT (Update) ******/

$app->put('/hunts', function ($request, $response, $args) {
    // Update book identified by $args['id'] (not anymore...)
    
    $response->getBody()->write(" HUNTS PUT ROUTE ");
    return $response;
});


/******  DELTE  ******/

$app->delete('/hunts', function ($request, $response, $args) {
    // Delete book identified by $args['id']
    
    $response->getBody()->write(" HUNTS DELETE ROUTE ");
    return $response;
});


/******  OPTIONS  ******/

$app->options('/hunts', function ($request, $response, $args) {
    // Return response headers
    
    $response->getBody()->write(" HUNTS OPTIONS ROUTE ");
    return $response;
});




?>
