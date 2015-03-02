<?php
class WotPlayer{
	private $id=null;
	private $name=null;
	private $lang=null;
	
	private $ratingGlobal=null;
	
	private $statsBattles=null;
	private $statsWins=null;
	private $statsShots=null;
	private $statsHits=null;
	private $statsDamage=null;
	private $statsWinRatePerBattle=null;
	private $statsAvgHitRate=null;
	private $statsAvgDamagePerBattle=null;
	
	private $statsWinRateClass=null;
	
	private $clanID=null;
	private $clanName=null;
	private $clanTag=null;
	private $clanColor=null;
	private $clanMembersCount=null;
	
	private $clanIsDisbanned=false;
	
	private $clanRole=null;
	private $clanRole_i18n=null;
	
	private $clanEmblemSmall=null;
	private $clanEmblemMedium=null;
	private $clanEmblemLarge=null;
	private $clanEmblemXLarge=null;
	private $clanEmblemXXLarge=null;

	private $apiLastUpdate=null;
	private $apiLastUpdateClan=null;

	function __construct($id, $name, $lang, $lastUpdate){
		$this->id = $id;
		$this->name = $name;
		$this->lang = $lang;
		$this->apiLastUpdate = $lastUpdate;
	}
	
	/* state getter */
	
	public function hasClan(){return !empty($this->clanID);}
	
	/* getter */
	
	public function getID(){return $this->id;}
	public function getName(){return $this->name;}
	public function getLang(){return $this->lang;}
	public function getGlobalRating(){return $this->ratingGlobal;}	
	public function getBattlesTotal(){return $this->statsBattles;}	
	public function getWinsTotal(){return $this->statsWins;}	
	public function getShotsTotal(){return $this->statsShots;}	
	public function getHitsTotal(){return $this->statsHits;}	
	public function getDamageTotal(){return $this->statsDamage;}
	public function getDamageTotal(){return $this->statsDamage;}
	public function getWinRatePerBattle(){return $this->statsWinRatePerBattle;}
	public function getAvgHitRate(){return $this->statsAvgHitRate;}
	public function getAvgDamagePerBattle(){return $this->statsAvgDamagePerBattle;}
	public function getWinRateClass(){return $this->statsWinRateClass;}
	public function getClanID(){return $this->clanID;}
	public function getClanName(){return $this->clanName;}
	public function getClanTag(){return $this->clanTag;}
	public function getClanColor(){return $this->clanColor;}
	public function getClanMembersCount(){return $this->clanMembersCount;}
	public function getClanRole(){return $this->clanRole;}
	public function getClanRole_i18n(){return $this->clanRole_i18n;}
	public function getClanEmblemSmall(){return $this->clanEmblemSmall;}
	public function getClanEmblemMedium(){return $this->clanEmblemMedium;}
	public function getClanEmblemLarge(){return $this->clanEmblemLarge;}
	public function getClanEmblemXLarge(){return $this->clanEmblemXLarge;}
	public function getClanEmblemXXLarge(){return $this->clanEmblemXXLarge;}
	
	public function getLastUpdate(){return $this->apiLastUpdate;}
	public function getLastUpdateClan(){return $this->apiLastUpdateClan;}
	
	/* setter */
	
	public function setRating($global=null){
		$this->ratingGlobal = $global;
	}
	
	public function setStats($battles=0, $wins=0, $shots=0, $hits=0, $damage=0, $lastUpdate=null){
		$this->statsBattles = $battles;
		$this->statsWins = $wins;
		$this->statsShots = $shots;
		$this->statsHits = $hits;
		$this->statsDamage = $damage;
		$this->apiLastUpdate = $lastUpdate;
		
		// calculations
		
		$this->statsWinRatePerBattle = $this->toWinRatePerBattle($battles, $wins);
		$this->statsAvgHitRate = $this->toHitRate($shots, $hits);
		$this->statsAvgDamagePerBattle = $this->toAvgDamge($battles, $damage);
		
		$this->statsWinRateClass = $this->toWinRateClass($this->statsWinRatePerBattle);
	}
	
	public function setClan($id=null, $name=null, $tag=null, $color=null, $membersCount=0, $isDisbanned=false, $role=null, $role_i18n=null, $eSmall=null, $eMedium=null, $eLarge=null, $eXLarge=null, $eXXLarge=null){
		
		$this->clanID=$id;
		$this->clanName=$name;
		$this->clanTag=$tag;
		$this->clanColor=$color;
		$this->clanMembersCount=$membersCount;
		
		$this->clanIsDisbanned=$isDisbanned;
		
		$this->clanRole=$role;
		$this->clanRole_i18n=$role_i18n;
		
		$this->clanEmblemSmall=$eSmall;
		$this->clanEmblemMedium=$eMedium;
		$this->clanEmblemLarge=$eLarge;
		$this->clanEmblemXLarge=$eXLarge;
		$this->clanEmblemXXLarge=$eXXLarge;
		
	}
	
	/* calculations */
	
	public function toWinRateClass($winRate){
		if(!isset($winRate)) return null;
		if($winRate < 47) return "boon";
		else if ($winRate < 50) return "average";
		else if ($winRate < 54) return "good";
		else if ($winRate < 59) return "elite";
		else return "legend";
	}
	
	public function toWinRatePerBattle($battles, $wins){
		if(isset($battles, $wins))
			return $battles != 0 ?  round($wins/$battles*100, 2) : 0;
		else return null;
	}
	
	public function toHitRate($shots, $hits){
		if(isset($shots, $hits))
			return $shots != 0 ?  round($hits/$shots*100, 2) : 0;
		else return null;
	}
	
	public function toAvgDamge($battles, $damage){
		if(isset($battles, $damage))
			return $battles != 0 ?  round($damage/$battles) : 0;
		else return null;
	}
	
}