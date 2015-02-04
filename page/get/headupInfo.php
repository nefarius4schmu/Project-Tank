<?php
/**
* Project Tank Webpage
* getter for headup display basic information
* @deprecated
* @author Steffen Lange
*/
error_log(E_ALL);
include_once("../login.php");
if(!isset($activeLogin)) return exitOnError(100);
/* ===================================================================================== */
if(!isset($_GET["accountID"], $_GET["tank_level"])) exitOnError(99);
$accountID = $_GET["accountID"];
$tankLevel = $_GET["tank_level"];

/* ===================================================================================== */
include_once("../libs/WotData.class.php");
include_once("../libs/Debug.class.php");
include_once("../libs/WotDB.class.php");
include_once("../libs/WotDBHandler.class.php");

$wotData = new WotData();
$dbh = new WotDBHandler(WotDB::getLink());
//$dbh->debug();
/* get member info ===================================================================== */
$memberInfo = $wotData->getClanInfo($wotUser["clanID"], "color,role,role_i18n,emblems");
if($memberInfo === false) $memberData = [];
else if(emtpy($memberInfo) || $memberInfo["status"] != "ok") exitOnError(80, $memberInfo["error"]["message"]);
else $memberData = $memberInfo["data"][$accountID];

//"color": "#A30000",
//"role_i18n": "Recruit",
//"role": "recruit",
//"emblems": {
//    "large": "http://clans.worldoftanks.eu/media/clans/emblems/cl_573/500044573/emblem_64x64.png",
//    "small": "http://clans.worldoftanks.eu/media/clans/emblems/cl_573/500044573/emblem_24x24.png",
//    "bw_tank": "http://clans.worldoftanks.eu/media/clans/emblems/cl_573/500044573/emblem_64x64_tank.png",
//    "medium": "http://clans.worldoftanks.eu/media/clans/emblems/cl_573/500044573/emblem_32x32.png"
//}
/* count tanks from db ================================================================== */
$count = $dbh->countTanksByLevel($accountID, $tankLevel);
if($count === false) $count = null;
$outData = array("count"=>$count);
$outData = array_merge($outData, $memberData);

//if($accountID == 500128447){
//	Debug::r($tanks);
//	Debug::r($outData);
//}

/* ===================================================================================== */


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
		case 80: $msg .= "failed to get member info"; break;
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