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

namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

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
		throw new UnsupportedOperationException();
	}		
	
	
	// Takes a object with the fields set to whatever you're searching for
	public function search(IMapperable $obj)
	{		
		throw new UnsupportedOperationException();
	}
	
	
	// No matter the state, the owner can get his children
	public function getAllChildren(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbGetAllChildren($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
	public function update(IMapperable $obj)
	{
		throw new UnsupportedOperationException();
	}	
	
	
	public function delete(IMapperable $obj)
	{
		throw new UnsupportedOperationException();
	}	
	
	
	public function add(IMapperable $obj)
	{
		throw new UnsupportedOperationException();
	}
	
	
	
	/*****************************************************
	 * 				CRUD OPERATIONS
	 * ****************************************************/

	/* Get - Get one object.
	 * 
	 * Takes a IMapperable, returns that object
	 * */
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
		
		if ($result == null)
		{
			throw new Exception("Couldn't get from database.");
		}
		
		return $result;
	}

	
	/* Query - Search for multiple PARENT objects. (Doesn't work on kids)
	 * 
	 * Takes an IMapperable, instantiated with the parameters that you're looking for,
	 * and returns an array of matching rows from the database.
	 * 
	 * There is some security built into the sql query statement.
	 * It will only return rows where the approval status is 'approved' or the 'uid' matches the current owner.
	 *  */
	protected function dbQuery(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('Mapper->dbQuery(): $object is null!');
		}
		
		$primarykey = $object->getPrimaryKey();
		
		$primarykeyName = $primarykey['name'];
		$idnumber = $primarykey['value'];
		$table = $object->getTable();
		
		$data = $object->getFields();
		
		
		//loop through and delete empty values in the array
		foreach ($data as $key => $value)
		{
			if (!isset($data[$key]))
			{
				unset($data[$key]);
			}
		}
		
		// build query...
		$sql  = 'SELECT * FROM '.$object->getTable().' WHERE ';

		foreach($data as $key => $value)
		{
			$sql = $sql.'`'.$key.'`=:'.$key.' AND ';
		}
		
		//$sql = rtrim($sql, ' AND ');		// remove trailing AND
		
		$sql = $sql.' (`approval_status` = "approved" OR `uid`=:uid)'; 		//most of the Mapper has no security, but this does.
		
		$data['uid'] = $this->uid;
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute($data); 
		$result = $stmt->fetchAll();
		
		// If no results returned
		if ($result == null)
		{
			$result = array();	//return an empty array, for now
		}
		
		return $result;
	}
	
	
	/* GET ALL CHILDREN OBJECTS
	 * 
	 * Takes a child object with a parentkey, returns all child objects with that parent.*/
	protected function dbGetAllChildren(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('Mapper->dbGetAllChildren: $object is null!');
		}
		
		$parentKey = $object->getParentKey();
		
		$parentKeyName = $parentKey['name'];
		$parentKeyValue = $parentKey['value'];
		$table = $object->getTable();
		
		$sql = 'SELECT * FROM '.$table.' WHERE '.$parentKeyName.'=?';
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$parentKeyValue]); 
		$result = $stmt->fetchAll();
		
		if ($result == null)
		{
			$result = array();
		}
		
		return $result;
		
	}
	
	
	/* Add - Insert a single object into database
	 * 
	 * Add an IMapperable with the passed parameters */
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
		
		
		if ($object->isParent())
		{
			//add stuff specific to parents
			$data['uid'] = $this->getCurrentUID();
		}
		else
		{
			// for children, add the parent's key in (to identify them)
			$parentKey = $object->getParentKey();
			$data[$parentKey['name']] = $parentKey['value'];
		}
		
		// build query...
		$sql  = "INSERT INTO ".$object->getTable();

		// implode keys of $array...
		$sql .= " (`".implode("`, `", array_keys($data))."`)";

		// implode placeholders of $array...
		$sql .= " VALUES (:".implode(", :",  array_keys($data)).") ";
		
		$stmt= $this->db->prepare($sql);
		$stmt->execute($data);
		
		// return something like {'hunt_id', '299292'};
		$result = array($object->getPrimaryKey()['name'] => $this->db->lastInsertId() );
		
		return $result;
	}
	
	
	/* Update
	 * 
	 * Update a IMapperable with the following parameters */
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
	
	
	/* Delete - Delete a single object from database
	 * 
	 * Delete the object with the specified ID */
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
	
	/* Is the specified hunt owned by the current owner? 
	 * 	- Parent: Check the hunt id with the owner 
	 *  - Child:  Get the hunt ID, then check the hunt id with owner 
	 * */
	// TODO: Make this work with other parent classes, by using variable for tablename
	public function isOwnedByCurrentUser(IMapperable $obj)
	{
		// Parent
		if ($obj->isParent())
		{
			$parentkey = $obj->getParentKey();
			$name = $parentkey['name'];
			$value = $parentkey['value'];
			
			$stmt = $this->db->prepare('SELECT uid FROM hunts WHERE '.$name.'=?');
			$stmt->execute([$value]); 
			$uidFromTable = $stmt->fetchColumn();
		}
		// Children
		else
		{
			$parentkey = $obj->getParentKey();
			$parentName = $parentkey['name'];
			
			$primaryName = $obj->getPrimaryKey()['name'];
			$primaryValue = $obj->getPrimaryKey()['value'];
			$table = $obj->getTable();
			
			$stmt = $this->db->prepare('SELECT uid FROM hunts INNER JOIN '.$table.' ON hunts.hunt_id = '.$table.'.hunt_id WHERE '.$primaryName.'=?');
			$stmt->execute([$primaryValue]); 
			$uidFromTable = $stmt->fetchColumn();
		}
		
		if ( isset($uidFromTable) )
		{
			return ($this->uid == $uidFromTable) ;
		}
		
		throw new ResourceNotFoundException();
	}
	
}


?>