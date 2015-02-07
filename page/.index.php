<?php
/**
* Project Tank Webpage
* 
* @version 0.4.0
* @author Steffen Lange
*/
error_log(E_ALL);
//$debug = true;
/* ===================================================================================== */
include_once("vars/globals.php");
_def("lang");
_def("settings");
_lib("Debug");
_lib("Router");
_lib("WotSession");
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
$_isMessage = isset($_GET["m"]);

if(!$_isError)
	$_route = isset($_GET["g"]) ? Router::getRoute($_GET["g"], $_isLogin) : Router::getDefault($_isLogin);
else
	$_route = Router::getDefault();	

$_routeType = Router::getType($_route);
$_isRedirect = isset($_GET["r"]) && $_GET["r"] == "1";



$_page = [
	"login"=>$_isLogin,
	"user"=>$_user,
	"error"=>$_isError ? $_GET["e"] : null,
	"message"=>$_isMessage ? $_GET["m"] : null,
];

//Debug::v($_page);
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