<?php

namespace MagpieAPI\Exceptions;

/* Just use existing Exception type with cool new names */


abstract class CustomException extends \Exception
{
	//return status code;
	public function getErrorCode()
	{
		return 0;	//??
		//need to be implemented in subclasses;
	}
}


?>
