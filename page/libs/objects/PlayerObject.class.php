<?php
/**
* basic player object
* @param int id
* @param string name
* @param string lang
* @param int lastUpdate
* @param RatingObject rating
* @param StatisticObject clan
* @param PlayerClanObject clan
* 
* @author Steffen Lange
*/
class PlayerObject{
	public $id = null;
	public $name = null;
	public $lang = null;
	
	public $lastUpdate = null;
	
	public $rating = null;
	public $statistic = null;
	public $clan = null;
	
	
}