<?php
/**
* Project Tank Webpage
* database handler
* @author Steffen Lange
*/
class DBHandler{
	const TIMESTAMP_FORMAT = "Y-m-d H:i:s";
	const DB_DEFAULTS = "defaults";
	const DB_CLAN_INFO = "clan_info";
	const DB_CLAN_MEMBERS = "clan_members";
	const DB_USER = "user";
	const DB_USER_STATS = "user_stats";
	const DB_USER_STATS_TYPES = "user_stats_types";
	const DB_USER_INFO = "user_info";
	const DB_USER_RATINGS = "user_ratings";
	const DB_USER_SETTINGS = "user_settings";
	const DB_USER_SETTINGS_TYPES = "user_settings_types";
	const DB_USER_HAS_TANKS = "hasTanks";
	const DB_WOT_USER_STATS = "wot_user_stats";
	const DB_WOT_USER_STATS_TYPES = "wot_user_stats_types";
	const DB_NEWS = "news";
	const DB_NEWS_EDITS = "news_edits";
	const DB_NEWS_DESCRIPTION = "news_description";
	const DB_NEWS_CATS = "news_cats";
	const DB_TANKS = "tanks";
	const DB_EVENTS = "wot_events";
	const DB_EVENTS_DESCRIPTION = "wot_events_description";
	const DB_EVENTS_EDITS = "wot_events_edits";
	const DB_EVENTS_MAPS = "wot_events_maps";
	const DB_EVENTS_PRICES = "wot_events_prices";
    const DB_EVENTS_PASSWORDS = "wot_events_passwords";
    const DB_EVENTS_BRIEFINGS = "wot_events_briefings";
	const DB_EVENTS_TYPES = "wot_events_types";
	const DB_EVENTS_TYPES_DESCRIPTION = "wot_events_types_description";
	const DB_EVENTS_TYPES_OPTIONS = "wot_events_types_options";
    const DB_EVENTS_USERS = "wot_events_users";

    const API_WOT_MAPS = "pt_wg_api.wot_maps";
    const API_WOT_MAPS_DESCRIPTION = "pt_wg_api.wot_maps_description";
    const API_WOT_GAMEMODES = "pt_wg_api.wot_gamemodes";
    const API_WOT_GAMEMODES_DESCRIPTION = "pt_wg_api.wot_gamemodes_description";

    /** @var PDO $db  */
	private $db = null;
	private $d = false;
	
	function __construct($db){
		if($db !== false)
			$this->db = $db;
	}
	
	public function debug($do=true){$this->d = $do;}
	public function isDebug(){return $this->d;}
	public function isConnection(){return $this->db !== null;}
	public function getDB(){return $this->db;}
	
	private function echoError($msg){
		echo "<pre class='sql error'>$msg</pre>";
	}
	
	private function toFieldList($array){
		return implode(",", $array);
	}

    /** generate mysql insert query
     * @param string $table DBHandler table const
     * @param array $data key=>value
     * @param array $options option array<ul>
     *  <li>bool quotes - default: true</li>
     *  <li>bool updateOnDuplicate - default: false</li>
     *  <li>array updateData - default: null (all)<br>
     *          key=>value<br>
     *          empty value for data value</li>
     *  </ul>
     */
    private function generateQueryInsert($table, $data, $options=[]){

        $escnull = function($a){
            return $a !== null ? $a : "NULL";
        };

        $mapUpdate = function($k,$v){
            $val = $v !== null ? $v : "NULL";
            return '`'.$k.'`' . "=" . $val;
        };

        $mapUpdateQuoted = function($k,$v){
            $val = $v !== null ? "'$v'" : "NULL";
            return '`'.$k.'`' . "=" . $val;
        };

        $setQuotes = isset($options["quotes"]) ? $options["quotes"] : true;
        $doUpdateOnDuplicate = isset($options["updateOnDuplicate"]) ? $options["updateOnDuplicate"] : false;
        $updateData = isset($options["updateData"]) ? $options["updateData"] : null;
        $isUpdateData = isset($updateData);

        // template
        $query = "INSERT INTO {{table}}({{keys}}) VALUES({{values}})";
        if($doUpdateOnDuplicate){
            $query .= " ON DUPLICATE KEY UPDATE {{update}}";
        }
        $query .= ";";

        // create data strings
        $dataKeys = array_keys($data);
        $keys = '`'.implode('`,`', $dataKeys).'`';
        $values = null;

        if($setQuotes){
            foreach($data as $key=>$value){
                $values .= $value !== null ? "'$value'" : "NULL";
                $values .= ",";
            }
            $values = trim($values, ",");
        }else{
            $dataNull = array_map($escnull, $data);
            $values = implode(",", $dataNull);
        }


//        $values = $setQuotes ? "'".implode("','", $dataNull)."'" : implode(",", $dataNull);

        $update = null;
        if($doUpdateOnDuplicate){
            if($isUpdateData){
                $update = $setQuotes
                    ? array_map($mapUpdateQuoted, array_keys($updateData), $updateData)
                    : array_map($mapUpdate, array_keys($updateData), $updateData);
            }else{
                $update = array_map(function($k){
                    return "`$k`=VALUES(`$k`)";
                }, $dataKeys);
            }
            $update = $isUpdateData && $setQuotes ? "'".implode("','", $update)."'" : implode(",", $update);
        }

        // generate
        $queryData = [
            "table"=>$table,
            "keys"=>$keys,
            "values"=>$values,
            "update"=>$update,
        ];
        return Html::template($query, $queryData);
    }

    /** generate mysql update query for target column
     * @param string $table DBHandler table const
     * @param array $data key=>value
     * @param string $column target column name which will be updated
     * @param string|int $columnValue value for $column
     * @param array $options option array<ul>
     *  <li>bool quotes - default: true</li>
     * @return mixed
     */
    private function generateQuerySingleUpdate($table, $data, $column, $columnValue, $options=[]){
        $setQuotes = isset($options["quotes"]) ? $options["quotes"] : true;

        // template
        $query = "UPDATE {{table}} SET {{data}} WHERE `{{col}}`={{value}};";

        // create data strings
        $dataKeys = array_keys($data);
        $itemData = $setQuotes
            ? array_map(function($k,$v){
                $v = $v !== null ? "'".$v."'" : "NULL";
                return "`".$k."`=".$v;
            }, $dataKeys, $data)
            : array_map(function($k,$v){
                return "`".$k."`=".$v;
            }, $dataKeys, $data);

        $itemData = implode(",", $itemData);
        if($setQuotes) $columnValue = "'".$columnValue."'";

        // generate
        $queryData = [
            "table"=>$table,
            "data"=>$itemData,
            "col"=>$column,
            "value"=>$columnValue,
        ];
        return Html::template($query, $queryData);
    }

    /** generate mysql delete query for target column
     * @param string $table DBHandler table const
     * @param string $column target column name which will be deleted
     * @param string|int $columnValue value for $column
     * @param array $options option array<ul>
     *  <li>bool quotes - default: true</li>
     * @return mixed
     */
    private function generateQuerySingleDelete($table, $column, $columnValue, $options=[]){
        $setQuotes = isset($options["quotes"]) ? $options["quotes"] : true;

        // template
        $query = "DELETE FROM {{table}} WHERE `{{col}}`={{value}};";
        if($setQuotes) $columnValue = "'".$columnValue."'";

        // generate
        $queryData = [
            "table"=>$table,
            "col"=>$column,
            "value"=>$columnValue,
        ];
        return Html::template($query, $queryData);
    }

    /**
     * helper
     */

//    public function update(){
//        $query = "UPDATE ".self::API_WOT_MAPS_DESCRIPTION." set name_i18n=".$this->quote("&Uuml;bungsgel&auml;nde")." where mapID=36 and lang='de';";
//        return $this->queryInsert($query);
//    }

    public function parse($result, $default=null, $debugOnError=true){
        if($this->isDebug()) Debug::i($result);
        return $result !== false && !$this->isDebug() ? $result : $default;
    }

    public function parseArray($result, $debugOnError=true){
        return $this->parse($result, [], $debugOnError);
    }

    public function parseMap($result, $fn=null, $debugOnError=true){
        $out = [];
        $result = $this->parse($result, false, $debugOnError);
        if($result === false) return $out;

        $fn = is_callable($fn) ? $fn : function($ele){return $ele;};
        $out = array_map($fn, $result);
//        foreach($result as $row){
//            $out[] = $fn($row);
//        }
        return $out;
    }

    public function rollback(){
        return $this->queryInsert("ROLLBACK;");
    }

	/**
	* data getter
	*/
	
	public function userExists($userID){
		$query = "SELECT userID FROM ".self::DB_USER." WHERE userID=$userID;";
		return $this->queryEntryExists($query);
	}
	
	public function getClanEmblems($clanID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." WHERE userID='$userID' AND deleted='0';";
		return $this->queryFirstRow($query);
	}

	public function getClanMemberByUserID($userID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." WHERE userID='$userID' AND deleted='0';";
		return $this->queryFirstRow($query);
	}

	public function getClanMembers($clanID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." WHERE clanID='$clanID' AND deleted='0';";
		return $this->queryIndexedArray($query, "userID");
	}
	
	public function getClanMembersInfo($clanID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." m
		LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=m.userID
		LEFT JOIN ".self::DB_USER_RATINGS." r ON r.userID=m.userID
		WHERE m.clanID='$clanID' AND m.deleted='0';";
		return $this->queryIndexedArray($query, "userID");
	}
	
	public function getTanks($accountID, $inGarage=null, $limit=null){
		$query = "SELECT a.tankID, a.inGarage, b.tankName, b.imageName, b.nation, b.level
				FROM ".self::DB_USER_HAS_TANKS." a
				LEFT JOIN ".self::DB_TANKS." b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID'";
		$query .= isset($inGarage) ? " AND a.inGarage=$inGarage" : "";
		$query .= " ORDER BY b.level DESC";
		$query .= isset($limit) ? " LIMIT 0, $limit;" : ";";
		return $this->queryAssoc($query);
	}
	
	public function countTanksByLevel($accountID, $level){
		if(!$this->isConnection()) return false;
		$query = "SELECT COUNT(a.tankID) as count FROM ".self::DB_USER_HAS_TANKS." a
				LEFT JOIN ".self::DB_TANKS." b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID' AND a.inGarage=1 AND b.level=$level";
		return $this->queryFirstValue($query);
	}

	public function getMapList($options =[]){
		if(!$this->isConnection()) return false;
        $offset = isset($options["offset"]) ? $options["offset"] : 0;
        $limit = isset($options["limit"]) ? " LIMIT ".$offset.",".$options["limit"] : null;
        $order = isset($options["order"]) ? " ORDER BY ".$options["order"] : null;
        $indexed = isset($options["indexed"]) && $options["indexed"];

		$query = "SELECT m.mapID,m.name,d.* FROM ".self::API_WOT_MAPS." m
		LEFT JOIN (
            SELECT * FROM ".self::API_WOT_MAPS_DESCRIPTION."
            WHERE lang=(
                SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d on d.mapID=m.mapID
		WHERE m.deleted=0".$order.$limit.";";
		return $indexed ? $this->queryIndexedArray($query, "mapID") : $this->queryAssoc($query);
	}

    public function getMapNames(){
        $query = "SELECT mapID,name FROM ".self::API_WOT_MAPS.";";
        return $this->queryKeyValuePair($query);
    }

    public function getMapNamesByIDs($ids){
        if(empty($ids)) return [];
        $idStr = implode(",", $ids);
        $query = "SELECT mapID,name FROM ".self::API_WOT_MAPS."
            WHERE mapID IN ($idStr);";
        return $this->queryKeyValuePair($query);
    }

	public function getGameModes($options =[]){
		if(!$this->isConnection()) return false;
        $indexed = isset($options["indexed"]) && $options["indexed"];

		$query = "SELECT m.modeID,m.name,d.* FROM ".self::API_WOT_GAMEMODES." m
		LEFT JOIN (
            SELECT * FROM ".self::API_WOT_GAMEMODES_DESCRIPTION."
            WHERE lang=(
                SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d on d.modeID=m.modeID
		WHERE m.deleted=0";
        return $indexed ? $this->queryIndexedArray($query, "modeID") : $this->queryAssoc($query);
	}

	public function getUserSettings($userID, $settingsIDs=null){
		$qs = !isset($settingsIDs) ? null : " WHERE settingsID IN (".implode(",", $settingsIDs).")"; 
		$query = "SELECT s.settingsID, ifnull(u.value, s.defaultValue) as value FROM ".self::DB_USER_SETTINGS_TYPES." s
				LEFT JOIN (
					SELECT * FROM ".self::DB_USER_SETTINGS." WHERE userID='$userID'
				) u ON u.settingsID=s.settingsID".$qs.";";
		return $this->queryKeyValuePair($query);
	}

//	public function getUserSettingsByName($userID, $settingsIDs=null){
//		$qs = !isset($settingsIDs) ? null : " WHERE settingsID IN (".implode(",", $settingsIDs).")";
//		$query = "SELECT s.name, ifnull(u.value, s.defaultValue) as value FROM ".self::DB_USER_SETTINGS_TYPES." s
//				LEFT JOIN (
//					SELECT * FROM ".self::DB_USER_SETTINGS." WHERE userID='$userID'
//				) u ON u.settingsID=s.settingsID".$qs.";";
//		return $this->queryKeyValuePair($query);
//	}

	public function getUserStats($userID){
		$query = "SELECT t.name, ifnull(s.value,t.defaultValue) as value FROM ".self::DB_USER_STATS_TYPES." t
			LEFT JOIN (SELECT statsID,value FROM ".self::DB_USER_STATS." WHERE userID='$userID') s ON s.statsID=t.statsID;";
		return $this->queryKeyValuePair($query);
	}

	public function getUserStatsInternal($userID){
		$query = "SELECT t.internal, s.value FROM pt_wg_db.wot_user_stats s
            LEFT JOIN wot_user_stats_types t ON t.statsID = s.statsID
            WHERE s.userID='$userID';";
		return $this->queryKeyValuePair($query);
	}

	public function getWotUserStats($userID){
		$query = "SELECT t.api, s.statsID, ifnull(s.value,t.defaultValue) as value FROM ".self::DB_WOT_USER_STATS_TYPES." t
			LEFT JOIN (SELECT statsID,value FROM ".self::DB_WOT_USER_STATS." WHERE userID='$user') s ON s.statsID=t.statsID;";
		return $this->queryIndexedArray($query, "api");
	}
	
	public function getWotUserGroupStats($userIDs){
		if(empty($userIDs)) return [];
		$idStr = $this->toFieldList($userIDs);
		$query = "SELECT t.api, s.statsID, ifnull(s.value,t.defaultValue) as value FROM ".self::DB_WOT_USER_STATS_TYPES." t
			LEFT JOIN (SELECT statsID,value FROM ".self::DB_WOT_USER_STATS." WHERE userID IN ($idStr)) s ON s.statsID=t.statsID;";
		return $this->queryIndexedArray($query, "api");
	}
	
	public function getClanMembersStatsTableData($memberIDs){
		if(empty($memberIDs)) return [];
		$idStr = $this->toFieldList($memberIDs);
		$query = "SELECT s.userID, t.internal, ifnull(s.value,t.defaultValue) as value FROM ".self::DB_WOT_USER_STATS_TYPES." t
			LEFT JOIN (SELECT userID,statsID,value FROM ".self::DB_WOT_USER_STATS." WHERE userID IN ($idStr)) s ON s.statsID=t.statsID
			WHERE t.inClanMembersStatsTable=1;";
		return $this->queryGroupResult($query);
	}
	
	public function getUserRatings($userID){
		$query = "SELECT * FROM ".self::DB_USER_STATS_TYPES." WHERE userID='$userID';";
		return $this->queryFirstRow($query);
	}
	
	public function getUserGroupRatings($userIDs){
		$idStr = $this->toFieldList($userIDs);
		$query = "SELECT * FROM ".self::DB_USER_STATS_TYPES." WHERE userID IN ($idStr);";
		return $this->queryFirstRow($query);
	}

    public function getNewsCategories(){
        $query = "SELECT * FROM ".self::DB_NEWS_CATS.";";
        return $this->queryAssoc($query);
    }

    public function getNewsCategoryByID($catID, $options=[]){
        $query = "SELECT * FROM ".self::DB_NEWS_CATS." WHERE catID='".$catID."';";
        return $this->queryFirstRow($query);
    }

    public function getNewsCategoryByName($name, $options=[]){
        $query = "SELECT * FROM ".self::DB_NEWS_CATS." WHERE name='".$name."';";
        return $this->queryFirstRow($query);
    }

	public function getLatestNews($options=[]){
        $offset = isset($options["offset"]) ? $options["offset"] : 0;
        $limit = isset($options["limit"]) ? " LIMIT ".$offset.",".$options["limit"] : null;
        $category = isset($options["catID"]) ? " AND n.catID='".$options["catID"]."'" : null;
        $clanID = isset($options["clanID"]) ? " AND (n.clanID IS NULL OR n.clanID='".$options["clanID"]."')" : " AND n.clanID IS NULL";

        $query = "SELECT n.*,d.*,u.name as user,c.tag as clantag FROM ".self::DB_NEWS." n
        LEFT JOIN ".self::DB_NEWS_DESCRIPTION." d ON d.newsID=n.newsID
        LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_MEMBERS." m ON m.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
        WHERE n.deleted=0".$category.$clanID." ORDER BY n.created DESC".$limit.";";
        return $this->queryAssoc($query);//WHERE n.created > FROM_UNIXTIME($timestamp_from) AND m.deleted=0
    }

	public function getLatestFeaturedNews($options=[]){
        $category = isset($options["catID"]) ? " AND n.catID='".$options["catID"]."'" : null;
        $clanID = isset($options["clanID"]) ? " AND (n.clanID IS NULL OR n.clanID='".$options["clanID"]."')" : " AND n.clanID IS NULL";

        $query = "SELECT n.*,d.*,u.name as user,c.tag as clantag FROM ".self::DB_NEWS." n
        LEFT JOIN ".self::DB_NEWS_DESCRIPTION." d ON d.newsID=n.newsID
        LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_MEMBERS." m ON m.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
        WHERE n.created = (
			SELECT MAX(created) FROM news WHERE featured=1
        ) AND n.featured=1 AND n.deleted=0".$category.$clanID." ORDER BY n.created DESC;";
        return $this->queryAssoc($query);// AND m.deleted=0
    }

	public function getNewsByUid($uid, $options=[]){
//        $offset = isset($options["offset"]) ? $options["offset"] : 0;
        $fields = isset($options["fields"]) ? $options["fields"] : "n.*,d.*,u.name as user,c.tag as clantag";

        $query = "SELECT ".$fields." FROM ".self::DB_NEWS." n
        LEFT JOIN ".self::DB_NEWS_DESCRIPTION." d ON d.newsID=n.newsID
        LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_MEMBERS." m ON m.userID=n.userID
        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
        WHERE d.uid='$uid' AND n.deleted=0;";
        return $this->queryFirstRow($query);//m.deleted
    }

    /**
     * events
     */

    public function existsBriefingID($briefingID){
        $query = "SELECT EXISTS(SELECT 1 FROM ".self::DB_EVENTS_BRIEFINGS." WHERE briefingID='$briefingID');";
        $result = $this->queryFirstValue($query);
        return is_numeric($result) ? $result*1 : false;
    }

    public function getEventTypes(){
        $query = "SELECT * FROM ".self::DB_EVENTS_TYPES." t
        LEFT JOIN (
            SELECT * FROM ".self::DB_EVENTS_TYPES_DESCRIPTION."
            WHERE lang=(
                SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d on d.typeID=t.typeID;";
        return $this->queryIndexedArray($query, "typeID");
    }

    public function getEventTypeByID($typeID, $options=[]){
//        $lang = isset($options["lang"]) ?

        $query = "SELECT * FROM ".self::DB_EVENTS_TYPES." t
        LEFT JOIN (
            SELECT * FROM ".self::DB_EVENTS_TYPES_DESCRIPTION."
            WHERE lang=(
                SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d on d.typeID=t.typeID
        WHERE t.typeID='$typeID';";
        return $this->queryFirstRow($query);
    }

    public function getEventTypeByName($name, $options=[]){
        $query = "SELECT * FROM ".self::DB_EVENTS_TYPES." t
        LEFT JOIN (
            SELECT * FROM ".self::DB_EVENTS_TYPES_DESCRIPTION."
            WHERE lang=(
                SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d on d.typeID=t.typeID
        WHERE t.name='$name';";
        return $this->queryFirstRow($query);
    }

    public function getEventTypesOptions($options=[]){
                $fields = isset($options["fields"]) ? $options["fields"] : "*";
        $query = "SELECT * FROM ".self::DB_EVENTS_TYPES_OPTIONS."
        WHERE lang=(
            SELECT value from ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
        );";
        return $this->queryIndexedArray($query, "name");
    }

    public function getLatestEvents($options=[]){
        $offset = isset($options["offset"]) ? $options["offset"] : 0;
        $limit = isset($options["limit"]) ? " LIMIT ".$offset.",".$options["limit"] : null;
        $type = isset($options["typeID"]) ? " AND e.typeID='".$options["typeID"]."'" : null;
        $clanID = isset($options["clanID"]) ? " AND (e.clanID IS NULL OR e.clanID='".$options["clanID"]."')" : " AND e.clanID IS NULL";
//        $clanID = isset($options["clanID"]) ? " AND e.clanID='".$options["clanID"]."'" : " AND e.clanID IS NULL";
        $userID = isset($options["userID"]) ? " AND (e.hidden=0 OR (e.hidden=1 AND e.userID='".$options["userID"]."'))" : " AND e.hidden=0";

        $query = "SELECT
            e.*,b.briefingID,b.start as briefingStart,
            s.users,map.mapID,map.mapsCount,d.*,u.name as user,c.tag as clantag
        FROM ".self::DB_EVENTS." e
        LEFT JOIN ".self::DB_EVENTS_DESCRIPTION." d USING (eventID)
        LEFT JOIN ".self::DB_EVENTS_BRIEFINGS." b USING (eventID)
        LEFT JOIN ".self::DB_USER_INFO." u USING (userID)
        LEFT JOIN ".self::DB_CLAN_MEMBERS." m USING (userID)
        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
        LEFT JOIN (
            SELECT eventID, count(userID) as users FROM ".self::DB_EVENTS_USERS." WHERE deleted=0 GROUP BY eventID
        ) s USING (eventID)
        LEFT JOIN (select *, count(eventID) as mapsCount from wot_events_maps group by eventID order by mapID) map USING (eventID)
        WHERE e.deleted=0 AND e.end > now()
        ".$type.$clanID.$userID." ORDER BY e.start ASC".$limit.";";
        return $this->queryAssoc($query);//WHERE n.created > FROM_UNIXTIME($timestamp_from) AND m.deleted=0
        // LEFT JOIN (select * from wot_events_maps m1 where mapID=(select min(mapID) from  wot_events_maps m2 where m2.eventID=m1.eventID)) map USING (eventID)
    }


    public function getEventListByClanID($clanID, $isFinished=0, $dateStart=null, $dateEnd=null){
        if(!isset($clanID)) return false;
        $query = "SELECT a.*, b.prices FROM ".self::DB_EVENTS." a
				LEFT JOIN (
					SELECT eventID, count(eventID) AS prices FROM ".self::DB_EVENTS_PRICES."
					WHERE eventID=1 GROUP BY eventID) b
				ON b.eventID=a.eventID
				WHERE clanID=".$clanID.";";
        return $this->queryAssoc($query);
    }

    public function getLatestFeaturedEvents($options=[]){
//        $category = isset($options["catID"]) ? " AND n.catID='".$options["catID"]."'" : null;
//        $clanID = isset($options["clanID"]) ? " AND (n.clanID IS NULL OR n.clanID='".$options["clanID"]."')" : " AND n.clanID IS NULL";
//
//        $query = "SELECT n.*,d.*,u.name as user,c.tag as clantag FROM ".self::DB_NEWS." n
//        LEFT JOIN ".self::DB_NEWS_DESCRIPTION." d ON d.newsID=n.newsID
//        LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=n.userID
//        LEFT JOIN ".self::DB_CLAN_MEMBERS." m ON m.userID=n.userID
//        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
//        WHERE n.created = (
//			SELECT MAX(created) FROM news WHERE featured=1
//        ) AND n.featured=1 AND n.deleted=0".$category.$clanID." ORDER BY n.created DESC;";
//        return $this->queryAssoc($query);// AND m.deleted=0
    }

    public function getEventByUid($uid, $options=[]){
        $clanID = isset($options["clanID"]) ? " AND (e.clanID IS NULL OR e.clanID='".$options["clanID"]."')" : " AND e.clanID IS NULL";
        $fields = !isset($options["fields"])
            ? "e.*,b.briefingID,b.start as briefingStart,s.users,d.*,u.name as user,c.tag as clantag"
            : $options["fields"];

        $query = "SELECT $fields
        FROM ".self::DB_EVENTS." e
        LEFT JOIN ".self::DB_EVENTS_DESCRIPTION." d USING (eventID)
        LEFT JOIN ".self::DB_EVENTS_BRIEFINGS." b USING (eventID)
        LEFT JOIN ".self::DB_USER_INFO." u USING (userID)
        LEFT JOIN ".self::DB_CLAN_MEMBERS." m USING (userID)
        LEFT JOIN ".self::DB_CLAN_INFO." c ON c.clanID=m.clanID
        LEFT JOIN (
            SELECT eventID, count(userID) as users FROM ".self::DB_EVENTS_USERS." WHERE deleted=0 GROUP BY eventID
        ) s USING (eventID)
        WHERE e.deleted=0".$clanID."
        AND e.uid='$uid';";
        return $this->queryFirstRow($query);
    }

    public function getEventInfoByBriefingID($briefingID, $options=[]){
        $query = "SELECT e.*,d.*,b.briefingID,b.start as briefingStart
        FROM ".self::DB_EVENTS." e
        LEFT JOIN ".self::DB_EVENTS_DESCRIPTION." d USING (eventID)
        LEFT JOIN ".self::DB_EVENTS_BRIEFINGS." b USING (eventID)
        WHERE e.deleted=0 AND b.briefingID='$briefingID'";
        return $this->queryFirstRow($query);
    }

    public function getEventInfoByUid($uid, $options=[]){
        $clanID = isset($options["clanID"]) ? " AND (e.clanID IS NULL OR e.clanID='".$options["clanID"]."')" : " AND e.clanID IS NULL";

        $query = "SELECT * FROM ".self::DB_EVENTS." e
        WHERE e.deleted=0".$clanID."
        AND e.uid='$uid';";
        return $this->queryFirstRow($query);
    }

    public function getEventMapIDs($eventID){
        $query = "SELECT mapID FROM ".self::DB_EVENTS_MAPS." WHERE eventID='$eventID';";
        return $this->queryList($query);
    }

    public function getEventMaps($eventID){
        $query = "SELECT * FROM ".self::DB_EVENTS_MAPS." WHERE eventID='$eventID';";
        return $this->queryAssoc($query);
    }

    public function getEventMapsFull($eventID){
        $query = "SELECT em.*,mi.mapID,mi.name,d.* FROM ".self::DB_EVENTS_MAPS." em
        LEFT JOIN ".self::API_WOT_MAPS." mi USING(mapID)
        LEFT JOIN (
            SELECT * FROM ".self::API_WOT_MAPS_DESCRIPTION."
            WHERE lang=(
                SELECT value FROM ".self::DB_DEFAULTS." WHERE `key`='i18n_lang'
            )
        ) d USING(mapID)
		WHERE em.eventID='$eventID' AND mi.deleted=0;";
        return $this->queryAssoc($query);
    }

    public function getEventPrices($eventID){
        $query = "SELECT * FROM ".self::DB_EVENTS_PRICES." WHERE eventID='$eventID';";
        return $this->queryAssoc($query);
    }

    public function getEventUsers($eventID){
        $query = "SELECT * FROM ".self::DB_EVENTS_USERS." WHERE eventID='$eventID' AND deleted=0;";
        return $this->queryAssoc($query);
    }

    public function getEventUserIDs($eventID){
        $query = "SELECT userID FROM ".self::DB_EVENTS_USERS." WHERE eventID='$eventID' AND deleted=0;";
        return $this->queryList($query);
    }

    public function getEventUserDetails($eventID){
        $query = "SELECT eu.userID, ci.name,cm.role FROM ".self::DB_EVENTS_USERS." eu
        LEFT JOIN ".self::DB_USER_INFO." ci USING (userID)
        LEFT JOIN ".self::DB_CLAN_MEMBERS." cm USING (userID)
        WHERE eventID='$eventID' AND eu.deleted=0;";
        return $this->queryAssoc($query);
    }

    public function getUserJoinedEvent($userID, $eventID){
        $query = "SELECT 1 FROM ".self::DB_EVENTS_USERS." WHERE userID='$userID' AND eventID='$eventID' AND deleted=0;";
        return $this->queryFirstValue($query) !== 0;
    }

    public function getUserJoinedEventIDs($userID){
        $query = "SELECT eventID FROM ".self::DB_EVENTS_USERS." WHERE userID='$userID' AND deleted=0;";
        return $this->queryList($query);
    }

    /**
	* data setter
	*/
	
	public function accountLogin($userID, $name){
		$c = !empty($clanID) ? $clanID : 0;
		$qn = $this->quote($name);
		if($qn === false) return false;
		$query = "INSERT INTO ".self::DB_USER."(userID, name, lastLogin) 
				VALUES('$userID', $qn, now()) 
				ON DUPLICATE KEY UPDATE lastLogin=now(), name=$qn;";
		return $this->queryInsert($query);
	}
	
	public function addTanks($accountID, $tanks){
		$query = "";
		foreach($tanks as $tank){
			$tankID = $tank["tank_id"];
			$inGarage = $tank["in_garage"];
			$query .= "INSERT IGNORE INTO ".self::DB_USER_HAS_TANKS." (accountID, tankID, inGarage) VALUES ('$accountID', '$tankID', '$inGarage');";
		}
		if(empty($query)) return true;
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update basic information about a clan
	* @param int $clanID
	* @param string $name
	* @param string $tag
	* @param string $color
	* @param int $isDisbanned
	* 
	* @return boolean
	*/
	public function setClanInfo($clanID, $name, $tag, $color, $isDisbanned){
		$qn = $this->quote($name);
		$query = "INSERT INTO ".self::DB_CLAN_INFO."(clanID,name,tag,color,isDisbanned) 
			VALUES('$clanID',$qn,'$tag','$color','$isDisbanned') 
			ON DUPLICATE KEY UPDATE 
				name=VALUES(name),
				tag=VALUES(tag),
				color=VALUES(color),
				isDisbanned=VALUES(isDisbanned);";
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a set of clan members
	* @param array $data array(userID=>info)
	* 						info = array(clanID=>int, role=>string, role_i18n=>string, joined=>timestamp)
	* @return boolean
	*/
	public function setClanMembers($data){
		$query = "INSERT INTO ".self::DB_CLAN_MEMBERS."(userID,clanID,role,role_i18n,joined) VALUES";
		$queryValues = "";
		foreach($data as $userID=>$info){
			$qRole_i18n = $this->quote($info["role_i18n"]);
			$queryValues .= "(
				'$userID',
				'".$info["clanID"]."',
				'".$info["role"]."',
				$qRole_i18n,
				'".$info["joined"]."'),";
		}
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= " ON DUPLICATE KEY UPDATE 
				userID=VALUES(userID),
				clanID=VALUES(clanID),
				role=VALUES(role),
				role_i18n=VALUES(role_i18n),
				joined=VALUES(joined),
				deleted=DEFAULT;";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update basic user information
	* @param int $userID
	* @param string(2) $lang
	* 
	* @return boolean
	*/
	public function setUserInfo($userID, $name, $lang){
		$qn = $this->quote($name);
		if($qn === false) return false;
		$query = "INSERT INTO ".self::DB_USER_INFO."(userID,name,lang) VALUES('$userID',$qn,'$lang') ON DUPLICATE KEY UPDATE name=VALUES(name),lang=VALUES(lang);";
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a group of user information
	* @param array $data array($userID=>[name,lang], ...)
	* 
	* @return boolean
	*/
	public function setUserGroupInfo($data){
		$query = "INSERT INTO ".self::DB_USER_INFO."(userID,name,lang) VALUES";
		// prepare data
		$queryValues = "";
		foreach($data as $userID => $info){
			$qn = $this->quote($info["name"]);
			$queryValues .= "('$userID',$qn,'".$info["lang"]."'),";
		}
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= "ON DUPLICATE KEY UPDATE name=VALUES(name),lang=VALUES(lang);";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a set of statistics
	* @param int $userID
	* @param array $data array($statsID=>$value, ...)
	* 
	* @return boolean
	*/
	public function setUserStats($userID, $data){
		$query = "INSERT INTO ".self::DB_USER_STATS."(userID,statsID,value) VALUES";
		// prepare data
		$queryValues = "";
		foreach($data as $statsID => $value)
			$queryValues .= "('$userID','$statsID','$value'),";
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= " ON DUPLICATE KEY UPDATE value=VALUES(value);";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a set of wot based statistics for one user
	* @param int $userID
	* @param array $data array($statsID=>$value, ...)
	* 
	* @return boolean
	*/
	public function setWotUserStats($userID, $data){
		$query = "INSERT INTO ".self::DB_WOT_USER_STATS."(userID,statsID,value) VALUES";
		// prepare data
		$queryValues = "";
		foreach($data as $statsID => $value)
			$queryValues .= "('$userID','$statsID','$value'),";
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= " ON DUPLICATE KEY UPDATE value=VALUES(value);";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a set of wot based statistics a set of users
	* @param int $userID
	* @param array $data array(userID=>info)
	* 						info = array($statsID=>$value, ...)
	* 
	* @return boolean
	*/
	public function setWotUserGroupStats($data){
		$query = "INSERT INTO ".self::DB_WOT_USER_STATS."(userID,statsID,value) VALUES";
		$queryValues = "";
		foreach($data as $userID=>$info)
			foreach($info as $statsID=>$value)
				$queryValues .= "('$userID','$statsID','$value'),";
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= " ON DUPLICATE KEY UPDATE value=VALUES(value);";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a rating stats for one user
	* @param int $userID
	* @param int $global
	* 
	* @return boolean
	*/
	public function setUserRatings($userID, $global){
		$query = "INSERT INTO ".self::DB_USER_RATINGS."(userID,global) VALUES('$userID',$global)
		ON DUPLICATE KEY UPDATE global=VALUES(global);";
		
		return $this->queryInsert($query);
	}
	
	/**
	* insert or update a set rating statistics for a set of users
	* @param int $userID
	* @param array $data array(userID=>info, ...)
	* 						info = array($global=>$value, ...)
	* 
	* @return boolean
	*/
	public function setUserGroupRatings($data){
		$query = "INSERT INTO ".self::DB_USER_RATINGS."(userID,global) VALUES";
		$queryValues = "";
		foreach($data as $userID=>$info)
			$queryValues .= "('$userID',".$info["global"]."),";
		if(empty($queryValues)) return true;
		$query .= trim($queryValues, ",");
		$query .= " ON DUPLICATE KEY UPDATE global=VALUES(global);";
		
		return $this->queryInsert($query);
	}
	
	public function setUserSetting($userID, $settingsID, $value){
		$query = "INSERT INTO ".self::DB_USER_SETTINGS."(userID, settingsID, value) VALUES('$userID', '$settingsID', '$value') ON DUPLICATE KEY UPDATE value='$value';";
		return $this->queryInsert($query);
	}
	
	public function setUserSettings($userID, $settings){
		if(!isset($userID, $settings)) return false;
		else if(empty($settings)) return true;
		$query = "";
		foreach($settings as $id=>$value){
			$query .= "INSERT INTO ".self::DB_USER_SETTINGS."(userID, settingsID, value) VALUES('$userID', '$id', '$value') ON DUPLICATE KEY UPDATE value='$value';";	
		}
		return $this->queryInsert($query);
	}

    public function joinEvent($userID, $eventID){
        $query = "INSERT INTO ".self::DB_EVENTS_USERS."(userID,eventID)
            VALUES('$userID','$eventID')
            ON DUPLICATE KEY UPDATE joined=now(), deleted=0, accepted=0;";
        return $this->queryInsert($query);
    }

    public function leaveEvent($userID, $eventID){
        $query = "UPDATE ".self::DB_EVENTS_USERS." SET deleted=1 WHERE userID='$userID' AND eventID='$eventID';";
        return $this->queryInsert($query);
    }

    public function postEvent($data){
        $userID = $data["userID"];
        $typeID = $data["typeID"];
        $title = $data["title"];
        $text = $data["text"];
        $summary = isset($data["summary"]) ? $data["summary"] : null;

        $start = date(self::TIMESTAMP_FORMAT, $data["start"]);
        $end = date(self::TIMESTAMP_FORMAT, $data["end"]);

        $maxUsers = isset($data["maxUsers"]) ? $data["maxUsers"] : null;
        $clanID = isset($data["clanID"]) ? $data["clanID"] : null;
//        $clan = isset($data["clan"]) ? $data["clan"] : null;
        $hidden = isset($data["hidden"]) ? $data["hidden"] : null;
        $public = isset($data["public"]) ? $data["public"] : null;
        $password = isset($data["password"]) ? $data["password"] : null;
        $briefingStart = isset($data["briefing"]) ? date(self::TIMESTAMP_FORMAT, $data["briefing"]) : null;
        $briefingID = isset($data["briefingID"]) ? $data["briefingID"] : null;
        $uid = $data["uid"];

        $maps = isset($data["maps"]) ? $data["maps"] : [];
        $prices = isset($data["prices"]) ? $data["prices"] : [];

        $qUid = isset($uid) ? $this->quote($uid, false) : null;
        $qTitle = $this->quote($title);
        $qText = $this->quote($text);
        $qSummary = isset($summary) ? $this->quote($summary) : null;
        //todo: add passwort saving
        $qPassword = isset($password) ? $this->quote($password, false) : null;

        $isBriefing = isset($briefingStart);

        // query: events table
        $dataEvents = [
            "userID"=>$userID,
            "typeID"=>$typeID,
            "start"=>$start,
            "end"=>$end,
            "maxUsers"=>$maxUsers,
            "clanID"=>$clanID,
//            "briefingStart"=>$briefingStart,
//            "briefingID"=>$briefingID,
            "uid"=>$qUid,
            "hidden"=>$hidden,
            "public"=>$public,
        ];
        $queryEvents = $this->generateQueryInsert(self::DB_EVENTS,$dataEvents);

        // query: event description
        $dataDescr = [
            "eventID"=>"LAST_INSERT_ID()",
            "title"=>$qTitle,
            "summary"=>$qSummary,
            "text"=>$qText,
        ];
        $queryDescr = $this->generateQueryInsert(self::DB_EVENTS_DESCRIPTION,$dataDescr,["quotes"=>false]);

        // query: event briefing
        $dataBriefing = [
            "eventID"=>"LAST_INSERT_ID()",
            "start"=>"'$briefingStart'",
            "briefingID"=>"'$briefingID'",
        ];
        $queryBriefing = $isBriefing
            ? $this->generateQueryInsert(self::DB_EVENTS_BRIEFINGS,$dataBriefing,["quotes"=>false])
            : null;

        // query: event prices
        $queryPrices = "";
        $index = 1;
        foreach($prices as $price){

            $dataPrice = [
                "eventID"=>"LAST_INSERT_ID()",
                "priceID"=>$index++,
                "rank_from"=>isset($price["rank_from"]) ? "'".$price["rank_from"]."'" : null,
                "rank_to"=>isset($price["rank_to"]) ? "'".$price["rank_to"]."'" : null,
                "gold"=>isset($price["gold"]) ? "'".$price["gold"]."'" : null,
                "others"=>isset($price["others"]) ? $this->quote($price["others"]) : null,
            ];
            $queryPrices .= $this->generateQueryInsert(self::DB_EVENTS_PRICES,$dataPrice,["quotes"=>false]);

        }

        // query: event prices
        $queryMaps = "";
        foreach($maps as $map){

            $dataMap = [
                "eventID"=>"LAST_INSERT_ID()",
                "mapID"=>isset($map["mapID"]) ? "'".$map["mapID"]."'" : null,
                "modeID"=>isset($map["modeID"]) ? "'".$map["modeID"]."'" : null,
                "order"=>isset($map["order"]) ? "'".$map["order"]."'" : "DEFAULT",
            ];
            $queryMaps .= $this->generateQueryInsert(self::DB_EVENTS_MAPS,$dataMap,["quotes"=>false]);

        }

//        SET @Event_ID = (SELECT LAST_INSERT_ID());
        // build transaction query
        $query = "BEGIN;
            $queryEvents
            $queryDescr
            $queryBriefing
            $queryPrices
            $queryMaps
        COMMIT;";

        return $this->queryInsert($query);
    }

    public function updateEvent($data){
        $eventID = $data["eventID"];
        $userID = $data["userID"];
        $typeID = $data["typeID"];
        $title = $data["title"];
        $text = $data["text"];
        $summary = isset($data["summary"]) ? $data["summary"] : null;

        $start = date(self::TIMESTAMP_FORMAT, $data["start"]);
        $end = date(self::TIMESTAMP_FORMAT, $data["end"]);

        $maxUsers = isset($data["maxUsers"]) ? $data["maxUsers"] : null;
        $clanID = isset($data["clanID"]) ? $data["clanID"] : null;
//        $clan = isset($data["clan"]) ? $data["clan"] : null;
        $hidden = isset($data["hidden"]) ? $data["hidden"] : null;
        $public = isset($data["public"]) ? $data["public"] : null;
        $password = isset($data["password"]) ? $data["password"] : null;
        $briefingStart = isset($data["briefing"]) ? date(self::TIMESTAMP_FORMAT, $data["briefing"]) : null;
        $briefingID = isset($data["briefingID"]) ? $data["briefingID"] : null;
        $uid = $data["uid"];

        $maps = isset($data["maps"]) ? $data["maps"] : [];
        $prices = isset($data["prices"]) ? $data["prices"] : [];

        $qUid = isset($uid) ? $this->quote($uid, false) : null;
        $qTitle = $this->quote($title);
        $qText = $this->quote($text);
        $qSummary = isset($summary) ? $this->quote($summary) : null;
        //todo: add passwort saving
        $qPassword = isset($password) ? $this->quote($password, false) : null;

        $isBriefing = isset($briefingStart);

        // query: events table
        $dataEvents = [
            "userID"=>$userID,
            "typeID"=>$typeID,
            "start"=>$start,
            "end"=>$end,
            "maxUsers"=>$maxUsers,
            "clanID"=>$clanID,
            "uid"=>$qUid,
            "hidden"=>$hidden,
            "public"=>$public,
        ];
        $queryEvents = $this->generateQuerySingleUpdate(self::DB_EVENTS,$dataEvents,"eventID",$eventID);

        // query: event description
        $dataDescr = [
            "title"=>$qTitle,
            "summary"=>$qSummary,
            "text"=>$qText,
        ];
        $queryDescr = $this->generateQuerySingleUpdate(self::DB_EVENTS_DESCRIPTION,$dataDescr,"eventID",$eventID,["quotes"=>false]);

        // query: event briefing
        $dataBriefing = [
            "start"=>"'$briefingStart'",
            "briefingID"=>"'$briefingID'",
        ];
        $queryBriefing = $isBriefing
            ? $this->generateQuerySingleUpdate(self::DB_EVENTS_BRIEFINGS,$dataBriefing,"eventID",$eventID,["quotes"=>false])
            : $this->generateQuerySingleDelete(self::DB_EVENTS_BRIEFINGS, "eventID", $eventID);

        // query: event prices
        $queryPrices = $this->generateQuerySingleDelete(self::DB_EVENTS_PRICES, "eventID", $eventID);
        $index = 1;
        foreach($prices as $price){
            $dataPrice = [
                "eventID"=>$eventID,
                "index"=>$index++,
                "rank_from"=>isset($price["rank_from"]) ? "'".$price["rank_from"]."'" : null,
                "rank_to"=>isset($price["rank_to"]) ? "'".$price["rank_to"]."'" : null,
                "gold"=>isset($price["gold"]) ? "'".$price["gold"]."'" : null,
                "others"=>isset($price["others"]) ? $this->quote($price["others"]) : null,
            ];
            $queryPrices .= $this->generateQueryInsert(self::DB_EVENTS_PRICES,$dataPrice,["quotes"=>false]);

        }

        // query: event prices
        $queryMaps = $this->generateQuerySingleDelete(self::DB_EVENTS_MAPS, "eventID", $eventID);;
        foreach($maps as $map){

            $dataMap = [
                "eventID"=>$eventID,
                "mapID"=>isset($map["mapID"]) ? "'".$map["mapID"]."'" : null,
                "modeID"=>isset($map["modeID"]) ? "'".$map["modeID"]."'" : null,
                "order"=>isset($map["order"]) ? "'".$map["order"]."'" : "DEFAULT",
            ];
            $queryMaps .= $this->generateQueryInsert(self::DB_EVENTS_MAPS,$dataMap,["quotes"=>false]);

        }

        // build transaction query
        $query = "BEGIN;
            $queryEvents
            $queryDescr
            $queryBriefing
            $queryMaps
            $queryPrices
        COMMIT;";

        return $this->queryInsert($query);
    }

    public function postNews($data){
        $userID = isset($data["userID"]) ? $data["userID"] : null;
        $title = isset($data["title"]) ? $data["title"] : null;
        $text = isset($data["text"]) ? $data["text"] : null;
        $summary = isset($data["summary"]) ? $data["summary"] : null;
        $uid = isset($data["uid"]) ? $data["uid"] : null;
        $catID = isset($data["catID"]) ? $data["catID"] : null;
        $coverimage = isset($data["coverimage"]) ? $data["coverimage"] : null;

        if(empty($userID) || empty($title) || empty($text)) return false;
        $qTitle = $this->quote($title);
        $qText = $this->quote($text);
        $qSummary = isset($summary) ? $this->quote($summary) : "null";
        $qUid = isset($uid) ? $this->quote($uid) : "null";
        $qCover = isset($coverimage) ? $this->quote($coverimage) : "null";

        $query = "BEGIN;
        INSERT INTO ".self::DB_NEWS."(userID,catID) VALUES('$userID','$catID');
        INSERT INTO ".self::DB_NEWS_DESCRIPTION."(newsID,title,text,summary,uid,coverimage)
            VALUES(LAST_INSERT_ID(),$qTitle,$qText,$qSummary,$qUid,$qCover);
        COMMIT;";
        return $this->queryInsert($query);
    }

    public function updateNews($data){
        $userID = isset($data["userID"]) ? $data["userID"] : null;
        $newsID = isset($data["newsID"]) ? $data["newsID"] : null;
        $title = isset($data["title"]) ? $data["title"] : null;
        $text = isset($data["text"]) ? $data["text"] : null;
        $summary = isset($data["summary"]) ? $data["summary"] : null;
        $uid = isset($data["uid"]) ? $data["uid"] : null;
        $catID = isset($data["catID"]) ? $data["catID"] : null;
        $coverimage = isset($data["coverimage"]) ? $data["coverimage"] : null;

        if(empty($userID) || empty($newsID) || empty($title) || empty($text)) return false;
        $qTitle = $this->quote($title);
        $qText = $this->quote($text);
        $qSummary = isset($summary) ? $this->quote($summary) : "null";
        $qUid = isset($uid) ? $this->quote($uid) : "null";
        $qCover = isset($coverimage) ? $this->quote($coverimage) : "null";

        $query = "BEGIN;
        INSERT INTO ".self::DB_NEWS_EDITS."(newsID,userID) VALUES('$newsID', '$userID');
        UPDATE ".self::DB_NEWS." SET catID='$catID' WHERE newsID='$newsID';
        UPDATE ".self::DB_NEWS_DESCRIPTION." SET title=$qTitle,text=$qText,summary=$qSummary,uid=$qUid,coverimage=$qCover WHERE newsID='$newsID';
        COMMIT;";
        return $this->queryInsert($query);
    }

    public function incNewsViewCount($id, $options=[]){
        $query = "UPDATE ".self::DB_NEWS." SET views=views+1 WHERE newsID='$id';";
        return $this->queryInsert($query);
    }

    public function incEventViewCount($id, $options=[]){
        $query = "UPDATE ".self::DB_EVENTS." SET views=views+1 WHERE eventID='$id';";
        return $this->queryInsert($query);
    }

    /**
	* data remover
	*/
	
	public function removeClanMemberByUserID($userID){
		$query = "UPDATE ".self::DB_CLAN_MEMBERS." SET deleted='1' WHERE userID='$userID';";
		return $this->queryInsert($query);
	}
	
	public function removeClanMembersByClanID($clanID){
		$query = "UPDATE ".self::DB_CLAN_MEMBERS." SET deleted='1' WHERE clanID='$clanID';";
		return $this->queryInsert($query);
	}

	public function removeNews($newsID){
		$query = "UPDATE ".self::DB_NEWS." SET deleted='1' WHERE newsID='$newsID';";
		return $this->queryInsert($query);
	}

	public function removeEvent($eventID){
		$query = "UPDATE ".self::DB_EVENTS." SET deleted='1' WHERE eventID='$eventID';";
		return $this->queryInsert($query);
	}

	/**
	* data fetch handler
	*/
	
	private function queryFirstRow($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		if($row = $sql->fetch(PDO::FETCH_ASSOC)){
			return $row;
		}
		return null;
	}
	
	private function queryFirstValue($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		if($row = $sql->fetch(PDO::FETCH_NUM)){
			return $row[0];
		}
		return 0;
	}
	
	private function queryAssoc($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function queryIndexedArray($query, $index){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		$out = [];
		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$item = [];
			foreach($row as $key=>$value)
				$item[$key] = $value;
			$out[$row[$index]] = $item;
		}
		return $out;
	}
	
	private function queryGroupResult($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		$out = [];
		while($row = $sql->fetch(PDO::FETCH_NUM)){
			$out[$row[0]][$row[1]] = $row[2];
//			$item[$row[1]] = $row[2];
//			foreach($row as $key=>$value)
//				if($key != $index) $item[$key] = $value;
//			$out[$row[$index]] = $item;
		}
		return $out;
	}
	
	private function queryKeyValuePair($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		$out = [];
		while($row = $sql->fetch(PDO::FETCH_NUM)){
			$out[$row[0]] = $row[1];
		}
		return $out;
	}

	private function queryList($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		$out = [];
		while($row = $sql->fetch(PDO::FETCH_NUM)){
			$out[] = $row[0];
		}
		return $out;
	}

	private function queryInsert($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
//        Debug::e("queryInsert");
//        Debug::v($sql);
		if($sql === false) return false;
		return true;
	}

	private function queryEntryExists($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
		if($sql === false) return false;
		if($row = $sql->fetch(PDO::FETCH_NUM))
			return true;
		return null;
	}


    /**
     * pdo functions
     */

	private function quote($text, $quotes=true){
		if(!$this->isConnection()) return false;
		else{
            $q = $this->db->quote($text);
            return $quotes ? $q : trim($q, "'");
        }
	}
	
	private function query($query){
		if(!$this->isConnection() || empty($query)) return false;
		try{
			$sql = $this->db->query($query);
//            Debug::e("pdo query");
//            Debug::v($sql);
		}catch(PDOException $e){
            Debug::e("error");
			$this->echoError($e->getMessage());
			return false;
		}
		return $sql;
	}

    public function getPlayerLoginData($userID){
        $query = "SELECT
            u.userID as id, u.name,
            ui.lang, ui.lastUpdate as updated,
            ur.`global` as ratingGlobal,
            cm.clanID, cm.role as clanRole, cm.role_i18n as clanRole_i18n, cm.joined as clanJoined,
            c.`name` as clanName, c.tag as clanTag, c.color as clanColor, c.isDisbanned as clanDisbanned, c.lastUpdate as clanLastUpdate
        FROM user u
            LEFT JOIN user_info ui ON ui.userID = u.userID
            LEFT JOIN user_ratings ur ON ur.userID = u.userID
            LEFT JOIN clan_members cm ON cm.userID = u.userID
            LEFT JOIN clan_info c ON c.clanID = cm.clanID
        WHERE u.userID='$userID';";
        return $this->queryFirstRow($query);
    }

}