<?php

/* This is a "factory-like" class.  It interfaces with the database.
 * 
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
	
	
	/* Get - Takes an ID, returns an array (values of that Hunt from the dbase) */
	public function get($id)
	{
		//print this->uid();
		return "GET RESULTS FROM ".$this->uid." BRAH";
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
