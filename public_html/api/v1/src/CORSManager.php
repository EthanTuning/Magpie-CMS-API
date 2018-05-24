<?php

/* 
 *  CORS is stupid.
 */


namespace MagpieAPI;

class CORSManager
{
	
	public function __invoke($request, $response, $next)
    {
		// if the request is an OPTIONS, die.  Read up on CORS to find out how it kills API functionality.
		if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
		{
			//header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Headers: *');
			header("HTTP/1.1 200 OK");
			die();
		}
		
        $response = $next($request, $response);	//next layer

        return $response;
    }




}




?>
