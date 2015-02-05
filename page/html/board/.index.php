<?php
/**
* Project Tank Webpage
* basic layout for board webpages
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
_def("wot");
//_lib("PageDefaults");
_lib("WotData");
_lib("WotHandler");
_lib("Calc");
_lib("BoardNav");
/* ===================================================================================== */
$activePage = $_page["board"];
$activePageClass = " class='active'";
$wotUser = $_page["user"];
$wh = new WotHandler(new WotData());
/* ===================================================================================== */
$playerInfo = $wh->getBasicPlayerInfo($wotUser);
if($playerInfo === false) _error(ERROR_API_GET_PLAYER_INFO);
/* ===================================================================================== */
$hasClan = isset($playerInfo["clan"]["id"]);
$useTheme = false;
/* ===================================================================================== */
$userName 		= $playerInfo["name"];
$userStyle 		= $hasClan && $useTheme ? getUserThemeStyle($playerInfo["clan"]["color"]): false;
$clanTag	 	= $hasClan ? $playerInfo["clan"]["tag"] : CLAN_NONE_TAG;
$clanName 		= $hasClan ? $playerInfo["clan"]["name"] : CLAN_NONE_NAME;
$clanImageURL 	= $hasClan ? $playerInfo["clan"]["emblems"]["large"] : CLAN_NONE_IMAGE_URL;
$clanRole 		= $hasClan ? $playerInfo["clan"]["role"] : CLAN_NONE_ROLE;
$clanRole_i18n 	= $hasClan ? $playerInfo["clan"]["role_i18n"] : CLAN_NONE_ROLE_I18N;
$clanRoleImgURL = $hasClan ? PATH_IMG_RANK.$clanRole.PATH_IMG_RANK_EXT : null;
$statsWins 		= $playerInfo["stats"]["wins"];
$statsBattles 	= $playerInfo["stats"]["battles"];
$statsWinRate 	= $playerInfo["stats"]["winRatePerBattle"];
$statsWinRateClass = $wh->winRateToClass($statsWinRate);
$statsHits 		= $playerInfo["stats"]["hits"];
$statsShots 	= $playerInfo["stats"]["shots"];
$statsHitAvg 	= $playerInfo["stats"]["avgHitRatePerBattle"];
$statsDamage 	= $playerInfo["stats"]["damage"];
$statsDamageAvg	= $playerInfo["stats"]["avgDamagePerBattle"];
$ratingGlobal 	= isset($playerInfo["rating"]["global"]) ? $playerInfo["rating"]["global"] : "<i class='wot wot-norating'></i>";


$clanImage = $clanImageURL != CLAN_NONE_IMAGE_URL ? "<i class='wot wot-emblem-large' style='background-image: url($clanImageURL)'></i>" : "<i class='wot wot-emblem-large wot-noclan'></i>";
$clanRoleImage = $clanRoleImgURL !== null ? "<i class='wot wot-rank' style='background-image:url($clanRoleImgURL)'></i>" : null;
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Planet Tank</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/board.css">
	<style><?=$userStyle;?></style>
</head><?php 
flush(); 
?><body>
	<header class='topbar navbar navbar-fixed-top'>
		<div class='container'>
			<div class='navbar-header'><p class='navbar-brand'><?=PAGE_BRAND;?></p></div>
			<nav class='navbar-collapse'>
				<ul class='nav navbar-nav navbar-right'>
					<li><p class='navbar-text'><i class='fa fa-fw fa-user'></i><?=$userName;?></p></li>
					<li><a href='<?=URL_ROOT.ROUTE_SETTINGS;?>/'><i class='fa fa-wrench' title='Einstellungen'></i></a></li>
					<li><a href='<?=URL_ROOT.ROUTE_LOGOUT;?>/'>Logout</a></li>
				</ul>	
			</nav>
		</div>
	</header>
	<div class='sidebar navbar navbar-fixed-left'>
		<div class='sidebar-header'>
			<div class='sidebar-info'>
				<div class='sidebar-logo'>
					<?=$clanImage;?>
				</div>
				<h4><?=$clanTag;?></h4>
				<small><?=$clanName;?></small>
			</div>
		</div>
		<div class='sidebar-content'>
			<div class='sidebar-info'>
				<h4><?=$userName;?></h4>
				<h5>
					<?=$clanRoleImage;?>
					<span><?=$clanRole_i18n;?></span>
					<?=$clanRoleImage;?>
				</h5>
				<ul class='row'>
					<li class='col-md-6'>
						<i class='fa fa-bullseye fa-fw'></i>
						<span data-tooltip='Treffer: <?=$statsHits;?>/<?=$statsShots;?>'><?=$statsHitAvg;?> %</span>
					</li>
					<li class='col-md-6'>
						<i class='fa fa-bomb fa-fw'></i>
						<span class='tooltip-nowrap' data-tooltip='<?=$statsDamage;?> Schaden in <?=$statsBattles?> Gefechten'><?=$statsDamageAvg;?></span>
					</li>
				</ul>
				<ul class='row'>
					<li class='col-md-6'>
						<i class='fa fa-trophy fa-fw'></i>
						<span class='<?=$statsWinRateClass;?>' data-tooltip='Gewonnen: <?=$statsWins;?>/<?=$statsBattles;?>'><?=$statsWinRate;?> %</span>
					</li>
					<li class='col-md-6'>
						<i class='fa fa-globe fa-fw'></i>
						<span class='tooltip-nowrap' data-tooltip='Pers&ouml;nliche Wertung'><?=$ratingGlobal;?></span>
					</li>
				</ul>
			</div>
		</div>
		<div class='sidebar-nav'>
			<h5>Navigation</h5>
			<ul class='nav nav-stacked'><?php
				$navs = BoardNav::getNavigations(true);
//				Debug::r($locs);
				foreach($navs as $nav){
					$reqClan = BoardNav::hasReqClan($nav);
					$enabled = !$reqClan || ($reqClan && $hasClan);
					$options = [
						"enabled"=>$enabled,
						"loc"=>$nav,
						"title"=>BoardNav::getTitle($nav),
						"url"=>URL_ROOT.$nav,
						"faimg"=>BoardNav::getFaImg($nav),
						"tooltip"=>$enabled ? null : TOOLTIP_REQ_CLAN,
					];
					echo getPageNavLink($options, $activePage);
//				Debug::r($options);
				}
			?>
			</ul>
		</div>
	</div>
	<div class='content'><?php
		$_page["playerInfo"] = $playerInfo;
		_loadBoard($activePage, $_page);
//		Debug::r($_page["user"]);
//		Debug::r($playerInfo);
	
	?></div>
</body>
</html>
<?php
/* ===================================================================================== */
/* ===================================================================================== */
function getActivePageClass($target, $active){
	return $target != $active ? "" : " class='active'";
}

function getUserThemeStyle($hexColor){
	list($r, $g, $b) = Calc::hexToRgb($hexColor);
	$grey = floor(($r+$g+$b)/3);
	$cssRGBA = Calc::rgbaToCSS($r, $g, $b, .8);
	$cssColor = $grey < 128 ? "#fafafa" : "#838383";
	
	return ".topbar,.sidebar{
			color: $cssColor;
			background-color: $cssRGBA;
		}";
}

function getPageNavLink($options, $activePage){
	$isEnabled = isset($options["enabled"]) && $options["enabled"];
	$isLoc = isset($options["loc"]);
	$isTitle = isset($options["title"]);
	$isURL = isset($options["url"]);
	$isTarget = isset($options["target"]);
	$isFaImg = isset($options["faimg"]);
	$isTooltip = isset($options["tooltip"]);
	// =============================================
	$active = $isLoc && $options["loc"] == $activePage ? " active" : null;
	$enabled = $isEnabled ? null : " disabled";
	$img = $isFaImg ? "<i class='fa ".$options["faimg"]."'></i>" : null;
	$url = $isEnabled && $isURL ? $options["url"] : "#";
	$target = $isTarget ? " target='".$options["target"]."'" : null;
	$title = $isTitle ? $options["title"] : null;
	$tooltip = $isTooltip ? " data-tooltip='".$options["tooltip"]."'" : null;
	$tooltipClass = $isTooltip ? " tooltip-text" : null;
	return "<li class='".$active.$tooltipClass.$enabled."'".$tooltip."><a href='$url'".$target.">".$img.$title."</a></li>";
}
?>