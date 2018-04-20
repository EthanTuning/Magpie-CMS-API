<?php

/* This class is a struct for the Hunt data.  It is meant to be populated by
 * FETCH_CLASS in PDO.  Some fields that exist on the database have been
 * ommited. */
 

public class Hunt
{
	private int hunt_id;
	private String abbreviation;
	private String approval_status;		//takes the place of the approval bit in the database
	private String audience;
	private String date_end;		//SQL date obj
	private String date_start;		//SQL date obj
	private String name;
	//private boolean ordered;  not sure how to do this
	private String summary;
	private String super_badge;
	private String creator_id;		//uid of person
	private int location_id;		//int on dbase
	private int award_id;


	/* This prevents PDO from populating undefined fields. */
	public function __set($name, $value)
	{
		//doesn't do anything
	}
	

}

?>
