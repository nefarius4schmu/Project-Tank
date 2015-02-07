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
	
	private static $routes = [
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
		ROUTE_SET=>[
			"login"=>true,
			"loc"=>"jobs/set/",
		],
		ROUTE_IMPRINT=>[
			"login"=>false,
		],
	];
	
	/* ===================================================================================== */
	
	private static function reqLogin($id){
		return isset(self::$routes[$id]["login"]) && self::$routes[$id]["login"];
	}
	
	private static function isRoute($id){
		return isset(self::$routes[$id]);
	}
	
	private static function hasRedirect($id){
		return self::isRoute($id) && isset(self::$routes[$id]["redirect"]);
	}
	
	private static function hasType($id){
		return self::isRoute($id) && isset(self::$routes[$id]["type"]);
	}
	
	private static function hasLocation($id){
		return self::isRoute($id) && isset(self::$routes[$id]["loc"]);
	}
	
	/* ===================================================================================== */
	
	public static function getRoute($id, $isLogin=false){
		if(!isset($id) || !self::isRoute($id) || (!$isLogin && self::reqLogin($id))) return self::getDefault($isLogin);
		return $id;
	}
	
	public static function getLocation($id){
		return self::hasLocation($id) ? self::$routes[$id]["loc"] : $id;
	}
	
	public static function getDefault($isLogin=false){
		return $isLogin ? self::$defaultLogged : self::$default;
	}
	
	public static function getType($id){
		return self::hasType($id) 
			? self::$routes[$id]["type"] 
			: self::$defaultType;
	}
	
	public static function getLoginRedirectURL(){
		return self::$loginRedirectURL;
	}
	
	public static function getDefaultRedirectURL(){
		return self::$defaultRedirectURL;
	}
	
	public static function getRedirectData($id){
		return self::hasRedirect($id) 
			? self::$routes[$id]["redirect"] 
			: false;
	}
	
	public static function getBreadcrumbs(){
		$loc = $_SERVER["REDIRECT_URL"];
		$crumbs =  explode("/", $loc);
		$out = [ROUTE_HOME=>self::getLocation(ROUTE_HOME)];
		foreach($crumbs as $crumb){
			if(self::isRoute($crumb)) $out[$crumb] = self::getLocation($crumb);
		}
		return $out;
	}
}