<?php
/**
* basic event map object
* @param int $eventID
* @param int $mapID
* @param int $order
*/
class EventMapObject{
//	private $eventID = null;
	private $mapID = null;
//    private $name = null;
    private $modeID = null;
	private $order = null;

    function __construct($data=null){
        if(is_array($data)) $this->generate($data);
    }

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["mapID"])) $this->mapID = $data["mapID"];
        if(isset($data["modeID"])) $this->modeID = $data["modeID"];
        if(isset($data["order"])) $this->order = $data["order"];
//        if(isset($data["eventID"])) $this->eventID = $data["eventID"];
//        if(isset($data["name"])) $this->name = $data["name"];
    }

    // getters and setters =====================================================

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
     * @return null
     */
    public function getMapID()
    {
        return $this->mapID;
    }

    /**
     * @param null $mapID
     */
    public function setMapID($mapID)
    {
        $this->mapID = $mapID;
    }

    /**
     * @return null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param null $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return null
     */
//    public function getName()
//    {
//        return $this->name;
//    }

    /**
     * @param null $name
     */
//    public function setName($name)
//    {
//        $this->name = $name;
//    }

    /**
     * @return null
     */
    public function getModeID()
    {
        return $this->modeID;
    }

    /**
     * @param null $modeID
     */
    public function setModeID($modeID)
    {
        $this->modeID = $modeID;
    }

}