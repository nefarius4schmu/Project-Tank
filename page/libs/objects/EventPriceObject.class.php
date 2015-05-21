<?php
/**
* basic event price object
* @param int eventID
* @param int priceID
* @param int rank_from
* @param int rank_to
* @param int gold
* @param string description
*/
class EventPriceObject{
	private $eventID = null;
	private $priceID = null;
	private $rank_from = null;
	private $rank_to = null;
	private $gold = null;
	private $description = null;

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["eventID"])) $this->eventID = $data["eventID"];
        if(isset($data["priceID"])) $this->priceID = $data["priceID"];
        if(isset($data["rank_from"])) $this->rank_from = $data["rank_from"];
        if(isset($data["rank_to"])) $this->rank_to = $data["rank_to"];
        if(isset($data["gold"])) $this->gold = $data["gold"];
        if(isset($data["description"])) $this->description = $data["description"];
    }

    // getters and setters =====================================================

    /**
     * @return null
     */
    public function getEventID()
    {
        return $this->eventID;
    }

    /**
     * @param null $eventID
     */
    public function setEventID($eventID)
    {
        $this->eventID = $eventID;
    }

    /**
     * @return null
     */
    public function getPriceID()
    {
        return $this->priceID;
    }

    /**
     * @param null $priceID
     */
    public function setPriceID($priceID)
    {
        $this->priceID = $priceID;
    }

    /**
     * @return null
     */
    public function getRankFrom()
    {
        return $this->rank_from;
    }

    /**
     * @param null $rank_from
     */
    public function setRankFrom($rank_from)
    {
        $this->rank_from = $rank_from;
    }

    /**
     * @return null
     */
    public function getRankTo()
    {
        return $this->rank_to;
    }

    /**
     * @param null $rank_to
     */
    public function setRankTo($rank_to)
    {
        $this->rank_to = $rank_to;
    }

    /**
     * @return null
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * @param null $gold
     */
    public function setGold($gold)
    {
        $this->gold = $gold;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}