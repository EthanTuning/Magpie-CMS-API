<?php

/* This interfaces with the database.  Its a go-between for the endpoints
 * to get stuff from the database.
 * 
 * It first checks the state of the object in the database.
 * Then it delegates the method to the State object.
 * 
 */
 

class Mapper
{
	protected $db;		// PDO object (already instantiated and stuff)
	protected $uid;		// user id extracted from Firebase token (represents current user)
	
	private $state;		// the state of the object the Mapper is operating on.
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
		try
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
		}
		catch (ResourceNotFoundException $e)
		{
			// At this point, there is no approval_status because the object doesn't exist in the database
			// thus the object is stateless
			if ($obj->isParent())
			{
				$this->state = new Stateless($this->db, $this->uid);
			}
			
		}
		
		if ($this->state == null) { throw new Exception('State not set');}
	}
		
	
	/* NO DUPLICATION OF SQL! */
	// TODO: make this work with other parent tables, possibly add a value to the getParentKey() to include a 'table' value
	private function getApprovalStatus($obj)
	{
		$parentid = $obj->getParentKey();
		$name = $parentid['name'];
		$value = $parentid['value'];
		
		$stmt = $this->db->prepare('SELECT approval_status FROM hunts WHERE '.$name.'=?');		//then shove table value in here
		$stmt->execute([$value]); 
		$approvalStatus = $stmt->fetchColumn();
		
		if ($approvalStatus == null)
		{
			throw new ResourceNotFoundException();
		}
		
		return $approvalStatus;
	}
	
	
}

?>
