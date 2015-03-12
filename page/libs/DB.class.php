<?php 
/**
* Project Tank Webpage
* basic database connection handler
* @author Steffen Lange
*/
_def("db");
class DB {
//	const DB_WOT = PATH_DB_INI_WOT;
	const DB_PTWG = PATH_DB_INI_PTWG;
	
//    private static $link = null ;
    private static $links = [] ;

	private static function is($db){return isset(self::$links[$db]) && self::$links[$db];}
	private static function get($db){return self::$links[$db];}
	private static function set($db, $link){self::$links[$db] = $link;}

    public static function getLink($db=self::DB_PTWG, $reconnect=false) {
        if (!$reconnect && self::is($db)) {
            return self::get($db);
        }

        $parse = parse_ini_file ( $db , true ) ;
		if($parse === false) return false;
		
        $driver = $parse [ "db_driver" ] ;
        $dsn = "${driver}:" ;
        $user = $parse [ "db_user" ] ;
        $password = $parse [ "db_password" ] ;
        $options = $parse [ "db_options" ] ;
        $attributes = $parse [ "db_attributes" ] ;

        foreach ( $parse [ "dsn" ] as $k => $v ) {
            $dsn .= "${k}=${v};" ;
        }
		
		try{
			$link = new PDO ( $dsn, $user, $password, $options ) ;
		}catch(PDOException $e){
			echo "<pre class='error'>".$e->getMessage()."</pre>";
			return false;
		}
        
        foreach ( $attributes as $k => $v ) {
            $link -> setAttribute ( constant ( "PDO::{$k}" )
                , constant ( "PDO::{$v}" ) ) ;
        }

		self::set($db, $link);
        return $link ;
    }
}