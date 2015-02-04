<?php
_lib("PageDefaults");
//include_once("libs/WotData.class.php");
//$wot = new WotData();

$redirectError = isset($_GET["redirect"]) ? $_GET["redirect"]*1 : 0;
/* ===================================================================================== */	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Clan Room</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/base.css">
	<link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
	<div class="b-main">
		<?php
		PageDefaults::getGlobalNav();
		if($redirectError > 0) printRedirectError($redirectError);
		PageDefaults::getHeader();	
		?>
		<div class="a-content clearfix">
			<a class='btn btn-danger btn-large btn-login' href="redirect/login" target="_self">Login</a>
		</div>
	</div>
	<?php
	PageDefaults::getFooter("clearfix");
	?>
</body>
</html>
<?php
/* ===================================================================================== */
/* ===================================================================================== */
function printRedirectError($errorCode){
	switch($errorCode){
		case 1: 
			Debug::e("Für vollen Funktionsumfang loggen Sie sich mit Ihrem Wargaming.net Account ein.($errorCode)");
			break;
		case 3:
			Debug::e("Die Datenbank ist derzeit nicht erreichbar.<br>Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt erneut.($errorCode)");
			break;
		case 2: 
		case 4:
		default:
			Debug::e("Login fehlgeschlagen!<br>Bitte versuchen Sie es erneut.($errorCode)");
			break;
	}
}

function print_d($var){
	echo "<pre>".print_r($var, true)."</pre>";
}

function print_v($var){
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

?>