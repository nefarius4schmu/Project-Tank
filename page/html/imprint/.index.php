<?php
/**
* Project Tank Webpage
* webpage for impressum
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
_lib("PageDefaults");
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=PAGE_BRAND;?> - Impressum</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="<?=Router::CSS_BASE?>">
	<link rel="stylesheet" type="text/css" href="<?=Router::CSS_IMPRINT?>">
</head>
<body>
	<div class="b-main">
		<?php
		PageDefaults::getGlobalNav();
		?>
		<header><?php
			PageDefaults::getHeaderContent();
		?></header>
		<div class="b-content clearfix">
		<p>
			<h1>Impressum</h1><br />Angaben gem. § 5 TMG<br/><br/><b>Betreiber und Kontakt:</b><br />Philipp Schnöckel<br /><br />Schwarzburger Str. 2 <br />12687 Berlin<br /><br />Telefonnummer: 030 53645726<br />E-Mail-Adresse: info@scale-studios.de<br /><br /><b>Verantwortlicher für journalistisch-redaktionelle Inhalte gem. § 55 II RstV:</b><br />Philipp Schnöckel
, Steffen Lange<br /><br /><b>Bilder und Grafiken:</b><br />Angaben der Quelle für verwendetes Bilder- und Grafikmaterial:<br /><a class="text-link" href="http://www.worldoftanks.com">worldoftanks.com</a>, 
<a class="text-link" href="http://www.wargaming.net">wargaming.net</a>

			</p>
		</div>
	</div>
	<?php
	PageDefaults::getFooter("clearfix");
	?>
</body>
</html>