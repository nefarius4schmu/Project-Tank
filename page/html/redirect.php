<?php
/* ===================================================================================== */
if(!isset($_page)) exit();
if(!isset($redirect)) _error(ERROR_REDIRECT_NOT_SET);
/* ===================================================================================== */
//Debug::r($redirect);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Bitte warten..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="refresh" content="<?=$redirect["delay"];?>; url=<?=$redirect["url"];?>" />
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css">
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
		}
	</style>
</head>
<body>
	<div class='b-row'>
		<div class='box'>
			<img src='images/loader/loader-bar.gif' alt='loader'/>
			<p>Sie werden in <?=$redirect["delay"];?> sec. automatisch zu <strong><?=$redirect["name"];?></strong> weitergeleitet...</p>
			<small>Falls Ihr Browser keine automatische Weiterleitung unterst&uuml;tzt, <a href='<?=$redirect["url"];?>'>klicken Sie hier</a>!</small>
		</div>
	</div>
</body>
</html>