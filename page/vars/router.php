<?php
/**
* Project Tank Webpage
* definitions
* @author Steffen Lange
*/
if(!defined("ROUTE_START"))  define("ROUTE_START", "start");
if(!defined("ROUTE_HOME"))  define("ROUTE_HOME", "home");
if(!defined("ROUTE_EVENTS"))  define("ROUTE_EVENTS", "events");
if(!defined("ROUTE_CLAN"))  define("ROUTE_CLAN", "clan");
if(!defined("ROUTE_CLANWARS"))  define("ROUTE_CLANWARS", "clanwars");
if(!defined("ROUTE_SETTINGS"))  define("ROUTE_SETTINGS", "settings");
if(!defined("ROUTE_LOGIN"))  define("ROUTE_LOGIN", "login");
if(!defined("ROUTE_LOGOUT"))  define("ROUTE_LOGOUT", "logout");
if(!defined("ROUTE_IMPRINT"))  define("ROUTE_IMPRINT", "imprint");
if(!defined("ROUTE_BOARD"))  define("ROUTE_BOARD", "board");

if(!defined("ROUTETYPE_DEFAULT"))  define("ROUTETYPE_DEFAULT", 0);
if(!defined("ROUTETYPE_BOARD"))  define("ROUTETYPE_BOARD", 1);

if(!defined("URL_ROOT"))  define("URL_ROOT", "http://".$_SERVER["HTTP_HOST"]."/wot2/");
if(!defined("URL_REDIRECT_LOGIN"))  define("URL_REDIRECT_LOGIN", "http://".$_SERVER["HTTP_HOST"]."/wot2/login/");
