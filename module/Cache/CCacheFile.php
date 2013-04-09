<?php
class CCacheFile {
	
	public function exists ( $key ) {
		return file_exists( $this->filename( $key ) );
	}
	
	
	public function delete ( $key ) {
		$filename = $this->filename( $key );
		if ( file_exists( $filename ) )
			return unlink( $filename );
	}
	
	
	public function add ( $key, $value, $ttl ) {
		$filename	= $this->filename( $key );
		$variable	= serialize( $value );
		$fp = fopen ( $filename, 'w' );
		fwrite( $fp, "{$ttl}-".base64_encode($variable) );
		fclose( $fp );
		return true;
	}
	
	
	public function fetch ( $key ) {
		$filename	= $this->filename( $key );
		if ( file_exists( $filename ) ) {
			$fp = fopen( $filename, 'r' );
			$variable = '';
			while ( !feof( $fp ) ) {
				$variable .= fgets( $fp, 200 );
			}
			fclose( $fp );
			$sparan	= strpos($variable, '-');
			$ttl	= substr( $variable, 0, $sparan );
			$value	= substr( $variable, $sparan+1 );

			if ( $ttl > 0 && ( time()-filemtime($filename) ) > $ttl ) {
				@unlink( $filename );
				return false;
			}
			return unserialize( base64_decode( $value ) );
		}
		return false;
	}
	
	
	private function filename ( $key ) {
		$filename	= CACHE_DIR."/{$key}.cache.php";
		$dirname	= dirname( $filename );
		if ( !is_dir($dirname) ) {
			makedir( $dirname );
		}
		return $filename;
	}
}
?>