<?php
/**
* basic event user object
* @param PlayerObject $player
* @param ClanObject $clan
 * @param bool $accepted
 * @param timestamp $joined
*/
class EventUserObject{
	private $player = null;
    private $clan = null;
	private $accepted = false;
    private $joined = null;

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["player"])) $this->player = $data["player"];
        if(isset($data["clan"])) $this->clan = $data["clan"];
        if(isset($data["accepted"])) $this->accepted = $data["accepted"];
        if(isset($data["joined"])) $this->joined = $data["joined"];
    }

    /**
     * @return null
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param null $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
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