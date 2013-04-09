<?php
/**
 * CCacheMemcache类库
 * YPi框架缓存引擎, 使用memcache引擎进行数据缓存
 * @author yagas@sohu.com
 *
 */
class CCacheMemcache {
	
	protected static $memcache;
	
	
	public function __construct() {
		if ( !self::$memcache ) {
			self::$memcache = new Memcache;
			self::$memcache->connect( MEMCACHE_HOST, MEMCACHE_PORT );
		}
	}
	
	
	public function exists( $key ) {
		return self::$memcache->get( $key );
	}
	
	
	public function fetch( $key ) {
		return self::$memcache->get( $key );
	}
	
	
	public function add( $key, $value, $ttl ) {
		return self::$memcache->get( $key, $value, $ttl );
	}
	
	
	public function delete( $key ) {
		return self::$memcache->delete( $key );
	}
}