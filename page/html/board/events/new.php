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
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
$dbh->update();
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
$type = isset($_POST["type"]) ? $_POST["type"] : null;
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <?php
    EventEditor::generate(new WotEvent(["typeID"=>$type]));
    ?>
</div>