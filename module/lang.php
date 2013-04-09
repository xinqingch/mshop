<?php

/**
 * Lang
 * 语言包文件库
 */

class Lang
{
	protected static $LangStorage	= array();
	
	public static function text ( $key, $replace=null, $lang = 'default' ) {
		$lang_file	= is_null( $lang )? 'default' : $lang;
		$lang_file	= LANG_DIR . "/${lang_file}.lang.php";
		$key		= trim( $key );
		$replace	= is_null( $replace )? $replace : trim($replace);
		
		if ( !$key ) return '';
		if ( !is_null($replace) && !$replace ) return $key;
		if ( !file_exists( $lang_file ) ) return $key;
		
		if ( !array_key_exists( $lang, self::$LangStorage ) ) {
			self::$LangStorage[ $lang ] = require( $lang_file );
		}
		
		return self::trans( self::$LangStorage[$lang], $key, $replace );
	}


	private static function trans( $storage, $key, $replace ) {
		if ( is_null( $replace ) ) {
			return (array_key_exists( $key, $storage )? $storage[$key] : $key );
		}
		else {
			if ( !array_key_exists( $key, $storage ) ) {
				return sprintf( $key, $replace );
			}
			else {
				return sprintf( $storage[$key], $replace );
			}
		}
	}
}
?>