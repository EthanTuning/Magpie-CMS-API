<?php

/* This class will check the authenticated user against the 'administrators' table
 * in the database.  If the user is not in the table, the request errors out.
 * 
 * Note:  This requires the AuthenticationMiddleware class to load the 'uid' into the
 * request.
 */


//TODO: Maybe make a User class and interface it with the Mapper?
namespace MagpieAPI;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class AdminChecker
{
	private $container;		// this is the container passed in (need that PDO connection brah)


    public function __construct($container)
    {
        $this->container = $container;
    }
	
	
	public function __invoke($request, $response, $next)
    {
		$uid = $request->getAttribute('uid');
		
		$db = $this->container->db;
		//$mapper = new Mapper($this->db, $uid);
		
		// For now just shove a user into the table unless it already exists.
		
		$sql = 'SELECT `uid` FROM `administrators` WHERE uid=?';
		$stmt = $db->prepare($sql);
		$stmt->execute([$uid]);		//yes we have to make a 1D array from an associative array
		
		if ( null == $stmt->fetchColumn() )		//if the user ID is not in the database, throw exception
		{
			throw new IllegalAccessException("You must be an administrator to access this endpoint.");
		}	
		
        $response = $next($request, $response);	//next layer

        return $response;
    }




}




?>
