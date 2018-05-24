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
use \Exception;
use \InvalidArgumentException;

abstract class State
{
	
	protected $db;			// PDO object (already instantiated and stuff)
	protected $uid;			// user id extracted from Firebase token (represents current user)
	protected $baseURL;		//base URL of API (from the container).  You know I should just pass the container.
	
	/* Constructor */
	function __construct($db, $uid, $baseURL)
	{
		if ($uid == null || $db == null || $baseURL == null)
		{
			throw new Exception('State CTOR: something is null!');
		}
		
		$this->db = $db;
		$this->uid = $uid;
		$this->baseURL = $baseURL;
	}
	
	
	private function getCurrentUID()
	{
		return $this->uid;
	}
	
	
	/*****************************************************************
	 * 			Public Access Functions
	 * 
	 * 	These functions should be defined in a subclass, by default they throw Exceptions.
	 *  Unless it's something can happen regardless of the state of the object.
	 * 
	 *  The whole permissions and state thing needs to be cleaned up.  This is getting hard to maintain.
	 * 
	 * ***************************************************************/
	
	
	// Get - Returns an IMapperable object
	public function get(IMapperable $obj)
	{
		throw new UnsupportedOperationException("Not supported in current state.");
	}		
	
	
	// Get All - Returns all the Hunts a user owns. (Regardless of state).
	public function getAll(IMapperable $obj)
	{
		return $this->dbGetAllHunts($obj);
	}
	
	
	// Takes a object with the fields set to whatever you're searching for
	public function search(IMapperable $obj)
	{		
		throw new UnsupportedOperationException("Not supported in current state.");
	}
	
	
	// No matter the state, the owner can get his children (and administrators)
	public function getAllChildren(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj) || $this->isCurrentUserAdmin() )
		{
			return $this->dbGetAllChildren($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot retrieve children you don't own and aren't part of an approved Hunt.");
		}
	}
	
	public function update(IMapperable $obj)
	{
		throw new UnsupportedOperationException("Not supported in current state.");
	}	
	
	
	// Delete - No matter the state someone can delete their Hunt
	public function delete(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj) && $obj->isParent())
		{
			return $this->dbDelete($obj);
		}
		
		throw new UnsupportedOperationException("Not supported in current state.");
	}	
	
	
	public function add(IMapperable $obj)
	{
		throw new UnsupportedOperationException("Not supported in current state.");
	}
	
	
	public function submit(IMapperable $obj)
	{
		throw new UnsupportedOperationException("Not supported in current state.");
	}
	
	/*****************************************************
	 * 				CRUD OPERATIONS
	 * 
	 * 	Also includes getAll() and submit() stuff	
	 * 
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
			throw new ResourceNotFoundException("Can't find that object");
		}
		
		// start building the return array
		$build = $this->buildResults($result, $object);
		
		return $build;
	}


	/* Get All - Get all the Hunts.
	 * 
	 * Takes a IMapperable, returns all Hunts owned by current user.
	 * */
	protected function dbGetAllHunts(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('Mapper->getAllHunts(): $object is null!');
		}
		
		$table = $object->getTable();
		$uid = $this->uid;
		$sql = 'SELECT * FROM '.$table.' WHERE `uid`=?';
		
		// PDO code
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$uid]); 
		$result = $stmt->fetchAll();
		
		if ($result == null)
		{
			throw new ResourceNotFoundException("No Hunts for this user.");
		}
		
		// start building the return array
		foreach ($result as $element)
		{
			$build[] = $this->buildResults($element, $object);
		}
		
		return $build;
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
			throw new InvalidArgumentException('Mapper->dbQuery(): $object is null!');
		}
		if (!$object->isParent())
		{
			throw new Exception("Can't query sub-resources.");
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
		
		if ($result == null)
		{
			throw new ResourceNotFoundException("No results matching that query");
		}
		
		foreach ($result as $element)
		{
			$build[] = $this->buildResults($element, $object);
		}
		
		return $build;
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
			throw new ResourceNotFoundException("No sub-resources found.");
		}
		
		foreach ($result as $element)
		{
			$build[] = $this->buildResults($element, $object);
		}
		
		return $build;
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
		
		// Add the assigned ID to the object
		$newid = $this->db->lastInsertId();
		$object->setPrimaryKeyValue($newid);
		
		return $this->dbSelect($object);		// make a SELECT call to return it to the user
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
			throw new Exception("Nothing updated.");
		}
		
		return $this->dbSelect($object);	//return the newly updated object from database
	}
	
	
	/* Submit
	 * 
	 * Submit a Hunt for Approval (Just change approval_status to submitted) */
	protected function dbSubmit(IMapperable $object)
	{
		if ($object == null)
		{
			throw new Exception('$object is null!');
		}
		
		// Get Primary Key
		$primarykey = $object->getPrimaryKey()['value'];

		$sql = 'UPDATE `hunts` SET `approval_status` ="submitted" WHERE `hunt_id`=?';
		$stmt = $this->db->prepare($sql);
		$stmt->execute([$primarykey]);
		$result = $stmt->rowCount();
	
		if ($result < 1)
		{
			throw new UnsupportedOperationException("Already submitted.");
		}

		return $this->responseMessage(true, "Successfully submitted.");
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
			throw new ResourceNotFoundException("Delete fail - Resource probably doesn't exist.");
		}
		
		return $this->responseMessage(true, "Successfully deleted.");
	}
	
	
	/******************************************************
	 * 					Helper Functions
	 * 
	 * 	Used to help.
	 * 
	 * ****************************************************/
	
	/* Is the specified hunt owned by the current owner? 
	 * 	- Parent: Check the hunt id with the owner 
	 *  - Child:  Get the hunt ID, then check the hunt id with owner 
	 * */
	// TODO: Make this work with other parent classes, by using variable for tablename
	public function isOwnedByCurrentUser(IMapperable $obj)
	{
		// Parents and blank Children
		if ($obj->isParent() || !isset($obj->getPrimaryKey()['value']) )
		{
			$parentkey = $obj->getParentKey();
			$name = $parentkey['name'];
			$value = $parentkey['value'];
			
			$stmt = $this->db->prepare('SELECT uid FROM hunts WHERE '.$name.'=?');
			$stmt->execute([$value]); 
			$uidFromTable = $stmt->fetchColumn();
		}
		// Child object with specific ID
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
		
		throw new ResourceNotFoundException("You don't own that resource.");
	}


	/* Administrator Check
	 * 
	 * Is the current user an Administrator?
	 * 
	 * This was origninally tied up in the AdminChecker middleware (and still is)
	 * but there was a lot of code duplication going on, making it hard to modify.
	 * 
	 * The /admin endpoint still exists, but to simplify getting resources administrators
	 * can use the existing sub-resource links.
	 */
	protected function isCurrentUserAdmin()
	{
		$uid = $this->uid;
		$db = $this->db;
		
		$sql = 'SELECT `uid` FROM `administrators` WHERE uid=?';
		$stmt = $db->prepare($sql);
		$stmt->execute([$uid]);		//yes we have to make a 1D array from an associative array
		
		if ( null == $stmt->fetchColumn() )	
		{
			return false;		// if the uid is not in the admin table, return false
		}
		
		return true;
	}


	/* Response Message
	 * 
	 * Inform the user what happened.
	 */
	public function responseMessage($bool, $message)
	{
		$array['status'] = $bool;
		$array['message'] = $message;
		
		return $array;
	}


	/* Build Results 
	 * 
	 * Takes an array of results representing an object and returns 
	 * a beefed-up array.
	 * */
	public function buildResults($result, IMapperable $object)
	{
		// start building the return array
		$class = strtolower(substr(strrchr(get_class($object), '\\'), 1));		// this removes the namespace from the returned string by get_class() and makes it lowercase
		
		$build['class'] = $class;
		$build['data'] = $this->expandURL($result);						// expand the fields that contain URLs
		
		$table = $object->getTable();
		
		if ($object->isParent())
		{	
			$build['href'] = $this->baseURL."/hunts/".$build['data']['hunt_id'];
			$build = $this->addChildren($build);	
		}
		else
		{
			//children objects are a bitch to program
			$keyName = $object->getPrimaryKey()['name'];
			$build['href'] = $this->baseURL."/hunts/".$build['data']['hunt_id']."/$table/".$build['data'][$keyName];
		}
		
		return $build;
	}



	/* Expand URL
	 * 
	 * Runs through the result set and turns specified fields into associative arrays.
	 * Turns a ['image' : 'url'] into a ['image': ['href' => 'url']] */
	private function expandURL($result)
	{
		if ($result == null)
		{
			throw new \Exception('expandURL: $results is null.');
		}
		
		//list of fields that are links
		$links = ['image', 'icon'];
		
		// change urls to have a 'href' key
		// for each field thats a link, change it to [href => url]
		foreach ($links as $key)
		{
			if ( isset($result[$key]))
			{
				$result[$key] = ['href' => $result[$key]];
			}
		}
		
		return $result;
	}


	/* Add Children
	 * 
	 * This adds in links to the sub-resources.  Yea it's hardcoded because child-parent relationships aren't really
	 * established very well.  Thus the children become undisciplined and hard to manage. JUST LIKE IN REAL LIFE LOL
	 * */
	private function addChildren($array)
	{
		$children[] = ['class' => 'badges', 'href' => $this->baseURL."/hunts/".$array['data']['hunt_id']."/badges", 'type' => 'json'];
		$children[] = ['class' => 'awards', 'href' => $this->baseURL."/hunts/".$array['data']['hunt_id']."/awards", 'type' => 'json'];
		
		$array['subresources'] = $children;
		
		return $array;
	}


	
}


?>
