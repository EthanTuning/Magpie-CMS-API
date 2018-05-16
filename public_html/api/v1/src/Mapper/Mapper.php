<?php

/* This interfaces with the database.  Its a go-between for the endpoints
 * to get stuff from the database.
 * 
 * It first checks the state of the object in the database.
 * Then it delegates the method called (get, add, whatever) to the State object.
 * 
 * It takes an IMapperable object and returns an array.
 */

namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

class Mapper
{
	protected $db;		// PDO object (already instantiated and stuff)
	protected $uid;		// user id extracted from Firebase token (represents current user)
	protected $baseURL;		//can be found from the Slim container
	
	private $container;		//Slim container
	private $state;		// the state of the object the Mapper is operating on.
							// this will change on setState()
	
	/* Constructor */
	function __construct($container, $uid)
	{
		if ($container == null || $uid == null)
		{
			throw new Exception('Mapper CTOR: something is null!');
		}
		
		$this->container = $container;
		$this->db = $container->db;
		$this->uid = $uid;
		$this->baseURL = $container['base_url'];
	}
	
	
	/***********************************
	 * 		Public Access Functions
	 * *********************************/
	
	// Get - Returns an IMapperable object
	public function get(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->get($obj);
	}		
	
	
	public function update(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->update($obj);
	}	
	
	
	public function delete(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->delete($obj);
	}	
	
	
	public function add(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->add($obj);
	}
	

	public function search(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->search($obj);
	}
	
	
	public function getAllChildren(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->getAllChildren($obj);
	}
	

	public function submit(IMapperable $obj)
	{
		$this->state = $this->setState($obj);
		return $this->state->submit($obj);
	}
	
	
	/******** Approval and Ownership checks *************/
	
	// Returns a new State object based on the passed in object
	private function setState(IMapperable $obj)
	{
		try
		{
			$status = $this->getApprovalStatus($obj);
			
			switch ($status)
			{
				case 'approved':
					return new StateApproved($this->db, $this->uid, $this->baseURL);
					break;
				case 'submitted':
					return new StateSubmitted($this->db, $this->uid, $this->baseURL);
					break;
				case 'non-approved':
					return new StateNonApproved($this->db, $this->uid, $this->baseURL);
					break;
				default:
					throw new ResourceNotFoundException();
			}
		}
		catch (ResourceNotFoundException $e)
		{
			// At this point, there is no approval_status because the object doesn't exist in the database
			// thus the object is stateless
			
			return new Stateless($this->db, $this->uid, $this->baseURL);
		}
		
	}
		
	
	/* NO DUPLICATION OF SQL! */
	// TODO: make this work with other parent tables, possibly add a value to the getParentKey() to include a 'table' value
	private function getApprovalStatus(IMapperable $obj)
	{
		$parentid = $obj->getParentKey();
		$name = $parentid['name'];
		$value = $parentid['value'];
		
		$stmt = $this->db->prepare('SELECT approval_status FROM hunts WHERE '.$name.'=?');		//then shove table value in here
		$stmt->execute([$value]); 
		$approvalStatus = $stmt->fetchColumn();
		
		if ( isset($approvalStatus) )
		{
			return $approvalStatus;
			
		}
		
		throw new ResourceNotFoundException();
	}
	
	
}

?>
