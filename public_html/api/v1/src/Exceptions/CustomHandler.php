<?php

/* This is a custom Exception handler for Slim.
 * Slim's default Exception handler is great for development/debugging,
 *   this is mainly for a deployed instance where we just want to send status codes back.
 * 
 * Sources:
 * https://www.slimframework.com/docs/v3/handlers/error.html
 * https://stackoverflow.com/questions/40356241/use-middleware-errorhandling-with-slim-3
 * 
 */
 
namespace MagpieAPI\Exceptions;


class CustomHandler
{
   public function __invoke($request, $response, $exception)
   {
		$message = $exception->getMessage(); 			// "could not save"
		$errorArray = ["status" => "error", "message" => $message];

		if($exception instanceof CustomException)
		{
			$errorCode = $exception->getErrorCode(); 		// CustomExceptions have this method
		}
		else
		{
			$errorCode = 500;
		}

		// return response to the client
		return $response
				->withStatus($errorCode)
				->withHeader('Content-Type','application/json')
				->write(json_encode($errorArray));
   }
}

?>
