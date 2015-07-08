<?php
/**
* Project Tank Webpage
* handler for creating custom html elements
* @author Steffen Lange
*/
class Html{
    const TEXT_ELLEPSIS = '...';
    const NEWS_LG_SUMMARY_MAX_LEN = 250;
    const EVENT_LG_SUMMARY_MAX_LEN = 250;
    const EVENT_LG_COL_COUNT = 3;
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

    /**
     * @param array $arr
     * @return string
     */
    public static function toAttrStr($arr){
        $keys = array_keys($arr);
        $values = array_values($arr);
        $attr = array_map(function($k,$v){return $k.'="'.$v.'"';}, $keys, $values);
        return implode(' ', $attr);
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

    /**
     * @param string $tmp
     * @param mixed $data
     * @param array $options
     * @return string
     */
    public static function template($tmp, $data, $options=[]){
        $out = $tmp;
        $empty = isset($options["empty"]) && $options["empty"];
        $escape = isset($options["escape"]) && $options["escape"];
        $debug = isset($options["debug"]) && $options["debug"];
        $path = isset($options["path"]) ? trim($options["path"], ".") : null;
        if($debug) Debug::r($options);
        if(is_array($data)) {
            foreach ($data as $key => $value) {
                if ($debug) {
                    Debug::s($key);
                    Debug::e($value);
                }
                if (is_string($value) || is_numeric($value)) {
                    $itemkey = isset($path) ? $path . "." . $key : $key;
                    if ($escape) $value = htmlentities($value);
                    if ($debug) Debug::i("set string: " . $itemkey . "=>" . $value);
                    $out = preg_replace('({{' . $itemkey . '}})', $value, $out);
                } else if (is_array($value)) {
                    if ($debug) Debug::i("refactor array");
                    $itempath = isset($path) ? $path . "." . $key : $key;
                    $out = self::template($out, $value, ["path" => $itempath, "debug" => $debug]);
//                $path = null;
                }
            }
        }else if(is_string($data) || is_numeric($data)) {
            $out = self::template($out, ['[^}}]+' => $data], $options);
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

    public static function getEventActionLang($action){
        switch($action){
            case "create": return "Ihr Event wird erstellt...";
            case "edit": return "Ihre &Auml;nderungen werden &uuml;bernommen...";
            case "delete": return "Ihr Event wird entfernt...";
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

    /**
     * @param array $options
     * @return string
     */
    public static function createInput($options){
        $id = isset($options["id"]) ? ' id="'.$options["id"].'"' : null;
        $class = isset($options["class"]) ? ' class="'.$options["class"].'"' : null;
        $type = isset($options["type"]) ? $options["type"] : 'text';
        $name = isset($options["name"]) ? ' name="'.$options["name"].'"' : null;
        $value = isset($options["value"]) ? ' value="'.$options["value"].'"' : null;
        $selected = isset($options["selected"]) ? $options["selected"] : null;
        $checked = isset($options["checked"]) && $options["checked"]  ? " checked" : null;
        $disabled = isset($options["disabled"]) && $options["disabled"]  ? " disabled" : null;
        $elements = isset($options["elements"]) ? $options["elements"] : [];
        $label = isset($options["label"]) ? '<label>'.$options["label"].'</label>' : null;
        $attr = isset($options["attributes"]) ? self::toAttrStr($options["attributes"]): null;

        $input = null;
        switch($type){
            default: $input = '<input type="'.$type.'"'.$id.$class.$name.$value.$attr.$checked.$disabled.'/>'; break;
            case "datetime":
                $class = isset($options["class"]) ? $options["class"] : null;
                $input = '<div class="input-group datetime-picker">
                    <input type="text" class="form-control'.$class.'"'.$id.$name.$value.$attr.$disabled.' />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default"'.$disabled.'><i class="fa fa-calendar"></i></button>
                    </span>
                </div>';
                break;
            case "select":
                $input = 'undefined type';
                break;


        }
        return $label.$input;
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

    public static function createNavList($id, $options, $inRow=false){
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
			<li class='col-xs-6'>
				<h5>$title</h5>
				<small>$descr</small>
			</li>
			<li class='col-xs-6'>$content</li>
		</ul>";
	}

	public static function createSettingsRowParam($options=[]){
        $id = isset($options["id"]) ? $options["id"] : null;
        $class = isset($options["class"]) ? $options["class"] : null;
        $title = isset($options["title"]) ? $options["title"] : null;
        $descr = isset($options["descr"]) ? $options["descr"] : null;
        $content = isset($options["content"]) ? $options["content"] : null;
        return self::createSettingsRow($id, $class, $title, $descr, $content);
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
            $news["menu"] = self::template(self::TMP_POST_MENU_EDIT, $menu, ["empty"=>true]);

            // delete news
            $menu["url"] = URL_ROOT.ROUTE_DELETE_NEWS.'/'.$news["uid"];
            $news["menu"] .= self::template(self::TMP_POST_MENU_DELETE, $menu, ["empty"=>true]);
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
            $news["menu"] = self::template(self::TMP_POST_MENU_EDIT, $menu, ["empty"=>true]);

            // delete news
            $menu["url"] = URL_ROOT.ROUTE_DELETE_NEWS.'/'.$news["uid"];
            $news["menu"] .= self::template(self::TMP_POST_MENU_DELETE, $menu, ["empty"=>true]);
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

    /**
     * @param WotPlayer $player
     * @param array $event
     * @param array $eventType
     * @param array $mapNames
     * @param array $options
     * @return string
     */
    public static function createEventLg($player, $event, $eventType, $mapNames, $options=[]){
        if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
        $options["class"] = isset($options["class"]) ? ' '.$options["class"] : '';

        $options["class"] .= isset($eventType["typeClass"]) ? ' '.$eventType["typeClass"] : ' bs-callout-primary';
        if(isset($event["briefingID"])) $options["class"] .=  ' event-briefing';
        if(isset($event["userID"]) && $event["userID"] == $player->getID()) $options["class"] .=  ' event-owner';
        if(isset($event["end"]) && strtotime($event["end"]) > time()) $options["class"] .=  ' event-finished';


        $event["url"] = isset($event["uid"]) ? URL_ROOT.ROUTE_EVENT_SHOW.'/'.$event["uid"] : "#";
        if(isset($event["clantag"])) $event["clantag"] =  '['.$event["clantag"].']';
        if(!isset($event["summary"]) || empty($event["summary"])) $event["summary"] = self::truncate($event["text"], self::EVENT_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);

        // set map info
        $firstMap = isset($event["mapID"]) && isset($mapNames[$event["mapID"]]) ? '<img src="'.PATH_IMG_MAPS_SMALL.$mapNames[$event["mapID"]].'.jpg" alt="event_map"/>' : null;
        $mapSum = isset($event["mapsCount"]) && $event["mapsCount"] > 1 ? self::template(self::TMP_EVENT_MULTIMAP_DISPLAY, ["sum"=>$event["mapsCount"]*1-1]) : null;
        $event["map"] = [
            "first"=>$firstMap,
            "sum"=>$mapSum
        ];

        // prepare event times
        $time = isset($event["start"]) ? strtotime($event["start"]) : null;
        $day = isset($time) ? date('d',$time) : '?';
        $month = isset($time) ? date('M',$time) : 'Unbekannt';
        $event["start"] = ["day"=>$day, "month"=>$month];

        $event["created"] = isset($event["created"]) ? date('d.m.y', strtotime($event["created"])) : "Unbekannt";

        // prepare infos
        $userCount = isset($event["users"]) ? $event["users"] : 0;
        $maxUser = !isset($event["maxUsers"]) ? 0 : $event["maxUsers"];
        $event["users"] = [
            "count"=>self::template(self::TMP_EVENT_USER_SUBS, ["count"=>$userCount]),
            "max"=>$maxUser > 0 ? self::template(self::TMP_EVENT_USER_SUBS_MAX, ["max"=>$maxUser]) : null,
        ];
        $event["views"] = isset($event["views"]) ? $event["views"] : 0;

        // check if user can edit post
        $event["menu"] = "";
        $menu = self::$tmpDefaults;
        if(isset($options["canEdit"]) && $options["canEdit"]) {
            // edit news
            $menu["url"] = URL_ROOT . ROUTE_EVENT_EDITOR . '/' . $event["uid"];
            $event["menu"] = self::template(self::TMP_POST_MENU_EDIT, $menu, ["empty" => true]);
        }

        // check if user can delete post
        if(isset($options["canDelete"]) && $options["canDelete"]){
            // delete news
            $menu["url"] = URL_ROOT.ROUTE_EVENT_DELETE.'/'.$event["uid"];
            $event["menu"] .= self::template(self::TMP_POST_MENU_DELETE, $menu, ["empty"=>true]);
        }

        $data = array_merge(self::$tmpDefaults, $event, $options);
//        Debug::r($data);
        return self::template(self::TMP_EVENT_LG, $data, ["empty"=>true, "debug"=>false]);
    }

    /**
     * @param WotPlayer $player
     * @param array $event
     * @param array $mapList
     * @param array $options
     * @return string
     */
    public static function createEventFull($player, $event, $mapList, $gameModes, $options=[]){
        if(isset($options["id"])) $options["id"] = ' id="'.$options["id"].'"';
        $options["class"] = isset($options["class"]) ? ' '.$options["class"] : '';

        // prepare classes
        $isBriefing = isset($event["briefingID"], $event["briefingStart"]);
        $isStarted = isset($event["start"]) && strtotime($event["start"]) < time();
        $isFinished = isset($event["end"]) && strtotime($event["end"]) < time();
        if(isset($event["userID"]) && $event["userID"] == $player->getID()) $options["class"] .=  ' event-owner';
        if($isStarted) $options["class"] .=  ' event-closed';
        if($isFinished) $options["class"] .=  ' event-finished';
        if($isBriefing) $options["class"] .= " event-briefing";

        // prepare briefing
        $briefingStart = $isBriefing ? strtotime($event["briefingStart"]) : null;
        $isBriefingStarted = $isBriefing && $briefingStart <= time();
        $event["briefing"] = ["start"=>$event["briefingStart"]];

        // prepare briefing
//        if(isset($event["briefingID"], $event["briefingStart"])){
////            $startTime = strtotime($event["briefingStart"]);
////            $started = $startTime <= time();
//            $briefingData = [
//                "start"=>$event["briefingStart"],
//                "url"=>URL_REDIRECT_BRIEFING.$event["briefingID"],
//            ];
//            Debug::r($briefingData);
//            $tmp = $isBriefingStarted ? self::TMP_EVENT_ACTION_BRIEFING : self::TMP_EVENT_BRIEFINGACTION_TIME;
//
//            $event["briefing"] = Html::template($tmp, $briefingData, ["empty"=>true]);
//        }

        // event texts
        if(!isset($event["summary"]) || empty($event["summary"])) $event["summary"] = self::truncate($event["text"], self::EVENT_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);

        // events maps
        if(isset($event["maps"]) ){
            $maps = "";
            foreach($event["maps"] as $mapID){
                if(!isset($mapList[$mapID])) continue;
                $map = '<img src="'.PATH_IMG_MAPS_SMALL.$mapList[$mapID]["name"].'.jpg" alt="event_map"/>';
//                $name_i18n = htmlentities($mapList[$mapID]["name_i18n"], 'ISO-8859-1');
                $name_i18n = $mapList[$mapID]["name_i18n"];
//                $mode = isset($gameModes[], $map->getModeID(), "modeID");
//                $modeID = $mode !== null ? $mode["modeID"] : null;
//                $modeName = $mode !== null ? $mode["name_i18n"] : "Unbekannter Modus";
//                $mode_i18n =
                $maps .= Html::template(self::TMP_EVENT_FULL_MAP, ["map"=>$map, "name_i18n"=>$name_i18n]);
            }
            $event["maps"] = $maps;
        }

        // event prices
        if(isset($event["prices"]) ){
            $prices = "";
            foreach($event["prices"] as $price){
                // set rank display
                $rank = isset($price["rank_from"]) ? '{{lang.pricerank}}'.$price["rank_from"] : null;
                $rank .= isset($price["rank_to"]) && !empty($price["rank_to"])
                        ? '{{lang.priceranksep}}'.$price["rank_to"] : null;
                if(empty($rank)) $rank = "{{lang.unknown}}";
                // set gold price display
                $pricelist = isset($price["gold"]) && !empty($price["gold"])
                    ? self::template(self::TMP_EVENT_PRICE_GOLD, ["gold"=>$price["gold"]])
                    : "";

                $pricelist .= isset($price["others"]) && !empty($price["others"])
                    ? self::template(self::TMP_EVENT_PRICE_OTHERS, ["others"=>$price["others"]])
                    : null;

                $prices .= Html::template(self::TMP_EVENT_FULL_PRICE, ["rank"=>$rank, "prices"=>$pricelist]);
            }
            $event["prices"] = $prices;
        }

        // prepare event times
//        $time = isset($event["start"]) ? strtotime($event["start"]) : null;
//        $day = isset($time) ? date('d',$time) : '?';
//        $month = isset($time) ? date('M',$time) : 'Unbekannt';
//        $year = isset($time) ? date('Y',$time) : 'Unbekannt';
//        $event["start"] = ["day"=>$day, "month"=>$month, "year"=>$year];

        $event["created"] = isset($event["created"]) ? date('d.m.y', strtotime($event["created"])) : "Unbekannt";

        // event meta
        if(isset($event["clantag"])) $event["clantag"] =  '['.$event["clantag"].']';

        // prepare infos
        $userCount = isset($event["users"]) ? $event["users"] : 0;
        $maxUser = !isset($event["maxUsers"]) ? 0 : $event["maxUsers"];
        $event["users"] = [
            "count"=>self::template(self::TMP_EVENT_USER_SUBS, ["count"=>$userCount]),
            "max"=>$maxUser > 0 ? self::template(self::TMP_EVENT_USER_SUBS_MAX, ["max"=>$maxUser]) : null,
        ];
        $event["views"] = isset($event["views"]) ? $event["views"] : 0;

        // check if user can edit post
        $event["menu"] = "";
        $menu = self::$tmpDefaults;
        if(isset($options["canEdit"]) && $options["canEdit"]) {
            // edit news
            $menu["url"] = URL_ROOT . ROUTE_EVENT_EDITOR . '/' . $event["uid"];
            $event["menu"] = self::template(self::TMP_POST_MENU_EDIT, $menu);
        }

        // check if user can delete post
        if(isset($options["canDelete"]) && $options["canDelete"]){
            // delete news
            $menu["url"] = URL_ROOT.ROUTE_EVENT_DELETE.'/'.$event["uid"];
            $event["menu"] .= self::template(self::TMP_POST_MENU_DELETE, $menu);
        }

        // prepare actions
        $canJoin = !$isStarted && ($maxUser === 0 || $maxUser > $userCount);
        $isJoined = isset($options["isJoined"]) && $options["isJoined"];
        $event["actions"] = "";
        if($isFinished){
            $event["actions"] .= self::TMP_EVENT_ACTION_FINISHED;
        }else if($isStarted) {
            $event["actions"] .= self::TMP_EVENT_ACTION_CLOSED;
        }else if($isJoined) {
            $tmpData = ["url"=>URL_ROOT.ROUTE_EVENT_LEAVE.'/'.$event["uid"]];
            $event["actions"] .= self::template(self::TMP_EVENT_ACTION_LEAVE, $tmpData);
            // join briefing button
            if($isBriefing && $isBriefingStarted){
                $tmpData = ["url"=>URL_REDIRECT_BRIEFING.$event["briefingID"]];
                $event["actions"] .= self::template(self::TMP_EVENT_ACTION_JOINBRIEFING, $tmpData);
            }
        }else if($canJoin){
            $tmpData = ["url"=>URL_ROOT.ROUTE_EVENT_JOIN.'/'.$event["uid"]];
            $event["actions"] .= self::template(self::TMP_EVENT_ACTION_JOIN, $tmpData);
        }

        // generate html
        $data = array_merge($event, $options);
//        Debug::r($data);
        $template = self::template(self::TMP_EVENT_FULL, $data, ["debug"=>false]);
        return self::template($template, self::$tmpDefaults, ["empty"=>true, "debug"=>false]);
    }

    /* templates =========================================================================== */

    private static $tmpDefaults = [
        "url"=>"#",
        "lang"=>[
            "unknown"=>"Unbekannt",
            "edit"=>"Bearbeiten",
            "delete"=>"L&oumlschen",
            "briefing"=>"Briefing",
            "startbriefing"=>"Briefing starten",
            "joinevent"=>"Einschreiben",
            "leaveevent"=>"Ausschreiben",
            "closedevent"=>"Geschlossen",
            "finishedevent"=>"Beendet",
            "pricerank"=>"#",
            "pricegold"=>"Gold",
            "priceothers"=>"Andere",
            "priceranksep"=>" - ",
//            "more"=>"weiter",
//            "from"=>"@",
        ]
    ];

    //<a class="more" href="{{url}}"><i class="fa fa-fw fa-long-arrow-right"></i>{{post.more}}</a>
    const TMP_SEPERATOR_YEAR = '<h2 class="year">{{year}}</h2><hr>';

    const TMP_POST_MENU_EDIT = '<a class="bt-edit{{class}}" href="{{url}}">{{lang.edit}}</a>';
    const TMP_POST_MENU_DELETE = '<a class="bt-delete{{class}}" href="{{url}}">{{lang.delete}}</a>';
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
            <div class="c-section">
                <h2>{{title}}</h2>
                <h3 class="summary">{{summary}}</h3>
                <div class="text">{{text}}</div>
                <div class="meta">
                    <span class="date">{{lang.from}}{{created}}</span>
                    <span class="user">{{user}}</span>
                    <span class="clan">{{clantag}}</span>
                </div>
                <div class="info clearfix">
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

    const TMP_EVENT_MULTIMAP_DISPLAY = '<span class="multimap">+{{sum}}</span>';
    const TMP_EVENT_USER_SUBS = '<i class="fa fa-fw fa-user-plus"></i>{{count}}';
    const TMP_EVENT_USER_SUBS_MAX = '<i class="fa fa-fw fa-users"></i>{{max}}';
    const TMP_EVENT_LG = '<div class="event event-card-lg bs-callout bs-callout-custom{{class}}">
            <h4 class="clearfix">
                <span class="day fa-stack fa-stack-sm pull-left">
                    <i class="fa fa-calendar-o fa-stack-2x"></i>
                    <strong class="fa-stack-1x fa-calendar-fix">{{start.day}}</strong>
                </span>
                <span class="month">{{start.month}}</span>
                <span class="briefing fa-stack fa-stack-sm pull-right" title="{{lang.briefing}}">
                    <i class="fa fa-square fa-stack-2x"></i>
                    <strong class="fa fa-coffee fa-inverse fa-square-fix"></strong>
                </span>
            </h4>
            <div class="map callout-content"><a href="{{url}}">{{map.first}}{{map.sum}}</a></div>
            <div class="info callout-content row">
                <span class="users col-xs-4">{{users.count}}</span>
                <span class="usersmax col-xs-4">{{users.max}}</span>
                <span class="viewed col-xs-4"><i class="fa fa-fw fa-eye"></i>{{views}}</span>
            </div>
            <div class="summary callout-content">
                <a href="{{url}}">
                    <h3>{{title}}</h3>
                    <div class="description">{{summary}}</div>
                </a>
                <div class="meta">
                    <span class="date">{{lang.from}}{{created}}</span>
                    <span class="user">{{user}}</span>
                    <span class="clan">{{clantag}}</span>
                </div>
                <div class="info clearfix">
                    <span class="menu pull-left">{{menu}}</span>
                </div>
            </div>
        </div>';

    const TMP_EVENT_ACTION_JOIN = '<a class="btn btn-sm btn-success" href="{{url}}"><i class="fa fa-fw fa-check"></i>{{lang.joinevent}}</a>';
    const TMP_EVENT_ACTION_LEAVE = '<a class="btn btn-sm btn-danger" href="{{url}}"><i class="fa fa-fw fa-times"></i>{{lang.leaveevent}}</a>';
    const TMP_EVENT_ACTION_CLOSED = '<a class="btn btn-sm btn-default disabled" href="#" disabled><i class="fa fa-fw fa-lock"></i>{{lang.closedevent}}</a>';
    const TMP_EVENT_ACTION_FINISHED = '<a class="btn btn-sm btn-default pull-left disabled" href="#" disabled><i class="fa fa-fw fa-flag-checkered"></i>{{lang.finishedevent}}</a>';
    const TMP_EVENT_ACTION_JOINBRIEFING = '<a class="btn btn-sm btn-primary " href="{{url}}" target="_blank"><i class="fa fa-fw fa-coffee"></i>{{lang.startbriefing}}</a>';

    const TMP_EVENT_FULL_MAP = '<div class="map"><span class="thumbnail">{{map}}<div class="caption"><h4>{{name_i18n}}</h4><small>{{modeName_i18n}}</small></div></span></div>';
    const TMP_EVENT_PRICE_GOLD = '<div class="price">{{gold}} {{lang.pricegold}}</div>';
    const TMP_EVENT_PRICE_OTHERS = '<div class="price">{{others}}</div>';
    const TMP_EVENT_FULL_PRICE = '<div class="col-lg-3">
            <div class="jumbotron">
                <h3 class="rank"><i class="fa fa-fw fa-trophy"></i>{{rank}}</h3>
                <div class="pricelist">{{prices}}</div>
            </div>
        </div>';
    const TMP_EVENT_FULL = '<div class="event event-full c-default{{class}}">
            <div class="c-section">
                <div class="clearfix">
                    <div class="col-lg-12 text-right">
                        {{users.count}}&nbsp;
                        {{users.max}}&nbsp;
                        <i class="fa fa-fw fa-eye"></i>{{views}}
                    </div>
                </div>
                <h2 class="row">{{title}}</h2>
                <div class="info text-center row">
                    <span class="date col-lg-4">
                        <span class="fa-stack"><i class="fa fa-2x fa-star"></i></span>
                        <span class="moment-date">{{start}}</span>
                    </span>
                    <span class="date col-lg-4">
                        <span class="fa-stack"><i class="fa fa-2x fa-flag-checkered"></i></span>
                        <span class="moment-date">{{end}}</span>
                    </span>
                    <span class="briefing date col-lg-4" title="{{lang.briefing}}">
                        <span class="fa-stack">
                            <i class="fa fa-square fa-stack-2x"></i>
                            <i class="fa fa-coffee fa-stack-1x fa-inverse"></i>
                        </span>
                        <span class="moment-date">{{briefing.start}}</span>
                    </span>
                </div>
                <div class="actions row">{{actions}}</div>
                <div class="text">{{text}}</div>
                <div class="maps row"><div class="center-wrapper">{{maps}}</div></div>
                <div class="prices row">{{prices}}</div>
                <div class="meta row">
                    <span class="date">{{lang.from}}{{created}}</span>
                    <span class="user">{{user}}</span>
                    <span class="clan">{{clantag}}</span>
                </div>
                <div class="clearfix">
                    <span class="menu pull-left">{{menu}}</span>
                </div>
            </div>
        </div>';

//<span class="usersmax col-xs-2">{{users.max}}</span>
//                    <span class="users col-xs-2">{{users.count}}</span>
//                    <span class="views col-xs-2"><i class="fa fa-fw fa-eye"></i>{{views}}</span>
//                    <span class="col-xs-6"></span>
}