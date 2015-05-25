<?php
/**
* Project Tank Webpage
* webpage to display and manage upcoming events
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
_lib("WotEvent");
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
/* ===================================================================================== */
$currentEventCat = isset($_GET["c"]) ? $_GET["c"] : null;
$isEventCat = !empty($currentEventCat);
//$clanLogoMedium = $hasClan ? $player->getClanEmblemMedium() : PATH_ICON_MEMBERS;
/* ===================================================================================== */
$eventOwner = new PlayerObject();
$eventData[] = new WotEvent([
	"id"=>123,
    "owner"=>new PlayerObject(["id"=>$player->getID(), "name"=>$player->getName()]),
    "clan"=>$player->hasClan() ?  new ClanObject(["id"=>$player->getClanID(), "name"=>$player->getClanName()]) : null,
	"type"=>"public",
	"created"=>time(),
	"updated"=>time(),
	"start"=>time()+24*60*60,
	"end"=>time()+3*24*60*60,
	"public"=>true,
    "hidden"=>false,
    "password"=>false,
	"title"=>"Die ist ein Test",
	"description"=>"Hier steht was tolles",
	"prices"=>[],
    "maps"=>[],
]);
/* ===================================================================================== */
$clanSwitchClass = !$player->hasClan() ? " disabled" : null;
$catAllActive = $currentEventCat == "all" || !$currentEventCat;
/* ===================================================================================== */
//Debug::v($currentEventCat);
//Debug::v($catAllActive);
/* ===================================================================================== */
?>
<!--<link rel="stylesheet" type="text/css" href="css/events.css?v=001"/>-->
<div class='page-wrapper'>
    <div class='row'>
        <?php
        echo Html::createNewsFeatured($player, []);
        ?>
        <ul class='nav nav-pills c-default js-switch'>
            <li class='<?=Html::isget($catAllActive, true)?>'><a href='?c=all' class=''><i class='fa fa-fw fa-star'></i>Alle</a></li>
            <li class='<?=Html::isget($currentEventCat, "public")?>'><a href='?c=public' class=''><i class='fa fa-fw fa-globe'></i>Öffentlich</a></li>
            <li class='<?=Html::isget($currentEventCat, "private")?>'><a href='?c=private' class=''><i class='fa fa-fw fa-lock'></i>Privat</a></li>
            <li class='<?=Html::isget($currentEventCat, "clan")?>'><a href='?c=clan' class='<?=$clanSwitchClass?>'><i class='fa fa-fw fa-trophy'></i>Clan</a></li>
<!--            <li class='fc-warning--><?//=Html::isget($catAllActive, true)?><!--'><a href='?c=all' class='card'><i class='fa fa-2x fa-star'></i>Alle</a></li>-->
<!--            <li class='fc-primary--><?//=Html::isget($currentEventCat, "public")?><!--'><a href='?c=public' class='card'><i class='fa fa-2x fa-globe'></i>Öffentlich</a></li>-->
<!--            <li class='fc-danger--><?//=Html::isget($currentEventCat, "private")?><!--'><a href='?c=private' class='card'><i class='fa fa-2x fa-lock'></i>Privat</a></li>-->
<!--            <li class='fc-warning--><?//=Html::isget($currentEventCat, "clan")?><!--'><a href='?c=clan' class='card--><?//=$clanSwitchClass?><!--'><i class='fa fa-2x fa-trophy'></i>Clan</a></li>-->
<!--            <li class='pull-right np-warning'><a href="#">+ Neues Event</a></li>-->
        </ul>
    </div>

    <h2 class='year'>2015</h2>
    <hr>
    <div id='eventList' class='row'>
        <div class='event event-card-lg event-briefing col-md-2 bs-callout bs-callout-primary bs-callout-custom'>
            <h4 class='clearfix'>
                <span class='day fa-stack fa-stack-sm pull-left'>
                    <i class='fa fa-calendar-o fa-stack-2x'></i>
                    <strong class='fa-stack-1x fa-calendar-fix'>30</strong>
                </span>
                <span class='month'>September</span>
                <span class='briefing fa-stack fa-stack-sm pull-right'>
                    <i class='fa fa-square fa-stack-2x'></i>
                    <strong class='fa fa-coffee fa-inverse fa-square-fix'></strong>
                </span>
            </h4>
            <div class='map callout-content'><img src='images/wot/maps/400x400/03_campania.jpg'/></div>
            <div class='summary callout-content'>
                <h3>Pro te vivendo</h3>
                <div class='description'>Nam molestiae eloquentiam ei, et facilisi definitiones nec. Pro te vivendo docendi, ei eos voluptaria vituperatoribus, ei alii reformidans pri. Eu meis vulputate ius? Duo an vide quando vulputate, ius no altera nominati accommodare, habeo brute sea et? An iudico reprimique reprehendunt qui.</div>
                <div class='meta'>
                    <span class='date'>@30.04.15</span>
                    <span class='user'>by Testuser</span>
                    <span class='clan'>[TESTCLAN]</span>
                </div>
            </div>
        </div>
        <div class='event event-card-lg col-md-2 bs-callout bs-callout-primary bs-callout-custom'>
            <h4 class='clearfix'>
                <span class='day fa-stack fa-stack-sm pull-left'>
                    <i class='fa fa-calendar-o fa-stack-2x'></i>
                    <strong class='fa-stack-1x fa-calendar-fix'>1</strong>
                </span>
                <span class='month'>Januar</span>
                <span class='briefing fa-stack fa-stack-sm pull-right'>
                    <i class='fa fa-square fa-stack-2x'></i>
                    <strong class='fa fa-coffee fa-inverse fa-square-fix'></strong>
                </span>
            </h4>
            <div class='map callout-content'><img src='images/wot/maps/400x400/14_siegfrid_line.jpg'/></div>
            <div class='summary callout-content'>
                <h3>Cu elit molestiae complectitur vix</h3>
                <div class='description'>Purto oblique per ut, facilis facilisis incorrupte ne nec. Insolens suavitate deterruisset sit cu. Tale adhuc eum ad, sea vocent instructior id, at legimus volumus eam. Ne sea tritani mentitum, libris mollis disputationi ea has, solum efficiantur contentiones et qui! Ne nec rebum essent sadipscing. Dicant decore dissentiunt sit eu, molestie constituto posidonium nec ex!</div>
                <div class='info'>
                    <span class='joined'><i class='fa fa-fw fa-user'></i>31/64</span>
                    <span class='viewed'><i class='fa fa-fw fa-eye'></i>201</span>
                </div>
                <div class='meta'>
                    <span class='date'>@12.12.14</span>
                    <span class='user'>by Testuser</span>
                    <span class='clan'>[TESTCLAN]</span>
                </div>
            </div>
        </div>
    </div>
</div>




<script type='text/template' id='tmpEventListRow'>
    <div class='event' data-type='{{event.type}}'>
        <div class='date'>{{event.date}}</div>
        <div class='map'>{{event.map}}</div>
        <div class='summary'>
            <h3>{{event.title}}</h3>
            <div class='info'>
                <span class='joined'><i class='fa fa-user'></i>{{event.info.joined}}<span class='joined-limit'>{{event.info.joinlimit}}</span></span>
            </div>
            <div class='meta'>
                <span class='time'>{{event.creator.time}}</span>
                <span class='user'>{{event.creator.user}}</span>
                <span class='clan'>{{event.creator.clan}}</span>
            </div>
        </div>
    </div>
</script>
<?php
/* ===================================================================================== */
/* ===================================================================================== */
function htmlEvent($event){
    $temp = "<div class='event'>
        <div class='map'></div>
        <div class='summary'>
            <h3></h3>
            <div class='info'></div>
            <div class='timestamp'>
                <span class='time'></span>
                <span class='user'></span>
                <span class='clan'></span>
            </div>
        </div>
    </div>";

}


?>