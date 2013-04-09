<?php
/**
 * 代码调试模块
 */

class YPiDebug {
	const FORMAT = '[<strong>%s</strong>]&nbsp;&nbsp;%s';
	protected static $sql_storage		= array();
	protected static $module_storage	= array();
	protected static $import_storage	= array();
	protected static $error_storage		= array();
	

	public static function push_sql ( $SQL, $start, $end ) {
		$time	= sprintf( '%.6fs', $end-$start );
		$row	= sprintf( self::FORMAT, $time, $SQL );
		array_push( self::$sql_storage, $row );
	}

	public static function push_sql_error( $SQL, $Error ) {
		array_push( self::$error_storage, "<span class=\"blue\">{$SQL}</span> <span class=\"red\">{$Error}</span>" );
	}



	public static function load_module ( $module ) {
		if ( !in_array($module, self::$module_storage) ) {
			array_push(self::$module_storage, $module);
		}
	}


	public static function import_module ( $module ) {
		if ( !in_array($module, self::$import_storage) ) {
			array_push(self::$import_storage, str_replace('\\', '/', $module));
		}
	}


	public static function error ( $msg ) {
		if ( !in_array($msg, self::$error_storage) ) {
			array_push(self::$error_storage, $msg);
		}
	}
	
	
	public static function showDebugWindow ( $total_run_time ) {

		echo '<style type="text/css">#debug,#debug *{padding:0;margin:0} #debug .YPiDebug_space{height:5px;display:block;width:100%} #debug dd.item{padding-left:10px} #debug h4{font-size:16px} .red {color:#FF0000} .blue{color:#0000FF}</style>';
		echo '<div style="clear:both;background:#E6F1FD;border:1px solid #000;padding:10px" id="debug">';
		echo '<h4>YPi Debug Information</h4><div class="YPiDebug_space"></div>';
		echo '<dd><strong>Execution Time: </strong>', sprintf('%.6fs', $total_run_time),'<dd>';

		if ( self::$import_storage ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>Import Files:</strong></dd>';
			foreach ( self::$import_storage as $index => $item ) {
				echo '<dd class="item">',$item,"</dd>";
			}
		}

		if ( self::$module_storage ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>Loaded Module:</strong></dd>';
			foreach ( self::$module_storage as $index => $item ) {
				echo '<dd class="item">',$item,"</dd>";
			}
		}

		if ( $_SESSION ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>SESSION:</strong></dd><dd>';
			foreach ( $_SESSION as $_key => $_value ) {
				echo $_key,'=>"',$_value,'"; ';
			}
			echo '</dd>';
		}

		if ( $_POST ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>POST:</strong></dd><dd>';
			foreach ( $_POST as $_key => $_value ) {
				echo $_key,'=>"',$_value,'"; ';
			}
			echo '</dd>';
		}


		if ( $_GET ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>GET:</strong></dd><dd>';
			foreach ( $_GET as $_key => $_value ) {
				echo $_key,'=>"',$_value,'"; ';
			}
			echo '</dd>';
		}

		if ( self::$sql_storage ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>SQL Query:</strong></dd>';
			foreach ( self::$sql_storage as $index => $item ) {
				echo '<dd class="item">',$item,"</dd>";
			}
		}

		if ( self::$error_storage ) {
			echo '<div class="YPiDebug_space"></div><dd><strong>Error:</strong></dd>';
			foreach ( self::$error_storage as $index => $item ) {
				echo '<dd class="item">',$index,': ',$item,"</dd>";
			}
		}
		
		echo '</div>';
	}
}