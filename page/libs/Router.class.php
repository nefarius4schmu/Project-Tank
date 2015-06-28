<?php
/**
* Project Tank Webpage
* webpage router class
* @author Steffen Lange
*/
_def("router");
class Router{
    const OFFLINE = false;
    const CONSTRUCTIONS = false;

    const CSS_FONT_OPENSANS = "//fonts.googleapis.com/css?family=Open+Sans:400,300,700";
    const CSS_FONT_LORA = "//fonts.googleapis.com/css?family=Lora:400,400italic";
    const CSS_LIB_IMAGEPICKER = "js/imagepicker/image-picker.css?024";
    const CSS_LIB_BOOTSTRAP = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css?332";
    const CSS_LIB_FA = "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css?430";
    const CSS_LIB_DATETIMEPICKER = "js/datetimepicker/bootstrap-datetimepicker.min.css?400";
    const CSS_CLAN = "css/clan.css?002";
    const CSS_NEWS = "css/news.css?002";
    const CSS_CREATOR = "css/creator.css?003";
    const CSS_EVENTS = "css/events.css?002";
    const CSS_BOARD = "css/board.css?011'";
    const CSS_BASE = "css/base.css?002'";
    const CSS_INDEX = "css/index.css?002'";
    const CSS_IMPRINT = "css/imprint.css?002'";

    const JS_LIB_JQUERY = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js?1112";
    const JS_LIB_TABLESORTER = "js/jquery.tablesorter.min.js?100";
    const JS_LIB_DDSLICK = "js/jquery.ddslick.min.js?200";
    const JS_LIB_CKEDITOR = "//cdn.ckeditor.com/4.4.7/standard/ckeditor.js?447";
    const JS_LIB_IMAGEPICKER = "js/imagepicker/image-picker.min.js?024";
    const JS_LIB_AJAX = "js/ajax.js?100";
    const JS_LIB_JSTOOLS = "js/jsTools-1.0.2.js?103";
    const JS_LIB_DATETIMEPICKER = "js/datetimepicker/bootstrap-datetimepicker.min.js?400";
    const JS_LIB_MOMENT = "js/moment/moment.min.js?2103";
    const JS_LIB_MOMENT_DE = "js/moment/de.js?2103";
    const JS_LIB_BOOTSTRAP_TC = "js/bootstrap/bootstrap.tc.min.js?334";
    const JS_BOARD = "js/board.js?004";
    const JS_CLAN = "js/clan.js?001";
    const JS_SETTINGS = "js/settings.js?001";
    const JS_CREATOR_NEWS = "js/creator.news.js?004";
    const JS_CREATOR_EVENTS = "js/creator.events.js?005";

	private static $default = ROUTE_START;
	private static $defaultLogged = ROUTE_HOME;
	private static $loginRedirectURL = URL_REDIRECT_LOGIN;
	private static $defaultRedirectURL = URL_ROOT;
	
	private static $defaultType = ROUTETYPE_DEFAULT;

	private static $cAdminKey = 'f1603cbefa1a87a26c10e75d466ec7bb72fb2a9c';

	private static $routes = [
		ROUTE_START=>[],
		ROUTE_IMPRINT=>[],
		ROUTE_HOME=>[
			"type"=>ROUTETYPE_BOARD,
            "title"=>"Home",
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_CLAN=>[
			"type"=>ROUTETYPE_BOARD,
            "title"=>"Clan",
			"req"=>[
				"login"=>true,
				"clan"=>true,
			],
            "css"=>[self::CSS_CLAN],
            "js"=>[self::JS_LIB_TABLESORTER,self::JS_CLAN]
		],
		ROUTE_CLANWARS=>[
			"type"=>ROUTETYPE_BOARD,
            "title"=>"Clanwars",
			"req"=>[
				"login"=>true,
				"clan"=>true,
			],
		],
		ROUTE_SETTINGS=>[
			"type"=>ROUTETYPE_BOARD,
            "title"=>"Einstellungen",
			"req"=>[
				"login"=>true,
			],
            "js"=>[self::JS_LIB_DDSLICK,self::JS_SETTINGS],
		],
		ROUTE_LOGIN=>[
			"redirect"=>[
                "offline"=>self::OFFLINE,
				"delay"=>3,
				"error"=>ERROR_LOGIN_GET_URL,
				"url"=>null,
                "offlineUrl"=>URL_REDIRECT_LOGIN_OFFLINE,
				"type"=>"wotLogin",
				"name"=>"worldoftanks.eu",
			],
		],
        ROUTE_CONSTRUCTIONS=>[
            "loc"=>"construction.php",
		],
		ROUTE_LOGOUT=>[
			"req"=>[
				"login"=>true,
			],
		],
		ROUTE_SET_SETTINGS=>[
			"loc"=>"jobs/settings/set.php",
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
        ROUTE_NEWS=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"News",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[self::CSS_NEWS],
        ],
        ROUTE_SHOW_NEWS=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"",
            "loc"=>"news/show.php",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[self::CSS_NEWS],
        ],
        ROUTE_CREATOR_NEWS=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"",
            "loc"=>"news/edit.php",
            "req"=>[
                "login"=>true,
                "settings"=>["3"=>"1"],
            ],
            "css"=>[self::CSS_LIB_IMAGEPICKER,self::CSS_CREATOR],
            "js"=>[self::JS_LIB_CKEDITOR,self::JS_LIB_IMAGEPICKER,self::JS_CREATOR_NEWS],
        ],
        ROUTE_POST_NEWS=>[
			"loc"=>"jobs/news/post.php",
			"req"=>[
				"login"=>true,
                "settings"=>["3"=>"1"],
			],
		],
        ROUTE_DELETE_NEWS=>[
			"loc"=>"jobs/news/del.php",
			"req"=>[
				"login"=>true,
			],
		],
        ROUTE_EVENTS=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"Events",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[self::CSS_EVENTS]
        ],
        ROUTE_EVENT_SHOW=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"",
            "loc"=>"events/show.php",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[],
        ],
        ROUTE_EVENT_EDITOR=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"",
            "loc"=>"events/edit.php",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[self::CSS_LIB_DATETIMEPICKER, self::CSS_CREATOR],
            "js"=>[self::JS_LIB_CKEDITOR, self::JS_LIB_DDSLICK,
                self::JS_LIB_BOOTSTRAP_TC,self::JS_LIB_DATETIMEPICKER,
                self::JS_LIB_JSTOOLS,self::JS_CREATOR_EVENTS
            ],
        ],
        ROUTE_EVENT_NEW=>[
            "type"=>ROUTETYPE_BOARD,
            "title"=>"",
            "loc"=>"events/new.php",
            "req"=>[
                "login"=>true,
            ],
            "css"=>[self::CSS_LIB_DATETIMEPICKER, self::CSS_CREATOR],
            "js"=>[self::JS_LIB_CKEDITOR, self::JS_LIB_DDSLICK,
                self::JS_LIB_BOOTSTRAP_TC,self::JS_LIB_DATETIMEPICKER,
                self::JS_LIB_JSTOOLS,self::JS_CREATOR_EVENTS
            ],
        ],
        ROUTE_EVENT_POST=>[
            "loc"=>"jobs/events/post.php",
            "req"=>[
                "login"=>true,
            ],
        ],
        ROUTE_EVENT_DELETE=>[
            "loc"=>"jobs/events/del.php",
            "req"=>[
                "login"=>true,
            ],
        ],
        ROUTE_EVENT_JOIN=>[
            "loc"=>"jobs/events/join.php",
            "req"=>[
                "login"=>true,
            ],
        ],
        ROUTE_EVENT_LEAVE=>[
            "loc"=>"jobs/events/leave.php",
            "req"=>[
                "login"=>true,
            ],
        ],
	];

    private static $sidemap = [
        ROUTE_HOME=>[
            ROUTE_NEWS=>[ROUTE_CREATOR_NEWS],
            ROUTE_EVENTS,
            ROUTE_CLAN,
            ROUTE_CLANWARS,
            ROUTE_SETTINGS,
        ],
        ROUTE_IMPRINT=>[],
        ROUTE_START=>[],
    ];

    private static $default_css = [
        ROUTETYPE_BOARD=>[
            self::CSS_FONT_OPENSANS, self::CSS_FONT_LORA,
            self::CSS_LIB_BOOTSTRAP, self::CSS_LIB_FA,
            self::CSS_BOARD
        ],
    ];

    private static $default_js = [
        ROUTETYPE_BOARD=>[
            self::JS_LIB_JQUERY, self::JS_LIB_AJAX,
            self::JS_LIB_MOMENT, self::JS_LIB_MOMENT_DE,
            self::JS_BOARD
        ]
    ];
	
	/* ===================================================================================== */
	
	private static function reqLogin($id){
		return self::hasReq($id, "login") && self::$routes[$id]["req"]["login"];
	}
	
	private static function reqClan($id){
		return self::hasReq($id, "clan") && self::$routes[$id]["req"]["clan"];
	}

    private static function reqMatchSettings($id, $settings){
        $hasReq = self::hasReq($id, "settings");
        if(!$hasReq) return true;
        else if($settings === false) return false;

        $reqs = self::$routes[$id]["req"]["settings"];
        foreach($reqs as $key=>$value)
            if(!isset($settings[$key]) || !$settings[$key] == $value) return false;
        return true;
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

    private static function hasCSS($id){
        return self::isRoute($id) && isset(self::$routes[$id]["css"]);
    }

    private static function hasJS($id){
        return self::isRoute($id) && isset(self::$routes[$id]["js"]);
    }

    private static function isValidCAdmin($key){
        return $key === self::$cAdminKey;
    }

    /* ===================================================================================== */
	
	public static function getRoute($id, $options=[]){
        $isLogin = isset($options["login"]) && is_bool($options["login"]) && $options["login"];
        $isClan = isset($options["clan"]) && is_bool($options["clan"]) && $options["clan"];
        $settings = isset($options["settings"]) && is_array($options["settings"]) ? $options["settings"] : false;
        $cAdmin = isset($options["cAdmin"]) && is_string($options["cAdmin"]) ? $options["cAdmin"] : false;
        if(self::CONSTRUCTIONS && !self::isValidCAdmin($cAdmin))
            return ROUTE_CONSTRUCTIONS;
		if(!isset($id)
			|| !self::isRoute($id) 
			|| (!$isLogin && self::reqLogin($id) ) 
			|| (!$isClan && self::reqClan($id))
            || !self::reqMatchSettings($id, $settings)
		) return self::getDefault($isLogin);
		else 
			return $id;
	}

	public static function canRoute($id, $options=[]){
        return $id === self::getRoute($id, $options);
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
		$loc = isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "?"));
		$crumbs =  explode("/", $loc);
		$out = [ROUTE_HOME=>self::getLocation(ROUTE_HOME)];
		foreach($crumbs as $crumb){
			if(self::isRoute($crumb)) $out[$crumb] = self::getLocation($crumb);
		}
		return $out;
	}

    public static function getCSS($id){
        return self::hasCSS($id)
            ? self::$routes[$id]["css"]
            : [];
    }

    public static function getJS($id){
        return self::hasJS($id)
            ? self::$routes[$id]["js"]
            : [];
    }

    public static function getDefaultCSS($type){
        return array_key_exists($type, self::$default_css)
            ? self::$default_css[$type]
            : null;
    }

    public static function getDefaultJS($type){
        return array_key_exists($type, self::$default_js)
            ? self::$default_js[$type]
            : null;
    }

}