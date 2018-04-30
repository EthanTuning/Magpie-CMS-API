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
		//if hunt is approved, or if it's owned by current user, retrieve it from dbase
		if ($this->isOwnedByCurrentUser($huntid) || $this->isApproved($huntid) )
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
			throw new IllegalAccessException('Cannot get specified Hunt');
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
		if ($hunt == null)
		{
			throw new Exception('HuntMapper->add(): $hunt is null!');
		}
		
		$data = $hunt->getFields();
		
		// delete things from array that are unwanted before adding to database
		unset($data['hunt_id'], $data['approval_status']);
		
		//loop through and delete empty values in the array
		foreach ($data as $key => $value)
		{
			if (!isset($data[$key]))
			{
				unset($data[$key]);
			}
		}
		
		// build query...
		$sql  = "INSERT INTO hunts";

		// implode keys of $array...
		$sql .= " (`".implode("`, `", array_keys($data))."`)";

		// implode placeholders of $array...
		$sql .= " VALUES (:".implode(", :",  array_keys($data)).") ";
		
		$stmt= $this->db->prepare($sql);
		$stmt->execute($data);
		
		// check for success
		$result = $stmt->rowCount();
		
		if ($result < 1)
		{
			throw new Exception("HuntMapper - add() fail. ");
		}
	}
	
	
	/* Update - Update a Hunt with the following parameters */
	public function update(Hunt $hunt)
	{
		if ($hunt == null)
		{
			throw new Exception('HuntMapper->update(): $hunt is null!');
		}
		
		$data = $hunt->getFields();
		
		if (!isset($data['hunt_id']))
		{
			throw new Exception("HuntMapper: huntid not set");
		}
		
		$huntid = $data['hunt_id'];		//save this for later
		
		/* Check approval status and ownership */
		
		
		
		/* Database ops */
		
		// delete things from array that are unwanted before adding to database
		unset($data['hunt_id'], $data['approval_status']);
		
		//loop through and delete empty values in the array
		foreach ($data as $key => $value)
		{
			if (!isset($data[$key]))
			{
				unset($data[$key]);
			}
		}
		
		
		// build query...
		$sql  = "UPDATE hunts SET ";

		foreach($data as $key => $value)
		{
			$sql = $sql.$key."=:".$key.", ";
		}
		
		$sql = rtrim($sql, ', ');		// remove trailing comma
		
		$sql = $sql." WHERE hunt_id=:hunt_id";
		
		
		//$sql = 'UPDATE hunts SET abbreviation=:abbreviation, name=:name WHERE hunt_id=:hunt_id';
		//echo $sql;
		//return;
		
		$data['hunt_id'] = $huntid;
		
		$stmt= $this->db->prepare($sql);
		$stmt->execute($data);
		
		// check for success
		$result = $stmt->rowCount();
		
		if ($result < 1)
		{
			throw new Exception("HuntMapper - update() fail. ");
		}
	}
	
	
	/* Delete - Delete the hunt with the specified ID */
	public function delete($id)
	{
		
	}

}

?>
