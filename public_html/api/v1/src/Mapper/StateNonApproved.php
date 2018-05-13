<?php

namespace MagpieAPI\Mapper;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;

/*** NonApproved ***/
class StateNonApproved extends State
{
	
	public function search(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbQuery($obj);
		}
		
		throw new IllegalAccessException();
	}
	
	
	public function get(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj))
		{
			return $this->dbSelect($obj);
		}
		else
		{
			throw new IllegalAccessException();
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
			throw new IllegalAccessException();
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
			throw new IllegalAccessException();
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
			throw new IllegalAccessException();
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
			throw new IllegalAccessException();
		}
	}
	
}


?>
