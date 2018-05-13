<?php

namespace MagpieAPI\Mapper;


use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

/*** Approved ***/
class StateApproved extends State
{
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
		// if the object is a Parent object, can delete (delete should cascade on database)
		if ($this->isOwnedByCurrentUser() && $obj->isParent())
		{
			return $this->dbDelete($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
}

?>
