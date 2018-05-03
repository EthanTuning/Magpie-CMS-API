<?php

/* This is a Hunt.  It can contain references to badges in the $badges
 * array, but has no clue what a Badge is.
 * 
 * If the database changes, this should be the only class that needs to change.
 * The names here have to match what is in the database.
 * This is basically an array of stuff with some methods attached.
 * 
 */


class Hunt implements JsonSerializable, IMapperable
{	
	private static $TABLENAME = 'hunts';		// change to const?  const vs static?
	private static $PRIMARY_KEY = 'hunt_id';	// just the column name of the primary key
	
	/* This is the columns from the hunts table.  */
	private static $COLUMNS = array(
			'hunt_id',
			'abbreviation', 
			'approval_status',
			'audience' ,
			'available',
			'date_end',
			'date_start',
			'name',
			'ordered',
			'summary',
			'sponsor',
			'super_badge',
			'uid',
			'city',
			'state',
			'zipcode',
			'award_id'
		);
	
	private $fields;	// Associative array to hold all the values for a Hunt
	private $badges;	// array to hold badges
	private $awards;	// array to hold awards
	private $images;	// array to hold image URIs in the future
	
	
	/* Constructor - Takes an associative array of parameters to build a Hunt */
	//TODO: change to take a ($key, $fieldArray) in CTOR ?
	function __construct($array)
	{	
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
		
		/*$this->badges = array(
			'badgestuff' => "badge object"
		);*/
	}
	
	
	/******************
	 * interface stuff
	 * *****************/
	 
	 
	/* Get Parent Key - Returns an assoc array containing information on this object's parent */
	public function getParentKey()
	{
		$name = self::$PRIMARY_KEY;						// hunt is it's own parent
		$value = $this->fields[$name];
		
		return array('name'=>$name, 'value'=>$value);
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
		return $this->getParentKey();			// a hunt is it's own parent
	}
	
	
	/* Returns the UID as a string for the owner of the instance of the class */
	public function getUID()
	{
		return $this->fields['uid'];
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
	
	
	/*
	public function addBadge(Badge $newbadge)
	{
			$this->badges[] = $newbadge;
	}
	*/
	
	/* Populate - Populates the values in Hunt 
	function populate($inputValues)
	{
       foreach ($this->fields as $key => $value)
       {
           print "$key => $value\n";
       }
    }*/

	

	/* Convert to an associative array for json_encode() to work with */
	function jsonSerialize()
	{
		//move the 3 references of arrays into the body
		
		$this->fields['badges'] = $this->badges;
		$this->fields['awards'] = $this->awards;
		$this->fields['images'] = $this->images;
		
		return $this->fields;
		
		//return get_object_vars($this);
	}
	

}

?>
