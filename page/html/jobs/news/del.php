<?php
/**
* Project Tank Webpage
* save posted news page
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
_lib("DB");
_lib("DBHandler");
_lib("Html");
/* ===================================================================================== */
$isLogin = $_page["login"] !== false && isset($_page["user"]["player"]);
if(!$isLogin) _error(ERROR_MISSING_LOGIN);
/* ===================================================================================== */
/** @var array $_user */
$_user = $_page["user"];
/** @var WotPlayer $_player */
$_player = $_user["player"];
$_route = ROUTE_NEWS;
/* ===================================================================================== */
//$action = isset($_GET["action"]) ? $_GET["action"] : ($isEdit ? "edit" : "create");
//$langActionText = Html::getNewsActionLang($action);
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=PAGE_BRAND;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/base.css"> 
	<style>
		html, body{
			height: 100%;
		}
		img{
			margin: 10px 0;
		}
		.b-row{
			position: relative;
			width: 100%;
			height: 100%;
		}
		.box {
			position: absolute;
			top: 50%;
			left: 50%;
			height: 50px;
			width: 600px;
			margin: -25px 0 0 -300px;
			text-align: center;
			border: 0;
		}
	</style>
</head>
<body>
	<div class='b-row'>
		<div class='box'>
			<img src='images/loader/loader-bar.gif' alt='loader'/>
			<p>Bitte warten Sie einen Moment.</p>
			<small>Ihr Beitrag wird entfernt...</small>
		</div>
	</div>
<?php
flush();
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
if(!$dbh->isConnection()) finish(_error(ERROR_DB_CONNECTION, null, false, true));
/* ===================================================================================== */
$uid = isset($_GET["uid"]) ? $_GET["uid"] : null;
$isEdit = !empty($uid);
$news = $isEdit ? $dbh->getNewsByUid($uid, ["fields"=>"n.newsID,n.userID,n.clanID"]) : null;
$isNews = !empty($news) && $news["userID"] == $_player->getID();
/* ===================================================================================== */
$canEdit = $isEdit && $news["userID"] == $_player->getID();
$canEdit = $canEdit && (!isset($news["clanID"]) || ($_player->hasClan() && $_player->getClanID() == $news["clanID"]) );
$isNews = $isNews && $canEdit;
/* ===================================================================================== */
$dbh->debug($debug);
if($debug){
    Debug::r($news);
}
/* write to db ========================================================================= */
$result = $dbh->removeNews($news["newsID"]);
//	Debug::v($result);
if (!$result) finish(_error(ERROR_DB_DEL_NEWS, null, false, true));
else if ($debug) Debug::h($result);
/* ===================================================================================== */	
if($debug) exit();
finish(URL_ROOT.$_route);
/* ===================================================================================== */
/* ===================================================================================== */
function finish($route){
    echo "<script>window.location.href = '$route'</script>
        </body>
    </html>";
    exit();
}
?>