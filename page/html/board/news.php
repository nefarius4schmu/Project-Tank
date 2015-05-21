<?php
/**
* Project Tank Webpage
* webpage to display and manage tickers and news
* @author Steffen Lange
*/
if(!isset($_page)) exit();
$debug = false;
_lib("DB");
_lib("DBHandler");
/* ===================================================================================== */
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
/* ===================================================================================== */
$options = ["login"=>$_page["login"], "clan"=>$player->hasClan(), "settings"=>$wotUser["settings"]];
$canCreateNews = Router::canRoute(ROUTE_CREATOR_NEWS, $options);
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
/* ===================================================================================== */
$result = $dbh->getNewsCategories();
$newsCats = $dbh->parseArray($result);
/* ===================================================================================== */
$currentNewsCat = isset($_GET["cat"]) ? $dbh->parse($dbh->getNewsCategoryByID($_GET["cat"])) : null;
$clanOnlyCat = $currentNewsCat["clanOnly"] == "1";
if($clanOnlyCat && !$player->hasClan()) $currentNewsCat = null;
$isNewsCat = isset($currentNewsCat);
//Debug::r($currentNewsCat);
/* ===================================================================================== */
$catID = $isNewsCat ? $currentNewsCat["catID"] : null;
$clanID = $player->hasClan() ? $player->getClanID() : null;// $isNewsCat && $clanOnlyCat &&
/* ===================================================================================== */
//$dbh->debug(true);
$timeLatest = Calc::getWeeks(1);
$options = ["limit"=>10, "catID"=>$catID, "clanID"=>$clanID];
$result = $dbh->getLatestNews($timeLatest, $options);
$newsLatest = $dbh->parseArray($result);
/* ===================================================================================== */
//$dbh->debug(true);
$options["limit"] = 1;
$result = $dbh->getLatestFeaturedNews($options);
$newsFeatured = $dbh->parseArray($result);
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <div class='row'>
        <?php
            if(!empty($newsFeatured))
                foreach ($newsFeatured as $news) echo Html::createNewsFeatured($player, $news);
            else echo Html::createNewsFeatured($player, []);
        ?>
        <ul class='nav nav-pills c-default js-switch'>
            <li class='switch<?=($isNewsCat?null:" active")?>' data-url='get/?t=news'><a href='?t=news'>Alle</a></li>
            <?php
                foreach($newsCats as $cat) {
                    $active = $isNewsCat && $cat["name"] == $currentNewsCat["name"] ? " active" : null;
                    $disabled = !$player->hasClan() && $cat["clanOnly"] == "1" ? " disabled" : null;
                    $href = !isset($disabled) ? URL_ROOT . ROUTE_NEWS . '?cat=' . $cat["catID"] : '#';
                    echo '<li class="switch'.$active.'"><a class="'.$disabled.'" href="'.$href.'">'.Html::getNewsCatLang($cat["name"]).'</a></li>';
                }
            ?>
            <li class='pull-right np-warning'><?=($canCreateNews?"<a href='".ROUTE_CREATOR_NEWS."'>+ Neuer Beitrag</a>":null)?></li>
        </ul>
    </div>
    <div id='list' class='list-wrapper row'>
        <?php
        if(!empty($newsLatest)){
            foreach ($newsLatest as $news) {
                $news["canEdit"] = $news["userID"] == $player->getID();
                echo Html::createNewsLg($player, $news);
            }
        }else{
            echo "Keine Beitr&auml;ge gefunden.";
        }
            ?>
    </div>
</div>