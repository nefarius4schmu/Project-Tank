<?php
require_once("objects/PlayerObject.class.php");
require_once("objects/PlayerClanObject.class.php");
require_once("objects/ClanObject.class.php");
require_once("objects/ClanMemberObject.class.php");
require_once("objects/ClanEmblemsObject.class.php");
require_once("objects/StatisticObject.class.php");
require_once("objects/RatingObject.class.php");

/**
* WoT Player Handler Class
* 
* @author Steffen Lange
*/
class WotPlayer implements JsonSerializable{
	
	// typeof PlayerObject player
	private $player = null;
	
//	private static $instance = null;
	
//	public static function getInstance(){
//		if(!isset(self::$instance) || !(self::$instance instanceof self))
//			self::$instance = new self();
//		return self::$instance;
//	}
	
	function __construct(){
		$this->player = new PlayerObject();
	}
	
//	private function __construct(){
//		$this->player = new PlayerObject();
//	}
//	
//	private function __clone(){}
	
	public function isRating(){return isset($this->player->rating) && $this->player->rating instanceof RatingObject;}
	public function isStatistic(){return isset($this->player->statistic) && $this->player->statistic instanceof StatisticObject;}
	public function isClan(){return isset($this->player->clan) && $this->player->clan instanceof PlayerClanObject;}
	public function isClanEmblems(){return isset($this->player->clan->emblems) && $this->player->clan->emblems instanceof ClanEmblemsObject;}
	
	/* public getter */
	
	public function hasClan(){return $this->isClan() && !empty($this->getClanID());}
	
	public function getID(){return $this->player->id;}
	public function getName(){return $this->player->name;}
	public function getLang(){return $this->player->lang;}
	public function getLastUpdate(){return $this->player->lastUpdate;}
	public function getClanID(){return $this->player->clan->id;}
	public function getClanName(){return $this->player->clan->name;}
	public function getClanTag(){return $this->player->clan->tag;}
	public function getClanColor(){return $this->player->clan->color;}
	public function getClanRole(){return $this->player->clan->role;}
	public function getClanRole_i18n(){return $this->player->clan->role_i18n;}
	public function getClanJoined(){return $this->player->clan->joined;}
	public function getClanIsDisbanned(){return $this->player->clan->isDisbanned;}
	public function getClanLastUpdate(){return $this->player->clan->lastUpdate;}
	public function getClanMembers(){return $this->player->clan->members;}
	public function getClanEmblemSmall(){return $this->player->clan->emblems->small;}
	public function getClanEmblemMedium(){return $this->player->clan->emblems->medium;}
	public function getClanEmblemLarge(){return $this->player->clan->emblems->large;}
	public function getClanEmblemXLarge(){return $this->player->clan->emblems->xlarge;}
	public function getClanEmblemXXLarge(){return $this->player->clan->emblems->xxlarge;}
	public function getStatsBattles(){return $this->player->statistic->battles;}
	public function getStatsWins(){return $this->player->statistic->wins;}
	public function getStatsShots(){return $this->player->statistic->shots;}
	public function getStatsHits(){return $this->player->statistic->hits;}
	public function getStatsDamage(){return $this->player->statistic->damage;}
	public function getStatsWinRatePerBattle(){return $this->player->statistic->winRatePerBattle;}
	public function getStatsWinRateClass(){return $this->player->statistic->winRateClass;}
	public function getStatsAvgHitRatePerBattle(){return $this->player->statistic->avgHitRatePerBattle;}
	public function getStatsAvgDamagePerBattle(){return $this->player->statistic->avgDamagePerBattle;}
	public function getStatsLastUpdate(){return $this->player->statistic->lastUpdate;}
	public function getRatingGlobal(){return $this->player->rating->global;}
	
	/* public setter */
	
	public function setID($v){$this->player->id=$v;}
	public function setName($v){$this->player->name=$v;}
	public function setLang($v){$this->player->lang=$v;}
	public function setLastUpdate($v){$this->player->lastUpdate=$v;}
	public function setClanID($v){$this->player->clan->id=$v;}
	public function setClanName($v){$this->player->clan->name=$v;}
	public function setClanTag($v){$this->player->clan->tag=$v;}
	public function setClanColor($v){$this->player->clan->color=$v;}
	public function setClanRole($v){$this->player->clan->role=$v;}
	public function setClanRole_i18n($v){$this->player->clan->role_i18n=$v;}
	public function setClanJoined($v){$this->player->clan->joined=$v;}
	public function setClanIsDisbanned($v){$this->player->clan->isDisbanned=$v;}
	public function setClanLastUpdate($v){$this->player->clan->lastUpdate=$v;}
	public function setClanMembers($v){$this->player->clan->members=$v;}
	public function setClanEmblemSmall($v){$this->player->clan->emblems->small=$v;}
	public function setClanEmblemMedium($v){$this->player->clan->emblems->medium=$v;}
	public function setClanEmblemLarge($v){$this->player->clan->emblems->large=$v;}
	public function setClanEmblemXLarge($v){$this->player->clan->emblems->xlarge=$v;}
	public function setClanEmblemXXLarge($v){$this->player->clan->emblems->xxlarge=$v;}
	public function setStatsBattles($v){$this->player->statistic->battles=$v;}
	public function setStatsWins($v){$this->player->statistic->wins=$v;}
	public function setStatsShots($v){$this->player->statistic->shots=$v;}
	public function setStatsHits($v){$this->player->statistic->hits=$v;}
	public function setStatsDamage($v){$this->player->statistic->damage=$v;}
	public function setStatsWinRatePerBattle($v){$this->player->statistic->winRatePerBattle=$v;}
	public function setStatsWinRateClass($v){$this->player->statistic->winRateClass=$v;}
	public function setStatsAvgHitRatePerBattle($v){$this->player->statistic->avgHitRatePerBattle=$v;}
	public function setStatsAvgDamagePerBattle($v){$this->player->statistic->avgDamagePerBattle=$v;}
	public function setStatsLastUpdate($v){$this->player->statistic->lastUpdate=$v;}
	public function setRatingGlobal($v){$this->player->rating->global=$v;}
	
	
	/* public setter functions */	
	
	public function set($id, $name, $lang, $lastUpdate){
		$this->player->id = $id;
		$this->player->name = $name;
		$this->player->lang = $lang;
		$this->player->lastUpdate = $lastUpdate;
	}
	
	public function setClan($id, $name, $tag, $color, $membersCount, $role, $role_i18n, $joined, $isDisbanned, $lastUpdate){
		$clan = new PlayerClanObject();
		$clan->id = $id;
		$clan->name = $name;
		$clan->tag = $tag;
		$clan->color = $color;
		$clan->membersCount = $membersCount;
		$clan->role = $role;
		$clan->role_i18n = $role_i18n;
		$clan->joined = $joined;
		$clan->isDisbanned = $isDisbanned;
		$clan->lastUpdate = $lastUpdate;
		
		$this->player->clan = $clan;
	}
	
//	public function setClanEmblems($small, $medium, $large, $xlarge, $xxlarge){
//		$emblems = new ClanEmblemsObject();
//		$emblems->small = $small;
//		$emblems->medium = $medium;
//		$emblems->large = $large;
//		$emblems->xlarge = $xlarge;
//		$emblems->xxlarge = $xxlarge;
//		
//		$this->player->clan->emblems = $emblems;
//	}
//	
	public function setClanEmblemsObject($emblems){
		$this->player->clan->emblems = $emblems;
	}
//	
//	public function setStatistic($battles, $wins, $shots, $hits, $damage, $lastUpdate){
//		$statistic = new StatisticObject();
//		$statistic->battles = $battles;
//		$statistic->wins = $wins;
//		$statistic->shots = $shots;
//		$statistic->hits = $hits;
//		$statistic->damage = $damage;
//		$statistic->lastUpdate = $lastUpdate;
//	}
//	
	public function setStatistic($statistic){
		$this->player->statistic = $statistic;
	}
	
	public function setRating($rating){
		$this->player->rating = $rating;
	}


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return $this->player;
    }
}