<?php
/**
* Project Tank Webpage
* login page, requested by WGP API
* handles user login and prepares basic information
* @author Steffen Lange
* 
* @todo
* check if login already exists
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
_lib("objects/LoginUserObject");
_lib("DB");
_lib("DBHandler");
_lib("WotData");
_lib("WotHandler");
_lib("WotPlayer");
/* ===================================================================================== */
$debug = false;
$isOffline = Router::OFFLINE && isset($_GET["offline"]) && $_GET["offline"] == 1;
$isLogin = $_page["login"] !== false;
//Debug::r($_GET);
//Debug::r($_page);
//Debug::v($isOffline);
//exit();
/* ===================================================================================== */
$redirect = URL_ROOT;
$doApiUpdate = !$isOffline;
/* ===================================================================================== */
//if() _error(ERROR_LOGIN_EXISTS);
$login = new LoginUserObject($_GET);
if(!$isOffline) {
    if ($login->isError()) _error(ERROR_LOGIN_AUTH, null, $debug);
    else if(!$login->isLogin()) _error(ERROR_LOGIN_FAILED, $login->getCode(), $debug);
//    if (!isset($_GET["status"], $_GET["access_token"], $_GET["nickname"], $_GET["account_id"], $_GET["expires_at"])) _error(ERROR_LOGIN_AUTH, null, $debug);
//    else if ($_GET["status"] != "ok") _error(ERROR_LOGIN_FAILED, isset($_GET["code"]) ? $_GET["code"] : null, $debug);
}else{
    if(!$login->isUser()) Debug::eoe(99);
    $login->setNickname("Offline User");
//    $_GET["access_token"] = 0;
//    $_GET["expires_at"] = 0;
}
//Debug::r($_GET);
if($debug){
    Debug::r($login);
}
/**
* TODO
* 
* catch http://localhost/wot/login.php?&status=error&message=AUTH_CANCEL&code=401
* catch no clanID
* 
* check session login failed
*/
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Willkommen!</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css?320">
	<link rel="stylesheet" type="text/css" href="css/base.css?001">
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
			<p>Willkommen, <?=$login->getNickname()?>!</p>
			<small>Dein Erlebnis wird vorbereitet..</small>
		</div>
	</div>
<?php
flush();
/* prepare handler ===================================================================== */

$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
if(!$dbh->isConnection()) redirect(_error(ERROR_DB_CONNECTION, null, $debug, true), $debug);//!$dbh->isConnection() ||
/* API get basic user info + clan info ================================================= */
//$wotData = new WotData();
$wh = new WotHandler(new WotData());

/* prepare session ===================================================================== */
$_page["login"] = WotSession::setLoginData($login->getAccountID(), $login->getNickname(), null, $login->getAccessToken(), $login->getExpiresAt());
$_page["user"] = WotSession::getLoginData();
/* ===================================================================================== */
if($doApiUpdate) {
    $dbh->debug($debug);

// get basic player info
    $player = $wh->getBasicPlayerInfo($_page["user"]);
    if ($player === false)
        redirect(_error(ERROR_API_GET_PLAYER_INFO, $player, $debug, true), $debug);

// get clan members list
    if ($player->hasClan()) {
        $members = $wh->getClanMemberStats($player->getClanID());
        if ($members === false)
            redirect(_error(ERROR_API_GET_CLAN_MEMBERS_STATS, $members, $debug, true), $debug);
        $player->setClanMembers($members);
    }

    /* get account info / clan id ========================================================== */
//$playerInfo = $wotData->getPlayerInfo($_GET["account_id"], $_GET["access_token"]);
//if($debug) Debug::r($playerInfo);
//if($playerInfo === false || empty($playerInfo) || $playerInfo["status"] != "ok") 
//	redirect(_error(ERROR_API_GET_PLAYER_INFO, $playerInfo, $debug, true), $debug);
//$playerData = $playerInfo["data"][$_GET["account_id"]];
//
//if(!isset($playerData["clan_id"]) || empty($playerData["clan_id"])){
//	$playerData["clan_id"] = null;
//}

//Debug::v($player); exit();
    /* DB login and update ================================================================= */

// update login database
    $result = $dbh->accountLogin($player->getID(), $player->getName());
    if ($result === false) redirect(_error(ERROR_DB_LOGIN, null, $debug, true), $debug);
//else if($dbhn->isDebug()) Debug::r($result);

// update database user stats
    /**
     * @todo
     * - do it dynamic
     * - change object StatisticObject.class to work with dynamic data from database
     * - array of stats, e.g. stats->name; stats->id; stats->value
     */
    if (!$player->hasClan()) {

        // update database user info
        $result = $dbh->setUserInfo($player->getID(), $player->getName(), $player->getLang());
        if ($result === false) redirect(_error(ERROR_DB_SET_USER_INFO, null, $debug, true), $debug);

        // update database user rating
        if ($player->isRating()) {
            $result = $dbh->setUserRatings($player->getID(), $player->getRatingGlobal());
            if ($result === false) redirect(_error(ERROR_DB_SET_USER_RATING, null, $debug, true), $debug);
        }
        if ($player->isStatistic()) {
            $data = [
                73 => $player->getStatsBattles(),
                80 => $player->getStatsHits(),
                82 => $player->getStatsWins(),
                84 => $player->getStatsDamage(),
                86 => $player->getStatsShots(),
            ];
            $result = $dbh->setWotUserStats($player->getID(), $data);
            if ($result === false) redirect(_error(ERROR_DB_SET_WOT_USER_STATS, null, $debug, true), $debug);

        }
    }

    /**
     *
     * @todo
     *    update user stats
     *    update clan members stats
     *    update user ranking
     *    update clan members ranking
     *
     */

// update database clan information
    if ($player->hasClan()) {
        // store clan info
        $result = $dbh->setClanInfo($player->getClanID(), $player->getClanName(), $player->getClanTag(), $player->getClanColor(), $player->getClanIsDisbanned() ? 1 : 0);
        if ($result === false) redirect(_error(ERROR_DB_SET_CLAN_INFO, null, $debug, true), $debug);
        // remove old members
        $result = $dbh->removeClanMembersByClanID($player->getClanID());
        if ($result === false) redirect(_error(ERROR_DB_DEL_CLAN_MEMBERS, null, $debug, true), $debug);
        // store members info
        $clanMembers = $player->getClanMembers();
        if (!empty($clanMembers)) {
            $userData = [];
            $membersData = [];
            $ratingData = [];
            $statsData = [];
            foreach ($clanMembers as $clanMember) {
                $userData[$clanMember->id] = [
                    "name" => $clanMember->name,
                    "lang" => $player->getLang(),
                ];

                $membersData[$clanMember->id] = [
                    "clanID" => $player->getClanID(),
                    "role" => $clanMember->role,
                    "role_i18n" => $clanMember->role_i18n,
                    "joined" => date(DBHandler::TIMESTAMP_FORMAT, $clanMember->joined),
                ];

                $ratingData[$clanMember->id] = [
                    "global" => $clanMember->rating->global,
                ];

                $statsData[$clanMember->id] = [
                    73 => $clanMember->statistic->battles,
                    80 => $clanMember->statistic->hits,
                    82 => $clanMember->statistic->wins,
                    84 => $clanMember->statistic->damage,
                    86 => $clanMember->statistic->shots,
                ];
            }

//		Debug::r($statsData);

            // insert into database
//		$dbhn->debug();
            $result = $dbh->setUserGroupInfo($userData);
            if ($result === false) redirect(_error(ERROR_DB_SET_USER_GROUP, null, $debug, true), $debug);
//		Debug::r($result);
//		$dbhn->debug();
            $result = $dbh->setUserGroupRatings($ratingData);
            if ($result === false) redirect(_error(ERROR_DB_SET_USER_GROUP_RATING, null, $debug, true), $debug);
//		Debug::r($result);
//		$dbhn->debug();
            $result = $dbh->setClanMembers($membersData);
            if ($result === false) redirect(_error(ERROR_DB_SET_CLAN_MEMBERS, null, $debug, true), $debug);
//		Debug::r($result);
//		$dbhn->debug();
            $result = $dbh->setWotUserGroupStats($statsData);
            if ($result === false) redirect(_error(ERROR_DB_SET_CLAN_WOT_USER_STATS, null, $debug, true), $debug);
//		Debug::r($result);
        }
//	exit();
    } else {
        // check if user was in clan - needed for history ??
//	$result = $dbhn->getClanMemberByUserID($player->getID());
//	if($result === false) redirect(_error(ERROR_DB_GET_CLAN_MEMBER, null, $debug, true), $debug);
//	else if(!empty($result)){
        // remove user from clan members list
//		$result = $dbhn->removeClanMemberByUserID($)
//	}

        $result = $dbh->removeClanMemberByUserID($player->getID());
        if ($result === false) redirect(_error(ERROR_DB_DEL_CLAN_MEMBER, null, $debug, true), $debug);
    }
    // end of api update block
}else{
    // get session data from database
    $playerData = $dbh->parseArray($dbh->getPlayerLoginData($login->getAccountID()));
    $player = $wh->parsePlayerInfo($playerData);
    if($player->getID() === null) redirect(_error(ERROR_DB_GET_LOGIN_DATA, null, $debug, true), $debug);

    // statsAll
    $statsAll = $dbh->parseArray($dbh->getUserStatsInternal($player->getID()));
    $player->setStatistic($wh->parseInternalPlayerStats($statsAll));

    if($player->hasClan()){
        // clanEmblems
//        $clanEmblems = $dbh->parseArray($dbh->getClanEmblems($player->getClanID()));
//        $player->setClanEmblemsObject($wh->pa);

        // clanMembersCount
    }
}

/* get user settings from database and store in session ================================= */
//$dbh->debug();
$userSettings = $dbh->getUserSettings($login->getAccountID());
//Debug::v($result); exit();
if($userSettings === false) redirect(_error(ERROR_DB_LOGIN_SETTINGS, null, $debug, true), $debug);

/* store player data in session ============================================================= */
if (!WotSession::setData($player, WotSession::WOT_PLAYER))
    redirect(_error(ERROR_SESSION_SET_DATA, $player, $debug, true), $debug);

if(Router::CONSTRUCTIONS && isset($_GET["admin"]))
    WotSession::setData($_GET["admin"], WotSession::CUSTOM_KEY);

if(!WotSession::setSettings($userSettings))
    redirect(_error(ERROR_SESSION_SET_SETTINGS, null, $debug, true), $debug);

if($debug){
    Debug::r(WotSession::getLoginData());
}

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