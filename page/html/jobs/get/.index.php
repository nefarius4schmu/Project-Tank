<?php
/**
* Project Tank Webpage
* getter file
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
/* ===================================================================================== */
if($_page["login"] === false) response(false, null, ERROR_LOGIN_AUTH);
else if(!isset($_GET["t"])) response(false, null, ERROR_GET_UNKNOWN_TYPE);
/* ===================================================================================== */
_lib("WotData");
_lib("WotHandler");
_lib("Calc");
_lib("Html");
_lib("WotPlayer");
_lib("DB");
_lib("DBHandler");
$wotUser = $_page["user"];
$playerInfo = $wotUser["player"];
if(!$playerInfo->hasClan()) response(false, null, "no clan");
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$wh = new WotHandler(new WotData());
$clanID = $playerInfo->getClanID();
/* ===================================================================================== */
$data = null;
switch($_GET["t"]){
	case "membersStats": $data = getMembersStats($dbh, $wh, $clanID); break;
}

/* ===================================================================================== */
if(!isset($data)) response(false, null, ERROR_GET_MISSING_TYPE);
else if($data === false) response(false, $data, "failed to get data");
else response(true, $data);
/* ===================================================================================== */
/* ===================================================================================== */
function getMembersStats($dbh, $wh, $clanID){
	/* get clan member data from api ======================================================= */
//	return $wh->getClanMemberStats($clanID);
	/* get clan member data from database ================================================== */
	$data = $dbh->getClanMembersInfo($clanID);
	if($data === false) response(false, $data, "failed to get clan members");
	
	$ids = array_keys($data);
	$stats = $dbh->getClanMembersStatsTableData($ids);
	if($data === false) response(false, $data, "failed to get clan members stats table data");
//	Debug::r($data);
	$out = [];
	foreach($data as $userID=>$member){
		
		$obj = new ClanMemberObject();
		$obj->id = $userID;
		$obj->name = $member["name"];
		$obj->role = $member["role"];
		$obj->role_i18n = $member["role_i18n"];
		$obj->joined = $member["joined"];
		$obj->rating = $wh->parsePlayerRating($member["global"]);
		if(isset($stats[$userID]))
			$obj->statistic = $wh->parseInternalPlayerStats($stats[$userID]);
		else
			$obj->statistic = $wh->parseInternalPlayerStats(null);
			
		$out[$userID] = $obj;
	}
	
	return $out;
	
}

function response($success=true, $data=null, $message=null){
	$out = [
		"success"=>$success,
		"data"=>$data,
		"message"=>$message,
	];
	echo json_encode($out);
	exit();
}

