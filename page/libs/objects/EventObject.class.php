<?php
/**
* basic event object
* @param int $id
* @param int $userID
* @param string $created
* @param string $updated
* @param string $start
* @param string $end
* @param int $views
* @param int $maxUsers
* @param string $type
* @param string $briefingID
* @param string $uid
* @param int $clanID
* @param bool $password
* @param bool $hidden
* @param bool $public
* @param bool $deleted
* @param EventMapObject[] $maps
* @param EventPriceObject[] $prices
* @param PlayerObject[] $users
* @param string $lang
* @param string $title
* @param string $description
* @param string $summary
*/
class EventObject{
    private $id = null;
    private $userID = null;

    private $created = 0;
    private $updated = 0;
    private $start = 0;
    private $end = 0;

    private $views = 0;
    private $maxUsers = 0;

    private $typeID = null;
    private $uid = null;
    private $briefingID = null;
    private $clanID = null;
    private $password = false;
    private $hidden = false;
    private $public = false;
    private $deleted = false;

    private $prices = [];
    private $maps = [];
    private $users = [];
    private $usersCount = 0;

    private $lang = null;
    private $title = null;
    private $summary = null;
    private $text = null;

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(empty($data)) return;
        if(isset($data["eventID"])) $this->id = $data["eventID"];
        if(isset($data["userID"])) $this->userID = $data["userID"];
        if(isset($data["typeID"])) $this->typeID = $data["typeID"];
        if(isset($data["created"])) $this->created = $data["created"];
        if(isset($data["updated"])) $this->updated = $data["updated"];
        if(isset($data["start"])) $this->start = $data["start"];
        if(isset($data["end"])) $this->end = $data["end"];
        if(isset($data["briefingID"])) $this->briefingID = $data["briefingID"];
        if(isset($data["clanID"])) $this->clanID = $data["clanID"];
        if(isset($data["password"])) $this->password = $data["password"] == "1";
        if(isset($data["hidden"])) $this->hidden = $data["hidden"] == "1";
        if(isset($data["public"])) $this->public = $data["public"] == "1";
        if(isset($data["lang"])) $this->lang = $data["lang"];
        if(isset($data["title"])) $this->title = $data["title"];
        if(isset($data["text"])) $this->text = $data["text"];
        if(isset($data["views"])) $this->views = $data["views"];
        if(isset($data["maxUsers"])) $this->maxUsers = $data["maxUsers"];
        if(isset($data["summary"])) $this->summary = $data["summary"];
        if(isset($data["uid"])) $this->uid = $data["uid"];

        if(isset($data["prices"])) $this->prices = $data["prices"];
        if(isset($data["maps"])) $this->maps = $data["maps"];
        if(isset($data["users"])) $this->users = $data["users"];

        $this->updateUsersCount();
    }

    private function updateUsersCount(){
        $users = $this->getUsers();
        if(!empty($users)) $this->usersCount = count($users);
    }

    // getters and setters =====================================================

    /**
     * @return int
     */
    public function getID()
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
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param PlayerObject $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
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
    public function getTypeID()
    {
        return $this->typeID;
    }

    /**
     * @param string $typeID
     */
    public function setTypeID($typeID)
    {
        $this->typeID = $typeID;
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
     * @param EventPriceObject $item
     */
    public function addPrice($item)
    {
        $this->prices[] = $item;
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
     * @param EventMapObject $item
     */
    public function addMaps($item)
    {
        $this->maps[] = $item;
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
        $this->updateUsersCount();
    }

    /**
     * @param EventUserObject $item
     */
    public function addUser($item)
    {
        $this->users[] = $item;
        $this->updateUsersCount();
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return int
     */
    public function getMaxUsers()
    {
        return $this->maxUsers;
    }

    /**
     * @param int $maxUsers
     */
    public function setMaxUsers($maxUsers)
    {
        $this->maxUsers = $maxUsers;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return null
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param null $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return null
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param null $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

}