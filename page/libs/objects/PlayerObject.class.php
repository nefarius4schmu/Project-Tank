<?php
/**
* basic player object
* @param int $id
* @param string $name
* @param string $lang
* @param int $lastUpdate
* @param RatingObject $rating
* @param StatisticObject $statistic
* @param PlayerClanObject $clan
* 
* @author Steffen Lange
*/
class PlayerObject{
	public $id = null;
	public $name = null;
	public $lang = null;
	
	public $lastUpdate = null;
	
	public $rating = null;
	public $statistic = null;
	public $clan = null;


    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["id"])) $this->id = $data["id"];
        if(isset($data["name"])) $this->name = $data["name"];
        if(isset($data["lang"])) $this->lang = $data["lang"];
        if(isset($data["lastUpdate"])) $this->lastUpdate = $data["lastUpdate"];
        if(isset($data["rating"])) $this->rating = $data["rating"];
        if(isset($data["statistic"])) $this->statistic = $data["statistic"];
        if(isset($data["clan"])) $this->clan = $data["clan"];
    }

    // getters and setters =====================================================

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param null $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return null
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param null $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param null $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return null
     */
    public function getStatistic()
    {
        return $this->statistic;
    }

    /**
     * @param null $statistic
     */
    public function setStatistic($statistic)
    {
        $this->statistic = $statistic;
    }

    /**
     * @return null
     */
    public function getClan()
    {
        return $this->clan;
    }

    /**
     * @param null $clan
     */
    public function setClan($clan)
    {
        $this->clan = $clan;
    }

}