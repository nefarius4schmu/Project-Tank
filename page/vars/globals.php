<?php
if(!defined("ERROR_MISSING_LOGIN"))  define("ERROR_MISSING_LOGIN", 4000);
if(!defined("ERROR_LOGIN_AUTH"))  define("ERROR_LOGIN_AUTH", 4001);
if(!defined("ERROR_LOGIN_FAILED"))  define("ERROR_LOGIN_FAILED", 4002);
if(!defined("ERROR_LOGIN_EXISTS"))  define("ERROR_LOGIN_EXISTS", 4003);
if(!defined("ERROR_LOGIN_GET_URL"))  define("ERROR_LOGIN_GET_URL", 4004);
if(!defined("ERROR_IS_LOGOUT"))  define("ERROR_IS_LOGOUT", 4500);
if(!defined("ERROR_LOGOUT_FAILED"))  define("ERROR_LOGOUT_FAILED", 4501);
if(!defined("ERROR_LOGOUT_SEND_FAILED"))  define("ERROR_LOGOUT_SEND_FAILED", 4502);
if(!defined("ERROR_REDIRECT"))  define("ERROR_REDIRECT", 2010);
if(!defined("ERROR_REDIRECT_NOT_SET"))  define("ERROR_REDIRECT_NOT_SET", 2011);
if(!defined("ERROR_REDIRECT_MISSING_TYPE"))  define("ERROR_REDIRECT_MISSING_TYPE", 2012);
if(!defined("ERROR_API_GET_PLAYER_INFO"))  define("ERROR_API_GET_PLAYER_INFO", 5001);
if(!defined("ERROR_API_LOGOUT"))  define("ERROR_API_LOGOUT", 5002);
if(!defined("ERROR_DB_CONNECTION"))  define("ERROR_DB_CONNECTION", 9099);
if(!defined("ERROR_DB_LOGIN"))  define("ERROR_DB_LOGIN", 9001);
if(!defined("ERROR_ROUTE_UNKNOWN_TYPE"))  define("ERROR_ROUTE_UNKNOWN_TYPE", 3001);


if(!defined("PAGE_BRAND"))  define("PAGE_BRAND", "Planet Tank");
if(!defined("PAGE_TITLE_SETTINGS"))  define("PAGE_TITLE_SETTINGS", "Settings");

if(!defined("TOOLTIP_REQ_CLAN"))  define("TOOLTIP_REQ_CLAN", "F&uuml;r diese Funktion m&uuml;ssen Sie Mitglied eines Clans sein.");

if(!defined("TEXT_SETTINGS_TANKS"))  define("TEXT_SETTINGS_TANKS", "Tanks in Garage");
if(!defined("TEXT_SETTINGS_TANKS_DESCR"))  define("TEXT_SETTINGS_TANKS_DESCR", "Allow to cache your current tanks in garage in our database for extended functions. This information will only be accessable for authorized members of your current clan.");

/* ===================================================================================== */
/* ===================================================================================== */

function _load($name, $_page=[]){
	include_once("html/$name/.index.php");
}

function _loadBoard($name, $_page=[]){
	include_once("html/board/$name.php");
}

function _lib($name){
	include_once("libs/$name.class.php");
}

function _get($name, &$out, $_page=[]){
	include_once("get/$name.php");
}

function _def($name){
	include_once("vars/$name.php");
}

function _error($errorCode, $data=null, $debug=false){
	if($debug){
		Debug::e("ERROR: $errorCode");
		if(isset($data)) Debug::v($data);
		return;
	}
	_lib("Router");
	$msg = isset($data) ? "/&m=".json_encode($data) : null;
	header("Location: ".Router::getDefaultRedirectURL()."error/".$errorCode.$msg);
	exit();
}

function _redirect($name, $_page){
	_lib("Router");
	$error = 0;
	$redirect = Router::getRedirectData($name);
//	Debug::v($name);
//	Debug::v($redirect);
	if($redirect === false) _error(ERROR_REDIRECT_NOT_SET);
	else if(!isset($redirect["url"])) $redirect["url"] = getRedirectByType($redirect["type"], $error);//, $error
	
	if($error > 0) _error($error);
	include_once("html/redirect.php");
}

function getRedirectByType($type, &$error){//, &$error
	if($type == "wotLogin"){
		_lib("WotData");
		
		$wotData = new WotData();
		date_default_timezone_set("UTC");
		$url = $wotData->getLoginURL(Router::getLoginRedirectURL(), time() + (7*24*60*60));
		if($url === false)
			$error = ERROR_LOGIN_GET_URL;
		return $url;
	}else 
		return null;
}