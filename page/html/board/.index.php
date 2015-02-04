<?php
if(!isset($_page)) exit();
/* ===================================================================================== */
_def("wot");
//_lib("PageDefaults");
_lib("WotData");
_lib("WotHandler");
_lib("Calc");
/* ===================================================================================== */
$activePage = $_page["board"];
$activePageClass = " class='active'";
$wotUser = $_page["user"];
$wh = new WotHandler(new WotData());
$playerInfo = $wh->getBasicPlayerInfo($wotUser);
if($playerInfo === false || empty($playerInfo)) _error(ERROR_API_GET_PLAYER_INFO);
/* ===================================================================================== */
$hasClan = true;//isset($playerInfo["clan"]);
$useTheme = false;
/* ===================================================================================== */
$userStyle = $hasClan && $useTheme ? getUserThemeStyle($playerInfo["clan"]["color"]): false;
$clanTag = $hasClan ? $playerInfo["clan"]["tag"] : CLAN_NONE_TAG;
$clanName = $hasClan ? $playerInfo["clan"]["name"] : CLAN_NONE_NAME;
$clanImage = $hasClan ? $playerInfo["clan"]["emblems"]["large"] : CLAN_NONE_IMAGE_URL;
$clanRole = $hasClan ? $playerInfo["clan"]["role"] : CLAN_NONE_ROLE;
$clanRole_i18n = $hasClan ? $playerInfo["clan"]["role_i18n"] : CLAN_NONE_ROLE_I18N;
$clanRoleImgURL = $hasClan ? PATH_IMG_RANK.$clanRole.PATH_IMG_RANK_EXT : null;
$clanRoleImg = $clanRoleImgURL !== null ? "<i class='wot wot-rank' style='background-image:url($clanRoleImgURL)'></i>" : null;
$userName = $wotUser["userName"];
$statsWins = $playerInfo["stats"]["wins"];
$statsBattles = $playerInfo["stats"]["battles"];
$statsWinRate = $playerInfo["stats"]["winRatePerBattle"];
$statsWinRateClass = $playerInfo["stats"]["winRatePerBattleClass"];
$statsHits = $playerInfo["stats"]["hits"];
$statsShots = $playerInfo["stats"]["shots"];
$statsHitAvg = $playerInfo["stats"]["avgHitratePerBattle"];
$ratingGlobal = $playerInfo["rating"]["global"];
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
</head>
<body>
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
					<i class='wot wot-emblem-large' style='background-image: url(<?=$clanImage;?>)'></i>
				</div>
				<h4><?=$clanTag;?></h4>
				<small><?=$clanName;?></small>
			</div>
		</div>
		<div class='sidebar-content'>
			<div class='sidebar-info'>
				<h4><?=$userName;?></h4>
				<h5>
					<?=$clanRoleImg;?>
					<span><?=$clanRole_i18n;?></span>
					<?=$clanRoleImg;?>
				</h5>
				<ul class='row'>
					<li class='col-md-6'>
						<i class='fa fa-trophy fa-fw'></i>
						<span class='<?=$statsWinRateClass;?>' data-tooltip='Gewonnen: <?=$statsWins;?>/<?=$statsBattles;?>'><?=$statsWinRate;?> %</span>
					</li>
					<li class='col-md-6'>
						<i class='fa fa-bullseye fa-fw'></i>
						<span data-tooltip='Treffer: <?=$statsHits;?>/<?=$statsShots;?>'><?=$statsHitAvg;?> %</span>
					</li>
				</ul>
				<ul class='row'>
					<li class='col-md-6'>
						<i class='fa fa-globe fa-fw'></i>
						<span class='tooltip-nowrap' data-tooltip='Pers&ouml;nliche Wertung'><?=$ratingGlobal;?></span>
					</li>
					<li class='col-md-6'>
					</li>
				</ul>
			</div>
		</div>
		<div class='sidebar-nav'>
			<h5>Navigation</h5>
			<ul class='nav nav-stacked'>
				<li<?=getActivePageClass(ROUTE_HOME, $activePage);?>><a href='<?=URL_ROOT.ROUTE_HOME;?>/'><i class='fa <?=Router::getFaImg(ROUTE_HOME);?>'></i><?=Router::getName(ROUTE_HOME)?></a></li>
				<li<?=getActivePageClass(ROUTE_CLAN, $activePage);?>><a href='<?=URL_ROOT.ROUTE_CLAN;?>/'><i class='fa <?=Router::getFaImg(ROUTE_CLAN);?>'></i><?=Router::getName(ROUTE_CLAN)?></a></li>
				<li<?=getActivePageClass(ROUTE_EVENTS, $activePage);?>><a href='<?=URL_ROOT.ROUTE_EVENTS;?>/'><i class='fa <?=Router::getFaImg(ROUTE_EVENTS);?>'></i><?=Router::getName(ROUTE_EVENTS)?></a></li>
				<li<?=getActivePageClass(ROUTE_CLANWARS, $activePage);?>><a href='<?=URL_ROOT.ROUTE_CLANWARS;?>/'><i class='fa <?=Router::getFaImg(ROUTE_CLANWARS);?>'></i><?=Router::getName(ROUTE_CLANWARS)?></a></li>
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
?>