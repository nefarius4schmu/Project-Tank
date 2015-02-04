<?php
class Session{
	
	private static $started = false;
	
	public static function start(){
		if(!self::$started) self::$started = session_start();
	}
	
	public static function is(){
		return session_status() == PHP_SESSION_ACTIVE;
	}
	
	public static function set($key, $value){
		$_SESSION[$key] = $value;
	}
	
	public static function get($key){
		return self::has($key) ? $_SESSION[$key] : null;
	}
	
	public static function has($key){
		return isset($_SESSION[$key]);
	}
	
	public static function close(){
		session_write_close();
		self::$started = false;
	}
	
	public static function destroy(){
		session_destroy();
		self::$started = false;
	}
	
	public static function clear(){
		if(!self::is()) self::start();
		$_SESSION = [];
		// delete session cookies
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		self::destroy();
	}
	
}