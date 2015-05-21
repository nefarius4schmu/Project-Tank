<?php
/**
* Project Tank Webpage
* webpage board home
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
//Debug::v($_SESSION);
//Debug::r($_page);
/* ===================================================================================== */
//$wd = new WotData();
//$wh = new WotHandler($wd);
//$wotUser = $_page["user"];
//$player = $wotUser["player"];
//$hasClan = $player->hasClan();// isset($player["clan"]);
/* ===================================================================================== */
//$clanBattles = $wh->getCWClanBattles($wotUser);
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <div class='bs-callout bs-callout-smooth bs-callout-custom bc-dash'>
        <h4><?=TEXT_WELCOME;?></h4>
        <div class='callout-buttons'>
            <span class='bt bt-collapse'><i class='fa fa-chevron-up'></i></span>
            <span class='bt bt-close'><i class='fa fa-times'></i></span>
        </div>
        <div class='callout-content row'><?php
            $options=[
                "title"=>"11 Neue Ereignisse (Beispiel)",
                "type"=>1,
                "class"=>"col-md-3",
                "elements"=>[
                    [
                        "title"=>"Neue Events",
                        "content"=>10,
                        "faimg"=>"star fa-fw fa-fb fb-primary",
                    ],
                    [
                        "title"=>"Clanwars",
                        "content"=>1,
                        "faimg"=>"trophy fa-fw fa-fb fb-danger",
                    ],
                    [
                        "title"=>"Deine Events",
                        "content"=>0,
                        "disabled"=>true,
                        "faimg"=>"star fa-fw fa-fb fb-warning",
                    ],
                ],
            ];
            echo Html::createBoardInfo($options);
        ?>
            <div class='col-md-6'></div>
        </div>
    </div>
<?php
//	Debug::v($player);
//	Debug::v($clanBattles);
?>
</div>
<?php
/* ===================================================================================== */
//Debug::v($player);
/* ===================================================================================== */
/* functions =========================================================================== */

?>