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
		$idnumber = $primarykey['object'];
		$table = $object->getTable();
		
		$sql = 'SELECT * FROM '.$table.' WHERE '.$primarykeyName.'=?';
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$idnumber]); 
		$result = $stmt->fetch();
		$newIMapperable;		// the new object to return
		
		// this is a little messy, but since Hunts and Badges don't inherit ....
		switch ($table)
		{
			case 'hunts':
				$newIMapperable = new Hunt($result);
				break;/*
			case 'badges':
				$newIMapperable = new Badge($result);
				break;*/
		}
		
		if ($newIMapperable == null)
		{
			throw new Exception("Couldn't get from database.");
		}
		
		return $newIMapperable;
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
			throw new Exception("Mapper - add() fail. ");
		}
		
		return true;
	}
	
	
	/* Update - Update a IMapperable with the following parameters */
	protected function dbUpdate(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('$object is null!');
		}
		
		// Get data fields to enter
		$object->sanitize();
		$data = $object->getFields();

		// Get Primary Key
		$primarykey = $object->getPrimaryKey();
		
		$primarykeyName = $primarykey['name'];
		$idnumber = $primarykey['value'];
		
		// Get tablename
		$table = $object->getTable();
		
		
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
		
		$sql = $sql.' WHERE '.$primarykeyName.'=:'$primarykeyName; 		//ex, hunt_id=:hunt_id;
		
		
		//$sql = 'UPDATE hunts SET abbreviation=:abbreviation, name=:name WHERE hunt_id=:hunt_id';
		//echo $sql;
		//return;
		
		// add parent ID and value to the data to be entered in the database
		// - actually this should be a part of the fields[] array, so it should be in $data
		
		$stmt= $this->db->prepare($sql);
		$stmt->execute($data);
		
		// check for success
		$result = $stmt->rowCount();
		
		if ($result < 1)
		{
			throw new Exception("Mapper - update() fail. ");
		}
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
	public function isOwnedByCurrentUser(IMapperable $obj)
	{
		$parentkey = $obj->getParentKey();
		$name = $parentkey['name'];
		$value = $parentkey['value'];
		
		$stmt = $this->db->prepare('SELECT uid FROM hunts WHERE '.$name.'=?');
		$stmt->execute([$value]); 
		$uidFromTable = $stmt->fetchColumn();
		
		return ($this->uid == $uidFromTable) ;
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
		if ($obj->isParent())
		{
			return $this->dbDelete($obj);
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
		if ($this->isOwnedByCurrentUser())
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
		return $this->dbInsert($obj);
	}
	
	
	public function delete(IMapperable $obj)
	{
		return $this->dbDelete($obj);
	}
	
	
}


/*** Stateless ***/
/* This is the state that a top-level IMapperable object lives in before being put in
 * the database.  (Hunt)
 */
class Stateless extends State
{
	public function add(IMapperable $obj)
	{
		return $this->dbInsert($obj);
	}
}




?>
