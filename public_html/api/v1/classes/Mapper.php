<?php

/* This interfaces with the database.  Its a go-between for the enpoints
 * to get stuff from the database.
 * 
 * 
 */
 

class Mapper
{
	protected $db;		// PDO object (already instantiated and stuff)
	protected $uid;		// user id extracted from Firebase token
	
	
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
	
	
	/******** Approval and Ownership checks *************/
	
	/* Is Approved check - returns True if the hunt is approved.
	 * 
	 * A return value of false doesn't mean it's "not-approved", it could be "submitted" as well. */
	public function isApproved($huntid)
	{
		//grab the record from the database
		
		$status = $this->getApprovalStatus($huntid);
		
		return ($status == 'approved');
	}
	
	
	/* Is Non-approved check - returns True if the hunt is 'non-approved'. */
	public function isNonApproved($huntid)
	{
		//grab the record from the database
		
		$status = getApprovalStatus($huntid);
		
		return ($status == 'non-approved');
	}
	
	
	/* NO DUPLICATION OF SQL! */
	private function getApprovalStatus($huntid)
	{
		$stmt = $this->db->prepare('SELECT approval_status FROM hunts WHERE hunt_id=?');
		$stmt->execute([$huntid]); 
		$approvalStatus = $stmt->fetchColumn();
		
		return $approvalStatus;
	}
	
	
	/* Is the specified hunt owned by the current owner? */
	public function isOwnedByCurrentUser($huntid)
	{
		$stmt = $this->db->prepare('SELECT uid FROM hunts WHERE hunt_id=?');
		$stmt->execute([$huntid]); 
		$uidFromTable = $stmt->fetchColumn();
		
		return ($this->uid == $uidFromTable) ;
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
