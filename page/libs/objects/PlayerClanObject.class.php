<?php
require_once("ClanObject.class.php");
/**
* extended clan object for player specs
* @param string role
* @param string role_i18n
* @param int joined
* 
* @author Steffen Lange
*/
class PlayerClanObject extends ClanObject{
	public $joined = null;
	public $role = null;
	public $role_i18n = null;
}