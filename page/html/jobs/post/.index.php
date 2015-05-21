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
/* ===================================================================================== */
$options = ["login"=>$isLogin, "clan"=>$_player->hasClan(), "settings"=>$_user["settings"]];
$_route = isset($_GET["b"]) ? Router::getRoute2($_GET["b"], $options) : Router::getDefault($isLogin);
/* ===================================================================================== */
$isNews = isset($_POST["news"]) && is_array($_POST["news"]);
if(!$isNews) finish($_route);
/* ===================================================================================== */
$news = $isNews ? $_POST["news"] : null;
if(!isset($news["title"]) || empty($news["title"])) _warning(WARNING_POST_MISSING_TITLE, $_route);
if(!isset($news["text"]) || empty($news["text"])) _warning(WARNING_POST_MISSING_TEXT, $_route);
$isEdit = isset($news["id"]);
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
			<small>Ihr Beitrag wird erstellt...</small>
		</div>
	</div>
<?php
flush();
/* prepare data ======================================================================== */
$options = ["useHyphens"=>true];
$newsLink = Html::clean($news["title"], $options)."-".date("YmdHis", time())."-".uniqid();
if(!isset($news["summary"])) $news["summary"] = Html::truncate($news["text"], Html::NEWS_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);
/* ===================================================================================== */
if($debug){
    Debug::s(URL_ROOT.$_route);
    Debug::r($news);
    foreach($news as $key=>$value)
        Debug::i(htmlentities($value));
//    exit();
}
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
/* write to db ========================================================================= */
if($dbh->isConnection()){
    $sqlData = [
        "userID"=>$_player->getID(),
        "newsID"=>$news["id"],
        "title"=>htmlentities($news["title"]),
        "text"=>$news["text"],
        "summary"=>$news["summary"],
        "uid"=>$newsLink,
        "catID"=>$news["cat"],
        "coverimage"=>$news["cover"],
    ];
//    Debug::v($sqlData);
	$result = $isEdit
        ? $dbh->updateNews($sqlData)
//        ? $dbh->updateNews($_player->getID(), $news["id"], htmlentities($news["title"]), $news["text"], $news["summary"], $newsLink, $news["cat"])
//        : $dbh->postNews($_player->getID(), htmlentities($news["title"]), $news["text"], $news["summary"], $newsLink, $news["cat"]);
        : $dbh->postNews($sqlData);
//	Debug::v($result);
    if(!$result) finish(_error(ERROR_DB_SET_SETTINGS, null, false, true));
	else if($debug) Debug::h($result);
} else
    finish(_error(ERROR_DB_CONNECTION, null, false, true));
/* ===================================================================================== */	
if($debug) exit();
finish(URL_ROOT.$_route);
?>
<?php
/* ===================================================================================== */
/* ===================================================================================== */
function finish($route){
    echo "<script>window.location.href = '$route'</script>
        </body>
    </html>";
    exit();
}
?>