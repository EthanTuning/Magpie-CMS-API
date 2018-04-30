<?php

/* Anything that references a Hunt as it's parent need to implement this
 * Badges, Awards, etc.  Also Hunts implement this too, just for the hell of it.
 */


interface IHuntElement
{
	public function getHuntID();		//returns a string of the Class's hunt_id

}


?>
