<?php


namespace MagpieAPI\Exceptions;

/* Just use existing Exception type with cool new names */

class UnsupportedOperationException extends CustomException
{
	//405
	public function getErrorCode()
	{
		return 405;
	}
}


?>
