<?php
/**
* Project Tank Webpage
* save settings page
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
_lib("DB");
_lib("DBHandler");
/* ===================================================================================== */
$isLogin = $_page["login"] !== false;
if(!$isLogin) _error(ERROR_MISSING_LOGIN);
/* ===================================================================================== */
$back = isset($_GET["b"]) ? $_GET["b"] : null;
$redirect = Router::getRoute($back, $isLogin);
$accountID = $_page["user"]["userID"];
/* ===================================================================================== */
$settings = isset($_POST["settings"]) ? $_POST["settings"] : [];
/* ===================================================================================== */
if($debug){
	Debug::r($_GET);
	Debug::r($_POST);
	Debug::v($back);
	Debug::s($redirect);
}
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
			<small>Ihre Einstellungen werden &uuml;bernommen..</small>
		</div>
	</div>
<?php
flush();
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink());
if($debug) $dbh->debug();
if(!$dbh->isConnection()) $redirect = "error/".ERROR_DB_CONNECTION;
else{
	// update user settings
	$result = $dbh->setUserSettings($accountID, $settings);
	if(!$result) $redirect = "error/".ERROR_DB_SET_SETTINGS;
	else if($debug) Debug::i($result);
	WotSession::setSettings($settings);
}
/* ===================================================================================== */	
if($debug)Debug::e($redirect);
/* ===================================================================================== */	
if($debug) exit();
?>
<script>window.location.href = "<?=URL_ROOT.ROUTE_SETTINGS;?>"</script>
</body>
</html>