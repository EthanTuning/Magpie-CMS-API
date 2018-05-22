<?php

namespace MagpieAPI\Mapper;


use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


/*** Submitted ***/
class StateSubmitted extends State
{
	public function get(IMapperable $obj)
	{
		if ($this->isOwnedByCurrentUser($obj) || $this->isCurrentUserAdmin() )
		{
			return $this->dbSelect($obj);
		}
		else
		{
			throw new IllegalAccessException();
		}
	}
	
}


?>
