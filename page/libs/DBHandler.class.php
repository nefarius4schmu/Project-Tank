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
		if(!$this->isConnection()) return false;
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
		$sql = $this->query($query);
		if($sql === false) return false;
		if($row = $sql->fetch(PDO::FETCH_NUM)){
			return $row[0];
		}
		return 0;
	}
	
	private function queryAssoc($query){
		$sql = $this->query($query);
		if($sql === false) return false;
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function queryInsert($query){
		$sql = $this->query($query);
		if($sql === false) return false;
		return true;
	}
	
	private function queryEntryExists($query){
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
		if($this->isDebug()) return $query;
		else return $this->queryEntryExists($query);
	}
	
	public function accountLogin($accountID, $nickname){
		$query = "INSERT INTO user(accountID, nickname, lastLogin) 
				VALUES('$accountID', '$nickname', now()) 
				ON DUPLICATE KEY UPDATE lastLogin = now()";
//		$query = "INSERT IGNORE INTO user(accountID, nickname, lastLogin) 
//				VALUES($accountID, '$nickname', now());"
				//ON DUBLICATE KEY UPDATE lastLogin=now();";
		if($this->isDebug()) return $query;
		else return $this->queryInsert($query);
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
		if($this->isDebug()) return $query;
		else return $this->queryInsert($query);
	}
	
	public function getTanks($accountID, $inGarage=null, $limit=null){
		$query = "SELECT a.tankID, a.inGarage, b.tankName, b.imageName, b.nation, b.level
				FROM hasTanks a
				LEFT JOIN tanks b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID'";
		$query .= isset($inGarage) ? " AND a.inGarage=$inGarage" : "";
		$query .= " ORDER BY b.level DESC";
		$query .= isset($limit) ? " LIMIT 0, $limit;" : ";";
		if($this->isDebug()) return $query;
		else return $this->queryAssoc($query);
	}
	
	public function countTanksByLevel($accountID, $level){
		if(!$this->isConnection()) return false;
		$query = "SELECT COUNT(a.tankID) as count FROM hasTanks a
				LEFT JOIN tanks b ON b.tankID = a.tankID
				WHERE a.accountID='$accountID' AND a.inGarage=1 AND b.level=$level";
		if($this->isDebug()) return $query;
		else return $this->queryCount($query);
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
		if($this->isDebug()) return $query;
		else return $this->queryAssoc($query);
	}
	
	public function setSettings($accountID, $showTanks){
		$query = "INSERT INTO settings (accountID, showTanks) VALUES($accountID, $showTanks) ON DUPLICATE KEY UPDATE status=1";
		if($query == "") return false;
		if($this->isDebug()) return $query;
		else return $this->queryInsert($query);
	}
	
	
}