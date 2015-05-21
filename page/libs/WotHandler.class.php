<?php
/**
* Project Tank Webpage
* handle and pre calc WGP API calls
* @author Steffen Lange
*/
require_once("WotData.class.php");
require_once("WotPlayer.class.php");
require_once("objects/PlayerObject.class.php");
require_once("objects/PlayerClanObject.class.php");
require_once("objects/ClanObject.class.php");
require_once("objects/ClanMemberObject.class.php");
require_once("objects/ClanEmblemsObject.class.php");
require_once("objects/StatisticObject.class.php");
require_once("objects/RatingObject.class.php");
class WotHandler{
	
	private $wotData = null;
	private $clanEmblemsTypeTable = [
		"24x24"=>"small",
		"32x32"=>"medium",
		"64x64"=>"large",
		"195x195"=>"xlarge",
		"256x256"=>"xxlarge"
	];
	private $clanEmblemAcceptedGames = ["","wowp"];
	
	function __construct($wotData){
		$this->wotData = $wotData;
	}
	
	/* ================================================================================= */
	
	private function isWotData(){
		return isset($this->wotData);
	}
	
	private function success($response){
		return $response !== false && isset($response["status"]) && $response["status"] == "ok";
	}
	
	private function errorMessage($response){
		return isset($response["error"], $response["error"]["message"]) ? $response["error"]["message"] : null;
	}
	
	private function getKey($key, $array, $default=null){
		return isset($array[$key]) ? $array[$key] : $default;
	}
	
	private function fieldToArray($field, $in){
		$out = [];
		foreach($in as $item)
			$out[] = $item[$field];
		return $out;
	}
	
	private function toIndexArray($key, $arr){
		$out = [];
		foreach($arr as $item)
			if(isset($item[$key])) $out[$item[$key]] = $item;
		return $out;
	}
	
	private function returnClanEmblems($small=null, $medium=null, $large=null, $xlarge=null, $xxlarge=null){
		
		$emblems = new ClanEmblemsObject();
		$emblems->small = $small;
		$emblems->medium = $medium;
		$emblems->large = $large;
		$emblems->xlarge = $xlarge;
		$emblems->xxlarge = $xxlarge;
		return $emblems;
	}
	
	public function parsePlayerRating($ratingGlobal){
		if(empty($ratingGlobal)) return null;
		$rating = new RatingObject();
		$rating->global = $ratingGlobal;
		return $rating;
	}
	
	private function parseClanEmblems($clanEmblems, $clanLastUpdate){
		function addToClanEmblemsObject(&$obj, $key, $value){
			switch($key){
				case "small": $obj->small = $value; break;
				case "medium": $obj->medium = $value; break;
				case "large": $obj->large = $value; break;
				case "xlarge": $obj->xlarge = $value; break;
				case "xxlarge": $obj->xxlarge = $value; break;
			}
		}

		$emblems = new ClanEmblemsObject();
		if(!isset($clanEmblems)) return $emblems;
		foreach($clanEmblems as $emblem){
			if(!in_array($emblem["game"], $this->clanEmblemAcceptedGames)) continue;
			$type = $emblem["type"];
			$key = array_key_exists($type,  $this->clanEmblemsTypeTable) ? $this->clanEmblemsTypeTable[$type] : $type;
			$url = $emblem["url"];
			$appendix = isset($clanLastUpdate) ? "?".$clanLastUpdate : null;
			addToClanEmblemsObject($emblems, $key, $url.$appendix);
		}
		return $emblems;
	}
	
	private function parsePlayerStats($statsAll){
		$battles = $wins = $shots = $hits = $damage = 0;
		$winRate = $hitRate = $avgDamage = 0;
		
		if(is_array($statsAll)){
			$battles= $this->getKey("battles", $statsAll, $battles);
			$wins 	= $this->getKey("wins", $statsAll, $wins);
			$shots 	= $this->getKey("shots", $statsAll, $shots);
			$hits 	= $this->getKey("hits", $statsAll, $hits);
			$damage = $this->getKey("damage_dealt", $statsAll, $damage);
			
			$winRate 	= $this->getWinRate($battles, $wins);
			$hitRate 	= $this->getHitRate($shots, $hits);
			$avgDamage	= $this->getAvgDamge($battles, $damage);
		}

		$stats = new StatisticObject();
		$stats->battles = $battles;
		$stats->wins = $wins;
		$stats->shots = $shots;
		$stats->hits = $hits;
		$stats->damage = $damage;
		$stats->winRatePerBattle = $winRate;
		$stats->winRateClass = $this->winRateToClass($winRate);
		$stats->avgDamagePerBattle = $avgDamage;
		$stats->avgHitRatePerBattle = $hitRate;

		return $stats;
	}
	
	public function parseInternalPlayerStats($statsAll){
		$battles = $wins = $shots = $hits = $damage = 0;
		$winRate = $hitRate = $avgDamage = 0;
		
		if(is_array($statsAll)){
			$battles= $this->getKey("battlesAll", $statsAll, $battles);
			$wins 	= $this->getKey("winsAll", $statsAll, $wins);
			$shots 	= $this->getKey("shotsAll", $statsAll, $shots);
			$hits 	= $this->getKey("hitsAll", $statsAll, $hits);
			$damage = $this->getKey("damageAll", $statsAll, $damage);
			
			$winRate 	= $this->getWinRate($battles, $wins);
			$hitRate 	= $this->getHitRate($shots, $hits);
			$avgDamage	= $this->getAvgDamge($battles, $damage);
		}

		$stats = new StatisticObject();
		$stats->battles = $battles;
		$stats->wins = $wins;
		$stats->shots = $shots;
		$stats->hits = $hits;
		$stats->damage = $damage;
		$stats->winRatePerBattle = $winRate;
		$stats->winRateClass = $this->winRateToClass($winRate);
		$stats->avgDamagePerBattle = $avgDamage;
		$stats->avgHitRatePerBattle = $hitRate;

		return $stats;
	}
	
	private function returnPlayerInfo($id=null, $name=null, $lang=null, $ratingGlobal=null, $updated=null,
									$statsAll=null,
									$clanID=null, $clanName=null, $clanTag=null, $clanRole=null,
									$clanMembersCount=null, $clanRole_i18n=null, $clanColor=null, 
									$clanDisbanned=false, $clanLastUpdate=null, $clanJoined=null, $clanEmblems=[]
	){
		$player = new WotPlayer();
		$player->set($id, $name, $lang, $updated);
		$player->setRating($this->parsePlayerRating($ratingGlobal));
		$player->setStatistic($this->parsePlayerStats($statsAll));
		if(isset($clanID)){
			$player->setClan($clanID, $clanName, $clanTag, $clanColor, $clanMembersCount, $clanRole, $clanRole_i18n, $clanJoined, $clanDisbanned, $clanLastUpdate);
			$player->setClanEmblemsObject($this->parseClanEmblems($clanEmblems, $clanLastUpdate));
		}
		return $player;
	}
	
	/* ================================================================================= */
	
	public function winRateToClass($winRate){
		if(!isset($winRate)) return null;
		else if($winRate < 47) return "boon";
		else if ($winRate < 50) return "average";
		else if ($winRate < 54) return "good";
		else if ($winRate < 59) return "elite";
		else return "legend";
	}
	
	public function getWinRate($battles, $wins){
		return $battles != 0 ?  round($wins/$battles*100, 2) : 0;
	}
	
	public function getHitRate($shots, $hits){
		return $shots != 0 ?  round($hits/$shots*100, 2) : 0;
	}
	
	public function getAvgDamge($battles, $damage){
		return $battles != 0 ?  round($damage/$battles) : 0;
	}
	
	public function getClanMemberStats($clanID){
		if(!$this->isWotData()) return false;
		/* get clan member list ======================================================== */
		$fields = "members";
		$response = $this->wotData->getClanInfo($clanID, null, $fields);
		if(!$this->success($response)) return false;
		$clanInfo = $response["data"][$clanID];
		if(empty($clanInfo) || !isset($clanInfo["members"])) return null;
		$clanMembers = $this->toIndexArray("account_id", $clanInfo["members"]);
		$ids = array_keys($clanMembers);
		if(empty($ids)) return null;
//		Debug::r($clanMembers);
		/* get member stats ============================================================ */
		$fields = "nickname,global_rating,statistics.all.battles,statistics.all.wins,statistics.all.damage_dealt,statistics.all.hits,statistics.all.shots";
		$response = $this->wotData->getPlayerInfo(implode(",", $ids), null, $fields);
//		Debug::e($response);
		if(!$this->success($response)) return false;
		
		/* prepare out data ============================================================ */
		$out = [];
		foreach($response["data"] as $id=>$member){
			$statsAll = isset($member["statistics"],$member["statistics"]["all"]) ? $member["statistics"]["all"] : null;
			
			$obj = new ClanMemberObject();
			$obj->id = $id;
			$obj->name = $member["nickname"];
			$obj->role = $clanMembers[$id]["role"];
			$obj->role_i18n = $clanMembers[$id]["role_i18n"];
			$obj->joined = $clanMembers[$id]["joined_at"];
			$obj->rating = $this->parsePlayerRating($member["global_rating"]);
			$obj->statistic = $this->parsePlayerStats($statsAll);
			
			$out[$id] = $obj;
		}
		return $out;
	}
	
	public function getBasicPlayerInfo($wotUser){
		if(!$this->isWotData()) return false;
//		Debug::r($wotUser);
		/* ============================================================================= */
		$fields = "global_rating,client_language,updated_at,statistics.all.battles,statistics.all.wins,statistics.all.damage_dealt,statistics.all.hits,statistics.all.shots";
		$response = $this->wotData->getPlayerInfo($wotUser["userID"], $wotUser["token"], $fields);
		if(!$this->success($response)) return false;
		$playerInfo = $response["data"][$wotUser["userID"]];
		if(empty($playerInfo)) return $this->returnPlayerInfo($wotUser["userID"], $wotUser["userName"]);
		
		/* get members info ======================================================== */
		$membersInfo = null;
		$fields = "joined_at,role,role_i18n,clan.clan_id,clan.color,clan.members_count,clan.name,clan.tag";
		$response = $this->wotData->getMembersInfo($wotUser["userID"], $fields);
		if($this->success($response))
			$membersInfo = $response["data"][$wotUser["userID"]];
//		else Debug::e($response);
		$hasClan = isset($membersInfo, $membersInfo["clan"], $membersInfo["clan"]["clan_id"]) && !empty($membersInfo["clan"]["clan_id"]);
//		Debug::v($membersInfo);
		/* get additional clan info ================================================ */
		$clanInfo = null;
		$isClanInfo = false;
		if($hasClan){
			$fields = "is_clan_disbanded,emblems,updated_at";
			$response = $this->wotData->getClanInfo($membersInfo["clan"]["clan_id"], $wotUser["token"], $fields);
			if($this->success($response))
				$clanInfo = $response["data"][$membersInfo["clan"]["clan_id"]];
//			else Debug::e($response);
			$isClanInfo = isset($clanInfo);
		}
		
		/* prepare out data ======================================================== */	
		$hasStats = isset($playerInfo["statistics"]);
		$hasStatsAll = $hasStats && isset($playerInfo["statistics"]["all"]);
		$statsAll = $playerInfo["statistics"]["all"];
		
		$lang 			= isset($playerInfo["client_language"]) ? $playerInfo["client_language"] : null;
		$ratingGlobal 	= isset($playerInfo["global_rating"]) ? $playerInfo["global_rating"] : null;
		$updated	 	= isset($playerInfo["updated_at"]) ? $playerInfo["updated_at"] : null;
//		$statsBattles 	= $hasStatsAll && isset($statsAll["battles"]) ? $statsAll["battles"] : 0;
//		$statsWins 		= $hasStatsAll && isset($statsAll["wins"]) ? $statsAll["wins"] : 0;
//		$statsShots 	= $hasStatsAll && isset($statsAll["shots"]) ? $statsAll["shots"] : 0;
//		$statsHits 		= $hasStatsAll && isset($statsAll["hits"]) ? $statsAll["hits"] : 0;
//		$statsDamage 	= $hasStatsAll && isset($statsAll["damage_dealt"]) ? $statsAll["damage_dealt"] : 0;
//		$statsWinRate 	= $hasStatsAll ? $this->getWinRate($statsBattles, $statsWins) : 0;
//		$statsHitRate 	= $hasStatsAll ? $this->getHitRate($statsShots, $statsHits) : 0;
//		$statsAvgDamage	= $hasStatsAll ? $this->getAvgDamge($statsBattles, $statsDamage) : 0;
		
		/* handle clan info ==================================================================== */
		$clanID 		= $hasClan ? $membersInfo["clan"]["clan_id"] : null;
		$clanName 		= $hasClan ? $membersInfo["clan"]["name"] : null;
		$clanTag 		= $hasClan ? $membersInfo["clan"]["tag"] : null;
		$clanColor 		= $hasClan ? $membersInfo["clan"]["color"] : null;
		$clanMembersCount = $hasClan ? $membersInfo["clan"]["members_count"] : null;
		$clanRole 		= $hasClan ? $membersInfo["role"] : null;
		$clanRole_i18n 	= $hasClan ? $membersInfo["role_i18n"] : null;
		$clanEmblems	= $isClanInfo ? $clanInfo["emblems"] : null;
		$clanDisbanned	= $isClanInfo ? $clanInfo["is_clan_disbanded"] : false;
		$clanLastUpdate	= $isClanInfo ? $clanInfo["updated_at"] : null;
		$clanJoined		= $isClanInfo ? $membersInfo["joined_at"] : null;
		
		return $this->returnPlayerInfo($wotUser["userID"], $wotUser["userName"], $lang, $ratingGlobal, $updated,
								$statsAll,
								$clanID, $clanName, $clanTag, $clanRole, $clanMembersCount,
								$clanRole_i18n, $clanColor, $clanDisbanned, $clanLastUpdate,
								$clanJoined, $clanEmblems);
	}
	
	public function getCWClanBattles($wotUser){
		$player = $wotUser["player"];
		if(!$player->hasClan()) return false;
		$response = $this->wotData->getCWClanBattles($wotUser["token"], WotData::MAP_ID_GLOBALMAP, $player->getClanID(), null, $player->getLang());
//		Debug::r($response);
		if(!$this->success($response)) return false;
		return $response;
	}
}