<?php

/* This is a Hunt.  It can contain references to badges in the $badges
 * array, but has no clue what a Badge is.
 * 
 * If the database changes, this should be the only class that needs to change.
 * The names here have to match what is in the database.
 * This is basically an array of stuff with some methods attached.
 * 
 */


class Hunt implements JsonSerializable 
{	
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
	private $badges;	// array to hold the badges
	private $images;	// array to hold image URIs in the future
	
	
	/* Constructor - Takes an associative array of parameters to build a Hunt */
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
	
	
	/* Approval Status */
	public function isApproved()
	{
		if ( isset($this->fields['approval_status']) && 
				$this->fields['approval_status'] == 'approved' )
		{
			return true;
		}
		else
		{
			return false;
		}	
	}

	
	public function isOwnedBy($otheruid)
	{
		if ( isset($this->fields['uid']) && 
				fields['uid'] == $otheruid )
		{
			return true;
		}
		else
		{
			return false;
		}	
	}
	
	
	public function addBadge(Badge $newbadge)
	{
			$this->badges[] = $newbadge;
	}
	
	
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
		//$this->fields['badges'] = $this->badges;
		return $this->fields;
		
		//return get_object_vars($this);
	}
	

}

?>
