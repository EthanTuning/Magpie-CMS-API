<?php

/* Contains the endpoint functions for Administrative Tasks. 
 * 
 * The AdminController is very monolithic.  
 * 
 * This class DOES NOT check if the user is an administrator - the
 * AdminChecker class needs to be run before this.  With Slim that means loading
 * the AdminChecker class as middleware on the '/admin' route group (done!).
 * 
 * This does not use a Mapper, it makes a AdminState object and uses that directly.
 * Or it does some direct SQL via PDO.
 * 
 * Currently the AdminController handles:
 * 
 * 1) Giving the client a list of "submitted" Hunts
 * 2) Handling Hunt approval
 * 3) Deleting Hunts
 * 
 * Getting resources is done via the normal /hunts/ routes, there's checks to see
 * if the requestor is an administrator.  Admins should have no trouble getting
 * submitted and non-approved resources for review.
 * 
 * The mailer functionality is currently disabled.
 * */

namespace MagpieAPI\Controllers;


use PHPMailer\PHPMailer\PHPMailer;		// phpmailer used to send emails
use PHPMailer\PHPMailer\Exception;		// https://github.com/PHPMailer/PHPMailer

use MagpieAPI\Mapper\AdminState;		// We're using this as a bypass to the state-checking

use MagpieAPI\AdminChecker;			// used for the HEAD request.  This is a client convenience method.

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


class AdminController
{
	protected $container;

	// constructor receives container instance
	public function __construct($container)
	{
		$this->container = $container;
	}	
	
	
	/************************************
	 *				GET
	 * 
	 * GET A LIST OF NON-APPROVED HUNTS FOR REVIEW
	 ***********************************/

	public function getNonApprovedList($request, $response, $args)
	{
		//TODO: add in query functionality
		
		//mapperBypass is just used for the buildResults() function
		$mapperBypass = new AdminState($this->container->db, $request->getAttribute('uid'), $this->container['base_url']);		
		
		//direct SQL query
		$sql = 'SELECT * from `hunts` WHERE `approval_status` = "submitted"';
		$stmt = $this->container->db->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll();
		
		if ($result == null)
		{
			throw new ResourceNotFoundException("No hunts have been submitted.");
		}
		
		foreach ($result as $element)
		{
			$build[] = $mapperBypass->buildResults($element, new Hunt(null));
		}
		
		$response->getBody()->write(json_encode($build));
		
		return $response;
	}


	/************************************
	 *				PUT (Update)
	 * 
	 * CHANGE THE STATUS OF A HUNT (APPROVE OR DISAPPROVE)
	 * 
	 * The hunt should be in the "submitted" state, but this doesn't check that.
	 * If you're an admin you can do whatever you want.
	 * (See Hunt state diagram)
	 ***********************************/

	public function changeStatus($request, $response, $args)
	{
		//mapperBypass is just used for the responseMessage() function
		$mapperBypass = new AdminState($this->container->db, $request->getAttribute('uid'), $this->container['base_url']);	
		
		// direct SQL query
		$huntid = $args['hunt_id'];			// get hunt ID from URI
		
		$parameters = $request->getParsedBody();
		
		$status = $parameters['approval_status'];
		
		if ($status == 'approved' || $status == 'non-approved')
		{
			$sql = 'UPDATE `hunts` SET `approval_status` =? WHERE `hunt_id`=?';
			$stmt = $this->container->db->prepare($sql);
			$stmt->execute([$status, $huntid]);
			$result = $stmt->rowCount();
		
			if ($result < 1)
			{
				$build = $mapperBypass->responseMessage(false, "Nothing changed.");
			}
			else
			{
				$this->sendEmail($status);
			
				$build = $mapperBypass->responseMessage(true, "Hunt $huntid has been $status.");
			}
			
			$response->getBody()->write(json_encode($build));
		
			return $response;
		}

		throw new \InvalidArgumentException("'approval_status' not found or not set correctly.");
	}

	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args)
	{
		// Bypassing functionality of the mapper by making a State object directly
		$mapperBypass = new AdminState($this->container->db, $request->getAttribute('uid'), $this->container['base_url']);
		
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);
		
		$result = $mapperBypass->delete($hunt);
		
		$response->getBody()->write(json_encode($result));
		return $response;
		
	}


	/************************************
	 *				GET (Used by a client to check if user is an admin to control flow of client)
	 ***********************************/

	public function isAdmin($request, $response, $args)
	{
		$adminChecker = new AdminChecker($this->container);
		$bool = $adminChecker->isAdmin($request);
		
		$mapperBypass = new AdminState($this->container->db, $request->getAttribute('uid'), $this->container['base_url']);
		$build = $mapperBypass->responseMessage($bool, "This user is an admin");
		$build['isAdmin'] = $bool;		// yea its in there twice oh well
		
		$response->getBody()->write(json_encode($build));
		return $response;
	}
		
		
	/* Send email to user with the approval status */
	private function sendEmail($status)
	{
		return;
		
		// this is disabled for now
		
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
			/*
			//Server settings
			//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			//$mail->isSMTP();                                      // Set mailer to use SMTP
			//$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
			//$mail->SMTPAuth = true;                               // Enable SMTP authentication
			//$mail->Username = 'user@example.com';                 // SMTP username
			//$mail->Password = 'secret';                           // SMTP password
			//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			//$mail->Port = 587;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('temp@rkwitz.com', 'Mailer');
			$mail->addAddress('admin@rkwitz.com', 'Joe User');     // Add a recipient
			//$mail->addAddress('ellen@example.com');               // Name is optional
			$mail->addReplyTo('admin@rkwitz.com', 'Information');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			//$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Here is the subject';
			$mail->Body    = "Your hunt has been <b>$status</b>";
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			*/
			
			
			// Set PHPMailer to use the sendmail transport
			//$mail->isSendmail();
			//Set who the message is to be sent from
			$mail->setFrom('noreply@magpiehunt.com', 'Magpiehunt.com');
			//Set an alternative reply-to address
			//$mail->addReplyTo('replyto@example.com', 'First Last');
			//Set who the message is to be sent to
			$mail->addAddress('insert address here', 'John Doe');
			//Set the subject line
			$mail->Subject = 'PHPMailer sendmail test';
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
			$mail->msgHTML("Your hunt has been <b>$status</b>");
			//Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.png');
			

			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
		
		
		
	}
	
}


?>
