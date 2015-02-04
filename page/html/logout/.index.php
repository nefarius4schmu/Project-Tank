<?php
/**
* Project Tank Webpage
* logout page
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
if($_page["login"] === false) _error(ERROR_IS_LOGOUT);
/* ===================================================================================== */
_lib("WotData");
_lib("WotSession");
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
			<p>Auf Wiedersehen, <?=$_GET["nickname"];?>!</p>
			<small>Sie werden jeden Moment ausgeloggt..</small>
		</div>
	</div>
<?php
flush();
/* clear session ======================================================================= */
WotSession::logout();
/* logout at wargamin ================================================================== */
$wotData = new WotData();
$data = $wotData->getLogoutData($_page["user"]["token"]);
if($data === false) _error(ERROR_LOGOUT_FAILED);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data["data"]),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($data["url"], false, $context);
if($result === false) _error(ERROR_LOGOUT_SEND_FAILED);
$response = json_decode($result, true);
if(!isset($response) || $response["status"] != "ok") _error(ERROR_API_LOGOUT);

/* store login data ==================================================================== */
$_page["login"] = false;
$_page["user"] = null;
/* ===================================================================================== */
?>
<script>window.location.href = "<?=URL_ROOT;?>"</script>
</body>
</html>