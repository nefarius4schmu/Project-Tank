<?php
/**
* <?=PAGE_BRAND;?> Webpage
* handler for webpage default elements
* @depricated
* @author Steffen Lange
*/
_def("wot");
/* ===================================================================================== */
class PageDefaults{
	
	private static function getGNavItem($content){
		return "<li class='g-item'>$content</li>";
	}
	
	public static function getGlobalNav($nickname=null, $class=null){
		$classA = isset($class) ? " $class" : "";
		$nicknameItem = isset($nickname) ? self::getGNavItem("<a id='user' href='settings/' target='_self'>$nickname</a>") : "";
		$logoutItem = isset($nickname) ? self::getGNavItem("<a id='logout' href='logout/' target='_self'>Logout</a>") : "";
		$settingsButton = isset($nickname) ? self::getGNavItem("<a id='settings' href='settings/' target='_self'><img src='images/icons/settings.png'</a>") : "";
		?>
		<div class="global-nav<?php echo $classA;?>">
			<div class="g-nav">
				<ul class="g-menu">
					<?php
					echo $nicknameItem;
					echo $logoutItem;
					echo $settingsButton;
					?>
				</ul>

			</div>
		</div>
		<?php
	}
	
	public static function getHeaderContent($activeLogin=false, $class=null, $showHeadup=false, $nickname=null, $rank=null){
		$redirect = $activeLogin ? "home/" : "/";
		?>
		<a class='home-link' href='<?php echo URL_ROOT;?>' target='_self'></a>
		<div id='wotLogoHeader' class='header-logo'>
			<a href='http://worldoftanks.com/' target='_blank' style='width:100%;height:100%;'></a>
			<div id='pageLogoHeader' class='header-logo'><a href='<?php echo URL_ROOT;?>' target='_self'><?=PAGE_BRAND;?></a></div>	
		</div>
		<?php
	}
	
	public static function getHeader($activeLogin=false, $class=null, $showHeadup=false, $nickname=null, $role_i18n=null){
		$classA = isset($class) ? " class='$class'" : "";
		$redirect = $activeLogin ? "home/" : "/";
		?>
		<header<?php echo $classA;?>>
			<a class='home-link' href='<?php echo $redirect;?>' target='_self'></a>
			<div id='wotLogoHeader' class='header-logo'>
				<a href='http://worldoftanks.com/' target='_blank' style='width:100%;height:100%;'></a>
				<div id='pageLogoHeader' class='header-logo'><a href='<?php echo $redirect;?>' target='_self'><?=PAGE_BRAND;?></a></div>	
			</div>
			<?php
			if($showHeadup) self::getHeadup($nickname, $role_i18n);
			?>
		</header>
		<?php
	}
	
	public static function getUserDisplay($nickname, $role=null, $role_i18n="?", $winRate="?", $battles="?", $tanks10="?", $clanEmblemLarge=null){
		$imgLoader = "<img src='".IMG_LOADER_THUMB_DEFAULT."' alt='loading' class='img-thumb-loader'>";
		?>
		<div class='user-headup a-box'>
			<div class='headup-logo a-col'><?php
				if(isset($clanEmblemLarge)) echo "<img class='img-clan emblem-large' src='$clanEmblemLarge' alt='clanLogo'>";
				else echo "<div class='img-error emblem-large'></div>";
			?></div>
			<div class='headup-info a-col'>
				<ul class='v-list'>
					<li class='v-item'>
						<div class='headup-name'><p><?php echo $nickname;?></p></div>
					</li>
					<li class='v-item'>
						<div class='headup-rank icon-box'>
							<?php if(isset($role)) echo "<img class='img-rank' src='images/icons/rank/$role.png'>";?>
							<p><?php echo $role_i18n;?></p>
						</div>
					</li>
					<li class='v-item'>
						<div class='headup-stats'>
							<ul class='l-list'>
								<li class='l-item'>
									<div class='headup-winrate icon-box' title='Win-Rate'>
										<img class='img-mini' src='images/logos/winrate_16.png'>
										<p><?php echo $winRate."%";?></p>
									</div>
								</li>
								<li class='l-item'>
									<div class='headup-battles icon-box' title='Battles'>
										<img class='img-mini' src='images/logos/battles_24.png'>
										<p><?php echo $battles;?></p>
									</div>
								</li>
								<li class='l-item icon-box'>
									<div class='headup-tier10' title='Tier10-Tanks'>
										<p><?php echo $tanks10;?></p>
									</div>
								</li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	public static function getHeadup($nickname, $rank, $winRate, $battles, $tanks10="?", $clanEmblemLarge){
		$imgLoader = "<img src='".IMG_LOADER_THUMB_DEFAULT."' alt='loading' class='img-thumb-loader'>";
		?>
		<div class='user-headup a-box'>
			<div class='headup-logo a-col'>
				<!--<div class='img-clan-logo'></div>-->
			</div>
			<div class='headup-info a-col'>
				<ul class='v-list'>
					<li class='v-item'>
						<div class='headup-name'><p><?php echo $nickname;?></p></div>
					</li>
					<li class='v-item'>
						<div class='headup-rank'><p><?php echo $rank;?></p></div>
					</li>
					<li class='v-item'>
						<div class='headup-stats'>
							<ul class='l-list'>
								<li class='l-item'>
									<div class='headup-winrate' title='Win-Rate'><?php echo $imgLoader;?></div>
								</li>
								<li class='l-item'>
									<div class='headup-battles' title='Battles'><?php echo $imgLoader;?></div>
								</li>
								<li class='l-item'>
									<div class='headup-tier10' title='Tier10-Tanks'><?php echo $imgLoader;?></div>
								</li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	public static function getFooter($class=null){
		$classA = isset($class) ? " class='$class'" : "";
		?>
		<footer<?php echo $classA;?>>
			<div class='logo-footer logo-wot pull-left'><a href='http://www.worldoftanks.com/' target='_blank' style='width:100%;height:100%;'></a></div>
			<div class='logo-footer logo-wotdev pull-right'><a href='http://www.wargaming.net/' target='_blank' style='width:100%;height:100%;'></a></div>
			<div class='f-content'>
				<ul class='footer-list h-list'>
					<li class='h-item'>
						&copy; 2015 Scale-Studios<br>
						<a href='<?=URL_ROOT.ROUTE_IMPRINT?>'><strong>Impressum</strong></a>
					</li>
				</ul>
			</div>
		</footer>
		<?php
	}
	
	public static function getNavigation($activeItem){
		$activeClass =" active";
		?>
		<div id='nav' class='b-nav'>
			<ul class='b-menu box'>
				<li id='nav-home' class='b-item'>
					<a href='home/' class='b-link<?php if($activeItem=='home')echo $activeClass?>'>Home</a>
				</li>
				<li id='nav-clan' class='b-item'>
					<a href='clan/' class='b-link<?php if($activeItem=='clan')echo $activeClass?>'>Clan</a>
				</li>
				<li id='nav-clanwars' class='b-item'>
					<a href='clanwars/' class='b-link<?php if($activeItem=='clanwars')echo $activeClass?>'>Clanwars</a>
				</li>
				<li id='nav-events' class='b-item'>
					<a href='events/' class='b-link<?php if($activeItem=='events')echo $activeClass?>'>Events</a>
				</li>
				<li id='nav-blog' class='b-item'>
					<a href='#' class='b-link<?php if($activeItem=='blog')echo $activeClass?>'>Blog</a>
				</li>
			</ul>
		</div>
		<?php
	}
	
	public static function getHorizontalNavigation($activeItem){
		$activeClass =" active";
		?>
		<div id='page-nav' class='h-nav'>
			<ul class='b-menu'>
				<li id='nav-home' class='h-item'>
					<a href='home/' class='b-link<?php if($activeItem=='home')echo $activeClass?>'>Home</a>
				</li>
				<li id='nav-clan' class='h-item'>
					<a href='clan/' class='b-link<?php if($activeItem=='clan')echo $activeClass?>'>Clan</a>
				</li>
				<li id='nav-clanwars' class='h-item'>
					<a href='clanwars/' class='b-link<?php if($activeItem=='clanwars')echo $activeClass?>'>Clanwars</a>
				</li>
				<li id='nav-events' class='h-item'>
					<a href='events/' class='b-link<?php if($activeItem=='events')echo $activeClass?>'>Events</a>
				</li>
				<li id='nav-blog' class='h-item'>
					<a href='#' class='b-link<?php if($activeItem=='blog')echo $activeClass?>'>Blog</a>
				</li>
			</ul>
		</div>
		<?php
	}
}
