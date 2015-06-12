<?php
/**
 * Project Tank Webpage
 * creator webpage for makings news entries
 * @author Steffen Lange
 */
if(!isset($_page)) exit();

$debug = false;
_lib("DB");
_lib("DBHandler");
_lib("WotEvent");
_lib("EventEditor");
_lib("objects/WotMapObject");
/* ===================================================================================== */
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
$dir = basename($_SERVER["PHP_SELF"]);
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
/* ===================================================================================== */
$uid = isset($_GET["uid"]) ? $_GET["uid"] : null;
$isEdit = !empty($uid);
$options = [
    "userID"=>$player->getID(),
    "clanID"=>$player->hasClan() ? $player->getClanID() : null,
];
$event = new WotEvent($isEdit ? $dbh->parse($dbh->getEventByUid($uid, $options)) : null);
$isEvent = $event->is() && $event->getUserID() == $player->getID();
/* ===================================================================================== */
$canEdit = $isEdit && $event->getUserID() == $player->getID();
$canEdit = $canEdit && (!$event->isClanEvent() || ($player->hasClan() && $player->getClanID() == $event->getClanID()) );
$isEvent = $isEvent && $canEdit;
/* ===================================================================================== */
//Debug::r($event);
/* ===================================================================================== */
if($isEdit)
    if(!$canEdit) exit("<p>Sie sind nicht berechtigt, dieses Event zu bearbeiten!</p>");
    else if(!$isEvent) exit("<p>Fehler beim Bearbeiten. Versuchen Sie es zu einem anderen Zeitpunkt erneut.</p>");

/* get event data ====================================================================== */
//$dbh->debug($debug);
$eventPrices = $dbh->parseArray($dbh->getEventPrices($event->getID()));
$eventMaps = $dbh->parseArray($dbh->getEventMaps($event->getID()));
$event->parsePrices($eventPrices);
$event->parseMaps($eventMaps);
/* ===================================================================================== */
$eventTypes = $dbh->parseArray($dbh->getEventTypes());
$typeOptions = $dbh->parseArray($dbh->getEventTypesOptions());
$mapList = $dbh->parseMap($dbh->getMapList(["order"=>"name_i18n ASC"]), function($row){return new WotMapObject($row);});
$gameModes = $dbh->parseArray($dbh->getGameModes());
/* ===================================================================================== */
EventEditor::setEventTypes($eventTypes);
EventEditor::setTypeOptions($typeOptions);
EventEditor::setMapList($mapList);
EventEditor::setGameModes($gameModes);
/* ===================================================================================== */
//Debug::r($event);
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <?php
    EventEditor::generate($event);
    ?>
</div>