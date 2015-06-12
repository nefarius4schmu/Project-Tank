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
if(!defined("ERROR_LOGIN_UNKNOWN_USER"))  define("ERROR_LOGIN_UNKNOWN_USER", 4005);
if(!defined("ERROR_IS_LOGOUT"))  define("ERROR_IS_LOGOUT", 4500);
if(!defined("ERROR_LOGOUT_FAILED"))  define("ERROR_LOGOUT_FAILED", 4501);
if(!defined("ERROR_LOGOUT_SEND_FAILED"))  define("ERROR_LOGOUT_SEND_FAILED", 4502);
if(!defined("ERROR_REDIRECT"))  define("ERROR_REDIRECT", 2010);
if(!defined("ERROR_REDIRECT_NOT_SET"))  define("ERROR_REDIRECT_NOT_SET", 2011);
if(!defined("ERROR_REDIRECT_MISSING_TYPE"))  define("ERROR_REDIRECT_MISSING_TYPE", 2012);
if(!defined("ERROR_EDIT_NEWS_MISSING"))  define("ERROR_EDIT_NEWS_MISSING", 301);
if(!defined("ERROR_API_GET_PLAYER_INFO"))  define("ERROR_API_GET_PLAYER_INFO", 5001);
if(!defined("ERROR_API_LOGOUT"))  define("ERROR_API_LOGOUT", 5002);
if(!defined("ERROR_API_GET_CLAN_MEMBERS_STATS"))  define("ERROR_API_GET_CLAN_MEMBERS_STATS", 5003);
if(!defined("ERROR_DB_CONNECTION"))  define("ERROR_DB_CONNECTION", 9000);
if(!defined("ERROR_DB_LOGIN"))  define("ERROR_DB_LOGIN", 9001);
if(!defined("ERROR_DB_LOGIN_SETTINGS"))  define("ERROR_DB_LOGIN_SETTINGS", 9002);
if(!defined("ERROR_DB_GET_LOGIN_DATA"))  define("ERROR_DB_GET_LOGIN_DATA", 9010);
if(!defined("ERROR_DB_GET_CLAN_MEMBERS"))  define("ERROR_DB_GET_CLAN_MEMBERS", 9022);
if(!defined("ERROR_DB_GET_CLAN_MEMBERS_STATS_TABLE_DATA"))  define("ERROR_DB_GET_CLAN_MEMBERS_STATS_TABLE_DATA", 9025);
if(!defined("ERROR_DB_GET_PARAM_UID"))  define("ERROR_DB_GET_PARAM_UID", 9030);
if(!defined("ERROR_DB_GET_EVENT_INFO"))  define("ERROR_DB_GET_EVENT_INFO", 9039);
if(!defined("ERROR_DB_GET_EVENT_TYPE"))  define("ERROR_DB_GET_EVENT_TYPE", 9040);
if(!defined("ERROR_DB_GET_EVENT_TYPE_OPTIONS"))  define("ERROR_DB_GET_EVENT_TYPE_OPTIONS", 9041);
if(!defined("ERROR_DB_GET_BRIEFINGID_EXISTS"))  define("ERROR_DB_GET_BRIEFINGID_EXISTS", 9042);
if(!defined("ERROR_DB_SET_USER_INFO"))  define("ERROR_DB_SET_USER_INFO", 9110);
if(!defined("ERROR_DB_SET_USER_GROUP"))  define("ERROR_DB_SET_USER_GROUP", 9111);
if(!defined("ERROR_DB_SET_USER_GROUP_RATING"))  define("ERROR_DB_SET_USER_GROUP_RATING", 9113);
if(!defined("ERROR_DB_SET_USER_RATING"))  define("ERROR_DB_SET_USER_RATING", 9114);
if(!defined("ERROR_DB_SET_CLAN_INFO"))  define("ERROR_DB_SET_CLAN_INFO", 9120);
if(!defined("ERROR_DB_SET_CLAN_MEMBERS"))  define("ERROR_DB_SET_CLAN_MEMBERS", 9122);
if(!defined("ERROR_DB_SET_CLAN_WOT_USER_STATS"))  define("ERROR_DB_SET_CLAN_WOT_USER_STATS", 9123);
if(!defined("ERROR_DB_SET_WOT_USER_STATS"))  define("ERROR_DB_SET_WOT_USER_STATS", 9133);
if(!defined("ERROR_DB_SET_SETTINGS"))  define("ERROR_DB_SET_SETTINGS", 9150);
if(!defined("ERROR_DB_SET_NEWS"))  define("ERROR_DB_SET_NEWS", 9160);
if(!defined("ERROR_DB_SET_EVENT"))  define("ERROR_DB_SET_EVENT", 9170);
if(!defined("ERROR_DB_DEL_CLAN_MEMBER"))  define("ERROR_DB_DEL_CLAN_MEMBER", 9221);
if(!defined("ERROR_DB_DEL_CLAN_MEMBERS"))  define("ERROR_DB_DEL_CLAN_MEMBERS", 9222);
if(!defined("ERROR_DB_DEL_NEWS"))  define("ERROR_DB_DEL_NEWS", 9260);
if(!defined("ERROR_DB_DEL_EVENT"))  define("ERROR_DB_DEL_EVENT", 9261);
if(!defined("ERROR_DB_LEAVE_EVENT"))  define("ERROR_DB_LEAVE_EVENT", 9262);
if(!defined("ERROR_DB_JOIN_EVENT"))  define("ERROR_DB_JOIN_EVENT", 9263);
if(!defined("ERROR_DB_LIMIT_BRIEFINGID_GEN"))  define("ERROR_DB_LIMIT_BRIEFINGID_GEN", 9300);
if(!defined("ERROR_SESSION_SET_SETTINGS"))  define("ERROR_SESSION_SET_SETTINGS", 8020);
if(!defined("ERROR_SESSION_SET_DATA"))  define("ERROR_SESSION_SET_DATA", 8021);
if(!defined("ERROR_ROUTE_UNKNOWN_TYPE"))  define("ERROR_ROUTE_UNKNOWN_TYPE", 3001);
if(!defined("ERROR_GET_UNKNOWN_TYPE"))  define("ERROR_GET_UNKNOWN_TYPE", 8500);
if(!defined("ERROR_GET_MISSING_TYPE"))  define("ERROR_GET_MISSING_TYPE", 8501);


if(!defined("WARNING_POST_MISSING_TITLE"))  define("WARNING_POST_MISSING_TITLE", 1010);
if(!defined("WARNING_POST_MISSING_TEXT"))  define("WARNING_POST_MISSING_TEXT", 1011);
if(!defined("WARNING_POST_MISSING_TIME_START"))  define("WARNING_POST_MISSING_TIME_START", 1012);
if(!defined("WARNING_POST_MISSING_TIME_END"))  define("WARNING_POST_MISSING_TIME_END", 1013);
if(!defined("WARNING_POST_MISSING_EVENT_TYPE"))  define("WARNING_POST_MISSING_EVENT_TYPE", 1014);
if(!defined("WARNING_EVENT_CANNOT_JOIN"))  define("WARNING_EVENT_CANNOT_JOIN", 1040);


/* ===================================================================================== */
/* ===================================================================================== */

/**
 * @param string $name
 * @param array $_page
 */
function _load($name, $_page=[]){
    if(strpos($name, '.php') === false)
	    include_once(ROOT."/html/$name/.index.php");
    else
        include_once(ROOT."/html/$name");
}

/**
 * @param string $name
 * @param array $_page
 */
function _loadBoard($name, $_page=[]){
    if(strpos($name, '.php') === false)
	    include_once(ROOT."/html/board/$name.php");
    else
        include_once(ROOT."/html/board/$name");
}

/**
 * @param string $name
 */
function _lib($name){
	include_once(ROOT."/libs/$name.class.php");
}

/**
 * @param string $name
 * @param mixed $out
 * @param array $_page
 */
function _get($name, &$out, $_page=[]){
	include_once(ROOT."/get/$name.php");
}

/**
 * @param string $name
 */
function _def($name){
	include_once(ROOT."/vars/$name.php");
}

/**
 * @param string $name
 * @param array $_page
 * @return mixed
 */
function _data($name, $_page=[]){
	return include ROOT."/get/$name.php";
}

/**
 * @param int $errorCode
 * @param mixed $data
 * @param bool $debug
 * @param bool $getLink
 * @return string|void
 */
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

/**
 * @param int $code
 * @param string $backroute
 * @param mixed $data
 * @param bool $debug
 * @param bool $getLink
 * @return string|void
 */
function _warning($code, $backroute=null, $data=null, $debug=false, $getLink=false){
	if($debug && !$getLink){
		Debug::e("Warning: $code");
		if(isset($data)) Debug::v($data);
		return;
	}
	_lib("Router");
	$msg = isset($data) ? "/&m=".rawurlencode(json_encode($data)) : null;
    $base = isset($backroute) ? Router::getDefaultRedirectURL().$backroute."/" : Router::getDefaultRedirectURL();
	$location = $base."warning/".$code.$msg;
	if($getLink) return $location;
	header("Location: ".$location);
	exit();
}

/**
 * @param string $name
 * @param array $_page
 * @param bool $debug
 */
function _redirect($name, $_page, $debug=false){
	_lib("Router");
	$error = 0;
	$redirect = Router::getRedirectData($name);
//	Debug::v($name);
//	Debug::v($redirect);
	if($redirect === false) _error(ERROR_REDIRECT_NOT_SET, null, $debug);
	else if(Router::OFFLINE && isset($redirect["offlineUrl"])) $redirect["url"] = $redirect["offlineUrl"];
    else if(!isset($redirect["url"])) $redirect["url"] = getRedirectByType($redirect["type"], $error);//, $error
	
	if($error > 0) _error($error);
	include_once(ROOT."/html/redirect.php");
}

/**
 * function to fill in string based templates
 * html: $temp = "<span class='{{class}}'>{{content}}</span>";
 * call: fn_template($temp, ["class"=>"bla", "content"=>"hallo world"]);
 * options:
 *      escape - escape special html characters
 *      clear - remove all data leftovers
 * @param string $temp
 * @param array $data
 * @param array $options
 * @return string
 */
function fn_template($temp, $data, $options=[]){
    $escape = isset($options["escape"]) && $options["escape"];
    $clear = isset($options["clear"]) && $options["clear"];

    foreach($data as $key=>$item){
        if($escape) $key = htmlentities($key);
        $temp = preg_replace('/({{'.$key.'}})/', $item, $temp);
    }
    return !$clear ? $temp : preg_replace('/({{[^}}]+}})/', "", $temp);
}

/**
 * @param string $type
 * @param int $error
 * @return bool|null|string
 */
function getRedirectByType($type, &$error){//, &$error
	if($type == "wotLogin"){
		_lib("WotData");
		_lib("Calc");

		$wotData = new WotData();
		date_default_timezone_set("UTC");
		$url = $wotData->getLoginURL(Router::getLoginRedirectURL(), Calc::getWeeks(1, true));
		if($url === false)
			$error = ERROR_LOGIN_GET_URL;
		return $url;
	}else 
		return null;
}