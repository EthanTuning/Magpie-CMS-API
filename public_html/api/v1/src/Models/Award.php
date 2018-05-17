<?php

/* This is an Award.  There are many like it, but this one is mine.
 * Without a Hunt, an Award is nothing.  Without an Award, a Hunt is nothing.
 * 
 * This class is used as a filter for user-entered data.
 * 
 * NOTE:  After constructing a Badge, if applicable, call:
 * 	setPrimaryKeyValue($newValue) to set the badge_id
 * 	setParentKeyValue($newValue) to set the hunt_id
 * 
 * If the database changes, this should be the only class that needs to change.
 * The names here have to match what is in the database.
 * 
 */

namespace MagpieAPI\Models;

use MagpieAPI\Mapper\IMapperable;


class Award implements \JsonSerializable, IMapperable
{	
	/* The name of the table that the class maps to */
	// TODO: change to const?  const vs static?
	private static $TABLENAME = 'awards';		

	/* This is a list of columns from the hunts table.
	 * These values should be user-editable.
	 * None of these should present a security concern if manually set by a user.  */
	private static $COLUMNS = array(
		'address',
		'description',
		'lat',
		'lon',
		'name',
		'redeem_code',
		'award_value');

		//'hunt_id');	//parent key
	
	private	$primaryKey;		// (2-element associative array holding the name and value of the primary key)
	private $parentKey;			// hunt_id
	private $uid;				// owner of object
	private $fields;			// Associative array to hold all the user-entered values for a Hunt
	
	
	/* Constructor - Takes an associative array of parameters to build a Hunt */
	//TODO: change to take a ($key, $fieldArray) in CTOR ?
	function __construct($array)
	{	
		// set the primary key
		$this->primaryKey = array('name' => 'award_id', 'value' => null);
		
		// set the parent key
		$this->parentKey = array('name' => 'hunt_id', 'value' => null);
		
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
		return $this->parentKey;		// the hunt_id
	}
	
	
	/* Set Parent Key */
	public function setParentKeyValue($obj)
	{
		$this->parentKey['value'] = $obj;
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
	
	
	/* Is this a Parent */
	public function isParent()
	{
		return false;		// Badge is a child of Hunt
	}


	/* Convert to an associative array for json_encode() to work with */
	function jsonSerialize()
	{
		/*move the 3 references of arrays into the body
		
		$this->fields['badges'] = $this->badges;
		$this->fields['awards'] = $this->awards;
		$this->fields['images'] = $this->images;*/
		
		return $this->fields;
		
		//return get_object_vars($this);
	}
	

}

?>
