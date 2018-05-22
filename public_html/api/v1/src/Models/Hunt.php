<?php

/* This is a Hunt.  This class is used as a filter for user-entered data.
 * 
 * NOTE:  After constructing a Hunt, if you want to set the 'hunt_id',
 * you must make a call to setPrimaryKeyValue($newValue) to set it.
 * 
 * If the database changes, this should be the only class that needs to change.
 * The names here have to match what is in the database.
 * 
 */
 
namespace MagpieAPI\Models;

use MagpieAPI\Mapper\IMapperable;


class Hunt implements \JsonSerializable, IMapperable
{	
	/* The name of the table that the class maps to */
	// TODO: change to const?  const vs static?
	private static $TABLENAME = 'hunts';		

	/* This is a list of columns from the hunts table.
	 * These values should be user-editable.
	 * None of these should present a security concern if manually set by a user.  */
	private static $COLUMNS = array(
			'abbreviation', 
			'audience' ,
			'date_end',
			'date_start',
			'name',
			'ordered',
			'summary',
			'sponsor',
			'super_badge',
			'city',
			'state',
			'zipcode'
		);
	
	
	private	$primaryKey;		// (2-element associative array holding the name and value of the primary key)
	private $uid;				// owner of object
	private $fields;			// Associative array to hold all the user-entered values for a Hunt
	
	
	/* Constructor - Takes an associative array of parameters to build a Hunt */
	//TODO: change to take a ($key, $fieldArray) in CTOR ?
	function __construct($array)
	{	
		// set the primary key
		$this->primaryKey = array('name' => 'hunt_id', 'value' => null);
		
		// for each entry in the $COLUMNS, grab that item from the passed in array
		foreach (self::$COLUMNS as $key)
		{
			if (isset($array[$key]))
			{
				$this->fields[$key] = $array[$key];
			}
			else
			{
				$this->fields[$key] = null;
			}
		}
		
	}
	
	
	/******************
	 * interface stuff
	 * *****************/
	 
	 
	/* Get Parent Key - Returns an assoc array containing information on this object's parent */
	public function getParentKey()
	{
		return $this->getPrimaryKey();		// a hunt is it's own parent
	}
	 
	/* Get Table name - return a string */
	public function getTable()
	{
		return self::$TABLENAME;
	}
	
	
	/* Get Fields - Returns an associative array of the fields to populate table row */
	public function getFields()
	{
		return $this->fields;
	}
	
	
	/* Get Primary Key
	 * Returns the (name, value) array of the key used to ID the table row (Primary key in database)
	 * as an associative array.*/
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}
	
	
	/* Set the primary key value */
	public function setPrimaryKeyValue($newValue)
	{
		$this->primaryKey['value'] = $newValue;
	}
	
	/* Set the UID */
	public function setUID($newUID)
	{
		$this->uid = $newUID;
	}
	
	/* Returns the UID as a string for the owner of the instance of the class */
	public function getUID()
	{
		return $this->uid;
	}

	
	/* SUPER IMPORTANT */
	public function sanitize()
	{
		unset($this->fields['hunt_id'], $this->fields['approval_status']);
	}
	
	
	/* Is this a Parent */
	public function isParent()
	{
		return true;		//its a Hunt, master of objects
	}


	/* Convert to an associative array for json_encode() to work with */
	function jsonSerialize()
	{		
		return $this->fields;
	}
	

}

?>
