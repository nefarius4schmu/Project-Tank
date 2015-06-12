<?php
/**
 * Project Tank Webpage
 * basic layout for board webpages
 * @author Steffen Lange
 */
if(!isset($_page)) exit();
$debug = false;
_lib("DB");
_lib("DBHandler");
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
/* ===================================================================================== */
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
$error = false;
/* ===================================================================================== */
$uid = isset($_GET["uid"]) ? $_GET["uid"] : null;
if(empty($uid)) $error = ERROR_DB_GET_PARAM_UID;
/* ===================================================================================== */
$options = [
    "userID"=>$player->getID(),
    "clanID"=>$player->hasClan() ? $player->getClanID() : null,
];
$event = !$error ? $dbh->getEventByUid($uid, $options) : null;
$error = $event === false || !is_array($event) || empty($event);
if(!$error){
    $result = $dbh->incEventViewCount($event["eventID"]);
    if($result) $event["views"] = $event["views"]*1+1;

    $event["maps"] = $dbh->parseArray($dbh->getEventMapIDs($event["eventID"]));
    $event["prices"] = $dbh->parseArray($dbh->getEventPrices($event["eventID"]));
}
/* ===================================================================================== */
$mapList = $dbh->parseArray($dbh->getMapList(["indexed"=>true]), true);
$isJoinedEvent = $dbh->parse($dbh->getUserJoinedEvent($player->getID(), $event["eventID"]));
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <?php
    if($debug) Debug::r($event);
    else if(!$error){
        $options = [
            "isJoined"=>$isJoinedEvent,
            "canEdit"=>$event["userID"] == $player->getID(),
            "canDelete"=>$event["userID"] == $player->getID(),
        ];
        echo Html::createEventFull($player, $event, $mapList, $options);
    }
    else Debug::e("Beitrag nicht gefunden. ($error)");
    ?>
</div>