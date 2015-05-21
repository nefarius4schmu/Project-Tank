<?php
/**
* Project Tank Webpage
* class for basic calculations
* @author Steffen Lange
*/
class Calc{
	
	public static function hexToRgb($hexStr){
		$hex = strpos($hexStr, "#") !== false ? substr($hexStr, 1) : $hexStr;
		$dec = hexdec($hex);
		$rgb[] = ($dec & 0xFF0000) >> 16;
		$rgb[] = ($dec & 0x00FF00) >> 8;
		$rgb[] = ($dec & 0x0000FF);
		return $rgb;
	}
	
	public static function cssHexToRgba($hexStr, $alpha=1){
		list($r, $g, $b) = self::hexToRgb($hexStr);
		return self::rgbaToCSS($r,$g,$b,$alpha);
	}
	
	public static function rgbaToCSS($r, $g, $b, $alpha=1){
		return "rgba($r,$g,$b,$alpha)";
	}

    public static function getWeeks($weeks, $asTimestamp=false){
        $t = $weeks*self::getDays(7);
        return $asTimestamp ? time() + $t : $t;
    }

    public static function getDays($days, $asTimestamp=false){
        $t = $days*24*60*60;
        return $asTimestamp ? time() + $t : $t;
    }
}