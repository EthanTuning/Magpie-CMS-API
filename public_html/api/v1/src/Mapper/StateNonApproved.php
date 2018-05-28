<?php

namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

/*** NonApproved ***/
class StateNonApproved extends State
{
	
	public function get(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj) || $this->isCurrentUserAdmin() )
		{
			return $this->dbSelect($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot retrieve non-approved resource that you don't own.");
		}
	}
	
	
	public function update(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbUpdate($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot update non-approved resource that you don't own.");
		}
	}
	
	
	public function add(IMapperable $obj)
	{
		if ($obj->isParent() || $this->isOwnedByCurrentUser($obj) )
		{
			return $this->dbInsert($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot add resource to a Hunt that you don't own.");
		}
	}
	
	
	public function delete(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbDelete($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot delete non-approved resource that you don't own.");
		}
	}
	
	
	public function submit(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbSubmit($obj);
		}
		else
		{
			throw new IllegalAccessException("Cannot submit non-approved resource that you don't own.");
		}
	}
	
}


?>
