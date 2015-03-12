<?php
/**
* Project Tank Webpage
* extendes Session class to handle user session
* @author Steffen Lange
*/
include_once(ROOT."/libs/Session.class.php");
class WotSession extends Session{
	
	const SESSION_KEY = "wotSession";
	const WOT_TOKEN = "token";
	const WOT_TOKEN_EXPIRES_AT = "expire";
	const WOT_USER_NAME = "userName";
	const WOT_USER_ID = "userID";
	const WOT_CLAN_ID = "clanID";
	const WOT_PLAYER = "player";
	const USER_SETTINGS = "settings";
	
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
		if(!self::isWotSession()) parent::start();
		if(!self::hasLoginData()) self::updateSession();
	}
	
	public static function setLoginData($id, $name, $clan, $token, $expire){
		self::startWotSession();
		if(!parent::is()) return false;
		self::$data = [
			self::WOT_USER_ID=>$id,
			self::WOT_USER_NAME=>$name,
			self::WOT_CLAN_ID=>$clan,
			self::WOT_TOKEN=>$token,
			self::WOT_TOKEN_EXPIRES_AT=>$expire,
			self::USER_SETTINGS=>null,
			self::WOT_PLAYER=>null,
		];
		self::updateSession();
		return true;
	}
	
	public static function getLoginData(){
		self::startWotSession();
		return self::isWotLogin() ? parent::get(self::SESSION_KEY) : false;
	}
	
	public static function setSettings($settings){
		$data = self::getLoginData();
		if($data === false) return false;
		$data[self::USER_SETTINGS] = $settings;
		self::$data = $data;
		self::updateSession();
		return true;
	}
	
	public static function setData($data, $key){
		$loginData = self::getLoginData();
		if($loginData === false) return false;
		$loginData[$key] = $data;
		self::$data = $loginData;
		self::updateSession();
		return true;
	}
	
	public static function logout(){
		parent::clear();
	}
	
}
