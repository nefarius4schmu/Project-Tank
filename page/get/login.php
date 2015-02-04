<?php
/**
* Project Tank Webpage
* getter for login session data
* @author Steffen Lange
*/
/**
* TODO
* 
* catch http://localhost/wot/login.php?&status=error&message=AUTH_CANCEL&code=401
* catch no clanID
*/
 
_lib("WotSession");
$out = WotSession::getLoginData();
