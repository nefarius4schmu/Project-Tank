<?php
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
$_page = [
	"login" => isset($loginData) && $loginData !== false,
	"user"=> isset($loginData) ? $loginData : false,
];

$_location = isset($_GET["g"]) ? Router::getLocation($_GET["g"], $_page["login"]) : Router::getDefault($_page["login"]);
$_routeType = Router::getType($_location);
$_isRedirect = isset($_GET["r"]) && $_GET["r"] == "1";

//Debug::v($_page);
//Debug::r($_GET);
//	exit();

if(!$_isRedirect)
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
else
	_redirect($_location, $_page);
	
/* ===================================================================================== */
/* ===================================================================================== */

?>