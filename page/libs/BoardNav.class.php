<?php
/**
* Project Tank Webpage
* handler for webpage board navigation
* @author Steffen Lange
*/
_def("router");
class BoardNav{
	
	private static $navs = [
		ROUTE_HOME=>[
			"title"=>"Home",
			"order"=>0,
			"faimg"=>"fa-home",
		],
		ROUTE_EVENTS=>[
			"title"=>"Events",
			"order"=>10,
			"faimg"=>"fa-star",
		],
		ROUTE_CLAN=>[
			"title"=>"Clan",
			"order"=>20,
			"faimg"=>"fa-users",
			"req"=>[
				"clan"=>true,
			],

		],
		ROUTE_CLANWARS=>[
			"title"=>"Clanwars",
			"order"=>30,
			"faimg"=>"fa-trophy",
			"req"=>[
				"clan"=>true,
			],
		],
		ROUTE_NEWS=>[
			"title"=>"Neuigkeiten",
			"order"=>5,
			"faimg"=>"fa-info-circle",
		],
	];
	
	/* ===================================================================================== */
	
	private static function isNav($nav){
		return isset(self::$navs[$nav]);
	}
	
	private static function hasTitle($nav){
		return self::isNav($nav) && isset(self::$navs[$nav]["title"]);
	}
	
	private static function hasFaImg($nav){
		return self::isNav($nav) && isset(self::$navs[$nav]["faimg"]);
	}
	
	private static function hasOrder($nav){
		return self::isNav($nav) && isset(self::$navs[$nav]["order"]);
	}
	
	private static function hasReq($nav){
		return self::isNav($nav) && isset(self::$navs[$nav]["req"]);
	}

	/* ===================================================================================== */
	
	public static function getNavigations($ordered=false){
		$out = array_keys(self::$navs);
		if(!$ordered) return $out;
		
		usort($out, function($a, $b){
			return self::$navs[$a]["order"] - self::$navs[$b	]["order"];
		});
		
		return $out;
	}
	
	public static function getTitle($nav){
		return self::hasTitle($nav) 
			? self::$navs[$nav]["title"] 
			: $nav;
	}
	
	public static function getFaImg($nav){
		return self::hasFaImg($nav) 
			? self::$navs[$nav]["faimg"] 
			: null;
	}
	
	public static function getOrder($nav){
		return self::hasOrder($nav) 
			? self::$navs[$nav]["order"] 
			: null;
	}

	public static function hasReqClan($nav){
		return self::hasReq($nav) && isset(self::$navs[$nav]["req"]["clan"]) && self::$navs[$nav]["req"]["clan"];
	}

	
}