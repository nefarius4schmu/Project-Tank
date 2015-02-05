<?php
/**
* Project Tank Webpage
* 
* @version 0.3.4
* @author Steffen Lange
*/
error_log(E_ALL);
//$debug = true;
/* ===================================================================================== */
include_once("vars/globals.php");
_lib("Debug");
_lib("Router");
/* ===================================================================================== */
// get login state
_get("login", $loginData);
//session_start();
//Debug::v($loginData);
//Debug::v($_SESSION);

// set current page infos
$_isLogin = isset($loginData) && $loginData !== false;
$_user = isset($loginData) ? $loginData : false;
$_isError =  isset($_GET["e"]);
$_isMessage = isset($_GET["m"]);

if(!$_isError)
	$_location = isset($_GET["g"]) ? Router::getLocation($_GET["g"], $_isLogin) : Router::getDefault($_isLogin);
else
	$_location = Router::getDefault();	

$_routeType = Router::getType($_location);
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

if($_isError) _load($_location, $_page);
else if(!$_isRedirect){
	switch($_routeType){
		case ROUTETYPE_BOARD:
			$_page["board"] = $_location;
			$_location = ROUTE_BOARD;
		case ROUTETYPE_DEFAULT: 
			_load($_location, $_page); 
			break;
//		case ROUTETYPE_BOARD: 	_loadBoard($_location, $_page); break;
		default: _error(ERROR_ROUTE_UNKNOWN_TYPE);
	}
}else
	_redirect($_location, $_page);
	
/* ===================================================================================== */
/* ===================================================================================== */

?>