<?php

/* This class will check the authenticated user against the 'creators' table
 * in the database.  If the user does not exist, this will add them in.
 * 
 * Note:  This requires the AuthenticationMiddleware class to load the 'email' into the
 * request.
 */


//TODO: Maybe make a User class and interface it with the Mapper?


class UserManager
{
	private $container;		// this is the container passed in (need that PDO connection brah)


    public function __construct($container)
    {
        $this->container = $container;
    }
	
	
	public function __invoke($request, $response, $next)
    {
		$array['uid'] = $request->getAttribute('uid');
		$array['email'] = $request->getAttribute('email');
		
		$db = $this->container->db;
		//$mapper = new Mapper($this->db, $uid);
		
		// For now just shove a user into the table unless it already exists.
		
		$sql = 'SELECT * FROM creators WHERE uid=?';
		$stmt = $db->prepare($sql);
		$stmt->execute(array($array['uid']));		//yes we have to make a 1D array from an associative array
		
		if ($stmt->fetch() == 0)
		{
			$sql = 'INSERT INTO creators (uid, email) VALUES (:uid, :email)';
			$stmt = $db->prepare($sql);
			$stmt->execute($array);	
		}	
		
        $response = $next($request, $response);	//next layer

        return $response;
    }




}




?>
