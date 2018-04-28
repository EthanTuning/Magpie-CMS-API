<?php

/* This interfaces with the database.  Its a go-between for the enpoints
 * to get stuff from the database.
 * 
 * This class ONLY returns objects belonging to the uid provided.
 * Approval status isn't taken into account when retrieving objects.
 * 
 * Update/Add won't work on approved Hunts
 * 
 */
 

class Mapper
{
	private $db;		// PDO object (already instantiated and stuff)
	private $uid;		// user id extracted from Firebase token
	
	
	/* Constructor */
	function __construct($db, $uid)
	{
		if ($uid == null || $db == null)
		{
			throw new Exception('Mapper CTOR: something is null!');
		}
		
		$this->db = $db;
		$this->uid = $uid;
	}
	
	/******************************************************
	 * Standard CRUD stuff - gotta use iCRUD interface in object
	 * ****************************************************/
	 
	/* Create */
	public function create(iCRUD $object)
	{
		echo "CREATE IN THE MAPPER".$object->getTableName();
	}
	
	
	/******************************************************
	 * 					HUNT Functions
	 * ****************************************************/
	
	/* Get a list of Hunts owned by the curent user */
	public function getOwned()
	{
		/* Return results */
		return "list goes here";
	}
	
	
	/* Get - Takes a string (Hunt ID), returns that hunt */
	public function getHunt($id)
	{
		if ($id == null)
		{
			throw new Exception('Mapper->get(): $id is null!');
		}
		
		
		
		/* Return results */
		return "ID".$id;
	}

	
	/* Add */
	public function addHunt(Hunt $hunt)
	{
		if ($hunt == null)
		{
			throw new Exception('Mapper->addHunt(): $params is null!');
		}
		
		echo "HUNT RECEIVED";
		
	}


	/* Update */
	public function updateHunt(Hunt $hunt)
	{
		
		
	}

	
	/* Delete - Delete the hunt with the specified ID */
	public function deleteHunt($id)
	{
		
	}





	/******************************************************
	 * 					Badge Functions
	 * ****************************************************/

	
	/* Get a list of Badges belonging to the hunt */
	public function getBadgesFromHunt($huntid)
	{
		/* Return results */
		return "list goes here";
	}
	
	
	/* Get - Takes a string (Badge ID), returns that badge */
	public function getBadge($id)
	{
		if ($id == null)
		{
			throw new Exception('Mapper->getBadge(): $id is null!');
		}
		
		
		
		/* Return results */
		return "ID".$id;
	}

	
	/* Add */
	public function addBadge(Badge $hunt)
	{
		if ($hunt == null)
		{
			throw new Exception('Mapper->addHunt(): $params is null!');
		}
		
		echo "HUNT RECEIVED";
	}


	/* Update */
	public function updateBadge(Badge $hunt)
	{
		
		
	}

	
	/* Delete - Delete the hunt with the specified ID */
	public function deleteBadge($id)
	{
		
	}




	/******************************************************
	 * 					Helper Functions
	 * ****************************************************/

	private function uidcheck()
	{
		//might not be needed, just hardcode "WHERE uid='uid'" into the SQL query
	}
	
	
	private function idClear()
	{
		
	}


}

?>
