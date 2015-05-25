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
_lib("Html");
//_lib("WotPlayer");
/* ===================================================================================== */
$activePage = $_page["board"];
$activePageClass = " class='active'";
$wotUser = $_page["user"];
/** @var WotPlayer $playerInfo */
$playerInfo = $wotUser["player"];
//Debug::r($_page);
$wd = new WotData();
$wh = new WotHandler($wd);
/* ===================================================================================== */
//Debug::r($playerInfo); exit();
/* ===================================================================================== */
$hasClan = $playerInfo->hasClan();
$useTheme = isset($wotUser[WotSession::USER_SETTINGS], $wotUser[WotSession::USER_SETTINGS][SETTINGS_ID_THEME_CLAN_COLOR])
			&& $wotUser[WotSession::USER_SETTINGS][SETTINGS_ID_THEME_CLAN_COLOR] == 1;
/* ===================================================================================== */
$userName 		= $playerInfo->getName();
$userStyle 		= $hasClan && $useTheme ? getUserThemeStyle($playerInfo->getClanColor()) : false;
$clanTag	 	= $hasClan ? $playerInfo->getClanTag() : CLAN_NONE_TAG;
$clanName 		= $hasClan ? $playerInfo->getClanName() : CLAN_NONE_NAME;
$clanImageURL 	= $hasClan ? $playerInfo->getClanEmblemLarge() : CLAN_NONE_IMAGE_URL;
$clanRole 		= $hasClan ? $playerInfo->getClanRole() : CLAN_NONE_ROLE;
$clanRole_i18n 	= $hasClan ? $playerInfo->getClanRole_i18n() : CLAN_NONE_ROLE_I18N;
$clanRoleImgURL = $hasClan ? PATH_IMG_RANK.$clanRole.PATH_IMG_RANK_EXT : null;
$statsWins 		= $playerInfo->getStatsWins();
$statsBattles 	= $playerInfo->getStatsBattles();
$statsWinRate 	= $playerInfo->getStatsWinRatePerBattle();
$statsWinRateClass = $wh->winRateToClass($statsWinRate);
$statsHits 		= $playerInfo->getStatsHits();
$statsShots 	= $playerInfo->getStatsShots();
$statsHitAvg 	= $playerInfo->getStatsAvgHitRatePerBattle();
$statsDamage 	= $playerInfo->getStatsDamage();
$statsDamageAvg	= $playerInfo->getStatsAvgDamagePerBattle();
$ratingGlobal 	= $playerInfo->isRating() ? number_format($playerInfo->getRatingGlobal()*1,0,',','.') : "<i class='wot wot-norating'></i>";


$clanImage = $clanImageURL != CLAN_NONE_IMAGE_URL ? "<i class='wot wot-emblem-large' style='background-image: url($clanImageURL)'></i>" : "<i class='wot wot-emblem-large wot-noclan'></i>";
$clanRoleImage = $clanRoleImgURL !== null ? "<i class='wot wot-rank' style='background-image:url($clanRoleImgURL)'></i>" : null;
/* ===================================================================================== */
$breadcrumbs = Router::getBreadcrumbs();

/* prepare page requirements =========================================================== */
//$css_defaults = [
//    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css?332',
//    '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css?430',
//    'css/board.css?003',
//];
//$js_defaults = [
//    'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js?1112',
//    'js/ajax.js?100',
//    'js/board.js?001',
//];
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=PAGE_BRAND;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?=Html::css(array_merge(Router::getDefaultCSS(ROUTETYPE_BOARD), Router::getCSS($activePage)))?>
	<style><?=$userStyle;?></style>
</head><?php 
flush(); 
?><body>
	<header class='topbar navbar navbar-fixed-top'>
		<div class='container'>
			<div class='navbar-header'><a href='<?=URL_ROOT;?>' class='navbar-brand'><?=PAGE_BRAND;?></a></div>
			<nav class='navbar-collapse'>
				<button class="navbar-toggle collapsed" data-toggle="dropdown" data-target="#accountNav">
					<i class='fa fa-bars fa-2x'></i>
				</button>
				<ul id='accountNav' class='nav navbar-nav navbar-right'>
					<li><p class='navbar-text'><i class='fa fa-fw fa-user'></i><?=$userName;?></p></li>
					<li><a href='<?=URL_ROOT.ROUTE_SETTINGS;?>/'><i class='fa fa-wrench' title='Einstellungen'></i></a></li>
					<li><a href='<?=URL_ROOT.ROUTE_LOGOUT;?>/'>Logout</a></li>
				</ul>	
			</nav>
		</div>
	</header>
	<div class='wrapper'>
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
							<span class='tooltip-nowrap' data-tooltip='&Oslash; Schaden in <?=$statsBattles?> Gefechten'><?=$statsDamageAvg;?></span>
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
				<h5><?=PAGE_HEADLINE_NAVIGATION;?></h5>
				<ul class='nav nav-stacked'><?php
					$navs = BoardNav::getNavigations(true);
//					Debug::r($navs);
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
		<div class='content-wrapper'>
			<div class='board-head'>
				<div class='b-breadcrumb'>
					<span>Du bist hier:</span>
					<ul class='breadcrumb'><?php
						foreach($breadcrumbs as $crumb=>$loc){
							$title = Router::getType($crumb) == ROUTETYPE_BOARD ? BoardNav::getTitle($crumb) : $crumb;
							$url = URL_ROOT.$loc;
							echo "<li><a href='".$url."'>".$title."</a></li>";
						}
					?></ul>
				</div>
			</div>
			<div class='content'><?php
                $loc = Router::getLocation($activePage);
				_loadBoard($loc, $_page);
			
			?></div>
		</div>
	</div>
<?=Html::js(array_merge(Router::getDefaultJS(ROUTETYPE_BOARD), Router::getJS($activePage)))?>
</body>
</html>
<?php
/* ===================================================================================== */
/* ===================================================================================== */
function getActivePageClass($target, $active){
	return $target != $active ? "" : " class='active'";
}

function getUserThemeStyle($hexColor){
	if(!isset($hexColor)) return null;
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