<?php
/**
* basic wot map object
* @param int $mapID
* @param string $name
* @param string $name_i18n
* @param string $description_i18n
*/
class WotMapObject{
	private $mapID = null;
	private $name = null;
	private $lang = null;
	private $name_i18n = null;
	private $description_i18n = null;

    function __construct($data=null){
        if(is_array($data)) $this->generate($data);
    }

    // public functions ========================================================

    /**
     * @param array $data
     */
    public function generate($data){
        if(isset($data["mapID"])) $this->mapID = $data["mapID"];
        if(isset($data["name"])) $this->name = $data["name"];
        if(isset($data["name_i18n"])) $this->name_i18n = $data["name_i18n"];
        if(isset($data["description_i18n"])) $this->description_i18n = $data["description_i18n"];
    }

    // getters and setters =====================================================

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
    public function getNameI18n()
    {
        return $this->name_i18n;
    }

    /**
     * @param null $name_i18n
     */
    public function setNameI18n($name_i18n)
    {
        $this->name_i18n = $name_i18n;
    }

    /**
     * @return null
     */
    public function getDescriptionI18n()
    {
        return $this->description_i18n;
    }

    /**
     * @param null $description_i18n
     */
    public function setDescriptionI18n($description_i18n)
    {
        $this->description_i18n = $description_i18n;
    }

}