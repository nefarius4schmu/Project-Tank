<?php
/**
* Project Tank Webpage
* getter for basic player information
* @deprecated
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
_lib("WotData");
_lib("DB");
_lib("DBHandler");
$wotUser = $_page["user"];
/* ===================================================================================== */
$wotData = new WotData();
//$dbh = new DBHandler(DB::getLink());
//$dbh->debug();
/* get planer info ===================================================================== */
//$data = $wotData->getPlayerInfo($wotUser["accountID"]);
$fields = "global_rating,client_language,statistics.all.battles,statistics.all.wins,statistics.all.damage_dealt,statistics.all.hits,statistics.all.shots";
$data = $wotData->getPlayerInfo($wotUser["accountID"], $fields);
if($data === false || empty($data) || $data["status"] != "ok") Debug::e($data);//exitOnError(80, $data["error"]["message"]);
else $playerInfo = $data["data"][$wotUser["accountID"]];
//Debug::r($playerInfo);

if(isset($playerInfo)){
	/* handle additional player info ======================================================= */
	$language = $playerInfo["client_language"];
	$isClan = !empty($wotUser["clanID"]);

	/* get clan info ===================================================================== */
	//$data = $wotData->getClanInfo($wotUser["clanID"], $language);
	if($isClan){
		$data = $wotData->getClanInfo($wotUser["clanID"], null, "abbreviation,color,name,motto,emblems.large,emblems.medium,emblems.small,members.account_name,members.role,members.role_i18n");
		if($data === false || empty($data) || $data["status"] != "ok") $clanInfo = null;//exitOnError(80, $data["error"]["message"]);
		else $clanInfo = $data["data"][$wotUser["clanID"]];
	}

	/* get member info ===================================================================== */
	//$data = $wotData->getMemberInfo($wotUser["accountID"], "abbreviation,color,clan_name,motto,role,role_i18n,emblems.large,emblems.medium,emblems.small");
	//if($data == null || $data["status"] != "ok") Debug::e($data);//exitOnError(80, $data["error"]["message"]);
	//else $memberInfo = $data["data"][$wotUser["clanID"]];

	//Debug::r($playerInfo);
	//Debug::r($clanInfo);
	//Debug::r($memberInfo);

	//exit();
	/* handle player stats =================================================================== */
	$allStats = $playerInfo["statistics"]["all"];
	$battles = $allStats["battles"];
	$wins = $allStats["wins"];
	$damage = $allStats["damage_dealt"];
	$hits = $allStats["hits"];
	$shots = $allStats["shots"];

	$winRate = $battles == 0 ? 0 : round($wins/$battles*100, 2);
	$winRateClass = getWinRateClass($winRate);
	$avgDamage = $battles == 0 ? 0 : round($damage/$battles);
	$avgHits = $shots == 0 ? 0 : round($hits/$shots*100, 2);

	$stats = array("winRatePerBattle"=>$winRate, "winRatePerBattleClass"=>$winRateClass, "avgDamagePerBattle"=>$avgDamage, "avgHitratePerBattle"=>$avgHits);
	$playerStats = array_merge($allStats, $stats);

	/* ===================================================================================== */
	$wotUser["info"]["stats"] = $playerStats;
	$wotUser["lang"] = $language;
	
	/* handle clan info ==================================================================== */
	if($isClan && isset($clanInfo)){
		$role = $clanInfo["members"][$wotUser["accountID"]]["role"];
		$role_i18n = $clanInfo["members"][$wotUser["accountID"]]["role_i18n"];
		
		$wotUser["info"]["clan"] = $clanInfo;
		$wotUser["role"] = $role;
		$wotUser["role_i18n"] = $role_i18n;
	}
}

/* ===================================================================================== */
/* functions =========================================================================== */
function exitOnError($errorCode, $appendix=null){
	$msg = "ERROR: ";
	switch($errorCode){
		case 100: $msg .= "login check failed"; break;
		case 99: $msg .= "missing parameter"; break;
		case 80: $msg .= "failed to get player info"; break;
		default: $msg = "Undefined Error ".$errorCode;
	}
	if(isset($appendix)) $msg .= "\n$appendix";
	echo $msg;
	exit();
}

function getWinRateClass($winRate){
	if($winRate < 47) return "boon";
	else if ($winRate < 50) return "average";
	else if ($winRate < 54) return "good";
	else if ($winRate < 59) return "elite";
	else return "legend";
}