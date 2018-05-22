<?php

namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

/*********************************************************
 * This is only for use by the AdminController
 * *****************************************************/


/*** Bypass the State of the parent object and do stuff ***/
class AdminState extends State
{
	
	public function search(IMapperable $obj)
	{
		return $this->dbQuery($obj);
	}
	
	
	public function get(IMapperable $obj)
	{
		return $this->dbSelect($obj);
	}
	
	
	public function getAllChildren(IMapperable $obj)
	{
		return $this->dbGetAllChildren($obj);
	}
	
	
	public function delete(IMapperable $obj)
	{
		return $this->dbDelete($obj);
	}
	
}


?>
