<?php
/**
* Project Tank Webpage
* database handler
* @author Steffen Lange
*/
class DBHandler{
	
	const DB_USER = "user";
	const DB_USER_SETTINGS = "user_settings";
	const DB_USER_SETTINGS_TYPES = "user_settings_types";
	const DB_USER_HAS_TANKS = "hasTanks";
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
	
	private function echoError($msg){
		echo "<pre class='sql error'>$msg</pre>";
	}
	
	private function quote($text){
		if(!$this->isConnection()) return false;
		else return $this->db->quote($text);		
	}
	
	private function query($query){
		if(!$this->isConnection() || empty($query)) return false;
		try{
			$sql = $this->db->prepare($query);
			$sql->execute();
		}catch(Exception $e){
			$this->echoError($e->getMessage());
			return false;
		}
		return $sql;
	}
	
	private function queryCount($query){
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
	
	public function getDB(){
		return $this->db;
	}
	
	public function accountExists($accountID){
		$query = "SELECT accountID FROM ".self::DB_USER." WHERE accountID=$accountID;";
		return $this->queryEntryExists($query);
	}
	
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
//			print_v($tank);
			$tankID = $tank["tank_id"];
			$inGarage = $tank["in_garage"];
			$query .= "INSERT IGNORE INTO ".self::DB_USER_HAS_TANKS." (accountID, tankID, inGarage) VALUES ('$accountID', '$tankID', '$inGarage');";
		}
		if($query == "") return false;
		return $this->queryInsert($query);
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
		return $this->queryCount($query);
	}
	
	public function updateClan($clanID, $data){
		
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
	
//	public function setSettings($accountID, $showTanks){
//		$query = "INSERT INTO ".self::DB_USER_SETTINGS_TYPES."(accountID, showTanks) VALUES($accountID, $showTanks) ON DUPLICATE KEY UPDATE status=1";
//		if($query == "") return false;
//		return $this->queryInsert($query);
//	}
//	
	public function getUserSettings($userID, $settingsIDs=null){
		$qs = !isset($settingsIDs) ? null : " WHERE settingsID IN (".implode(",", $settingsIDs).")"; 
//		$query = "SELECT * FROM usersettings WHERE accountID='".$userID."'".$qs.";";
		$query = "SELECT s.settingsID, ifnull(u.value, s.defaultValue) as value FROM ".self::DB_USER_SETTINGS_TYPES." s
				LEFT JOIN (
					SELECT * FROM ".self::DB_USER_SETTINGS." WHERE userID='$userID'
				) u ON u.settingsID=s.settingsID".$qs.";";
		return $this->queryKeyValuePair($query);
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
	
	
}