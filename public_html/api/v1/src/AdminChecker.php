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
		$adminStatus = $this->isAdmin($request);
		
		// if user is not an Admin, throw an exception
		if (!$adminStatus)
		{
			throw new IllegalAccessException("You must be an administrator to access this endpoint.");
		}

		$response = $next($request, $response);	//next layer
        return $response;
    }


	/* Is the current user and Admin?
	 * 
	 * This is a convenience method for a client to call to determine if they should show
	 * the admin pages or not.  This should not be used for internal logic.
	 * 
	 * Takes a request object from Slim. Returns a boolean.
	 */
	public function isAdmin($request)
	{
		$uid = $request->getAttribute('uid');
		
		$db = $this->container->db;
		
		$sql = 'SELECT `uid` FROM `administrators` WHERE uid=?';
		$stmt = $db->prepare($sql);
		$stmt->execute([$uid]);		//yes we have to make a 1D array from an associative array
		
		if ( null == $stmt->fetchColumn() )		//if the result column is empty, they're not an admin
		{
			return false;
		}	
		
		return true;		
	}

}




?>
