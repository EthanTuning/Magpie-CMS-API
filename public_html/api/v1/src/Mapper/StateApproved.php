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
}

?>
