<?php
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
	
	private function winRateToClass($winRate){
		if($winRate < 47) return "boon";
		else if ($winRate < 50) return "average";
		else if ($winRate < 54) return "good";
		else if ($winRate < 59) return "elite";
		else return "legend";
	}
	
	/* ================================================================================= */
	
	public function getBasicPlayerInfo($wotUser){
		if(!$this->isWotData()) return false;
//		Debug::r($wotUser);
		/* ============================================================================= */
		$fields = "global_rating,client_language,statistics.all.battles,statistics.all.wins,statistics.all.damage_dealt,statistics.all.hits,statistics.all.shots";
		$response = $this->wotData->getPlayerInfo($wotUser["userID"], $fields);
		if(!$this->success($response)) return false;
		$playerInfo = $response["data"][$wotUser["userID"]];
		if(empty($playerInfo)) return $playerInfo;
		
		/* handle additional player info =========================================== */
//		$language = $playerInfo["client_language"];
		$isClan = !empty($wotUser["clanID"]);

		/* get clan info =========================================================== */
		//$data = $this->wotData->getClanInfo($wotUser["clanID"], $language);
		$clanInfo = null;
		if($isClan){
			$fields = "abbreviation,color,name,emblems,members.role,members.role_i18n";
			$response = $this->wotData->getClanInfo($wotUser["clanID"], null, $fields);
			if($this->success($response))
				$clanInfo = $response["data"][$wotUser["clanID"]];
		}

		/* get member info ========================================================= */
		//$response = $this->wotData->getMemberInfo($wotUser["userID"], "abbreviation,color,clan_name,motto,role,role_i18n,emblems.large,emblems.medium,emblems.small");
		//if($this->success($response))
		//	$memberInfo = $response["data"][$wotUser["clanID"]];

		//exit();
		/* prepare out data ======================================================== */
		$out = [
			"stats"=>$playerInfo["statistics"]["all"],
			"rating"=>[
				"global"=>$playerInfo["global_rating"],
			],
			"lang"=>$playerInfo["client_language"],
			"clan"=>null,
		];
		
		
		/* handle player stats ===================================================== */
		$statsAll = $playerInfo["statistics"]["all"];
		$battles = $statsAll["battles"];
		$wins = $statsAll["wins"];
		$damage = $statsAll["damage_dealt"];
		$hits = $statsAll["hits"];
		$shots = $statsAll["shots"];
		
		$winRate = $battles == 0 ? 0 : round($wins/$battles*100, 2);

		$out["stats"]["winRatePerBattle"] = $winRate;
		$out["stats"]["winRatePerBattleClass"] = $this->winRateToClass($winRate);
		$out["stats"]["avgDamagePerBattle"] = $battles == 0 ? 0 : round($damage/$battles);
		$out["stats"]["avgHitratePerBattle"] = $shots == 0 ? 0 : round($hits/$shots*100, 2);

//		$stats = array("winRatePerBattle"=>$winRate, "winRatePerBattleClass"=>$winRateClass, "avgDamagePerBattle"=>$avgDamage, "avgHitratePerBattle"=>$avgHits);
//		$playerStats = array_merge($statsAll, $stats);

		/* ===================================================================================== */
//		$wotUser["info"]["stats"] = $playerStats;
//		$wotUser["lang"] = $language;
		
		/* handle clan info ==================================================================== */
		if(isset($clanInfo)){
			$out["clan"]["name"] = $clanInfo["name"];
			$out["clan"]["role"] = $clanInfo["members"][$wotUser["userID"]]["role"];
			$out["clan"]["role_i18n"] = $clanInfo["members"][$wotUser["userID"]]["role_i18n"];
			$out["clan"]["emblems"] = $clanInfo["emblems"];
			$out["clan"]["color"] = $clanInfo["color"];
			$out["clan"]["tag"] = $clanInfo["abbreviation"];
		}
		
		return $out;
	}
}