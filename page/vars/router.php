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
if(!defined("ROUTE_SET"))  define("ROUTE_SET", "set");
if(!defined("ROUTE_GET"))  define("ROUTE_GET", "get");
if(!defined("ROUTE_POST"))  define("ROUTE_POST", "post");
if(!defined("ROUTE_NEWS"))  define("ROUTE_NEWS", "news");
if(!defined("ROUTE_SHOW_NEWS"))  define("ROUTE_SHOW_NEWS", "news/show");
if(!defined("ROUTE_CREATOR_NEWS"))  define("ROUTE_CREATOR_NEWS", "creator/news");
if(!defined("ROUTE_DELETE_NEWS"))  define("ROUTE_DELETE_NEWS", "delete/news");
//if(!defined("ROUTE_CREATOR_NEWS"))  define("ROUTE_CREATOR_NEWS", "news/post");
//if(!defined("ROUTE_CREATOR_POST"))  define("ROUTE_CREATOR_POST", "creator/post/");

if(!defined("ROUTETYPE_DEFAULT"))  define("ROUTETYPE_DEFAULT", 0);
if(!defined("ROUTETYPE_BOARD"))  define("ROUTETYPE_BOARD", 1);

if(!defined("URL_ROOT"))  define("URL_ROOT", "http://".$_SERVER["HTTP_HOST"]."/wot2/");
if(!defined("URL_REDIRECT_LOGIN"))  define("URL_REDIRECT_LOGIN", "http://".$_SERVER["HTTP_HOST"]."/wot2/login/");
