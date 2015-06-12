<?php
/**
* Project Tank Webpage
* webpage to display and manage upcoming events
* @author Steffen Lange
*/
if(!isset($_page)) exit();
$debug = false;
/* ===================================================================================== */
_lib("WotEvent");
_lib("DB");
_lib("DBHandler");
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
//$dbh->debug($debug);
/* ===================================================================================== */
$currentEventType = isset($_GET["c"]) ? $dbh->parse($dbh->getEventTypeByName($_GET["c"])) : null;
$clanOnlyCat = $currentEventType["clan"] == "1";
if($clanOnlyCat && !$player->hasClan()) $currentEventType = null;
$isEventType = isset($currentEventType);
/* ===================================================================================== */
$eventTypes = $dbh->parseArray($dbh->getEventTypes());
/* ===================================================================================== */
$dbh->debug($debug);
$options = [
    "limit"=>20,
    "typeID"=>$isEventType ? $currentEventType["typeID"] : null,
    "userID"=>$player->getID(),
    "clanID"=>$player->hasClan() ? $player->getClanID() : null,
];
$latestEvents = $dbh->parseArray($dbh->getLatestEvents($options));
$userJoinedEventIDs = $dbh->parseArray($dbh->getUserJoinedEventIDs($player->getID()));
/* ===================================================================================== */
$mapList = $dbh->parseArray($dbh->getMapNames(), true);
//Debug::r($options);
//Debug::r($latestEvents);
/* ===================================================================================== */
$catAllActive = $currentEventType == "all" || !$currentEventType;
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <div class='row'>
        <?php
        echo Html::createEventFeatured($player, []);
        ?>
        <ul class='nav nav-pills c-default js-switch'>
            <li class='<?=Html::isget($catAllActive, true)?>'><a href='?c=all' class=''><i class='fa fa-fw fa-star'></i>Alle</a></li>
            <?php
//            Debug::s($currentEventType);
            foreach($eventTypes as $type){
//                Debug::e($type);
                $name = $type["name"];
                $name_i18n = htmlentities($type["name_i18n"]);
                $icon = $type["iconClass"];
                $isClanType = $type["clan"] == "1";
                $disabled = $isClanType && !$player->hasClan() ? " disabled" : null;
                $active = Html::isget($currentEventType["name"], $name);

                echo '<li class="'.$active.'"><a href="?c='.$name.'" class="'.$disabled.'"><i class="fa fa-fw '.$icon.'"></i>'.$name_i18n.'</a></li>';
            }
            ?>
            <li class='pull-right np-warning'><a href="<?=URL_ROOT.ROUTE_EVENT_NEW?>">+ Neues Event</a></li>
        </ul>
    </div>

<!--    <h2 class='year'>2015</h2>-->
<!--    <hr>-->
    <div id='eventList' class='row'>
        <?php
        $cols = 0;
        $currentYear = 0;
        foreach($latestEvents as $event){
            // render year seperator
            $year = date("Y",strtotime($event["start"]));
            if($currentYear != $year){

                echo "</div><div id='eventList' class='row'>";
                echo Html::template(Html::TMP_SEPERATOR_YEAR, $year);
                $currentYear = $year;
            }
            // render event
            $options = [
                "isJoined"=>in_array($event["eventID"], $userJoinedEventIDs),
                "canEdit"=>$event["userID"] == $player->getID(),
                "canDelete"=>$event["userID"] == $player->getID(),
            ];
            $type = isset($event["typeID"], $eventTypes[$event["typeID"]]) ? $eventTypes[$event["typeID"]] : [];
            echo '<div class="col-md-4">'.Html::createEventLg($player, $event, $type, $mapList, $options).'</div>';
            // insert new row if col is full
            $cols++;
            if($cols % Html::EVENT_LG_COL_COUNT == 0)
                echo "</div><div id='eventList' class='row'>";
        }
        ?>
    </div>
</div>