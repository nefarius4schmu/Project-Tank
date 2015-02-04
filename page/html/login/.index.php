<?php
if(!isset($_page)) exit();
/* ===================================================================================== */
if($_page["login"] !== false) _error(ERROR_LOGIN_EXISTS);
else if(!isset($_GET["status"],$_GET["access_token"],$_GET["nickname"],$_GET["account_id"],$_GET["expires_at"])) _error(ERROR_LOGIN_AUTH);
if(!isset($_GET["status"],$_GET["access_token"],$_GET["nickname"],$_GET["account_id"],$_GET["expires_at"])) _error(ERROR_LOGIN_AUTH);
else if($_GET["status"] != "ok") _error(ERROR_LOGIN_FAILED);
//Debug::r($_GET);

/**
* TODO
* 
* catch http://localhost/wot/login.php?&status=error&message=AUTH_CANCEL&code=401
* catch no clanID
* 
* check session login failed
*/
/* ===================================================================================== */
_lib("WotData");
_lib("DB");
_lib("DBHandler");
/* get account info / clan id ========================================================== */
$wotData = new WotData();
$playerInfo = $wotData->getPlayerInfo($_GET["account_id"]);
if($playerInfo === false || empty($playerInfo) || $playerInfo["status"] != "ok") _error(ERROR_API_GET_PLAYER_INFO);
$playerData = $playerInfo["data"][$_GET["account_id"]];

if(!isset($playerData["clan_id"]) || empty($playerData["clan_id"])){
	$playerData["clan_id"] = null;
}
/* store login data ==================================================================== */
$_page["login"] = WotSession::setLoginData($_GET["account_id"], $_GET["nickname"], $playerData["clan_id"], $_GET["access_token"], $_GET["expires_at"]);
$_page["user"] = WotSession::getLoginData();
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink());
if($dbh === false) _error(ERROR_DB_CONNECTION);
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Willkommen!</title>
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
			<p>Willkommen, <?=$_GET["nickname"];?>!</p>
			<small>Dein Erlebnis wird vorbereitet..</small>
		</div>
	</div>
<?php
flush();
/* ===================================================================================== */	
// get first login 
$firstLogin = false;

// update login database
$result = $dbh->accountLogin($_GET["account_id"], $_GET["nickname"]);
if($result === false) _error(ERROR_DB_LOGIN); // do smth else cause page is printed

//$firstLogin = true;
//$activeLogin = true;
?>
<script>window.location.href = "<?=URL_ROOT;?>"</script>
</body>
</html>