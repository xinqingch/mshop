<?php
class Cache {

	/**
	 * setBinCache($filename, $data)
	 * 缓存二进制内容
	 * @param string $filename 缓存文件名称
	 * @param bin $data
	 */
	static public function setBinCache($filename, $data) {
		$path      = dirname(str_replace('.', '/', $filename));
		if (!file_exists(CACHE_DIR.'/'.$path))
			makedir(CACHE_DIR.'/'.$path);

		$cacheFile = CACHE_DIR.'/'.$path.'/'.md5($filename);
		
		$pf = fopen($cacheFile, 'wb');
		fwrite($pf, $data);
		fclose($pf);
	}


	/**
	 * getBinCache($filename, $expired=null)
	 * 读取二进制缓存内容
	 * @param string $filename 缓存文件名称
	 * @param int $expired 缓存内容过期时间
	 */
	static public function getBinCache($filename, $expired=null) {
		$path      = dirname(str_replace('.', '/', $filename));
		if (!file_exists(CACHE_DIR.'/'.$path))
			makedir(CACHE_DIR.'/'.$path);
			
		$cacheFile = CACHE_DIR.'/'.$path.'/'.md5($filename);
		if (!file_exists($cacheFile)) return FALSE;
		
		if ( ! self::validate_expired( $expired, filemtime($cacheFile) ) ) {
			return false;
		}
		
		$pf = fopen($cacheFile, 'rb');
		$bin = fread($pf, filesize($cacheFile));
		fclose($pf);
		return $bin;
	}


	static public function setCache($filename, $data) {
		$path      = dirname(str_replace('.', '/', $filename));
		if (!file_exists(CACHE_DIR.'/'.$path))
			makedir(CACHE_DIR.'/'.$path);
		
		$cacheFile = CACHE_DIR.'/'.$path.'/'.md5($filename);
		
		$data      = "<?php\nreturn ".var_export($data,true).";\n?>";
		return file_put_contents($cacheFile, $data);
	}

	static public function getCache($filename, $expired=null) {
		$path      = dirname(str_replace('.', '/', $filename));
		if (!file_exists(CACHE_DIR.'/'.$path))
			makedir(CACHE_DIR.'/'.$path);
			
		$cacheFile = CACHE_DIR.'/'.$path.'/'.md5($filename);
		if (!file_exists($cacheFile)) return FALSE;
		
		if ( ! self::validate_expired( $expired, filemtime($cacheFile) ) ) {
			return false;
		}
		
		$result = require( $cacheFile );
		return $result; 
	}
	
	
	/**
	 * 检查缓存文件是否过期
	 */
	static private function validate_expired ( $expired, $modify ) {
		if ( is_null( $expired ) ) {
			return true;
		}
		else {
			if ( !is_numeric( $expired ) || $modify < $expired ) {
				return false;
			}
			return true;
		}
	}
}