<?php
/**
* Project Tank Webpage
* basic handler for debug output
* @author Steffen Lange
*/
class Debug{
	
	const STYLE_DEFAULT = "background-color:#f9f9f9;border:1px solid #d1d1d1;";
	const STYLE_ERROR = "background-color:#ffe7e7;border:1px solid #FC8787;";
	const STYLE_INFO = "background-color:#e7f3ff;border:1px solid #87BAFC;";
	const STYLE_SUCCESS = "background-color:#dfffdf;border:1px solid #68CF68;";
	
	const CLASS_ERROR = "error";
	const CLASS_INFO = "info";
	const CLASS_SUCCESS = "success";
	
	private static function print_r($v, $class=null, $style=null){
        $style = isset($style) ? $style : self::STYLE_DEFAULT;
		$data = print_r($v, true);
		$style = "style='$style'";
		return "<pre class='$class' $style>$data</pre>";
	}
	
	public static function r($v, $return=false){
		$msg = self::print_r($v, self::CLASS_INFO, self::STYLE_INFO);
		if($return) return $msg;
		else echo $msg; 
	}
	
	public static function i($v, $return=false){
		$msg = self::print_r($v, self::CLASS_INFO, self::STYLE_INFO);
		if($return) return $msg;
		else echo $msg; 
	}
	
	public static function v($v){
		$style = "style='".self::STYLE_INFO."'";
		echo "<pre class='info' $style>";
		var_dump($v);
		echo "</pre>";
	}
	
	public static function e($v, $return=false){
		$msg = self::print_r($v, self::CLASS_ERROR, self::STYLE_ERROR);
		if($return) return $msg;
		else echo $msg;
	}
	
	public static function s($v, $return=false){
		$msg = self::print_r($v, self::CLASS_SUCCESS, self::STYLE_SUCCESS);
		if($return) return $msg;
		else echo $msg;
	}

	public static function h($v, $return=false){
		$msg = self::print_r(htmlentities($v));
		if($return) return $msg;
		else echo $msg;
	}

	public function exitOnError($errorCode, $appendix=null){
		$msg = "ERROR: ";
		switch($errorCode){
			case 100: $msg .= "login check failed"; break;
			case 99: $msg .= "missing parameter"; break;
			case 80: $msg .= "failed to get player info"; break;
			case 70: $msg .= "failed to connect to database"; break;
			default: $msg = "Undefined Error ".$errorCode;
		}
		if(isset($appendix)) $msg .= "\n$appendix";
		echo $msg;
		exit();
	}
}