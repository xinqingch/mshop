<?php

/**
 * CFileHttpSession
 * session引擎, 以文件的方式对session进行存储, YPi框架默认session存储引擎
 * SESSION_DIR 设置session文件存储路径
 */


class CHttpSessionFile {

	public function add( $key, $data, $cg_maxlifetime ) {
		$filepath = substr( $key, 7 );
		file_put_contents( SESSION_DIR.$filepath, $data );
		return true;
	}

	public function fetch( $key ) {
		$filepath = substr( $key, 7 );
		if ( !file_exists(SESSION_DIR.$filepath) ) {
			file_put_contents( SESSION_DIR.$filepath, '' );
			return true;
		}
		return file_get_contents( SESSION_DIR.$filepath );
	}

	public function delete( $key ) {
		$filepath = substr( $key, 7 );
		if ( file_exists( SESSION_DIR.$filepath ) ) {
			unlink( SESSION_DIR.$filepath );
		}
		return true;
	}
};