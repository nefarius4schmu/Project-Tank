<?php
include_once(dirname(__FILE__)."\\Session.class.php");
class WotSession extends Session{
	
	const SESSION_KEY = "wotSession";
	const WOT_TOKEN = "token";
	const WOT_TOKEN_EXPIRES_AT = "expire";
	const WOT_USER_NAME = "userName";
	const WOT_USER_ID = "userID";
	const WOT_CLAN_ID = "clanID";
	
	private static $data = null;
	
	private static function isWotSession(){
		return parent::is() && self::hasLoginData();
	}
	
	public static function isWotLogin(){
		return self::isWotSession() && self::hasLoginData();
	}
	
	private function hasLoginData(){
		return parent::has(self::SESSION_KEY);
	}
	
	private static function updateSession(){
		parent::set(self::SESSION_KEY, self::$data);
	} 
	
	public static function startWotSession(){
		parent::start();
		if(!self::hasLoginData()) self::updateSession();
	}
	
	public static function setLoginData($id, $name, $clan, $token, $expire){
		if(!self::isWotSession()) self::startWotSession();
		if(!parent::is()) return false;
		self::$data = [
			self::WOT_USER_ID=>$id,
			self::WOT_USER_NAME=>$name,
			self::WOT_CLAN_ID=>$clan,
			self::WOT_TOKEN=>$token,
			self::WOT_TOKEN_EXPIRES_AT=>$expire,
		];
		self::updateSession();
		return true;
	}
	
	public static function getLoginData(){
		if(!self::isWotSession()) self::startWotSession();
		return self::isWotLogin() ? parent::get(self::SESSION_KEY) : false;
	}
	
	public static function logout(){
		parent::clear();
	}
	
}
