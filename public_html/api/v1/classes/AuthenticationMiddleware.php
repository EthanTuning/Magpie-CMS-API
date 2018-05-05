<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

/* This holds the logic for authenticating a user.
 * 
 * 1) Initialize Firebase Admin SDK w/credentials
 * 2) Verify ID token (passed from client) using Firebase Admin SDK
 * 3) Add the user id (uid) extracted from the token to the $request object
 * 
 */

class AuthenticationMiddleware
{
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
		$firebase = $this->initialize();					//create the firebase object
		$verifiedIdToken = $this->authenticate($firebase, $request);	//authenticate the request with firebase
		
		$uid = $verifiedIdToken->getClaim('sub');			// get the UID from the token
		/* Step 3 
		 * 
		 * Send the extracted uid and email to the $request object so the Mapper 
		 * classes can do security checks on the data requested.
		 * 
		 * */
		
		$request = $request->withAttribute('uid', $uid);		// put the UID in the $request
		
		// Get email from token, shove it in request
		$email = $verifiedIdToken->getClaim('email');			// get the email from the token
		$request = $request->withAttribute('email', $email);	// set the email in the $request
		
        $response = $next($request, $response);		//next layer

        return $response;
    }
    
    
    /* Step 1 
     * 
     * Create the Firebase object that connects back home with google.
     * 
     * Requires the Firebase credentials json file from the Firebase Console
     * to be placed in ./creds/
     * 
     * */
    private function initialize()
    {
		// ************Firebase Credentials go here ******************
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
    
    
    /* Step 2 
     * 
     * This decodes the token that was in the request header.
     * 
     * If something is wrong with the token, an error is thrown.
     * 
     * */
    private function authenticate($firebase, $request)
	{
		/* Get the idTokenString */
		$data = $request->getHeader('Authorization');
		
		if ( ! isset($data[0]) )
		{
		   $data[0] = null;
		}

		//error_log("Authorization Header: ".$data[0]);
		$pieces = explode(" ", $data[0]);
		
		if ( ! isset($pieces[1]) )
		{
		   $pieces[1] = null;
		}
		
		$idTokenString = $pieces[1];
		//error_log("idTokenString: ".$idTokenString);
		
		//foreach ($data as $item)
		{
			//echo $idTokenString;
		}
		//die;
		//$idTokenString = 'abcdef.abcdef.abcdef';
		$verifiedIdToken;

		/* Verify it */
		try 
		{
			$verifiedIdToken = $firebase->getAuth()->verifyIdToken($idTokenString);
			//echo "VERIFIED TOKEN";
		}
		catch (InvalidToken $e)
		{
			//echo "TOKEN PROBLEM";
			echo $e->getMessage();
			die;
		}
		
		
		//$user = $firebase->getAuth()->getUser($uid);
		
		return $verifiedIdToken;
	}
    
    
    
    
    
    
}

?>
