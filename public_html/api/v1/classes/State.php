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
	protected $uid;		// user id extracted from Firebase token (represents current user)
	
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
	
	
	private function getCurrentUID()
	{
		return $this->uid;
	}
	
	
	/*****************************************************************
	 * 			Public Access Functions
	 * 
	 * 	These functions should be defined in a subclass, by default they throw Exceptions.
	 * 
	 * ***************************************************************/
	
	
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

	/* Get - Takes a IMapperable, returns that object */
	protected function dbSelect(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('Mapper->get(): $object is null!');
		}
		
		$primarykey = $object->getPrimaryKey();
		
		$primarykeyName = $primarykey['name'];
		$idnumber = $primarykey['value'];
		$table = $object->getTable();
		
		$sql = 'SELECT * FROM '.$table.' WHERE '.$primarykeyName.'=?';
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$idnumber]); 
		$result = $stmt->fetch();
		/*$newIMapperable;		// the new object to return
		
		// this is a little messy, but since Hunts and Badges don't inherit ....
		switch ($table)
		{
			case 'hunts':
				$newIMapperable = new Hunt($result);
				break;/*
			case 'badges':
				$newIMapperable = new Badge($result);
				break;
		}*/
		
		if ($result == null)
		{
			throw new Exception("Couldn't get from database.");
		}
		
		return $result;
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
		
		$data = $object->getFields();
		
		//loop through and delete empty values in the array
		foreach ($data as $key => $value)
		{
			if (!isset($data[$key]))
			{
				unset($data[$key]);
			}
		}
		
		//add the UID into the data set
		$data['uid'] = $this->getCurrentUID();
		
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
			throw new Exception("Mapper - add() fail. ");
		}
		
		//TODO: return the ID of the resource added
		
		return true;
	}
	
	
	/* Update - Update a IMapperable with the following parameters */
	protected function dbUpdate(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('$object is null!');
		}
		
		// Get Primary Key
		$primarykey = $object->getPrimaryKey();
		
		$primarykeyName = $primarykey['name'];
		$idnumber = $primarykey['value'];
		
		// Get tablename
		$tablename = $object->getTable();
		
		// Get data fields to enter
		$data = $object->getFields();

		if (!isset($primarykey))
		{
			throw new Exception("Mapper: primary key not set");
		}
		
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
		
		// add the primarykeyName back into the $data set, it's used by the PDO in the query
		$data[$primarykeyName] = $idnumber;
		
		// build query...
		$sql  = 'UPDATE '.$tablename.' SET ';

		foreach($data as $key => $value)
		{
			$sql = $sql.$key."=:".$key.", ";
		}
		
		$sql = rtrim($sql, ', ');		// remove trailing comma
		
		$sql = $sql.' WHERE '.$primarykeyName.'=:'.$primarykeyName; 		//ex, hunt_id=:hunt_id;
		
		
		//$sql = 'UPDATE hunts SET abbreviation=:abbreviation, name=:name WHERE hunt_id=:hunt_id';
		//echo "\n".$sql;
		//return;
		
		// add parent ID and value to the data to be entered in the database
		// - actually this should be a part of the fields[] array, so it should be in $data
		
		$stmt= $this->db->prepare($sql);
		$stmt->execute($data);
		
		// check for success
		$result = $stmt->rowCount();
		
		if ($result < 1)
		{
			return "Nothing updated.";
		}
		
		return true;
	}
	
	
	/* Delete - Delete the object with the specified ID */
	protected function dbDelete(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('$object is null!');
		}
		
		$primarykey = $object->getPrimaryKey();
		
		$primarykeyName = $primarykey['name'];
		$idnumber = $primarykey['value'];
		$table = $object->getTable();
		
		$sql = 'DELETE FROM '.$table.' WHERE '.$primarykeyName.'=?';
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$idnumber]); 
		
		// check for success
		$result = $stmt->rowCount();
		
		if ($result < 1)
		{
			throw new Exception("Delete fail.");
		}
		
		return true;
	}
	
	
	/******************************************************
	 * 					Helper Functions
	 * ****************************************************/
	
	/* Is the specified hunt owned by the current owner? */
	// TODO: Make this work with other parent classes, by using variable for tablename
	public function isOwnedByCurrentUser(IMapperable $obj)
	{
		$parentkey = $obj->getParentKey();
		$name = $parentkey['name'];
		$value = $parentkey['value'];
		
		$stmt = $this->db->prepare('SELECT uid FROM hunts WHERE '.$name.'=?');
		$stmt->execute([$value]); 
		$uidFromTable = $stmt->fetchColumn();
		
		if ( isset($uidFromTable) )
		{
			return ($this->uid == $uidFromTable) ;
		}
		
		throw new ResourceNotFoundException();
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
		// if the object is a Parent object, can delete (delete should cascade on database)
		if ($this->isOwnedByCurrentUser() && $obj->isParent())
		{
			return $this->dbDelete($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
}


/*** Submitted ***/
class StateSubmitted extends State
{
	public function get(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser())
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
		if ($this->isOwnedByCurrentUser($obj))
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
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbUpdate($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
	
	public function add(IMapperable $obj)
	{
		return $this->dbInsert($obj);
	}
	
	
	public function delete(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbDelete($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
	
}


/*** Stateless ***/
/* This is the state that a top-level IMapperable object lives in before being put in
 * the database.  (Hunt)
 */
class Stateless extends State
{
	// If the object is stateless, it doesn't exist in the database.
	public function get(IMapperable $obj)
	{
		throw new ResourceNotFoundException();
	}
	
	
	public function add(IMapperable $obj)
	{
		// Adding Parent objects when they don't exist in the database is fine.
		if ($obj->isParent())
		{
			return $this->dbInsert($obj);
		}
		// Throw an Exception if someone tries to add a Child into the database without a parent.
		else
		{
			throw new IllegalAccessException();
		}
	}
}




?>