<?php
/*** Stateless ***/
/* This is the state that a top-level IMapperable object lives in before being put in
 * the database.  (Hunt)
 * 
 * It also applies to anything when using the query() function.
 */
 
namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;
 
class Stateless extends State
{
	// If the object is stateless, it doesn't exist in the database.
	public function get(IMapperable $obj)
	{
		throw new ResourceNotFoundException();
	}
	
	
	// Takes a object with the fields set to whatever you're searching for
	public function search(IMapperable $obj)
	{	
		if ($obj->isParent())
		{
			return $this->dbQuery($obj);
		}
		else
		{
			throw new UnsupportedOperationException();
		}
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
