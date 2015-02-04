<?php
/**
* Project Tank Webpage
* getter for basic player information
* @deprecated
* @author Steffen Lange
*/
error_log(E_ALL);
include_once("../login.php");
if(!isset($activeLogin)) return exitOnError(100);
/* ===================================================================================== */
if(!isset($_GET["accountID"])) exitOnError(99, "Params: "+print_r($_GET, true));
$accountID = $_GET["accountID"];

$limit = isset($_GET["tank_limit"]) ? $_GET["tank_limit"]*1 : 7;

/* ===================================================================================== */
include_once("../libs/WotData.class.php");
include_once("../libs/Debug.class.php");
include_once("../libs/WotDB.class.php");
include_once("../libs/WotDBHandler.class.php");

$wotData = new WotData();
$dbh = new WotDBHandler(WotDB::getLink());
//$dbh->debug();
/* get player info ===================================================================== */
$playerInfo = $wotData->getPlayerInfo($accountID, "statistics");
if($playerInfo === false || empty($playerInfo) || $playerInfo["status"] != "ok") exitOnError(80);
$playerData = $playerInfo["data"][$accountID];

/* get tanks from db =================================================================== */
$tanks = $dbh->getTanks($accountID, 1, $limit);
if($tanks === false) $tanks = null;
$outData = array("tanks"=>$tanks);

//if($accountID == 500128447){
//	Debug::r($tanks);
//	Debug::r($playerInfo);
//}

/* ===================================================================================== */
$allStats = $playerData["statistics"]["all"];
$battles = $allStats["battles"];
$wins = $allStats["wins"];
$damage = $allStats["damage_dealt"];
$hits = $allStats["hits"];
$shots = $allStats["shots"];

$winRate = $battles == 0 ? 0 : round($wins/$battles*100, 2);
$winRateClass = getWinRateClass($winRate);

$avgDamage = $battles == 0 ? 0 : round($damage/$battles);
$avgHits = $shots == 0 ? 0 : round($hits/$shots*100, 2);

$outStats = array("winRatePerBattle"=>$winRate, "winRatePerBattleClass"=>$winRateClass, "avgDamagePerBattle"=>$avgDamage, "avgHitratePerBattle"=>$avgHits);
$outData = array_merge($outData, $outStats, $playerData["statistics"]);

/* ===================================================================================== */
//Debug::v($outData);
echo json_encode($outData);
/* ===================================================================================== */
/* functions =========================================================================== */
function exitOnError($errorCode, $appendix=null){
	$msg = "ERROR: ";
	switch($errorCode){
		case 100: $msg .= "login check failed"; break;
		case 99: $msg .= "missing parameter"; break;
		case 80: $msg .= "failed to get player info (gps)"; break;
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