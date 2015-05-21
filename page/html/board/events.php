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
$player = $wotUser["player"];
//$clanLogoMedium = $hasClan ? $player->getClanEmblemMedium() : PATH_ICON_MEMBERS;
/* ===================================================================================== */
$eventOwner = new PlayerObject();
$eventData[] = new WotEvent([
	"id"=>123,
    "owner"=>new PlayerObject(["id"=>$player->getID(), "name"=>$player->getName()]),
    "clan"=>$player->hasClan() ?  new ClanObject(["id"=>$player->getClanID(), "name"=>getClanName()]) : null,
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
/* ===================================================================================== */
?>
<!--<link rel="stylesheet" type="text/css" href="css/events.css?v=001"/>-->
<div class='page-wrapper'>
    <h1>Events</h1>
    <div class='row row-sep'>
        <div class='js-switch js-template-list row' data-target='#eventList' data-template-list data-template-item='#tmpEventListRow'>
            <div class='switch col-xs-3 hc-warning' data-url='get/?t=events&c=all'><a href='#' class='card'><i class='fa fa-3x fa-star'></i>Alle</a></div>
            <div class='switch col-xs-3 hc-primary active' data-url='get/?t=events&c=public'><a href='#' class='card'><i class='fa fa-3x fa-globe'></i>Öffentlich</a></div>
            <div class='switch col-xs-3 hc-danger' data-url='get/?t=events&c=private'><a href='#' class='card'><i class='fa fa-3x fa-lock'></i>Privat</a></div>
            <div class='switch col-xs-3 hc-warning<?=$clanSwitchClass?>' data-url='get/?t=events&c=clan'><a href='#' class='card'><i class='fa fa-3x fa-trophy'></i>Clan</a></div>
        </div>
    </div>
    <hr>
    <h2 class='year'>2015</h2>
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