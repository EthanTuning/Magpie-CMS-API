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
 

class BadgeMapper extends Mapper
{
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
