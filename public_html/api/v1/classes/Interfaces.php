<?php

/* Anything that wants to go into a Mapper needs to implement this.
 * Badges, Awards, etc.  Also Hunts implement this too, but they are their own parent.
 */


interface IMapperable
{
	public function getParentId();		//returns a string of the Class's hunt_id (since Hunt is the Parent class */
	
	public function getPrimaryKey();	//returns a (key, value) pair (assoc array) of the Class's primary key in database (eg, 'hunt_id' => '3929292')
	
	public function getTable();			//returns string of tablename that the class is mapped to
	
	public function getFields();		//returns an associative array of fields (minus primary key), corresponding to table columns
	
	public function sanitize();			// cleans the class's fields() array for insertion and updating
										// remove primary key, remove 'approval_status', etc

}


?>
