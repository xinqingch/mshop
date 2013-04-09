<?php
class driver_daesina {
	
	protected $settings;
	protected static $instance;
	protected static $resource;


	public static function & instance( $settings, $isNew=null ) {
		$ClassName = get_class();
		if ( !is_array($settings) ) {
			throw new Exception( '"invalid database connection params' );
		}

		if ( !self::$instance ) {
			self::$instance = new $ClassName( $settings );
		}
		return self::$instance;
	}

	
	public function __construct ( $settings ) {
		try {
			self::$resource	= new SaeMysql();
			if ( !self::$resource ) {
				throw new Exception( 'Can not connect to saeapp database' );
			}
		}
		catch ( Exception $Error ) {
			exit( $Error->getMessage() );
		}
	}
	

	public function __destruct () {}
	
	
	final public function query ( $SQL, $mode='FetchAll' ) {
		$start	= microtime(true);
		$mode	= strtolower( $mode );

		if ( self::$resource ) {
			switch ( $mode ) {				
				case 'fetch':
				case 'row':
					$result = self::$resource->getLine( $SQL );
					break;
				
				case 'column':
					$result = self::$resource->getVar( $SQL );
					break;
				
				default:
					$result = self::$resource->getData( $SQL );
					break;
			}

			if (DEBUG) {
				$end = microtime(true);					
				YPiDebug::push_sql( $SQL, $start, $end );
			}
			return $result;
		}
		else {
			$error = 'Can not connect to saeapp database';
			YPiDebug::push_sql_error( $SQL, $error );
			throw new Exception( "saeapp:{$error}" );
		}
	}
	
	
	final public function exec ( $SQL ) {
		$start	= microtime(true);
		
		if ( self::$resource ) {
			$result = self::$resource->runSql( $SQL );
			if (DEBUG) {
				$end = microtime(true);					
				YPiDebug::push_sql( $SQL, $start, $end );
			}

			if ( !$result ) {
				$erron	= self::$resource->errno();
				$errmsg	= self::$resource->errmsg();
				throw new Exception( "{$errno}:{$errmsg}" );
			}
			return $result;
		}
		else {
			$error = 'Can not connect to saeapp database';
			YPiDebug::push_sql_error( $SQL, $error );
			throw new Exception( "saeapp:{$error}" );
		}
	}
	
	
	final public function lastInsertId () {
		return self::$resource->lastId();
	}

	
	//此模式不支持存储过程
	final public function procedure( $procedure, $mode=null ) {
		throw new Exception( "procedure:saeapp driver not support procedure" );
		return false;
	}
}
