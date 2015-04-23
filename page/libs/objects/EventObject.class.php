<?php
/**
* basic event object
* @param int $id
* @param EventUserObject $owner
* @param timestamp $created
* @param timestamp $updated
* @param timestamp $start
* @param timestamp $end
* @param string $type
* @param int $briefingID
* @param int $clanID
* @param bool $password
* @param bool $hidden
* @param bool $public
* @param EventMapObject[] $maps
* @param EventPriceObject[] $prices
* @param PlayerObject[] $users
* @param string $lang
* @param string $title
* @param string $description
*/
class EventObject{
    private $id = null;
    private $owner = null;

    private $created = 0;
    private $updated = 0;
    private $start = 0;
    private $end = 0;

    private $type = null;
    private $briefingID = null;
    private $clanID = null;
    private $password = false;
    private $hidden = false;
    private $public = false;
    private $prices = [];
    private $maps = [];
    private $users = [];

    private $lang = null;
    private $title = null;
    private $description = null;

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["id"])) $this->id = $data["id"];
        if(isset($data["owner"])) $this->owner = $data["owner"];
        if(isset($data["created"])) $this->created = $data["created"];
        if(isset($data["updated"])) $this->updated = $data["updated"];
        if(isset($data["start"])) $this->start = $data["start"];
        if(isset($data["end"])) $this->end = $data["end"];
        if(isset($data["type"])) $this->type = $data["type"];
        if(isset($data["briefingID"])) $this->briefingID = $data["briefingID"];
        if(isset($data["clanID"])) $this->clanID = $data["clanID"];
        if(isset($data["password"])) $this->password = $data["password"];
        if(isset($data["hidden"])) $this->hidden = $data["hidden"];
        if(isset($data["public"])) $this->public = $data["public"];
        if(isset($data["prices"])) $this->prices = $data["prices"];
        if(isset($data["maps"])) $this->maps = $data["maps"];
        if(isset($data["users"])) $this->users = $data["users"];
        if(isset($data["lang"])) $this->lang = $data["lang"];
        if(isset($data["title"])) $this->title = $data["title"];
        if(isset($data["description"])) $this->description = $data["description"];
    }

    // getters and setters =====================================================

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return PlayerObject
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param PlayerObject $userID
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param int $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param int $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getBriefingID()
    {
        return $this->briefingID;
    }

    /**
     * @param int $briefingID
     */
    public function setBriefingID($briefingID)
    {
        $this->briefingID = $briefingID;
    }

    /**
     * @return int
     */
    public function getClanID()
    {
        return $this->clanID;
    }

    /**
     * @param int $clanID
     */
    public function setClanID($clanID)
    {
        $this->clanID = $clanID;
    }

    /**
     * @return boolean
     */
    public function isPassword()
    {
        return $this->password;
    }

    /**
     * @param boolean $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return EventPriceObject[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param EventPriceObject[] $prices
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;
    }

    /**
     * @return EventMapObject[]
     */
    public function getMaps()
    {
        return $this->maps;
    }

    /**
     * @param EventMapObject[] $maps
     */
    public function setMaps($maps)
    {
        $this->maps = $maps;
    }

    /**
     * @return PlayerObject[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param PlayerObject[] $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return string[2]
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string[2] $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}