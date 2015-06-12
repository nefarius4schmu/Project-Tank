<?php
/**
* Project Tank Webpage
* 
* @version 0.7.0
* @author Steffen Lange
*/
error_log(E_ALL);
//ini_set('display_errors',1);
//echo "<pre>".print_r(ROOT, true)."</pre>";
//echo "<pre>".print_r($_SERVER, true)."</pre>";
//$debug = true;
/* ===================================================================================== */
include_once(ROOT."/vars/globals.php");

_def("lang");
_def("settings");
_lib("Debug");
_lib("Router");
_lib("WotSession");
_lib("WotPlayer");

/* ===================================================================================== */
// get login state
//_get("login", $loginData);
$loginData = WotSession::getLoginData();
//session_start();
//Debug::v($loginData);
//Debug::v($_SESSION);

// set current page infos
$_isLogin = isset($loginData) && $loginData !== false;
$_user = isset($loginData) ? $loginData : false;
$_isError =  isset($_GET["e"]);
$_isWarning =  isset($_GET["w"]);
$_isMessage = isset($_GET["m"]);
$_cAdmin = null;
if(Router::CONSTRUCTIONS){
    $_cAdmin = $_isLogin && isset($_user[WotSession::CUSTOM_KEY])
        ? $_user[WotSession::CUSTOM_KEY]
        : (isset($_GET["admin"]) ? $_GET["admin"] : null);
    if($_cAdmin !== null && $_isLogin && !isset($_user[WotSession::CUSTOM_KEY])) WotSession::setData($_cAdmin, WotSession::CUSTOM_KEY);
}
/** @var WotPlayer $_player */
$_player = $_user !== false && isset($_user["player"]) && $_user["player"] instanceof WotPlayer ? $_user["player"] : false;
$_isClan = $_player !== false && $_player->hasClan();
$_settings = $_user !== false ? $_user["settings"] : false;

if(!$_isError) {
    $options = ["login"=>$_isLogin, "clan"=>$_isClan, "settings"=>$_settings, "cAdmin"=>$_cAdmin];
    $_route = isset($_GET["g"]) ? $_GET["g"] : null;
    $_route = Router::getRoute($_route, $options);
//    $_route = isset($_GET["g"]) ? Router::getRoute2($_GET["g"], $options) : Router::getDefault($_isLogin);
//    $_route = isset($_GET["g"]) ? Router::getRoute($_GET["g"], $_isLogin, $_isClan) : Router::getDefault($_isLogin);
}else {
    // on error: send user to start page and logout
    $_route = Router::getRoute(null);
//    WotSession::logout();
}
$_routeType = Router::getType($_route);
$_isRedirect = isset($_GET["r"]) && $_GET["r"] == "1";



$_page = [
	"login"=>$_isLogin,
	"user"=>$_user,
	"error"=>$_isError ? $_GET["e"] : null,
    "warning"=>$_isWarning ? $_GET["w"] : null,
	"message"=>$_isMessage ? $_GET["m"] : null,
];

//Debug::v($_page);
//Debug::v($_route);
//Debug::r($_GET);
//	exit();

if($_isError) _load($_route, $_page);
else if(!$_isRedirect){
	switch($_routeType){
		case ROUTETYPE_BOARD:
			$_page["board"] = $_route;
			$_route = ROUTE_BOARD;
		case ROUTETYPE_DEFAULT:
			$_location = Router::getLocation($_route);
			_load($_location, $_page); 
			break;
//		case ROUTETYPE_BOARD: 	_loadBoard($_route, $_page); break;
		default: _error(ERROR_ROUTE_UNKNOWN_TYPE);
	}
}else
	_redirect($_route, $_page);
	
/* ===================================================================================== */
/* ===================================================================================== */

?>