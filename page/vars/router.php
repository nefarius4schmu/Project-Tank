<?php
/**
* Project Tank Webpage
* definitions
* @author Steffen Lange
*/
if(!defined("ROUTE_START"))  define("ROUTE_START", "start");
if(!defined("ROUTE_HOME"))  define("ROUTE_HOME", "home");
if(!defined("ROUTE_CLAN"))  define("ROUTE_CLAN", "clan");
if(!defined("ROUTE_CLANWARS"))  define("ROUTE_CLANWARS", "clanwars");
if(!defined("ROUTE_SETTINGS"))  define("ROUTE_SETTINGS", "settings");
if(!defined("ROUTE_LOGIN"))  define("ROUTE_LOGIN", "login");
if(!defined("ROUTE_LOGOUT"))  define("ROUTE_LOGOUT", "logout");
if(!defined("ROUTE_IMPRINT"))  define("ROUTE_IMPRINT", "imprint");
if(!defined("ROUTE_BOARD"))  define("ROUTE_BOARD", "board");
if(!defined("ROUTE_CONSTRUCTIONS"))  define("ROUTE_CONSTRUCTIONS", "constructions");

if(!defined("ROUTE_SET_SETTINGS"))  define("ROUTE_SET_SETTINGS", "set/settings");
if(!defined("ROUTE_GET"))  define("ROUTE_GET", "get");

if(!defined("ROUTE_NEWS"))  define("ROUTE_NEWS", "news");
if(!defined("ROUTE_POST_NEWS"))  define("ROUTE_POST_NEWS", "post/news");
if(!defined("ROUTE_SHOW_NEWS"))  define("ROUTE_SHOW_NEWS", "show/news");
if(!defined("ROUTE_CREATOR_NEWS"))  define("ROUTE_CREATOR_NEWS", "edit/news");
if(!defined("ROUTE_DELETE_NEWS"))  define("ROUTE_DELETE_NEWS", "delete/news");

if(!defined("ROUTE_EVENTS"))  define("ROUTE_EVENTS", "events");
if(!defined("ROUTE_EVENT_POST"))  define("ROUTE_EVENT_POST", "post/events");
if(!defined("ROUTE_EVENT_SHOW"))  define("ROUTE_EVENT_SHOW", "show/events");
if(!defined("ROUTE_EVENT_EDITOR"))  define("ROUTE_EVENT_EDITOR", "edit/events");
if(!defined("ROUTE_EVENT_DELETE"))  define("ROUTE_EVENT_DELETE", "delete/events");
if(!defined("ROUTE_EVENT_NEW"))  define("ROUTE_EVENT_NEW", "new/events");
if(!defined("ROUTE_EVENT_JOIN"))  define("ROUTE_EVENT_JOIN", "join/events");
if(!defined("ROUTE_EVENT_LEAVE"))  define("ROUTE_EVENT_LEAVE", "leave/events");

if(!defined("ROUTETYPE_DEFAULT"))  define("ROUTETYPE_DEFAULT", 0);
if(!defined("ROUTETYPE_BOARD"))  define("ROUTETYPE_BOARD", 1);

if(!defined("URL_ROOT"))  define("URL_ROOT", "http://".$_SERVER["HTTP_HOST"]."/wot2/");
if(!defined("URL_REDIRECT_LOGIN"))  define("URL_REDIRECT_LOGIN", "http://".$_SERVER["HTTP_HOST"]."/wot2/login/");
if(!defined("URL_REDIRECT_LOGIN_OFFLINE"))  define("URL_REDIRECT_LOGIN_OFFLINE", "http://".$_SERVER["HTTP_HOST"]."/wot2/login?offline=1");
if(!defined("URL_REDIRECT_BRIEFING"))  define("URL_REDIRECT_BRIEFING", "http://".$_SERVER["HTTP_HOST"]."/wot2/events/briefing/");
