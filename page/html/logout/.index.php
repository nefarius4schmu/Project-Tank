<?php
/**
* Project Tank Webpage
* logout page
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
if($_page["login"] === false) _error(ERROR_IS_LOGOUT);
$_route = URL_ROOT;
/* ===================================================================================== */
_lib("WotData");
_lib("WotSession");
/* clear session ======================================================================= */
WotSession::logout();
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
			<p>Auf Wiedersehen, <?=$_page["user"]["userName"];?>!</p>
			<small>Sie werden jeden Moment ausgeloggt..</small>
		</div>
	</div>
<?php
flush();
/* logout at wargamin ================================================================== */
$wotData = new WotData();
$data = $wotData->getLogoutData($_page["user"]["token"]);
if($data === false) $_route = _error(ERROR_LOGOUT_FAILED, null, false, true);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data["data"]),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($data["url"], false, $context);
if($result === false) $_route = _error(ERROR_LOGOUT_SEND_FAILED, null, false, true);
$response = json_decode($result, true);
if(!isset($response) || $response["status"] != "ok") $_route = _error(ERROR_API_LOGOUT, null, false, true);

/* store login data ==================================================================== */
$_page["login"] = false;
$_page["user"] = null;
/* ===================================================================================== */
?>
<script>window.location.href = "<?=$_route;?>"</script>
</body>
</html>