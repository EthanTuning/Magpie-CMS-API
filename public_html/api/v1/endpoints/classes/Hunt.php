<?php

public class Hunt
{
	private int hunt_id;
	private String abbreviation;




	/* This prevents PDO from populating undefined fields. */
	public function __set($name, $value)
	{
		//doesn't do anything
	}
	
	



}

?>
