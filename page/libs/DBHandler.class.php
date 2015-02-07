<?php
/**
* Project Tank Webpage
* database handler
* @author Steffen Lange
*/
class DBHandler{
	
	private $db = null;
	private $d = false;
	
	function __construct($db){
		if($db !== false)
			$this->db = $db;
	}
	
	public function debug(){$this->d = true;}
	public function isDebug(){return $this->d;}
	public function isConnection(){return $this->db !== null;}
	
	private function echoError($msg){
		echo "<pre class='sql error'>$msg</pre>";
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
		$query = "SELECT accountID FROM user WHERE accountID=$accountID;";
		return $this->queryEntryExists($query);
	}
	
	public function accountLogin($accountID, $nickname){
		$query = "INSERT INTO user(accountID, nickname, lastLogin) 
				VALUES('$accountID', '$nickname', now()) 
				ON DUPLICATE KEY UPDATE lastLogin = now()";
//		$query = "INSERT IGNORE INTO user(accountID, nickname, lastLogin) 
//				VALUES($accountID, '$nickname', now());"
				//ON DUBLICATE KEY UPDATE lastLogin=now();";
		return $this->queryInsert($query);
	}
	
	public function addTanks($accountID, $tanks){
		$query = "";
		foreach($tanks as $tank){
//			print_v($tank);
			$tankID = $tank["tank_id"];
			$inGarage = $tank["in_garage"];
			$query .= "INSERT IGNORE INTO `hasTanks` (`accountID`, `tankID`, `inGarage`) VALUES ('$accountID', '$tankID', '$inGarage');";
		}
		if($query == "") return false;
		return $this->queryInsert($query);
	}
	
	public function getTanks($accountID, $inGarage=null, $limit=null){
		$query = "SELECT a.tankID, a.inGarage, b.tankName, b.imageName, b.nation, b.level
				FROM hasTanks a
				LEFT JOIN tanks b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID'";
		$query .= isset($inGarage) ? " AND a.inGarage=$inGarage" : "";
		$query .= " ORDER BY b.level DESC";
		$query .= isset($limit) ? " LIMIT 0, $limit;" : ";";
		return $this->queryAssoc($query);
	}
	
	public function countTanksByLevel($accountID, $level){
		if(!$this->isConnection()) return false;
		$query = "SELECT COUNT(a.tankID) as count FROM hasTanks a
				LEFT JOIN tanks b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID' AND a.inGarage=1 AND b.level=$level";
		return $this->queryCount($query);
	}
	
	public function updateClan($clanID, $data){
		
	}

	public function getEventListByClanID($clanID, $isFinished=0, $dateStart=null, $dateEnd=null){
		if(!isset($clanID)) return false;
		$query = "SELECT a.*, b.prices FROM `events` a 
				LEFT JOIN ( 
					SELECT eventID, count(eventID) AS prices FROM eventHasPrice 
					WHERE eventID=1 GROUP BY eventID) b 
				ON b.eventID=a.eventID 
				WHERE clanID=$clanID;";
		return $this->queryAssoc($query);
	}
	
	public function setSettings($accountID, $showTanks){
		$query = "INSERT INTO settings (accountID, showTanks) VALUES($accountID, $showTanks) ON DUPLICATE KEY UPDATE status=1";
		if($query == "") return false;
		return $this->queryInsert($query);
	}
	
	public function getUserSettings($accountID, $settingsIDs=null){
		$qs = !isset($settingsIDs) ? null : " WHERE settingsID IN (".implode(",", $settingsIDs).")"; 
//		$query = "SELECT * FROM usersettings WHERE accountID='".$accountID."'".$qs.";";
		$query = "SELECT s.settingsID, ifnull(u.value, s.defaultValue) as value FROM settings s
				LEFT JOIN (
					SELECT * FROM usersettings WHERE accountID='$accountID'
				) u ON u.settingsID=s.settingsID".$qs.";";
		return $this->queryKeyValuePair($query);
	}
	
	public function setUserSetting($accountID, $settingsID, $value){
		$query = "INSERT INTO usersettings(accountID, settingsID, value) VALUES('$accountID', '$settingsID', '$value') ON DUPLICATE KEY UPDATE value='$value';";
		return $this->queryInsert($query);
	}
	
	public function setUserSettings($accountID, $settings){
		if(!isset($accountID, $settings)) return false;
		else if(empty($settings)) return true;
		$query = "";
		foreach($settings as $id=>$value){
			$query .= "INSERT INTO usersettings(accountID, settingsID, value) VALUES('$accountID', '$id', '$value') ON DUPLICATE KEY UPDATE value='$value';";	
		}
		return $this->queryInsert($query);
	}
	
	
}