<?php
/**
* TODO
* 
* catch http://localhost/wot/login.php?&status=error&message=AUTH_CANCEL&code=401
* catch no clanID
*/
 
_lib("WotSession");
$out = WotSession::getLoginData();
