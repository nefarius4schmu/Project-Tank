<?php
/**
* Project Tank Webpage
* definitions and global functions
* @author Steffen Lange
*/
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
if(!defined("ERROR_API_GET_CLAN_MEMBERS_STATS"))  define("ERROR_API_GET_CLAN_MEMBERS_STATS", 5003);
if(!defined("ERROR_DB_CONNECTION"))  define("ERROR_DB_CONNECTION", 9099);
if(!defined("ERROR_DB_LOGIN"))  define("ERROR_DB_LOGIN", 9001);
if(!defined("ERROR_DB_LOGIN_SETTINGS"))  define("ERROR_DB_LOGIN_SETTINGS", 9002);
if(!defined("ERROR_DB_GET_CLAN_MEMBERS"))  define("ERROR_DB_GET_CLAN_MEMBERS", 9022);
if(!defined("ERROR_DB_GET_CLAN_MEMBERS_STATS_TABLE_DATA"))  define("ERROR_DB_GET_CLAN_MEMBERS_STATS_TABLE_DATA", 9025);
if(!defined("ERROR_DB_SET_USER_INFO"))  define("ERROR_DB_SET_USER_INFO", 9110);
if(!defined("ERROR_DB_SET_USER_GROUP"))  define("ERROR_DB_SET_USER_GROUP", 9111);
if(!defined("ERROR_DB_SET_USER_GROUP_RATING"))  define("ERROR_DB_SET_USER_GROUP_RATING", 9113);
if(!defined("ERROR_DB_SET_USER_RATING"))  define("ERROR_DB_SET_USER_RATING", 9114);
if(!defined("ERROR_DB_SET_CLAN_INFO"))  define("ERROR_DB_SET_CLAN_INFO", 9120);
if(!defined("ERROR_DB_SET_CLAN_MEMBERS"))  define("ERROR_DB_SET_CLAN_MEMBERS", 9122);
if(!defined("ERROR_DB_SET_CLAN_WOT_USER_STATS"))  define("ERROR_DB_SET_CLAN_WOT_USER_STATS", 9123);
if(!defined("ERROR_DB_SET_WOT_USER_STATS"))  define("ERROR_DB_SET_WOT_USER_STATS", 9133);
if(!defined("ERROR_DB_SET_SETTINGS"))  define("ERROR_DB_SET_SETTINGS", 9150);
if(!defined("ERROR_DB_DEL_CLAN_MEMBER"))  define("ERROR_DB_DEL_CLAN_MEMBER", 9221);
if(!defined("ERROR_DB_DEL_CLAN_MEMBERS"))  define("ERROR_DB_DEL_CLAN_MEMBERS", 9222);
if(!defined("ERROR_SESSION_SET_SETTINGS"))  define("ERROR_SESSION_SET_SETTINGS", 8020);
if(!defined("ERROR_SESSION_SET_DATA"))  define("ERROR_SESSION_SET_DATA", 8021);
if(!defined("ERROR_ROUTE_UNKNOWN_TYPE"))  define("ERROR_ROUTE_UNKNOWN_TYPE", 3001);
if(!defined("ERROR_GET_UNKNOWN_TYPE"))  define("ERROR_GET_UNKNOWN_TYPE", 8500);
if(!defined("ERROR_GET_MISSING_TYPE"))  define("ERROR_GET_MISSING_TYPE", 8501);


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

function _error($errorCode, $data=null, $debug=false, $getLink=false){
	if($debug && !$getLink){
		Debug::e("ERROR: $errorCode");
		if(isset($data)) Debug::v($data);
		return;
	}
	_lib("Router");
	$msg = isset($data) ? "/&m=".rawurlencode(json_encode($data)) : null;
	$location = Router::getDefaultRedirectURL()."error/".$errorCode.$msg;
	if($getLink) return $location;
	header("Location: ".$location);
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