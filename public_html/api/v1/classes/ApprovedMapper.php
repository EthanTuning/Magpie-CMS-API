<?php

/* This interfaces with the database.  Its a go-between for the enpoints
 * to get stuff from the database.
 * 
 * This class ONLY returns objects that are APPROVED.
 * 
 * Also it just works on Hunts.  Returned a single hunt, specified by ID,
 * should return all the badges as well.
 */
 

class ApprovedMapper
{
	private $db;		// PDO object (already instantiated and stuff)
	//private $uid;		// user id extracted from Firebase token
	
	
	/* Constructor */
	function __construct($db)//, $uid)
	{
		if ($db == null)
		{
			throw new Exception('ApprovedMapper CTOR: something is null!');
		}
		
		$this->db = $db;
		//$this->uid = $uid;
	}
	
	
	/******************************************************
	 * 					HUNT Functions
	 * ****************************************************/
	
	
	/* Get All - Returns a list of all approved Hunts */
	public function getAll()
	{
		
	}
	
	
	/* Get - Takes a string (Hunt ID), returns that Hunt + Badges*/
	public function getHunt($id)
	{
		if ($id == null)
		{
			throw new Exception('ApprovedMapper->getHunt(): $id is null!');
		}
		
		/* Return results */
		return "ID".$id;
	}


	/* Query - Takes an associative array (k, v) returns a list of hunts that match */
	public function query($params)
	{
		if ($params == null)
		{
			throw new Exception('AddprovedMapper->query(): params is null!');
		}
		
	}



	/******************************************************
	 * 					Helper Functions
	 * ****************************************************/

	//??


}

?>
