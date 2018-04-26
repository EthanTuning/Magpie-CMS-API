<?php

class BadgeMapper
{
	private $db;		// PDO object (already instantiated and stuff)
	private $uid;		// user id extracted from Firebase token
	
	
	/* Constructor */
	function __construct($db, $uid)
	{
		$this->db = $db;
		$this->uid = $uid;
	}
	
	
	/* Get - Takes a string (Badge ID), returns that Badge */
	public function get($id)
	{
		if ($id == null)
		{
			throw new Exception('BadgeMapper->get(): $id is null!');
		}
		
		
		/* Return results */
		return "YAY";
	}

	
	/* Search - Takes an array of parameters, returns an array (values of that Hunt from the dbase) 
	 * Not Implemented - can't search for badges.
	public function search($params)
	{
		if ($params == null)
		{
			throw new Exception('BadgeMapper->search(): $params is null!');
		}
	}
	* */

	/* Getall - Retreives a list of all badges belonging to a specified Hunt */
	public function fromHunt($huntID)
	{
		
		
	}

	
	/* Add - Add a Badge with the passed parameters */
	public function add($array)
	{
		// return a boolean?
	}
	
	
	/* Update - Update a Badge with the following parameters */
	public function update($array)
	{
		
	}
	
	
	/* Delete - Delete the Badge with the specified ID */
	public function delete($id)
	{
		
	}


}

?>
