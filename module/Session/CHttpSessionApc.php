<?php

/**
 * CApcHttpSession
 * session引擎, 以APC缓存的方式对session进行存储
 * SESSION_ENGINE 设置值为apc，以启用APC方式对session进行存储
 */


class CHttpSessionApc {

	public function add( $key, $data, $cg_maxlifetime ) {
		apc_store( $key, $data, $cg_maxlifetime );
		return true;
	}

	public function fetch( $key ) {
		if ( !apc_exists( $key ) ) {
			apc_store( $key, '' );
			return true;
		}
		return apc_fetch( $key );
	}

	public function delete( $key ) {
		if ( apc_exists( $key ) ) {
			apc_delete( $key );
		}
		return true;
	}
};