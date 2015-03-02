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
		ROUTE_START=>[],
		ROUTE_IMPRINT=>[],
		ROUTE_HOME=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_EVENTS=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_CLAN=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
				"clan"=>true,
			],
		],
		ROUTE_CLANWARS=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
				"clan"=>true,
			],
		],
		ROUTE_SETTINGS=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_NEWS=>[
			"type"=>ROUTETYPE_BOARD,
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_LOGIN=>[
			"redirect"=>[
				"delay"=>3,
				"error"=>ERROR_LOGIN_GET_URL,
				"url"=>null,
				"type"=>"wotLogin",
				"name"=>"worldoftanks.eu",
			],
		],
		ROUTE_LOGOUT=>[
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_SET=>[
			"loc"=>"jobs/set/",
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_GET=>[
			"loc"=>"jobs/get/",
			"req"=>[
				"login"=>true,
			],
		],
	];
	
	/* ===================================================================================== */
	
	private static function reqLogin($id){
		return self::hasReq($id, "login") && self::$routes[$id]["req"]["login"];
	}
	
	private static function reqClan($id){
		return self::hasReq($id, "clan") && self::$routes[$id]["req"]["clan"];
	}
	
	private static function isRoute($id){
		return isset(self::$routes[$id]);
	}
	
	private static function hasReq($id, $req){
		return self::isRoute($id) && isset(self::$routes[$id]["req"],self::$routes[$id]["req"][$req]);
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
	
	public static function getRoute($id, $isLogin=false, $isClan=false){
		if(!isset($id) 
			|| !self::isRoute($id) 
			|| (!$isLogin && self::reqLogin($id) ) 
			|| (!$isClan && self::reqClan($id)) 
		) return self::getDefault($isLogin);
		else 
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