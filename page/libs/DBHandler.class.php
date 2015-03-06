<?php
/**
* Project Tank Webpage
* database handler
* @author Steffen Lange
*/
class DBHandler{
	const DB_TIMESTAMP_FORMAT = "Y-m-d H:i:s";
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
	const DB_TANKS = "tanks";
	const DB_EVENTS = "events";
	const DB_EVENT_HAS_PRICE = "eventHasPrice";
	
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
	
	/**
	* data getter
	*/
	
	public function userExists($userID){
		$query = "SELECT userID FROM ".self::DB_USER." WHERE userID=$userID;";
		return $this->queryEntryExists($query);
	}
	
	public function getClanMemberByUserID($userID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." WHERE userID='$userID' AND deleted='0';";
		return $this->queryFirstRow($query);
	}
	
	public function getClanMembers($clanID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." WHERE clanID='$clanID';";
		return $this->queryIndexedArray($query, "userID");
	}
	
	public function getClanMembersInfo($clanID){
		$query = "SELECT * FROM ".self::DB_CLAN_MEMBERS." m
		LEFT JOIN ".self::DB_USER_INFO." u ON u.userID=m.userID
		LEFT JOIN ".self::DB_USER_RATINGS." r ON r.userID=m.userID
		WHERE clanID='$clanID';";
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
	
	public function getEventListByClanID($clanID, $isFinished=0, $dateStart=null, $dateEnd=null){
		if(!isset($clanID)) return false;
		$query = "SELECT a.*, b.prices FROM ".self::DB_EVENTS." a 
				LEFT JOIN ( 
					SELECT eventID, count(eventID) AS prices FROM ".self::DB_EVENT_HAS_PRICE." 
					WHERE eventID=1 GROUP BY eventID) b 
				ON b.eventID=a.eventID 
				WHERE clanID=$clanID;";
		return $this->queryAssoc($query);
	}
	
	public function getUserSettings($userID, $settingsIDs=null){
		$qs = !isset($settingsIDs) ? null : " WHERE settingsID IN (".implode(",", $settingsIDs).")"; 
		$query = "SELECT s.settingsID, ifnull(u.value, s.defaultValue) as value FROM ".self::DB_USER_SETTINGS_TYPES." s
				LEFT JOIN (
					SELECT * FROM ".self::DB_USER_SETTINGS." WHERE userID='$userID'
				) u ON u.settingsID=s.settingsID".$qs.";";
		return $this->queryKeyValuePair($query);
	}
	
	public function getUserStats($userID){
		$query = "SELECT t.name, ifnull(s.value,t.defaultValue) as value FROM ".self::DB_USER_STATS_TYPES." t
			LEFT JOIN (SELECT statsID,value FROM ".self::DB_USER_STATS." WHERE userID='$user') s ON s.statsID=t.statsID;";
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
				if($key != $index) $item[$key] = $value;
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
	
	private function queryInsert($query){
		if($this->isDebug()) return $query;
		$sql = $this->query($query);
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
	
	private function quote($text){
		if(!$this->isConnection()) return false;
		else return $this->db->quote($text);		
	}
	
	private function query($query){
		if(!$this->isConnection() || empty($query)) return false;
		try{
			$sql = $this->db->query($query);
		}catch(Exception $e){
			$this->echoError($e->getMessage());
			return false;
		}
		return $sql;
	}
	
}