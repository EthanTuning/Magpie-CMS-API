<?php

/* Anything that wants to go into a Mapper needs to implement this.
 * Badges, Awards, etc.  Also Hunts implement this too, but they are their own parent.
 */


interface IMapperable
{
	public function isParent();			// returns true if it's a Parent class (in this case, a Hunt);
	
	public function getParentKey();		//same as getPrimaryKey but for it's parent (usually this would be a foreign key) */
	
	public function getPrimaryKey();	//returns an associative array of the Class's primary key in database
										// Format: ['name'=>'badge_id', 'value'=>'323223'] or ['name'=>'hunt_id', 'value'=>'6463']
										
	public function setPrimaryKeyValue($newValue);	// set the 'value' portion of the PrimaryKey
	
	public function getTable();			//returns string of tablename that the class is mapped to
	
	public function getFields();		//returns an associative array of fields (minus primary key), corresponding to table columns
}


?>
