<?php

/* This class builds a "complete" Hunt (a Hunt with links to Badges, Awards, etc).
 * 
 * Constructor Input: A Mapper object.
 * 
 * build($hunt): 	Input: A Hunt object; the Factory queries the database to assemble the rest of the components.
 * 					Returns: an associative array.
 * 
 * NOTE:  This might be easier to just shove in the HuntController.
 * Originally this way going to assemble an entire Hunt.  I'm leaving it in just in case
 * something changes and we need to assemble a complete hunt (with a list of badges in the response and not just a link to them.)
 * 
 */

namespace MagpieAPI\Models;

use MagpieAPI\Mapper\Mapper;

use MagpieAPI\Models\Hunt;
use MagpieAPI\Models\Badges;
//use MagpieAPI\Models\Awards;

class HuntFactory
{
	private $mapper;
	private $container;
	
	public function __construct(Mapper $mapper_in, $container_in)
	{
		$this->mapper = $mapper_in;
		$this->container = $container_in;
	}
	
	
	//puts together an entire Hunt (by adding links to badges, etc.)
	public function build(Hunt $hunt)
	{
		if ($hunt == null)
		{
			throw new \InvalidArgumentException();
		}
		
		$huntid = $hunt->getPrimaryKey()['value'];		// get the hunt_id
		$huntResult = $this->mapper->get($hunt);		// get the Hunt	
		$huntResult['badges'] = $this->container['base_url']."/hunts/".$huntid."/badges";	// add the URI to the badges
		
		return $huntResult;
	}
	
}



?>
