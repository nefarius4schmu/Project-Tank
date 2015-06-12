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
$news = !$error ? $dbh->getNewsByUid($uid) : null;
$error = $news === false || !is_array($news) || empty($news);
if(!$error){
    $result = $dbh->incNewsViewCount($news["newsID"]);
    if($result) $news["views"] = $news["views"]*1+1;
}
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <?php
    if($debug) Debug::r($news);
    else if(!$error) echo Html::createNewsFull($player, $news);
    else Debug::e("Beitrag nicht gefunden. ($error)");
    ?>
</div>