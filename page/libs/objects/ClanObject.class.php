<?php
/**
* basic clan object
* @param int clanID
* @param string name
* @param string tag
* @param string color
* @param int membersCount
* @param boolean isDisbanned
* @param int lastUpdate
* @param ClanMemberObject[] members
* @param ClanEmblemsObject emblems
* 
* @author Steffen Lange
*/
class ClanObject{
	public $id = null;
	public $name = null;
	public $tag = null;
	public $color = null;
	public $membersCount = null;
	
	public $isDisbanned = false;
	public $lastUpdate = null;
	
	public $members = [];
	public $emblems = null;
}