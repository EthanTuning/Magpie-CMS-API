<?php

/* Contains the endpoint functions for Images. 
 * 
 * Note: GET handled by apache2
 * */

namespace MagpieAPI\Controllers;

use MagpieAPI\Mapper\Mapper;

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badge;

use MagpieAPI\Exceptions\IllegalAccessException;
use MagpieAPI\Exceptions\ResourceNotFoundException;
use MagpieAPI\Exceptions\UnsupportedOperationException;


use Slim\Http\UploadedFile;

class ImageController
{
	protected $container;

	// constructor receives container instance
	public function __construct(/*Interop\Container\ContainerInterface*/ $container)
	{
		$this->container = $container;
	}


	/**
	 * Moves the uploaded file to the upload directory and assigns it a unique name
	 * to avoid overwriting an existing uploaded file.
	 *
	 * @param string $directory directory to which the file is moved
	 * @param UploadedFile $uploaded file uploaded file to move
	 * @return string filename of moved file
	 */
	private function moveUploadedFile($directory, UploadedFile $uploadedFile)
	{
		$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
		$basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
		$filename = sprintf('%s.%0.8s', $basename, $extension);

		$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

		return $filename;
	}
	

	/************************************
	 *				POST (Add)
	 ***********************************/

	public function add($request, $response, $args)
	{
		$directory = $this->container->get('upload_directory');
		//$uid = $request->getAttribute('uid');
		
		$uploadedFiles = $request->getUploadedFiles();

		// handle single input with single file upload
		$uploadedFile = $uploadedFiles['image'];
		if ($uploadedFile->getError() === UPLOAD_ERR_OK)
		{
			$filename = $this->moveUploadedFile($directory, $uploadedFile);
			$response->write(json_encode(['uri' => $this->container['base_url'].'/uploads/'.$filename]));
		}

		/*
		// handle multiple inputs with the same key
		foreach ($uploadedFiles['example2'] as $uploadedFile) {
			if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
				$filename = moveUploadedFile($directory, $uploadedFile);
				$response->write('uploaded ' . $filename . '<br/>');
			}
		}

		// handle single input with multiple file uploads
		foreach ($uploadedFiles['example3'] as $uploadedFile) {
			if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
				$filename = moveUploadedFile($directory, $uploadedFile);
				$response->write('uploaded ' . $filename . '<br/>');
			}
		}
		*/
		return $response;
	}


	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args)
	{
		/* Create the Mappers used */
		$uid = $request->getAttribute('uid');
		$mapper = new Mapper($this->container->db, $uid);
		
		/* Make blank Hunt */
		$hunt = new Hunt(null);
		$hunt->setPrimaryKeyValue($args['hunt_id']);		// set the Hunt ID from the URL
		
		try
		{
			/* Use the Mapper to delete the hunt with that hunt_id */
			$temp = $mapper->delete($hunt);
			$response->getBody()->write(json_encode($temp));		//add jsonSerialze() to interface?
		}
		catch (IllegalAccessException $e)
		{
			$response = $response->withStatus(403);
		}
		catch (ResourceNotFoundException $e)
		{
			$response = $response->withStatus(404);
		}
		
		return $response;
		
	}


	/************************************
	 *				OPTIONS
	 ***********************************/

	public function options($request, $response, $args)
	{
		// Return response headers
		
		$response->getBody()->write("IMAGES OPTIONS ROUTE ");
		return $response;
	}
		
		

	
}


?>
