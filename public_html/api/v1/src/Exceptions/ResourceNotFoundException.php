<?php


namespace MagpieAPI\Exceptions;

/* Just use existing Exception type with cool new names */

class ResourceNotFoundException extends CustomException
{
	//404
	public function getErrorCode()
	{
		return 404;
	}
}

?>
