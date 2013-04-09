<?php
class Connect {
	
	protected static $database;
	protected static $resource;
	protected $found;
	
// 	deprecate use variable
//	即将作废不再使用的内容
	static protected $db;
	
	
	
	final public static function & instance () {
		$current	= get_called_class();
		
		if ( !isset( self::$resource[ $current ] ) ) {
			self::$resource[ $current ] = new $current;
		}

		return self::$resource[ $current ];
	}
	
	
	final public function __construct() {
		
		if ( method_exists( $this, 'connection' ) ) {
			$this->connection();
		}
		else {
			$driver			= 'driver_' . DB_DRIVER;
			$driver_file	= SYS_MODULE . '/' . $driver . '.php';
			$this->found	= array( 'table'=>null, 'where'=>null, 'order'=>'', 'limit'=>null, 'field'=>null );
			
			if ( !class_exists( $driver ) ) {
				if ( !file_exists( $driver_file ) ) {
					throw new Exception( "Not found driver by '{$driver_file}'" );
				}
				
				require_once( $driver_file );
				if ( !class_exists( $driver ) ) {
					throw new Exception( "Not found driver by '{$driver}'" );
				}
			}
			
			if ( defined( DB_SETTING ) ) {
				$settings = unserialize( DB_SETTING );
			}
			else {
				$settings = array( array( 'data'=>'mysql', 'host'=>DB_HOST, 'username'=>DB_USER, 'password'=>DB_PASS, 'charset'=>DB_CHARSET, 'dbname'=>DB_NAME) );
			}
			
			self::$db = self::$database	= call_user_func_array( array( $driver, 'instance'),  array( $settings ) );			
			
			if ( !self::$database ) {
				throw new Exception( "Not connect to database" );
			}
			
		}
		
		$this->SetTableFullName();
		if (method_exists($this, 'init')) {
			$this->init();
		}
	}
	
	
	final public function reset () {
		self::$database = null;
	}
	
	
	private function SetTableFullName () {
		$result = get_object_vars( $this );
		if ( $result ) {
			
			$vars = array_keys( $result );
			
			foreach ( $vars as $var ) {
				if ( preg_match('/^table/', $var) ) {
					$this->$var = DB_PRE . $this->$var;
				}
			}
		}
	}
	
	
	final public function query( $SQL, $fetch_type = null ) {
		return self::$database->query( $SQL, $fetch_type );
	}
	
	
	public function exec( $SQL ) {
		return self::$database->exec( $SQL );
	}
}