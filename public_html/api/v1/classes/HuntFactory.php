<?php

/* This class builds a Hunt.
 */
 

class HuntFactory
{
	private HuntMapper $huntMapper;
	private BadgeMapper $badgeMapper;
	//AwardMapper $awardMapper;
	
	
	__construct(HuntMapper $huntmap, BadgeMapper $badgemap)
	{
		$this->huntMapper = $huntmap;
		$this->badgeMapper = $badgemap;
	}
	
	
	public getHunt($huntID)
	{
		//puts together an entire Hunt (badges, etc.)
		
	}
	
}



?>
