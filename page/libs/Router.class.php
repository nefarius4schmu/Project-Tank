<?php
/**
* Project Tank Webpage
* webpage router class
* @author Steffen Lange
*/
_def("router");
class Router{
	private static $default = ROUTE_START;
	private static $defaultLogged = ROUTE_HOME;
	private static $loginRedirectURL = URL_REDIRECT_LOGIN;
	private static $defaultRedirectURL = URL_ROOT;
	
	private static $defaultType = ROUTETYPE_DEFAULT;
	
	private static $locations = [
		ROUTE_START=>[
			"login"=>false,
		],
		ROUTE_HOME=>[
			"login"=>true,
			"type"=>ROUTETYPE_BOARD,	
		],
		ROUTE_EVENTS=>[
			"login"=>true,
			"type"=>ROUTETYPE_BOARD,
		],
		ROUTE_CLAN=>[
			"login"=>true,
			"type"=>ROUTETYPE_BOARD,
		],
		ROUTE_CLANWARS=>[
			"login"=>true,
			"type"=>ROUTETYPE_BOARD,
		],
		ROUTE_SETTINGS=>[
			"login"=>true,
			"type"=>ROUTETYPE_BOARD,
		],
		ROUTE_LOGIN=>[
			"login"=>false,
			"redirect"=>[
				"delay"=>3,
				"error"=>ERROR_LOGIN_GET_URL,
				"url"=>null,
				"type"=>"wotLogin",
				"name"=>"worldoftanks.eu",
			],
		],
		ROUTE_LOGOUT=>[
			"login"=>true,
		],
		ROUTE_IMPRINT=>[
			"login"=>false,
		],
	];
	
	/* ===================================================================================== */
	
	private static function reqLogin($loc){
		return isset(self::$locations[$loc]["login"]) && self::$locations[$loc]["login"];
	}
	
	private static function isLocation($loc){
		return isset(self::$locations[$loc]);
	}
	
	private static function hasRedirect($loc){
		return self::isLocation($loc) && isset(self::$locations[$loc]["redirect"]);
	}
	
	private static function hasType($loc){
		return self::isLocation($loc) && isset(self::$locations[$loc]["type"]);
	}
	
	/* ===================================================================================== */
	
	public static function getLocation($loc, $isLogin=false){
		if(!self::isLocation($loc) || (!$isLogin && self::reqLogin($loc))) return self::getDefault($isLogin);
		return $loc;
	}
	
	public static function getDefault($isLogin=false){
		return $isLogin ? self::$defaultLogged : self::$default;
	}
	
	public static function getType($loc){
		return self::hasType($loc) 
			? self::$locations[$loc]["type"] 
			: self::$defaultType;
	}
	
	public static function getLoginRedirectURL(){
		return self::$loginRedirectURL;
	}
	
	public static function getDefaultRedirectURL(){
		return self::$defaultRedirectURL;
	}
	
	public static function getRedirectData($loc){
		return self::hasRedirect($loc) 
			? self::$locations[$loc]["redirect"] 
			: false;
	}
	
}