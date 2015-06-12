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
        if(is_array($event)) $this->generate($event);
    }

    public function is(){return $this->getID() != null;}
    public function isClanEvent(){return $this->getClanID() != null;}

    public function parsePrices($rows){
        if(is_array($rows))
            foreach($rows as $row)
                $this->addPrice(new EventPriceObject($row));
    }

    public function parseMaps($rows){
        if(is_array($rows))
            foreach($rows as $row)
                $this->addMaps(new EventMapObject($row));
    }

    public function parseUsers($rows){
        if(is_array($rows))
            foreach($rows as $row)
                $this->addUser(new EventUserObject($row));
    }

    public function getTypeOptionValue($name){
        switch($name){
            case "clan": return $this->getClanID();
            case "hidden": return $this->isHidden();
            case "password": return $this->isPassword();
            case "briefing": return $this->getBriefingID();
        }
        return null;
    }

    public function isActiveTypeOptionValue($name){
        $value = $this->getTypeOptionValue($name);
        return is_bool($value) ? $value : $value !== null;
//        switch($name){
//            case "clan": return $this->getClanID() !== null;
//            case "hidden": return $this->isHidden();
//            case "password": return $this->isPassword();
//            case "briefing": return $this->getBriefingID() !== null;
//        }
//        return null;
    }



}