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


	/************************************
	 *				POST (Add)
	 ***********************************/

	public function add($request, $response, $args)
	{	
		//note: uploaded files must have a key
		
		$uploadedFiles = $request->getUploadedFiles();	//gets all the files
		$array = array();				//holds the result urls
		
		foreach ($uploadedFiles as $name => $uploadedFile)		//for every file, save it and add url to $array
		{
			if ($uploadedFile->getError() === UPLOAD_ERR_OK)
			{
				$result = $this->addImage($uploadedFile);
				$array[$name] = ['href' => $result];
			}
		}
		
		$response->write(json_encode($array));
			
		return $response;
	}
	

	/************************************
	 *				DELETE
	 ***********************************/

	public function delete($request, $response, $args)
	{
		$response->getBody()->write("Not implemented");
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
		
	
	
	/************************************
	 *				HELPER FUNCTIONS
	 * 
	 * Actual logic stuff.
	 ***********************************/
	
	
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
	

	// Takes a Slim\Http\UploadedFile type
	// other Controllers use this though so its public
	public function addImage($uploadedFile)
	{
		if ($uploadedFile->getError() === UPLOAD_ERR_OK)
		{
			$directory = $this->container->get('upload_directory');
			$filename = $this->moveUploadedFile($directory, $uploadedFile);
			return $this->container['base_url'].'/uploads/'.$filename;		// returns URL string
			
		}
		else
		{
			throw new Exception("Can't add an image for whatever reason.");
		}
	}

	
}


?>
