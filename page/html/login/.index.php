<?php
/**
* Project Tank Webpage
* login page, requested by WGP API
* handles user login and prepares basic information
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
$isLogin = $_page["login"] !== false;
/* ===================================================================================== */
//if() _error(ERROR_LOGIN_EXISTS);
if(!isset($_GET["status"],$_GET["access_token"],$_GET["nickname"],$_GET["account_id"],$_GET["expires_at"])) _error(ERROR_LOGIN_AUTH, null, $debug);
if(!isset($_GET["status"],$_GET["access_token"],$_GET["nickname"],$_GET["account_id"],$_GET["expires_at"])) _error(ERROR_LOGIN_AUTH, null, $debug);
else if($_GET["status"] != "ok") _error(ERROR_LOGIN_FAILED, isset($_GET["code"]) ? $_GET["code"] : null, $debug);
//Debug::r($_GET);

/**
* TODO
* 
* catch http://localhost/wot/login.php?&status=error&message=AUTH_CANCEL&code=401
* catch no clanID
* 
* check session login failed
*/
/* ===================================================================================== */
$redirect = URL_ROOT;
$isError = false;
/* ===================================================================================== */
_lib("DB");
_lib("DBHandler");
_lib("WotData");
_lib("WotHandler");
_lib("WotPlayer");
_def("db");
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Willkommen!</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/base.css"> 
	<style>
		html, body{
			height: 100%;
		}
		img{
			margin: 10px 0;
		}
		.b-row{
			position: relative;
			width: 100%;
			height: 100%;
		}
		.box {
			position: absolute;
			top: 50%;
			left: 50%;
			height: 50px;
			width: 600px;
			margin: -25px 0 0 -300px;
			text-align: center;
			border: 0;
		}
	</style>
</head>
<body>
	<div class='b-row'>
		<div class='box'>
			<img src='images/loader/loader-bar.gif' alt='loader'/>
			<p>Willkommen, <?=$_GET["nickname"];?>!</p>
			<small>Dein Erlebnis wird vorbereitet..</small>
		</div>
	</div>
<?php
flush();
/* prepare session ===================================================================== */
$_page["login"] = WotSession::setLoginData($_GET["account_id"], $_GET["nickname"], null, $_GET["access_token"], $_GET["expires_at"]);
$_page["user"] = WotSession::getLoginData();

/* API get basic user info + clan info ================================================= */
$wh = new WotHandler(new WotData());

// get basic player info
$player = $wh->getBasicPlayerInfo($_page["user"]);
if($player === false)
	redirect(_error(ERROR_API_GET_PLAYER_INFO, $player, $debug, true), $debug);

// get clan members list
if($player->hasClan()){
	$members = $wh->getClanMemberStats($player->getClanID());
	if($members === false)
		redirect(_error(ERROR_API_GET_CLAN_MEMBERS_STATS, $members, $debug, true), $debug);
	$player->setClanMembers($members);
}

/* store player in session ============================================================= */
if(!WotSession::setData($player, WotSession::WOT_PLAYER))
	redirect(_error(ERROR_SESSION_SET_DATA, $player, $debug, true), $debug);

/* prepare handler ===================================================================== */
$wotData = new WotData();
$dbhn = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh = new DBHandler(DB::getLink(DB::DB_WOT));
$dbhn->debug($debug);
$dbh->debug($debug);
if(!$dbh->isConnection() || !$dbhn->isConnection()) redirect(_error(ERROR_DB_CONNECTION, null, $debug, true), $debug);

/* get account info / clan id ========================================================== */
$playerInfo = $wotData->getPlayerInfo($_GET["account_id"], $_GET["access_token"]);
if($debug) Debug::r($playerInfo);
if($playerInfo === false || empty($playerInfo) || $playerInfo["status"] != "ok") 
	redirect(_error(ERROR_API_GET_PLAYER_INFO, $playerInfo, $debug, true), $debug);
$playerData = $playerInfo["data"][$_GET["account_id"]];

if(!isset($playerData["clan_id"]) || empty($playerData["clan_id"])){
	$playerData["clan_id"] = null;
}
/* store login data ==================================================================== */
//$_page["login"] = WotSession::setLoginData($_GET["account_id"], $_GET["nickname"], $playerData["clan_id"], $_GET["access_token"], $_GET["expires_at"]);
//$_page["user"] = WotSession::getLoginData();

/* DB login and update ================================================================= */

// update login database
//$dbhn->debug();
$result = $dbhn->accountLogin($_GET["account_id"], $_GET["nickname"], $playerData["clan_id"]);
if($result === false) redirect(_error(ERROR_DB_LOGIN, null, $debug, true), $debug);
//else if($dbhn->isDebug()) Debug::r($result);

// update database


// get user settings and store in session
//$dbh->debug();
$result = $dbhn->getUserSettings($_GET["account_id"]);
//Debug::v($result); exit();
if($result === false) redirect(_error(ERROR_DB_LOGIN_SETTINGS, null, $debug, true), $debug);
$result = WotSession::setSettings($result);
if($result === false) redirect(_error(ERROR_SESSION_SET_SETTINGS, null, $debug, true), $debug);
	
/* redirect to board =================================================================== */
redirect($redirect, $debug);
/* ===================================================================================== */
/* ===================================================================================== */

function redirect($location, $debug=false){
	if(!$debug) echo "<script>window.location.href = '".$location."';</script></body></html>";
	else Debug::v($location);
	exit();
}
?>