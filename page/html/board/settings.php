<?php
/* ===================================================================================== */
$playerInfo = $_page["playerInfo"];
$hasClan = isset($playerInfo["clan"]);
$clanLogoMedium = $hasClan ? $playerInfo["clan"]["emblems"]["medium"] : "images/icons/settings/conference_call-32.png";

/* ===================================================================================== */
?>
<h2><?=PAGE_TITLE_SETTINGS;?></h2>
<div class='bs-callout bs-callout-danger bs-callout-custom'>
	<h4>World of Tanks Private Data</h4>
	<div class='row'>
		<ul class='row'>
			<li class='col-md-6'>
				<h5><?=TEXT_SETTINGS_TANKS;?></h5>
				<small><?=TEXT_SETTINGS_TANKS_DESCR;?></small>
			</li>
			<li class='col-md-6'>
				<select class="vertical-align" id="showTanks">
					<option value="0" selected="selected" data-imagesrc="images/icons/settings/lock-32.png"
						data-description="Panzer verbergen">Nicht anzeigen</option>
					<option value="1" data-imagesrc="images/icons/settings/contacts-32.png"
						data-description="nur der Clan Führung anzeigen">Clan Führung</option>
					<option value="2" data-imagesrc="images/icons/settings/group-32.png"
						data-description="alle Rängen ab Junior Offizier zeigen">Offizieren</option>
					<option value="3" data-imagesrc="images/icons/settings/conference_call-32.png"
						data-description="alle Rängen ab Soldat zeigen">Soldaten</option>	
					<option value="4" data-imagesrc='<?=$clanLogoMedium;?>'
						data-description="für alle Mitglieder des Clans sichtbar">Clan</option>
				</select>
			</li>
		</ul>
	</div>
</div>
<div class='bs-callout bs-callout-custom'>
	<h4>Global</h4>
	<div class='row'>content</div>
</div>

