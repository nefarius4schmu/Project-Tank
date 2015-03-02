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
$wotUser = $_page["user"];
$playerInfo = $wotUser["player"];
if(!$playerInfo->hasClan()) response(false, null, "no clan");
$wh = new WotHandler(new WotData());
$clanID = $playerInfo->getClanID();
/* ===================================================================================== */
$data = null;
switch($_GET["t"]){
	case "membersStats": $data = getMembersStats($wh, $clanID); break;
}

/* ===================================================================================== */
if(!isset($data)) response(false, null, ERROR_GET_MISSING_TYPE);
else if($data === false) response(false, $data, "failed to get data");
else response(true, $data);
/* ===================================================================================== */
/* ===================================================================================== */
function getMembersStats($wh, $clanID){
	/* get clan member data ================================================================ */
	return $wh->getClanMemberStats($clanID);
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

