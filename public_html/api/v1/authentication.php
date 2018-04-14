<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

/* This holds the logic for authenticating a user.
 * 
 * 1) Initialize Firebase Admin SDK w/credentials
 * 2) Verify ID token (passed from client) using Firebase Admin SDK
 * 3) Continue processing, or drop out and return an error
 */

class AuthenticationMiddleware
{
	/* Load in the Firebase stuff */
	
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    
    
    /* This is called from Slim */
    public function __invoke($request, $response, $next)
    {
		$firebase = $this->initialize();
		
		//$this->authenticate($firebase);
		
        $response->getBody()->write('BEFORE');
        $response = $next($request, $response);
        $response->getBody()->write('AFTER');

        return $response;
    }
    
    
    /* Step 1 */
    private function initialize()
    {
		// Firebase Credentials go here
		$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/creds/Magpie CMS API-8d356af4830f.json');

		$firebase = (new Factory)
			->withServiceAccount($serviceAccount)
			// The following line is optional if the project id in your credentials file
			// is identical to the subdomain of your Firebase project. If you need it,
			// make sure to replace the URL with the URL of your project.
			//->withDatabaseUri('https://my-project.firebaseio.com')
			->create();

		//$database = $firebase->getDatabase();
		
		return $firebase;
	}
    
    
    /* Step 2 */
    private function authenticate($firebase)
	{
		/* Get the idTokenString */
		
		$idTokenString = 'abcdef.abcdef.abcdef';

		/* Verify it */
		try 
		{
			$verifiedIdToken = $firebase->getAuth()->verifyIdToken($idTokenString);
		}
		catch (InvalidToken $e)
		{
			echo $e->getMessage();
			die;
		}

		$uid = $verifiedIdToken->getClaim('sub');
		$user = $firebase->getAuth()->getUser($uid);
	}
    
    
    
    
    
    
}

?>
