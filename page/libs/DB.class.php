<?php 
_def("db");
/**
* Project Tank Webpage
* basic database connection handler
* @author Steffen Lange
*/
class DB {
    private static $link = null ;

    public static function getLink() {
        if ( self :: $link ) {
            return self :: $link ;
        }

        $parse = parse_ini_file ( PATH_DB_INI_WOT , true ) ;
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
			self :: $link = new PDO ( $dsn, $user, $password, $options ) ;
		}catch(PDOException $e){
			echo "<pre class='error'>".$e->getMessage()."</pre>";
			return false;
		}
        
        foreach ( $attributes as $k => $v ) {
            self :: $link -> setAttribute ( constant ( "PDO::{$k}" )
                , constant ( "PDO::{$v}" ) ) ;
        }

        return self :: $link ;
    }
}