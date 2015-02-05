<?php
/**
* Project Tank Webpage
* handle and pre calc WGP API calls
* @author Steffen Lange
*/
class WotHandler{
	
	private $wotData = null;
	
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
	
	private function returnPlayerInfo($id=null, $name=null, $lang=null, $ratingGlobal=null,
									$statsBattles=0, $statsWins=0, 
									$statsShots=0, $statsHits=0, $statsDamage=0,
									$statsWinRate=0, $statsHitRate=0, $statsAvgDamage=0,
									$clanID=null, $clanName=null, $clanTag=null, $clanRole=null,
									$clanRole_i18n=null, $clanColor=null, 
									$emblemSmall=null, $emblemMedium=null, 
									$emblemLarge=null
	){
		return [
			"id"=>$id,
			"name"=>$name,
			"lang"=>$lang,
			"rating"=>[
				"global"=>$ratingGlobal,
			],
			"stats"=>[
				"battles"=>$statsBattles,
				"wins"=>$statsWins,
				"shots"=>$statsShots,
				"hits"=>$statsHits,
				"damage"=>$statsDamage,
				"winRatePerBattle"=>$statsWinRate,
				"avgHitRatePerBattle"=>$statsHitRate,
				"avgDamagePerBattle"=>$statsAvgDamage,
			],
			"clan"=>[
				"id"=>$clanID,
				"name"=>$clanName,
				"tag"=>$clanTag,
				"role"=>$clanRole,
				"role_i18n"=>$clanRole_i18n,
				"color"=>$clanColor,
				"emblems"=>[
					"small"=>$emblemSmall,
					"medium"=>$emblemMedium,
					"large"=>$emblemLarge,
				],
			],
		];
	}
	
	/* ================================================================================= */
	
	public function winRateToClass($winRate){
		if($winRate < 47) return "boon";
		else if ($winRate < 50) return "average";
		else if ($winRate < 54) return "good";
		else if ($winRate < 59) return "elite";
		else return "legend";
	}
		
	public function getBasicPlayerInfo($wotUser){
		if(!$this->isWotData()) return false;
//		Debug::r($wotUser);
		/* ============================================================================= */
		$fields = "global_rating,client_language,statistics.all.battles,statistics.all.wins,statistics.all.damage_dealt,statistics.all.hits,statistics.all.shots";
		$response = $this->wotData->getPlayerInfo($wotUser["userID"], $fields);
		if(!$this->success($response)) return false;
		$playerInfo = $response["data"][$wotUser["userID"]];
		if(empty($playerInfo)) return $this->returnPlayerInfo($wotUser["userID"], $wotUser["userName"]);
		
		/* handle additional player info =========================================== */
		$hasClan = !empty($wotUser["clanID"]);

		/* get clan info =========================================================== */
		//$data = $this->wotData->getClanInfo($wotUser["clanID"], $language);
		$clanInfo = null;
		if($hasClan){
			$fields = "abbreviation,color,name,emblems,members.role,members.role_i18n";
			$response = $this->wotData->getClanInfo($wotUser["clanID"], null, $fields);
			if($this->success($response))
				$clanInfo = $response["data"][$wotUser["clanID"]];
			$hasClan = isset($clanInfo);
		}
		/* get member info ========================================================= */
		//$response = $this->wotData->getMemberInfo($wotUser["userID"], "abbreviation,color,clan_name,motto,role,role_i18n,emblems.large,emblems.medium,emblems.small");
		//if($this->success($response))
		//	$memberInfo = $response["data"][$wotUser["clanID"]];

		//exit();
		/* prepare out data ======================================================== */	
		$hasStats = isset($playerInfo["statistics"]);
		$hasStatsAll = $hasStats && isset($playerInfo["statistics"]["all"]);
		$statsAll = $playerInfo["statistics"]["all"];
		
		$lang 			= isset($playerInfo["client_language"]) ? $playerInfo["client_language"] : null;
		$ratingGlobal 	= isset($playerInfo["global_rating"]) ? $playerInfo["global_rating"] : null;
		$statsBattles 	= $hasStatsAll && isset($statsAll["battles"]) ? $statsAll["battles"] : 0;
		$statsWins 		= $hasStatsAll && isset($statsAll["wins"]) ? $statsAll["wins"] : 0;
		$statsShots 	= $hasStatsAll && isset($statsAll["shots"]) ? $statsAll["shots"] : 0;
		$statsHits 		= $hasStatsAll && isset($statsAll["hits"]) ? $statsAll["hits"] : 0;
		$statsDamage 	= $hasStatsAll && isset($statsAll["damage_dealt"]) ? $statsAll["damage_dealt"] : 0;
		$statsWinRate 	= $hasStatsAll && $statsBattles != 0 ?  round($statsWins/$statsBattles*100, 2) : 0;
		$statsHitRate 	= $hasStatsAll && $statsShots != 0 ?  round($statsHits/$statsShots*100, 2) : 0;
		$statsAvgDamage = $hasStatsAll && $statsBattles != 0 ?  round($statsDamage/$statsBattles) : 0;

		/* handle clan info ==================================================================== */
		$clanID 		= $hasClan ? $wotUser["userID"] : null;
		$clanName 		= $hasClan ? $clanInfo["name"] : null;
		$clanTag 		= $hasClan ? $clanInfo["abbreviation"] : null;
		$clanRole 		= $hasClan ? $clanInfo["members"][$wotUser["userID"]]["role"] : null;
		$clanRole_i18n 	= $hasClan ? $clanInfo["members"][$wotUser["userID"]]["role_i18n"] : null;
		$clanColor 		= $hasClan ? $clanInfo["color"] : null;
		$emblemSmall 	= $hasClan ? $clanInfo["emblems"]["small"] : null;
		$emblemMedium 	= $hasClan ? $clanInfo["emblems"]["medium"] : null;
		$emblemLarge 	= $hasClan ? $clanInfo["emblems"]["large"] : null;
		
		return $this->returnPlayerInfo($wotUser["userID"], $wotUser["userName"], $lang, $ratingGlobal,
								$statsBattles, $statsWins, 
								$statsShots, $statsHits, $statsDamage,
								$statsWinRate, $statsHitRate, $statsAvgDamage,
								$clanID, $clanName, $clanTag, $clanRole,
								$clanRole_i18n, $clanColor, 
								$emblemSmall, $emblemMedium, 
								$emblemLarge);
	}
}