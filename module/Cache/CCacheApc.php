<?php
/**
 * CCacheApc类库
 * YPi框架缓存引擎, 使用APC引擎进行数据缓存
 * @author yagas@sohu.com
 *
 */
class CCacheApc {
	
	public function exists( $key ) {
		return apc_exists( $key );
	}
	
	public function add ( $key, $value, $ttl ) {
		return apc_store( $key, $value, $ttl );
	}
	
	public function fetch ( $key ) {
		return apc_fetch( $key );
	}
	
	public function delete ( $key ) {
		return apc_delete( $key );
	}
}