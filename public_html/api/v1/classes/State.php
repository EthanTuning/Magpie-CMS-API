<?php

/* The State class contains the real logic for the Mapper class.
 * 
 * The operations that the Mapper can perform are based on the state on the
 * object passed in (the IMapperable).
 * 
 * 1) Mapper recieves IMapperable. (Public Access Functions)
 * 2) Mapper checks the database for the state of the object.
 * 3) Mapper sets the state in a switch statement
 * 4) State object is asked to perform the request.
 * 5) The request(add, get, update, delete) will be handled by the state
 * 		or throw an IllegalAccessException.
 * 
 */


abstract class State
{
	protected $db;		// PDO object (already instantiated and stuff)
	protected $uid;		// user id extracted from Firebase token
	
	/* Constructor */
	function __construct($db, $uid)
	{
		if ($uid == null || $db == null)
		{
			throw new Exception('State CTOR: something is null!');
		}
		
		$this->db = $db;
		$this->uid = $uid;
	}
	
	
	/***********************************
	 * 		Public Access Functions
	 * 
	 * 	These should be defined in a subclass.
	 * 
	 * *********************************/
	
	
	// Get - Returns an IMapperable object
	public function get(IMapperable $obj)
	{
		throw new IllegalAccessException();
	}		
	
	
	public function update(IMapperable $obj)
	{
		throw new IllegalAccessException();
	}	
	
	
	public function delete(IMapperable $obj)
	{
		throw new IllegalAccessException();
	}	
	
	
	public function add(IMapperable $obj)
	{
		throw new IllegalAccessException();
	}
	
	
	
	/*****************************************************
	 * 				CRUD OPERATIONS
	 * ****************************************************/

	/* dbSelect - Takes a IMapperable, returns that object */
	protected function dbSelect(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('Mapper->get(): $object is null!');
		}
		
		$huntid = $object->getParentId();
		
		// PDO code
		$stmt = $this->db->prepare('SELECT * FROM hunts WHERE hunt_id=?');
		$stmt->execute([$huntid]); 
		$result = $stmt->fetch();
		$newHunt = new Hunt($result);
		
		return $newHunt;
	}

	
	/* Search - Takes an array of parameters, returns an array (values of that Hunt from the dbase) */
	protected function dbQuery($params)
	{
		if ($params == null)
		{
			throw new Exception('HuntMapper->search(): $params is null!');
		}
		
		
		return false;
	}
	
	
	/* Add - Add a Hunt with the passed parameters */
	protected function dbInsert(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('HuntMapper->add(): $hunt is null!');
		}
		
		$object->sanitize();
		
		$data = $object->getFields();
		
		// delete things from array that are unwanted before adding to database
		//unset($data['hunt_id'], $data['approval_status']);
		
		//loop through and delete empty values in the array
		foreach ($data as $key => $value)
		{
			if (!isset($data[$key]))
			{
				unset($data[$key]);
			}
		}
		
		// build query...
		$sql  = "INSERT INTO ".$object->getTable();

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
		
		return true;
	}
	
	
	/* Update - Update a Hunt with the following parameters */
	protected function dbUpdate(Hunt $hunt)
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
	protected function dbDelete($id)
	{
		
	}
	
}


/*** Approved ***/
class StateApproved extends State
{
	public function get(IMapperable $obj)
	{
		return $this->dbSelect($obj);
	}
	
	public function delete(IMapperable $obj)
	{
		return $this->dbDelete($obj);
	}
}


/*** Submitted ***/
class StateSubmitted extends State
{
	public function get(IMapperable $obj)
	{
		if (/* if owned by current user */ true)
		{
			return $this->dbSelect($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
}


/*** NonApproved ***/
class StateNonApproved extends State
{
	public function get(IMapperable $obj)
	{
		if (/* if owned by current user */ true)
		{
			return $this->dbSelect($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
	
	public function update(IMapperable $obj)
	{
		return $this->dbUpdate($obj);
	}
	
	
	public function add(IMapperable $obj)
	{
		return $this->dbUpdate($obj);
	}
	
	
	public function delete(IMapperable $obj)
	{
		return $this->dbUpdate($obj);
	}
	
	
}






?>
