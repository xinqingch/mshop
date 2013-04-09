<?php
define( 'SYS', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

defined( 'ROOT' )||define( 'ROOT', dirname(__FILE__).'/..' );
defined( 'CONTROL_DIR' )||define( 'CONTROL_DIR', ROOT.'/Controller' );
defined( 'MODULE_DIR' )||define( 'MODULE_DIR', ROOT.'/Module' );
defined( 'VIEW_DIR' )||define( 'VIEW_DIR', ROOT.'/View' );
defined( 'LANG_DIR' )||define( 'LANG_DIR',		ROOT.'/Lang' );
defined( 'CACHE_DIR' )||define( 'CACHE_DIR',		ROOT.'/Cache' );
defined( 'SYS_MODULE' )||define( 'SYS_MODULE',	SYS.'/module' );
defined( 'DEBUG' )||define( 'DEBUG', TRUE );
defined( 'SMYCACHE' )||define( 'SMYCACHE', FALSE ); //smarty cache
defined( 'SMCOMPILE' )||define( 'SMCOMPILE', CACHE_DIR.'/compile' ); //smarty compile directory
defined( 'DB_DRIVER' )||define( 'DB_DRIVER', 'pdo' );
defined( 'DB_CHARSET' )||define( 'DB_CHARSET', 'utf8' );
defined( 'DB_USER' )||define( 'DB_USER', 'root' );
defined( 'DB_PASS' )||define( 'DB_PASS', '' );
defined( 'DB_PRE' )||define( 'DB_PRE', 'db_' );
defined( 'SESSION_ENGINE' )||define( 'SESSION_ENGINE',	'file' ); //file, apc, memcache
defined( 'SESSION_DIR' )||define( 'SESSION_DIR', CACHE_DIR . '/session' );
defined( 'MEMCACHE_HOST' )||define( 'MEMCACHE_HOST',	'127.0.0.1' );
defined( 'MEMCACHE_PORT' )||define( 'MEMCACHE_PORT',	'11211' );

if (!defined('COOKIE_DOMAIN')) {
	$domain	= $_SERVER['HTTP_HOST'];
	$strpos	= strpos( $domain, '.' );
	if ( $strpos !== false ) {
		$domain	= substr( $domain, $strpos );
	}
	define('COOKIE_DOMAIN',	$domain);
}

require 'smarty/Smarty.class.php';
require 'function.php';
require 'module/route.php';

//load Exception module
if ( file_exists(MODULE_DIR.'/exception.php') ) {
	require MODULE_DIR.'/exception.php';
}
else {
	require 'module/exception.php';
}

require 'module/cache.php';
require 'module/lang.php';
require 'module/connect.php';
require 'module/debug.php';
require 'module/controller.php';
require 'module/dbmodule.php';
require 'module/CHttpSession.php';
require 'module/CCache.php';


class YPi {

	//static
	static protected $YPi;
	static protected $modules = array();		//loaded modules
	static protected $modules_index = array();	//modules index
	
	//private
	private $page_screen_start;
	
	
	//public
	
	
	public static function & instance () {
		if ( is_null( self::$YPi ) ) {
			self::$YPi = new YPi();
		}
		
		return self::$YPi;
	}
	
	
	public function __construct() {

		if ( !file_exists( SESSION_DIR ) ) {
			makedir( SESSION_DIR );
		}
		
		CHttpSession::engine( SESSION_ENGINE );
		
		ob_start();
		$this->page_screen_start = microtime(true);
	}

	
	public function __destruct() {
		$page_screen_end = microtime(true);
		
		if ( DEBUG ) {
			$code		= ob_get_contents();
			YPiDebug::showDebugWindow( $page_screen_end - $this->page_screen_start );
			$message	= ob_get_contents();
			ob_clean();
			$message	= substr( $message, strlen($code) );
			echo str_replace( '<!-- debug message -->', $message, $code );		
		}

		ob_end_flush();
	}
	
	
	public function start () {
		try {
			$route = route::instance();
			$route->render();
		}
		catch ( Exception $Error ) {
			exit( $Error->__toString() );
		}
	}

	
	//old start action
	public static function run () {	
		$YPi = self::instance();
		$YPi->start();
	}

	//old import method
	public static function imports() {
		$modules = func_get_args();
		if ($modules) {
			foreach ($modules as $item) {
				
				$item = str_replace('.', '/', $item);
				$file = SYS.'/module/'.$item.'.php';

				if (file_exists($file)) {
					if (DEBUG)
						YPiDebug::import_module($file);

					require_once $file;
				}
			}
		}
	}


	public static function interfaces() {
		$interface = func_get_args();
		if ($interface) {
			foreach ($interface as $item) {
				$item = str_replace('.', '/', $item);
				$file = SYS.'/interface/'.$item.'.php';
				if (file_exists($file)) {
					require $file;
				}
			}
		}
	}

	static private function require_file ( $moduleFile ) {
		$fileName	= SYS_MODULE . '/' . $moduleFile;
		if ( file_exists( $fileName ) ) {
			require_once $fileName;
			return true;
		}
		else {
			$fileName = MODULE_DIR . '/' . $moduleFile;
			if ( file_exists( $fileName ) ) {
				require_once $fileName;
				return true;
			}
		}
		return false;
	}


	static public function loadModule($moduleName) {
		
		if ( strpos( $moduleName, '.' ) !== -1 ) {
			$moduleName	= str_replace('.', '/', $moduleName);
		}
		$module_path	= $moduleName . '.php';
		$module_name	= str_replace( '/', '_', $moduleName ) . '_module';
		
		$index	= array_search( $moduleName, self::$modules_index );
		
		if ( $index !== false ) {
			return self::$modules[ $index ];
		}
		
		$result	= self::require_file( $module_path );		
		if ( $result ) {				
			if ( !class_exists( $module_name ) ) {
				$module_name = basename( $moduleName );
				$module_name .= '_module';
			}
			
			$module	= @call_user_func( $module_name . '::instance' );
			//Compatibility mode
			if ( !$module ) {
				$module = new $module_name;
			}
			
			array_push( self::$modules_index, $moduleName );
			array_push( self::$modules, $module );
				
			return $module;
		}
		return false;
	}
	
	
}