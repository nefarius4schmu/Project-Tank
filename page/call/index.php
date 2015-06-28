<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"].dirname($_SERVER["PHP_SELF"]).'/..');
include_once("./../vars/globals.php");
_lib("Debug");
_lib("Router");
_lib("Calc");
/* ===================================================================================== */
define("IN_REQUEST_LOGIN", "login");
define("IN_REQUEST_PLAYER", "player");
define("IN_REQUEST_BRIEFING", "briefing");
define("OUT_TYPE_URL", "url");
define("OUT_TYPE_OBJECT", "object");
define("LOGIN_REDIRECT_TYPE", "wotLogin");
$debug = false;
/* ===================================================================================== */
//Debug::r($_GET);
/* ===================================================================================== */
$request = isset($_GET["get"]) ? $_GET["get"] : null;
//$options = isset($_GET["options"]) && is_array($_GET["options"]) ? $_GET["options"] : [];
/* ===================================================================================== */
//$oRedirectURL = isset($_GET["data"]) ? $_GET["redirect"] : null;
//$oWotLoginData = isset($_GET["data"]) ? $_GET["data"] : null;
$inData = isset($_GET["data"]) ? $_GET["data"] : null;
/* ===================================================================================== */
$error = 0;
$type = null;
$data = null;
switch($request){
    case IN_REQUEST_LOGIN:
        $type = OUT_TYPE_URL;
        $redirectURL = is_string($inData) && !empty($inData) ? $inData : Router::getLoginRedirectURL();
        $data = getRedirectByType(LOGIN_REDIRECT_TYPE, $error, ["redirect"=>$redirectURL]);
        if($data === false || $data === null) error(ERROR_CALL_LOGIN);
        break;
    case IN_REQUEST_PLAYER:
        $type = OUT_TYPE_OBJECT;
        $loginData = json_decode($inData, true);
        // todo: get error code with is_number for debugging
        $data = is_array($loginData) && !empty($loginData) ? getWotPlayerByLogin($loginData, $debug) : false;
        if($data === false || $data === null) error(ERROR_CALL_LOGIN, print_r($data, true));
        break;
    case IN_REQUEST_BRIEFING:
        $type = OUT_TYPE_OBJECT;
        // get event data
        $data = is_string($inData) && !empty($inData) ? getWotEvent($inData, $debug) : false;
        if($data === false || $data === null) error(ERROR_CALL_LOGIN, print_r($data, true));
        break;
}

if($type !== null)
    success($type, $data);
else
    error(ERROR_CALL_UNKOWN_TYPE);



/* ===================================================================================== */
/* ===================================================================================== */
function success($type, $output){
    $data = null;
    switch($type){
        case OUT_TYPE_URL:
        case OUT_TYPE_OBJECT:
            $data = $output;
            break;
    }

    echo json_encode([
        "success"=>true,
        "type"=>$type,
        "data"=>$data
    ]);
    exit();
}

function error($code, $message=null){
    echo json_encode([
        "success"=>false,
        "code"=>$code,
        "message"=>$message,
    ]);
    exit();
}

function getWotPlayerByLogin($loginData, $debug=false)
{
    _lib("objects/LoginUserObject");
    _lib("DB");
    _lib("DBHandler");
    _lib("WotData");
    _lib("WotHandler");
    _lib("WotPlayer");
    _lib("WotSession");
    /* prepare handler ===================================================================== */
    $dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
    if (!$dbh->isConnection()) return false;
    $wh = new WotHandler(new WotData());
    /* ===================================================================================== */
    $login = new LoginUserObject($loginData);
    if ($login->isError() || !$login->isLogin()) return false;
    $aLoginData = WotSession::loginData2Array($login->getAccountID(), $login->getNickname(), null, $login->getAccessToken(), $login->getExpiresAt());
    /* ===================================================================================== */

    // get basic player info
    $player = $wh->getBasicPlayerInfo($aLoginData);
    if ($player === false) return false;

    // get clan members list
    if ($player->hasClan()) {
        $members = $wh->getClanMemberStats($player->getClanID());
        if ($members === false) return false;
        $player->setClanMembers($members);
    }

    /* DB login and update ================================================================= */
    // update login database
    // todo: update database??
//    $result = $dbh->accountLogin($player->getID(), $player->getName());
//    if ($result === false) return false;
    $dbh->debug($debug);

    /* get user settings from database and store in session ================================= */
    $userSettings = $dbh->getUserSettings($login->getAccountID());
    if ($userSettings === false) return false;

    /* store player data in session ============================================================= */
    $aLoginData[WotSession::WOT_PLAYER] = $player;
    $aLoginData[WotSession::USER_SETTINGS] = $userSettings;
//    Debug::r($aLoginData);
    return $aLoginData;
}

function getWotEvent($briefingID, $debug=false){
    _lib("DB");
    _lib("DBHandler");
    /* prepare handler ===================================================================== */
    $dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
    if (!$dbh->isConnection()) return false;
    /* ===================================================================================== */
    $options = ["validation"=>false];
    $event = $dbh->getEventInfoByBriefingID($briefingID, $options);
    if($event === false || $event === null) return false;

    $maps = $dbh->parseArray($dbh->getEventMapsFull($event["eventID"]));

    $event["maps"] = array_map(function($map){
        // hotfix: json error on specials characters
        $map["name_i18n"] = utf8_encode($map["name_i18n"]);
        $map["description_i18n"] = utf8_encode($map["description_i18n"]);
        return $map;
    }, $maps);
    $event["prices"] = $dbh->parseArray($dbh->getEventPrices($event["eventID"]));
    $event["users"] = $dbh->parseArray($dbh->getEventUserDetails($event["eventID"]));

    return $event;
}
