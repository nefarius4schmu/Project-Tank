<?php
/**
* Project Tank Webpage
* handler for creating custom html elements
* @author Steffen Lange
*/
class Html{
    const TEXT_ELLEPSIS = '...';
    const NEWS_LG_SUMMARY_MAX_LEN = 250;
	/* ===================================================================================== */

    public static function css($urls){
        $tmp = "<link rel='stylesheet' type='text/css' href='{{url}}'/>\n";
        return !empty($urls) ? self::templateList($tmp, 'url', $urls) : null;
    }

    public static function js($urls){
        $tmp = "<script src='{{url}}'></script>\n";
        return !empty($urls) ? self::templateList($tmp, 'url', $urls) : null;
    }

	/* helper functions ==================================================================== */

	public static function toDataString($data){
		if(!is_array($data)) return null;
		$out = "";
		foreach($data as $name=>$value){
			$out .= " data-".$name."='".$value."'";
		}
		return $out;
	}

    public static function truncate($text, $maxlen, $options=[]){
        $removeLineBreak = isset($options["removeLineBreak"]) && $options["removeLineBreak"];
        $removeHtml = isset($options["removeHtml"]) && $options["removeHtml"];
        $ellipsis = !isset($options["ellipsis"]) ? ''
            : (is_string($options["ellipsis"]) ? $options["ellipsis"]
                : (is_bool($options["ellipsis"]) && $options["ellipsis"]) ? self::TEXT_ELLEPSIS
                    : '');

        if($removeLineBreak) $text = str_replace(["\n", "\r"], "", $text);
        if($removeHtml){
            $text = preg_replace("/(<\/[^>]+>)/", " ", $text);
            $text = preg_replace("/(<[^>]+>)/", "", $text);
        }
        $len = strlen($text);
        $eLen = strlen($ellipsis);
        $text = $len+$eLen > $maxlen ? trim(substr($text, 0, $maxlen)).$ellipsis : $text;
        return $text;
    }

    public static function clean($text, $options=[]){
        $useHyphens = isset($options["useHyphens"]) && $options["useHyphens"];
        $allowMultiHyphens = isset($options["allowMultiHyphens"]) && $options["allowMultiHyphens"];

        if($useHyphens) $text = str_replace(" ", "-", $text);
        $text = preg_replace("/[^A-Za-z0-9\-]/", "", $text);
        if(!$allowMultiHyphens) $text = preg_replace("/-+/", "-", $text);
        return $text;
    }

    public static function template($tmp, $data, $options=[]){
        $out = $tmp;
        $empty = isset($options["empty"]) && $options["empty"];
        $escape = isset($options["escape"]) && $options["escape"];
        $debug = isset($options["debug"]) && $options["debug"];
        $path = isset($options["path"]) ? trim($options["path"], ".") : null;
        if($debug) Debug::r($options);
        foreach($data as $key=>$value){
            if($debug){ Debug::s($key); Debug::e($value);}
            if(is_string($value)){
                $itemkey = isset($path) ? $path.".".$key : $key;
                if($escape) $value = htmlentities($value);
                if($debug) Debug::i("set string: ".$itemkey."=>".$value);
                $out = preg_replace('({{'.$itemkey.'}})',$value,$out);
            }
            else if(is_array($value)){
                if($debug) Debug::i("refactor array");
                $itempath = isset($path) ? $path.".".$key : $key;
                $out = self::template($out, $value, ["path"=>$itempath, "debug"=>$debug]);
//                $path = null;
            }
        }
        if($empty) $out = self::template($out, ['[^}}]+'=>'']);
        return $out;
    }

    public static function templateList($tmp, $key, $list){
        if(is_string($list)) return self::template($tmp, [$key=>$list]);
        else{
            $out = "";
            foreach($list as $item){
                $out .= self::template($tmp, [$key=>$item]);
            }
            return $out;
        }
    }

    public static function isget($v1, $v2, $true=" active", $false=null){
        return $v1 == $v2 ? $true : $false;
    }

    public static function getNewsCatLang($name){
        switch($name){
            case "wot": return "WoT";
            case "clan": return "Clan";
            case "others": return "Andere";
        }
        return "Unbekannt";
    }

    public static function getNewsActionLang($action){
        switch($action){
            case "create": return "Ihr Beitrag wird erstellt...";
            case "edit": return "Ihre &Auml;nderungen werden &uuml;bernommen...";
            case "delete": return "Ihr Beitrag wird entfernt...";
        }
        return "Aktion wird ausgef&uml;hrt...";
    }

    /* ===================================================================================== */

    public static function createFaImg($options=[]){
        if(is_string($options)) return "<i class='fa fa-".$options."'></i>";
        $type = isset($options["type"]) ? " fa-".$options["type"] : null;
        $class = isset($options["class"]) ? " ".$options["class"] : null;
        $content = isset($options["content"]) ? $options["content"] : null;
        return "<i class='fa".$type.$class."'>".$content."</i>";
    }

    public static function createSwitchButton($id, $options, $inRow=false){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;
		
		$html = "<div class='btn-switch' id='".$id."'>";
		if($hasElements){
			$count = 0;
			foreach($options["elements"] as $txt=>$value){
				$active = $activeValue != $value ? "" : " checked";
				$html .= "<input id='".$id."_switch_".$count."' type='radio' value='".$value."'".$inputName.$active."/><label for='".$id."_switch_".$count."'>".$txt."</label>";
				$count++;
			}
		}
		$html .= "</div>";	
		if($inRow) return self::createSettingsRow(null, $class, $title, $descr, $html);
		else return $html;
	}

    public static function createNavList($items){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;

		$html = "<div class='btn-switch' id='".$id."'>";
		if($hasElements){
			$count = 0;
			foreach($options["elements"] as $txt=>$value){
				$active = $activeValue != $value ? "" : " checked";
				$html .= "<input id='".$id."_switch_".$count."' type='radio' value='".$value."'".$inputName.$active."/><label for='".$id."_switch_".$count."'>".$txt."</label>";
				$count++;
			}
		}
		$html .= "</div>";
		if($inRow) return self::createSettingsRow(null, $class, $title, $descr, $html);
		else return $html;
	}

	public static function createDataSelect($id, $options, $inRow=false){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;
		
		$html = "<select id='".$id."' class='data-select vertical-align'".$inputName.">";
		if($hasElements)
			foreach($options["elements"] as $option){
				$txt = isset($option["text"]) ? $option["text"] : null;
				$value = isset($option["value"]) ? " value='".$option["value"]."'" : null;
				$data = isset($option["data"]) ? self::toDataString($option["data"]) : null;
				$active = isset($option["value"]) && $activeValue != $option["value"] ? "" : " selected";
				$html .= "<option".$value.$data.$active.">".$txt."</option>";
			}
		$html .= "</select>";
		
		if($inRow) return self::createSettingsRow(null, $class, $title, $descr, $html);
		else return $html;
	}
	
	public static function createSettingsRow($id=null, $class=null, $title=null, $descr=null, $content=null){
		$htmlID = isset($id) ? " id='$id'" : null;
		$htmlClass = isset($class) ? " $class" : null;
		return "<ul".$htmlID." class='row row-table".$htmlClass."'>
			<li class='col-md-6'>
				<h5>$title</h5>
				<small>$descr</small>
			</li>
			<li class='col-md-6'>$content</li>
		</ul>";
	}
	
	public static function createBoardInfo($options){
		$id = isset($options["id"]) ? " id='".$options["id"]."'" : null;
		$title = isset($options["title"]) ? $options["title"] : null;
		$type = isset($options["type"]) ? "-".$options["type"] : null;
		$class = isset($options["class"]) ? " ".$options["class"] : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		
		$rows = "";
		if($hasElements) foreach($options["elements"] as $e){
			$eTitle = isset($e["title"]) ? $e["title"] : null;
			$eContent = isset($e["content"]) ? $e["content"] : null;
			$disabled = isset($e["disabled"]) && $e["disabled"] ? " disabled": null;
			$faimg = isset($e["faimg"]) ? self::createFaImg($e["faimg"]) : null;
			$rows .= "<li class='row".$disabled."'>".$faimg.$eTitle."<span class='pull-right'>".$eContent."</span></li>";
		}
		
		return "<div".$id." class='board-info".$type.$class."'>
			<h6>".$title."</h6>
			<ul>".$rows."</ul>
		</div>";
	}

    /**
     * @param WotPlayer $player
     * @param array $news
     * @param array $options
     * @return string
     */
	public static function createNewsLg($player, $news, $options=[]){
		if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
		if(isset($options["class"])) $options["class"] =  ' '.$options["class"];

        $news["url"] = isset($news["uid"]) ? URL_ROOT.ROUTE_SHOW_NEWS.'/'.$news["uid"] : "#";
		if(isset($news["clantag"])) $news["clantag"] =  '['.$news["clantag"].']';
        if(!isset($news["summary"]) || empty($news["summary"])) $news["summary"] = self::truncate($news["text"], self::NEWS_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);
        if(isset($news["coverimage"]))
            $news["image"] = '<img src="'.$news["coverimage"].'" alt="news_cover"/>';

        $news["created"] = isset($news["created"]) ? date('d.m.y', strtotime($news["created"])) : "Unbekannt";

        // check if user can edit post
        if(isset($news["canEdit"]) && $news["canEdit"]){
            // edit news
            $menu = ["url"=>URL_ROOT.ROUTE_CREATOR_NEWS.'/'.$news["uid"]];
            $menu = array_merge(self::$tmpDefaults, $menu);
            $news["menu"] = self::template(self::TMP_NEWS_MENU_EDIT, $menu, ["empty"=>true]);

            // delete news
            $menu["url"] = URL_ROOT.ROUTE_DELETE_NEWS.'/'.$news["uid"];
            $news["menu"] .= self::template(self::TMP_NEWS_MENU_DELETE, $menu, ["empty"=>true]);
        }

        $data = array_merge(self::$tmpDefaults, $news, $options);
//        Debug::r($data);
		return self::template(self::TMP_NEWS_LG, $data, ["empty"=>true]);
	}

    /**
     * @param WotPlayer $player
     * @param array $news
     * @param array $options
     * @return string
     */
	public static function createNewsFeatured($player, $news, $options = []){
		if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
		if(isset($options["class"])) $options["class"] =  ' '.$options["class"];

        $news["url"] = isset($news["uid"]) ? URL_ROOT.ROUTE_SHOW_NEWS.'/'.$news["uid"] : "#";
//		if(isset($news["clantag"])) $news["clantag"] =  '['.$news["clantag"].']';
        if(!isset($news["summary"]) && isset($news["text"])) $news["summary"] = self::truncate($news["text"], self::NEWS_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);
        if(isset($news["coverimage"]))
            $news["attributes"] = " background-image: url(".$news["coverimage"].")";//'<img src="'.$news["imageurl"].'" alt="news_cover"/>';

//        $news["created"] = isset($news["created"]) ? date('d.m.y', strtotime($news["created"])) : "Unbekannt";

        $data = array_merge(self::$tmpDefaults, $news, $options);
//        Debug::r($data);
		return self::template(self::TMP_NEWS_FEATURED, $data, ["empty"=>true]);
	}

    /**
     * @param WotPlayer $player
     * @param array $news
     * @param array $options
     * @return string
     */
	public static function createNewsFull($player, $news, $options=[]){
		if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
		if(isset($options["class"])) $options["class"] =  ' '.$options["class"];

//        $news["url"] = isset($news["uid"]) ? URL_ROOT.ROUTE_SHOW_NEWS.'/'.$news["uid"] : "#";
		if(isset($news["clantag"])) $news["clantag"] =  '['.$news["clantag"].']';
//        if(!isset($news["summary"])) $news["summary"] = self::truncate($news["text"], self::NEWS_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);
//        if(isset($news["imageurl"]))
//            $news["image"] = '<img src="'.$news["imageurl"].'" alt="news_cover"/>';

        $news["created"] = isset($news["created"]) ? date('d.m.y', strtotime($news["created"])) : "Unbekannt";

        // check if user can edit post
        if($news["userID"] == $player->getID()){
            // edit news
            $menu = ["url"=>URL_ROOT.ROUTE_CREATOR_NEWS.'/'.$news["uid"]];
            $menu = array_merge(self::$tmpDefaults, $menu);
            $news["menu"] = self::template(self::TMP_NEWS_MENU_EDIT, $menu, ["empty"=>true]);

            // delete news
            $menu["url"] = URL_ROOT.ROUTE_DELETE_NEWS.'/'.$news["uid"];
            $news["menu"] .= self::template(self::TMP_NEWS_MENU_DELETE, $menu, ["empty"=>true]);
        }

        $data = array_merge(self::$tmpDefaults, $options, $news);
//        Debug::r($data);
        $options = ["empty"=>true];
		return self::template(self::TMP_NEWS_FULL, $data, $options);
	}

    /**
     * @param WotPlayer $player
     * @param array $news
     * @param array $options
     * @return string
     */
    public static function createEventFeatured($player, $event, $options = []){
        if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
        if(isset($options["class"])) $options["class"] =  ' '.$options["class"];

        $data = array_merge(self::$tmpDefaults, $event, $options);
        return self::template(self::TMP_NEWS_FEATURED, $data, ["empty"=>true]);
    }

    /* templates =========================================================================== */

    private static $tmpDefaults = [
        "url"=>"#",
        "lang"=>[
            "edit"=>"Bearbeiten",
            "delete"=>"L&oumlschen",
//            "more"=>"weiter",
//            "from"=>"@",
        ]
    ];

    //<a class="more" href="{{url}}"><i class="fa fa-fw fa-long-arrow-right"></i>{{post.more}}</a>
    const TMP_NEWS_MENU_EDIT = '<a class="news-edit{{class}}" href="{{url}}">{{lang.edit}}</a>';
    const TMP_NEWS_MENU_DELETE = '<a class="news-delete{{class}}" href="{{url}}">{{lang.delete}}</a>';
    const TMP_NEWS_LG = '<div class="news news-lg c-default{{class}}">
            <div class="news-wrapper">
                <a href="{{url}}">
                    <h2>{{title}}</h2>
                    <div class="text-wrapper">
                        <div class="cover">{{image}}</div>
                        <h3 class="summary">{{summary}}</h3>
                    </div>
                </a>
                <div class="meta">
                    <span class="date">{{lang.from}}{{created}}</span>
                    <span class="user">{{user}}</span>
                    <span class="clan">{{clantag}}</span>
                </div>
                <div class="info">
                    <span class="viewed"><i class="fa fa-fw fa-eye"></i>{{views}}</span>
                    <span class="menu pull-left">{{menu}}</span>
                </div>
            </div>
        </div>';

    const TMP_NEWS_FULL = '<div class="news news-full c-default{{class}}">
            <div class="news-wrapper">
                <h2>{{title}}</h2>
                <h3 class="summary">{{summary}}</h3>
                <div class="text">{{text}}</div>
                <div class="meta">
                    <span class="date">{{lang.from}}{{created}}</span>
                    <span class="user">{{user}}</span>
                    <span class="clan">{{clantag}}</span>
                </div>
                <div class="info">
                    <span class="menu pull-left">{{menu}}</span>
                </div>
            </div>
        </div>';

    const TMP_NEWS_FEATURED = '<div class="news news-featured">
            <div class="featured-wrapper"{{attributes}}>
                <a class="summary" href="{{url}}">
                    <h3>{{title}}</h3>
                    <span>{{summary}}</span>
                </a>
            </div>
        </div>';

}