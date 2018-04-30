<?php

/* This interfaces with the database.  Its a go-between for the endpoints
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
 

class HuntMapper extends Mapper
{
	/* Get - Takes a string (Hunt ID), returns that hunt */
	public function get($huntid)
	{
		if ($huntid == null)
		{
			throw new Exception('HuntMapper->get(): $huntid is null!');
		}
		
		if ($this->isOwnedByCurrentUser($huntid))
		{
			// insert PDO code
			$stmt = $this->db->prepare('SELECT * FROM hunts WHERE hunt_id=?');
			$stmt->execute([$huntid]); 
			$result = $stmt->fetch();
			$newHunt = new Hunt($result);
			return $newHunt;
		}
		else
		{
			//set 403 - forbidden
			return "403 FORBIDDEN";
		}

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
	public function add(Hunt $hunt)
	{
		
		
		
		
		
		echo "ADDED HUNT";
		// return a boolean?
	}
	
	
	/* Update - Update a Hunt with the following parameters */
	public function update(Hunt $hunt)
	{
		
	}
	
	
	/* Delete - Delete the hunt with the specified ID */
	public function delete($id)
	{
		
	}

}

?>
