<?php
/**
 * CCache类库
 * YPi框架缓存引擎
 * @author yagas@sohu.com
 *
 */
class CCache {
	
	protected $engine;
	
	public static function instance ( $engine ) {
		
		$enginer		= ucfirst( $engine );
		$engineName		= "CCache{$enginer}";
		$filename		= SYS_MODULE . '/Cache/' . $engineName . '.php';
		
		if ( !file_exists( $filename ))
			throw new Exception( "Fatal: not found {$filename} file" );
		
		require_once( $filename );
		
		if ( !class_exists( $engineName ) )
			throw new Exception( "Fatal: not found {$engineName} object" );
		
		$result = new CCache( new $engineName );
		return $result;
	}
	
	public function __construct( & $engine ) {
		$this->engine = $engine;
	}
	
	public function exists ( $key ) {
		return $this->engine->exists( $key );
	}
	
	public function add ( $key, $value, $time_out=0 ) {
		return $this->engine->add( $key, $value, $time_out );
	}
	
	public function fetch ( $key ) {
		return $this->engine->fetch( $key );
	}
	
	public function delete ( $key ) {
		return $this->engine->delete( $key );
	}
}