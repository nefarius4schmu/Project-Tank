<?php
/**
* Project Tank Webpage
* basic handler for Wargaming.net Public API
* @author Steffen Lange
*/
class WotData{
	const MAP_ID_GLOBALMAP = "globalmap";
	
	// login
	private $applicationID = '751e3d9076d6833b0e2ebd5e16edecb8';
	private $urlLogin = 'http://api.worldoftanks.eu/wot/auth/login/';
	private $urlLogout = 'https://api.worldoftanks.eu/wot/auth/logout/';

	// wot api links
	private $urlClanList = 'https://api.worldoftanks.eu/wgn/clans/list/';
	private $urlClanInfo = 'https://api.worldoftanks.eu/wgn/clans/info/';
	private $urlMembersInfo = 'https://api.worldoftanks.eu/wgn/clans/membersinfo/';
	private $urlPlayerInfo = 'https://api.worldoftanks.eu/wot/account/info/';
	private $urlPlayerTank = 'http://api.worldoftanks.eu/wot/tanks/stats/';
	
	// wot api clan wars
	private $urlClanWarsClanBattles = 'https://api.worldoftanks.com/wot/globalwar/battles/';
	
	// required params names
	private $_paramApplicationID = "application_id";
	private $_paramAccessToken 	= "access_token";
	private $_paramExpiresAt 	= "expires_at";
	private $_paramRedirectURI 	= "redirect_uri";
	private $_paramAccountID 	= "account_id";
	private $_paramClanID 		= "clan_id";
	private $_paramMemberID 	= "member_id"; // deprecated
	private $_paramInGarage 	= "in_garage";
	private $_paramMapID 		= "map_id";
	
	// optional param names
	private $_paramFields	 	= "fields";
	private $_paramSearch	 	= "search";
	private $_paramLimit	 	= "limit";
	private $_paramOrderBy	 	= "order_by";
	private $_paramPageNo	 	= "page_no";
	private $_paramLanguage		= "language";
	
	function __construct(){}
	
	private function paramApplicationID(){return $this->_paramApplicationID."=".$this->applicationID;}
	private function paramAccessToken($v){return $this->_paramAccessToken."=$v";}
	private function paramExpiresAt($v)	 {return $this->_paramExpiresAt."=$v";}
	private function paramRedirectURI($v){return $this->_paramRedirectURI."=$v";}
	private function paramClanID($v)	 {return $this->_paramClanID."=$v";}
	private function paramMapID($v)	 	 {return $this->_paramMapID."=$v";}
	private function paramAccountID($v)	 {return $this->_paramAccountID."=$v";}
	private function paramMemberID($v)	 {return $this->_paramMemberID."=$v";}
	private function paramInGarage($v)	 {return $this->_paramInGarage."=$v";}
	private function paramFields($v)	 {return $this->_paramFields."=$v";}
	private function paramSearch($v)	 {return $this->_paramSearch."=$v";}
	private function paramLimit($v)		 {return $this->_paramLimit."=$v";}
	private function paramOrderBy($v)	 {return $this->_paramOrderBy."=$v";}
	private function paramPageNo($v)	 {return $this->_paramPageNo."=$v";}
	private function paramLanguage($v)	 {return $this->_paramLanguage."=$v";}
	
	private function getContents($url){
		$content = file_get_contents($url);
		if($content === false) return null;
		return json_decode($content, true);
	}
	
	public function getLoginURL($urlLoginRedirect, $expiresAt){
		if(!isset($urlLoginRedirect, $expiresAt)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramRedirectURI($urlLoginRedirect);
		$c = $this->paramExpiresAt(rawurlencode($expiresAt));
		return $this->urlLogin."?$a&$b&$c";
	}
	
	public function getLogoutData($accessToken){
		if(!isset($accessToken)) return false;
		$url = $this->urlLogout;
		$appID = $this->applicationID;
		$data = array("application_id"=>$appID, $this->_paramAccessToken=>$accessToken);
		return array("url"=>$url, "data"=>$data);
	}
	
	public function getClanList($fields=null, $search=null, $language=null, $limit=null, $orderBy=null, $pageNo=null){
		$a = $this->paramApplicationID();
		$b = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$c = !isset($search) ? "" : "&".$this->paramSearch($search);
		$d = !isset($limit) ? "" : "&".$this->paramLimit($limit);
		$e = !isset($orderBy) ? "" : "&".$this->paramOrderBy($orderBy);
		$f = !isset($pageNo) ? "" : "&".$this->paramPageNo($pageNo);
		$g = !isset($language) ? "" : "&".$this->paramLanguage($language);
		$url = $this->urlClanList."?$a".$b.$c.$d.$e.$f.$g;
		$content = $this->getContents($url);
		return $content;
	}
	
	public function getClanInfo($clanID, $token=null, $fields=null, $language=null){
		if(!isset($clanID)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramClanID($clanID);
		$c = !isset($language) ? "" : "&".$this->paramLanguage($language);
		$d = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$e = !isset($token) ? "" : "&".$this->paramAccessToken($token);
		$url = $this->urlClanInfo."?$a&$b".$c.$d.$e;
		$content = $this->getContents($url);
		return $content;
	}
	
	public function getPlayerInfo($accountID, $token=null, $fields=null, $language=null){
		if(!isset($accountID)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramAccountID($accountID);
		$c = !isset($language) ? "" : "&".$this->paramLanguage($language);
		$d = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$e = !isset($token) ? "" : "&".$this->paramAccessToken($token);
		$url = $this->urlPlayerInfo."?$a&$b".$c.$d.$e;
		$content = $this->getContents($url);
		return $content;
	}
	
	public function getMembersInfo($accountID, $fields=null){
		if(!isset($accountID)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramAccountID($accountID);
		$c = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$url = $this->urlMembersInfo."?$a&$b".$c;
		$content = $this->getContents($url);
		return $content;
	}
	
	public function getPlayerTanks($accessToken, $accountID, $inGarage, $fields=null){
		if(!isset($accessToken, $accountID, $inGarage)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramAccountID($accountID);
		$c = $this->paramAccessToken($accessToken);
		$d = $this->paramInGarage($inGarage);
		$e = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$url = $this->urlPlayerTank."?$a&$b&$c&$d".$e;
		$content = $this->getContents($url);
		return $content;
	}
	
	public function getCWClanBattles($accessToken, $mapID, $clanID, $fields=null, $language=null){
		if(!isset($mapID, $clanID)) return false;
		$a = $this->paramApplicationID();
		$b = $this->paramMapID($mapID);
		$c = $this->paramClanID($clanID);
		$d = $this->paramAccessToken($accessToken);
		$e = !isset($language) ? "" : "&".$this->paramLanguage($language);
		$f = !isset($fields) ? "" : "&".$this->paramFields($fields);
		$url = $this->urlClanWarsClanBattles."?$a&$b&$c&$d".$e.$f;
		$content = $this->getContents($url);
		return $content;
	}
	
}
