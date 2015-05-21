<?php
/**
* basic clan emblems object
* @param string $small     ->x24
* @param string $medium    ->x32
* @param string $large     ->x64
* @param string $xlarge    ->x128
* @param string $xxlarge   ->x256
* 
* @author Steffen Lange
* @depricated keys small, 24x24, etc
*/
class ClanEmblemsObject{
    const KEY_SMALL = "x24";
    const KEY_MEDIUM = "x32";
    const KEY_LARGE_TANK = "x64";
    const KEY_XLARGE = "x128";
    const KEY_XXLARGE = "x256";
    const KEY_TANK = "x256";
    const INDICATOR_TANK = "tank";

	public $small = null;
	public $medium = null;
	public $large = null;
	public $xlarge = null;
	public $xxlarge = null;
    public $tank = null;
}