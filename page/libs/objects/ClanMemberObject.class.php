<?php
/**
* basic clan member object
* @param int id
* @param string name
* 
* @param RatingObject rating
* @param StatisticObject clan
* 
* @author Steffen Lange
*/
class ClanMemberObject{
	public $id = null;
	public $name = null;
	public $role = null;
	public $role_i18n = null;
	public $joined = null;
	
	public $rating = null;
	public $statistic = null;
}