<?php


namespace MagpieAPI\Exceptions;

/* Just use existing Exception type with cool new names */


class IllegalAccessException extends CustomException
{
	//403
	public function getErrorCode()
	{
		return 403;
	}
}


?>
