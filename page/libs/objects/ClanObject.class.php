<?php
/**
* basic clan object
* @param int $id clanID
* @param string $name
* @param string $tag
* @param string $color
* @param int $membersCount
* @param boolean $isDisbanned
* @param int $lastUpdate
* @param ClanMemberObject[] $members
* @param ClanEmblemsObject $emblems
* 
* @author Steffen Lange
*/
class ClanObject{
	public $id = null;
	public $name = null;
	public $tag = null;
	public $color = null;
	public $membersCount = null;
	
	public $isDisbanned = false;
	public $lastUpdate = null;
	
	public $members = [];
	public $emblems = null;


    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["id"])) $this->id = $data["id"];
        if(isset($data["name"])) $this->name = $data["name"];
        if(isset($data["tag"])) $this->tag = $data["tag"];
        if(isset($data["color"])) $this->color = $data["color"];
        if(isset($data["membersCount"])) $this->membersCount = $data["membersCount"];
        if(isset($data["isDisbanned"])) $this->isDisbanned = $data["isDisbanned"];
        if(isset($data["lastUpdate"])) $this->lastUpdate = $data["lastUpdate"];
        if(isset($data["members"])) $this->members = $data["members"];
        if(isset($data["clan"])) $this->clan = $data["clan"];
        if(isset($data["emblems"])) $this->emblems = $data["emblems"];
    }

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
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param null $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param null $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return null
     */
    public function getMembersCount()
    {
        return $this->membersCount;
    }

    /**
     * @param null $membersCount
     */
    public function setMembersCount($membersCount)
    {
        $this->membersCount = $membersCount;
    }

    /**
     * @return boolean
     */
    public function isIsDisbanned()
    {
        return $this->isDisbanned;
    }

    /**
     * @param boolean $isDisbanned
     */
    public function setIsDisbanned($isDisbanned)
    {
        $this->isDisbanned = $isDisbanned;
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
     * @return array
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param array $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * @return null
     */
    public function getEmblems()
    {
        return $this->emblems;
    }

    /**
     * @param null $emblems
     */
    public function setEmblems($emblems)
    {
        $this->emblems = $emblems;
    }

    // getters and setters =====================================================

}