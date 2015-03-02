<?php
/**
* basic statistic object
* @param int battles
* @param int wins
* @param int shots
* @param int hits
* @param int damage
* @param int winRatePerBattle
* @param int avgHitRatePerBattle
* @param int avgDamagePerBattle
* @param string winRateClass
* 
* @author Steffen Lange
*/
class StatisticObject{
	public $battles = 0;
	public $wins = 0;
	public $shots = 0;
	public $hits = 0;
	public $damage = 0;
	public $winRatePerBattle = 0;
	public $avgHitRatePerBattle = 0;
	public $avgDamagePerBattle = 0;
	
	public $winRateClass = null;
	
	public $lastUpdate = null;
}