<?php
/**
* Project Tank Webpage
* webpage for user settings
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
/* ===================================================================================== */
_def("settings");
_lib("DB");
_lib("DBHandler");
$dbh = new DBHandler(DB::getLink());
if($dbh === false) _error(ERROR_DB_CONNECTION, null, $debug);
/* ===================================================================================== */
$playerInfo = $_page["playerInfo"];
$hasClan = isset($playerInfo["clan"]);
$clanLogoMedium = $hasClan ? $playerInfo["clan"]["emblems"]["medium"] : "images/icons/settings/conference_call-32.png";
/* ===================================================================================== */
$settings = $dbh->getUserSettings($playerInfo["id"]);
//Debug::v($settings);
/* ===================================================================================== */
?>
<form action='<?=URL_ROOT.ROUTE_SETTINGS."/".ROUTE_SET;?>' method='POST'>
	<h2><?=PAGE_TITLE_SETTINGS;?></h2>
	<div class='button-bar bar-top'>
		<button type='submit' class='btn btn-success btn-big pull-right'>Anwenden</button>
	</div>
	<div class='bs-callout bs-callout-primary bs-callout-custom'>
		<h4>Anzeige</h4>
		<div class='callout-content'><?php
			$options = [
				"title"=>TEXT_SETTINGS_USE_THEME,
				"descr"=>TEXT_SETTINGS_USE_THEME_DESCR,
				"elements"=>[
					"Ja"=>1,
					"Nein"=>0,
				],
				"active"=>$settings[SETTINGS_ID_THEME_CLAN_COLOR],
				"input"=>"settings[".SETTINGS_ID_THEME_CLAN_COLOR."]",
			];
			echo Html::createSwitch("useTheme", $options, true);
		?></div>
	</div>
	<div class='bs-callout bs-callout-danger bs-callout-custom'>
		<h4>Private Daten</h4>
		<div class='callout-content'><?php
			
			$options = [
				"title"=>TEXT_SETTINGS_TANKS,
				"descr"=>TEXT_SETTINGS_TANKS_DESCR,
				"active"=>$settings[SETTINGS_ID_SHOW_TANKS],
				"input"=>"settings[".SETTINGS_ID_SHOW_TANKS."]",
				"elements"=>[
					[
						"text"=>"Nicht anzeigen",
						"value"=>0,
						"data"=>[
							"imagesrc"=>"images/icons/settings/lock-32.png",
							"description"=>"Panzer verbergen",
						],
					],
					[
						"text"=>"Clan F&uuml;hrung",
						"value"=>1,
						"data"=>[
							"imagesrc"=>"images/icons/settings/contacts-32.png",
							"description"=>"nur für Clan-Führung sichtbar",
						],
					],
					[
						"text"=>"Offiziere",
						"value"=>2,
						"data"=>[
							"imagesrc"=>"images/icons/settings/group-32.png",
							"description"=>"für Ränge ab Junior Offizier sichtbar",
						],
					],
					[
						"text"=>"Soldaten",
						"value"=>3,
						"data"=>[
							"imagesrc"=>"images/icons/settings/conference_call-32.png",
							"description"=>"für Ränge ab Soldat sichtbar",
						],
					],
					[
						"text"=>"Clan",
						"value"=>4,
						"data"=>[
							"imagesrc"=>$clanLogoMedium,
							"description"=>"für alle Mitglieder des Clans sichtbar",
						],
					],
				],
			];
			echo Html::createDataSelect("showTanks", $options, true);
		?>
			<!--<ul class='row row-table'>
				<li class='col-md-6'>
					<h5><?=TEXT_SETTINGS_TANKS;?></h5>
					<small><?=TEXT_SETTINGS_TANKS_DESCR;?></small>
				</li>
				<li class='col-md-6'>
					<select class='vertical-align' id='showTanks'>
						<option value='0' selected='selected' data-imagesrc='images/icons/settings/lock-32.png'
							data-description='Panzer verbergen'>Nicht anzeigen</option>
						<option value='1' data-imagesrc='images/icons/settings/contacts-32.png'
							data-description='nur der Clan Führung anzeigen'>Clan Führung</option>
						<option value='2' data-imagesrc='images/icons/settings/group-32.png'
							data-description='alle Rängen ab Junior Offizier zeigen'>Offizieren</option>
						<option value='3' data-imagesrc='images/icons/settings/conference_call-32.png'
							data-description='alle Rängen ab Soldat zeigen'>Soldaten</option>	
						<option value='4' data-imagesrc='<?=$clanLogoMedium;?>'
							data-description='für alle Mitglieder des Clans sichtbar'>Clan</option>
					</select>
				</li>
			</ul>-->
		</div>
	</div>
	<!--<div class='bs-callout bs-callout-custom'>
		<h4>Global</h4>
		<div class='callout-content'>content</div>
	</div>
	<div class='bs-callout bs-callout-success bs-callout-custom'>
		<h4>Schmu</h4>
		<div class='callout-content'>content</div>
	</div>
	<div class='bs-callout bs-callout-warning bs-callout-custom'>
		<h4>Noch mehr Schmu</h4>
		<div class='callout-content'>content</div>
	</div>-->
	<!--<div class='button-bar'>
		<button type='submit' class='btn btn-success btn-big pull-right'>Anwenden</button>
	</div>-->
</form>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js'></script>
<script src='js/jquery.ddslick.min.js'></script>
<script src='js/settings.js'></script>

