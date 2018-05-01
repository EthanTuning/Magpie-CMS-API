<?php

/* This interfaces with the database.  Its a go-between for the enpoints
 * to get stuff from the database.
 * 
 * It first checks the state of the object in the database.
 * Then it delegates the method to the State object.
 * 
 */
 

class Mapper
{
	protected $db;		// PDO object (already instantiated and stuff)
	protected $uid;		// user id extracted from Firebase token
	
	private $state;	// the state of the object the Mapper is operating on.
							// this will change on setState()
	
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
	
	
	/***********************************
	 * 		Public Access Functions
	 * *********************************/
	
	// Get - Returns an IMapperable object
	public function get(IMapperable $obj)
	{
		$this->setState($obj);
		return $this->state->get($obj);
	}		
	
	
	public function update(IMapperable $obj)
	{
		$this->setState($obj);
		return $this->state->update($obj);
	}	
	
	
	public function delete(IMapperable $obj)
	{
		$this->setState($obj);
		return $this->state->delete($obj);
	}	
	
	
	public function add(IMapperable $obj)
	{
		$this->setState($obj);
		return $this->state->add($obj);
	}
	

	
	/******** Approval and Ownership checks *************/
	
	private function setState(IMapperable $obj)
	{
		$status = $this->getApprovalStatus($obj);
		
		switch ($status)
		{
			case 'approved':
				$this->state = new StateApproved($this->db, $this->uid);
				break;
			case 'submitted':
				$this->state = new StateSubmitted($this->db, $this->uid);
				break;
			case 'non-approved':
				$this->state = new StateNonApproved($this->db, $this->uid);
				break;
		}
		
		if ($this->state == null)
		{
			throw new Exception("STATE NOT SET");
		}
		
	}
		
	
	/* NO DUPLICATION OF SQL! */
	private function getApprovalStatus($obj)
	{
		$huntid = $obj->getParentId();
		
		$stmt = $this->db->prepare('SELECT approval_status FROM hunts WHERE hunt_id=?');
		$stmt->execute([$huntid]); 
		$approvalStatus = $stmt->fetchColumn();
		
		if ($approvalStatus == null)
		{
			throw new ResourceNotFoundException();
		}
		
		return $approvalStatus;
	}
	
	
	/* Is the specified hunt owned by the current owner? */
	public function isOwnedByCurrentUser(IMapperable $obj)
	{
		$huntid = $obj->getParentId();
		
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
