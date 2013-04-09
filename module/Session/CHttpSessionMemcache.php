<?php

/**
 * CMemcacheHttpSession
 * session引擎, 以memcache缓存的方式对session进行存储 * 
 * SESSION_ENGINE	设置值为memcache，以启用memcache方式对session进行存储
 * MEMCACHE_HOST	设置memcache服务器地址
 * MEMCACHE_PORT	设置memcache服务器访问端口号
 */


class CHttpSessionMemcache {

	private static $memcache;

	public function __constrct( $config ) {
		self::$memcache = new Memcache;
		self::$memcache->connect( MEMCACHE_HOST, MEMCACHE_PORT );
	}

	public function __destroy() {
		self::$memcache->close();
	}

	public function add( $key, $data, $cg_maxlifetime ) {
		return self::$memcache->add( $key, $data, $cg_maxlifetime );
	}

	public function fetch( $key ) {
		return self::$memcache->get( $key );
	}

	public function delete( $key ) {
		return self::$memcache->delete( $key );
	}
};