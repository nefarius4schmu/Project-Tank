<?php
/**
* basic event user object
* @param PlayerObject $player
* @param ClanObject $clan
* @param bool $accepted
* @param string $joined
*/
class EventUserObject{
	private $userID = null;
//    private $eventID = null;
	private $accepted = false;
    private $joined = null;

    function __construct($data=null){
        if(is_array($data)) $this->generate($data);
    }

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["userID"])) $this->userID = $data["userID"];
        if(isset($data["accepted"])) $this->accepted = $data["accepted"];
        if(isset($data["joined"])) $this->joined = $data["joined"];
//        if(isset($data["eventID"])) $this->eventID = $data["eventID"];
    }

    /**
     * @return null
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param null $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return null
     */
//    public function getEventID()
//    {
//        return $this->eventID;
//    }

    /**
     * @param null $eventID
     */
//    public function setEventID($eventID)
//    {
//        $this->eventID = $eventID;
//    }

    /**
     * @return boolean
     */
    public function isAccepted()
    {
        return $this->accepted;
    }

    /**
     * @param boolean $accepted
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    }

    /**
     * @return null
     */
    public function getJoined()
    {
        return $this->joined;
    }

    /**
     * @param null $joined
     */
    public function setJoined($joined)
    {
        $this->joined = $joined;
    }

    // getters and setters =====================================================

}