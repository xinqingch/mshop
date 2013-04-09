<?php

/**
 * $rule = array(
 *    array('phone','isNumeric', '电话号码不能为空'),
 *    array('email', 'isEmail', '邮箱地址格式不正确')
 * )
 * 
 * $chk = Verify::getInstance();
 * $chk->setRule($rule);
 * $chk->check($_POST);
 * $error = $chk->getError();
 * if ($error) {
 *	 echo 'ERROR';
 * }
 */



class Verify {
	static protected $instance;
	protected $Error = array();
	protected $Rule;


	static public function getInstance () {
		if (is_null(self::$instance))
			self::$instance = new Verify;
		
		return self::$instance;
	}


	public function setRule ($ruleData) {
		$this->Rule = $ruleData;
	}


	public function check ($data) {
		if ($this->Rule && $data) {
			foreach ($this->Rule as $item) {
				$field  = array_shift($item);
				$action = array_shift($item);				
				$msg    = array_pop($item);
				
				if (isset($data[$field])) {
					if (!method_exists($this, $action)) {
						exit('not fine method "'.$action.'" by Verify.');
					}
	
					$result = call_user_func_array(array($this, $action), (array)$data[$field]);
					if (!$result) {
						$this->Error[$field] = $msg;
					}
				}
				else {
					$this->Error[$field] = '无法取得该值';
				}
			}
		}
		
		return (!$this->Error)? true : false;
	}


	public function getError () {
		return $this->Error;
	}

	//值不能为空
	public function notEmpty ($value) {
		if (!$value || empty($value)) {
			return false;
		}
		else {
			return true;
		}
	}

	//值是否为数字
	public function isNumeric ($value) {
		return is_numeric($value);
	}

	//值不能小于
	public function mix ($mix, $value) {
		return $mix > $value;
	}

	//值不能大于
	public function max ($max, $value) {
		return $max < $value;
	}

	// 邮箱地址是否正确
	public function isEmail ($value) {
		$result = filter_var($value, FILTER_VALIDATE_EMAIL);
		return !$result? false : true;
	}

	//使用Perl正则表达式进行校验
	public function regex ($rule, $value) {
		return preg_match($rule, $value);
	}

	/**
	 *	字数限制方法
	 *	@param $string 要限制的字符串
	 *	@param $array  上下界限制 如 $array = array(2,6);
	 */
	public function strRang($string, $array){
		$len = strlen($string);
		if($array[0] > $array[1]){
			$max = $array[0];
			$array[0] = $array[1];
			$array[1] = $max;
		}

		if($len >= $array[0] && $len <= $array[1]){
			return true;
		}
		return false;
	}

	/**
	 * 数值范围限制方法
	 * @param $mumber 要判断的数字
	 * @param $array 数值最大最小值的限制
	 * 如: $array = array(2,6)  $mumber 的取值在2到6之间.
	 */
	public function rang($mumber, $array){
		if($array[0] > $array[1]){
			$max = $array[0];
			$array[0] = $array[1];
			$array[1] = $max;
		}

		if($mumber >= $array[0] && $mumber <= $array[1]){
			return true;
		}
		return false;			
	}
}