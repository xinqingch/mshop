<?php
class Controller {
	static protected $smarty;
	static protected $resuouce;
	
	final static public function & instance () {
		$ClassName = get_called_class();

		if ( !isset( self::$resuouce[ $ClassName ] ) ) {			
			self::$resuouce[ $ClassName ] = new $ClassName;
		}

		return self::$resuouce[ $ClassName ];
	}

	public function __construct() {
		if (!self::$smarty) {
			self::$smarty = new Smarty();
			self::$smarty->setTemplateDir(VIEW_DIR);

			self::$smarty->compile_dir   = SMCOMPILE;
			self::$smarty->allow_php_tag = true;
			self::$smarty->compile_check = DEBUG;
			self::$smarty->caching       = SMYCACHE;
		}
		
		if (method_exists($this, 'init')) {
			$this->init();
		}
	}


	/**
	 * void assign($key, $value=null)
	 * 设置模板变量
	 * @param string/Array $key
	 * @param $value
	 */
	final public function assign($key, $value=null) {
		if (is_array($key) && is_nan($value)) {
			self::$smarty->assign($key);
		}
		else {
			self::$smarty->assign($key, $value);
		}
	}


	/**
	 * void display($template, $cache_id = null, $compile_id = null, $parent = null)
	 * 显示模板内容
	 * @param string $template
	 * @param string $cache_id
	 * @param string $compile_id
	 * @param string $parent
	 */
	final public function display($template, $cache_id = null, $compile_id = null, $parent = null) {
		self::$smarty->fetch($template, $cache_id, $compile_id, $parent, TRUE);
	}
	
	
	/**
	 * void self::$smarty->fetch($template, $cache_id, $compile_id, $parent, TRUE)
	 * 返回编译过的模板内容
	 * @param string $template
	 * @param string $cache_id
	 * @param string $compile_id
	 * @param string $parent
	 */
	public function fetch ($template, $cache_id = null, $compile_id = null, $parent = null) {
		return self::$smarty->fetch($template, $cache_id, $compile_id, $parent, FALSE);
	}


	/**
	 * void setTemplateDir($template_dir)
	 * 设置模板目录
	 * @param string $template_dir
	 */
	final public function setTemplateDir($template_dir) {
		self::$smarty->setTemplateDir($template_dir);
	}

	
	/**
	 * redirect ($url, $timeout, $message)
	 * 将页面跳转到指定的地址
	 */
	public function redirect ($url, $timeout, $message) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title><meta http-equiv="refresh" content="'.$timeout.';URL='.$url.'" /></head><body>'.$message.'</body></html>';
	}
}