<?php

/**
 * CHttpSession
 * http session 数据存储引擎
 */

class CHttpSession {

	private static $engine;
	private static $gc_maxlifetime;

	public static function engine( $enginer ) {
		$enginer		= ucfirst( $enginer );
		$engineInstance = "CHttpSession{$enginer}";
		$filename		= SYS_MODULE . '/Session/' . $engineInstance . '.php';

		if ( !file_exists( $filename )) {
			throw new Exception( "Fatal: not found {$filename} file" );
		}
		require( $filename );

		if ( !class_exists( $engineInstance ) ) {
			throw new Exception( "Fatal: not found {$engineInstance} object" );
		}

		$handler	= new CHttpSession( new $engineInstance );

		ini_set( "session.save_handler", "user" );
		ini_set( 'apc.ttl', 3600 );
		ini_set( 'apc.user_ttl', 1200 );
		ini_set( 'apc.gc_ttl', 3600 );

		session_set_save_handler(
			array($handler, 'open'),
			array($handler, 'close'),
			array($handler, 'read'),
			array($handler, 'write'),
			array($handler, 'destroy'),
			array($handler, 'gc')
		);
		
		if ( isset( $_COOKIE['PHPSESSID'] ) ) {
			session_start( $_COOKIE['PHPSESSID'] );
		}
		else {
			session_start( );
			setcookie( 'PHPSESSID', session_id(), null, '/', COOKIE_DOMAIN );
		}
	}

	public function __construct( & $engine ) {
		self::$engine = $engine;
		self::$gc_maxlifetime = ini_get( 'session.gc_maxlifetime' );
	}

	public function read( $id ) {
		return self::$engine->fetch( 'session/'.$id );
	}

	public function write ( $id , $data ) {
		return self::$engine->add( 'session/'.$id, $data, self::$gc_maxlifetime );
	}

	public function close ( ) {
		return true;
	}


	public function destroy ( $id ) {
		return self::$engine->delete( 'session/'.$id );
	}

	public function __destruct ( ) {
		session_write_close();
	}


	public function gc ( $maxlifetime ) {
		return true;
	}


	public function open ( $save_path , $session_name ) {
		return true;
	}
};