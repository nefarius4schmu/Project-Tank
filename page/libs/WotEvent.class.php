<?php
require_once("objects/EventObject.class.php");
require_once("objects/EventMapObject.class.php");
require_once("objects/EventPriceObject.class.php");
require_once("objects/EventUserObject.class.php");

/**
* WoT Event Handler Class
* 
* @author Steffen Lange
*/
class WotEvent extends EventObject{

	function __construct($event=null){
        if(isset($event)) $this->generate($event);
    }


}