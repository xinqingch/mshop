<?php

/**
 * require file.
 * example:
 * import( 'core.data.member' );
 */
function import ($FilePath) {
	$FilePath = str_replace('.', '/', $FilePath) . '.php';
	
	if ( file_exists( MODULE_DIR . "/{$FilePath}" ) ) {
		require_once MODULE_DIR . "/{$FilePath}";
	}
	elseif( file_exists( SYS . "/module/{$FilePath}" ) ) {
		require_once SYS . "/module/{$FilePath}";
	}
	else {
		return false;
		throw new YPiException( "not found file:'{$FilePath}.php'" );
	}
}


//Check the POST submit mode
function isPost () {
	return (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')? TRUE : FALSE;
}

//Check the AJAX submit mode
function isAjax () {
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
		if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){
			return true;
		}
	}
	return false;
}


//Create a directory. Support the creation of multi-level directory.
function makedir ( $path, $permissions = 0777 ) {
	
	$result = array();
	while( $path != dirname( $path) ) {
		array_push( $result, $path );
		$path = dirname( $path );
	}
	sort( $result );

	foreach( $result as $directory ) {
		if ( !file_exists( $directory ) ) mkdir( $directory, $permissions );
		if ( !file_exists( $directory ) ) return false;
	}
	return true;
}


function def (&$value, $default='') {
	if ( !isset($value) || is_null($value) ) {
		return $default;
	}
	else {
		$value;
	}
}


function dump() {
	$params = func_get_args();
	$output = array();
	
	foreach($params as $item) {
		array_push($output, htmlspecialchars(print_r($item, true)));
	}
	echo '<pre>', implode("\n", $output), '</pre>';
}


function trims( $variable ) {
	if(is_array($variable)) {
		return array_map( "trims", $variable );
	}
	elseif ( is_null( $variable ) ) {
		return null;
	}
	else {
		return trim( $variable );
	}
}


//View rendering controller
function Render( $controller, $action=null ) {
	$route = route::instance( $controller, $action );
	$route->render();
}


function & LoadModule ( $module ) {
	$result	= YPi::loadModule( $module );
	return $result;
}


function _T( $string, $dict='default' ) {
	$word	= Lang::text( $string, $dict );
	return $word;
}