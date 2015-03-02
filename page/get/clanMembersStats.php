<?php
/**
* Project Tank Webpage
* getter file
* @return list of current clan members
*/
error_log(E_ALL);
//$debug = true;
/* ===================================================================================== */
include_once("../vars/globals.php");
_def("lang");
_def("settings");
_lib("Debug");
_lib("Router");
_lib("WotSession");
/* ===================================================================================== */
// get login state
//_get("login", $loginData);
$loginData = WotSession::getLoginData();