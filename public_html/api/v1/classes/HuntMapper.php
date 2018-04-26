<?php

/* This interfaces with the database.  Its a go-between for the enpoints
 * to get stuff from the database.
 * 
 * This class is responsible for doing Approval and Security checks.
 * 
 * 	Approval - make sure Hunt is approved before returning it
 * 	Security - make sure UID provided matches UID of resource
 * 
 *  The only instances where non-approved resources can be returned is if
 *  the UID of the resource matches the UID of the requestor.
 */
 

class HuntMapper
{
	private $db;		// PDO object (already instantiated and stuff)
	private $uid;		// user id extracted from Firebase token
	
	
	/* Constructor */
	function __construct($db, $uid)
	{
		$this->db = $db;
		$this->uid = $uid;
	}
	
	
	/* Get - Takes a string (Hunt ID), returns that hunt */
	public function get($id)
	{
		if ($id == null)
		{
			throw new Exception('HuntMapper->get(): $id is null!');
		}
		
		
		/* Return results */
		return "ID".$id;
	}

	
	/* Search - Takes an array of parameters, returns an array (values of that Hunt from the dbase) */
	public function search($params)
	{
		if ($params == null)
		{
			throw new Exception('HuntMapper->search(): $params is null!');
		}
		
		
		
	}

	/* Getall - Retreives a list of all approved Hunts */
	public function getall()
	{
		
		
	}

	
	/* Add - Add a Hunt with the passed parameters */
	public function add($array)
	{
		// return a boolean?
	}
	
	
	/* Update - Update a Hunt with the following parameters */
	public function update($array)
	{
		
	}
	
	
	/* Delete - Delete the hunt with the specified ID */
	public function delete($id)
	{
		
	}

}

?>
