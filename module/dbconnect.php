<?php
if (!defined('DB_DRIVER')) define('DB_DRIVER', 'mysql:dbname=test;host=127.0.0.1;port=3306');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8');
if (!defined('DB_PRE')) define('DB_PRE', 'db_');

class DBConnect {
	static protected $connect;
	static protected $instance;
	protected $error;
	protected $single	= 'default';
	
	final public function __construct( $driver, $username, $password, $charset, $single ) {
		$this->single = $single;
		self::$connect[$single] = new PDO($driver, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES '.$charset));
		if (!self::$connect)
			exit('do not connect to database.');
	}


	static public function &getConnect() {
		$config	= array();

		if ( func_num_args() > 0 ) {
			$config = func_get_arg(0);
			if ( !is_array( $config ) ) $config = array();
		}

		if ( !is_array( $config ) ) $config = array();
		if ( !isset( $config['drive'] ) )		$config['drive']	= DB_DRIVER;
		if ( !isset( $config['user'] ) )		$config['user']		= DB_USER;
		if ( !isset( $config['password'] ) )	$config['password']	= DB_PASS;
		if ( !isset( $config['charset'] ) )		$config['charset']	= DB_CHARSET;
		if ( !isset( $config['single'] ) )		$config['single']	= 'default';

		if (!self::$connect[$config['single']]) {
			self::$instance = new DBConnect( $config['drive'], $config['user'], $config['password'], $config['charset'], $config['single'] );
		}
		
		return self::$instance;
	}


	public function query($SQL, $type='') {
		$this->error = null;
		$cmd = self::$connect[$this->single]->prepare($SQL);
		$cmd->execute();
		$result = array();
		
		if (DEBUG) {
			$start = microtime(true);
		}
			
		switch ( strtolower($type) ) {
			case "column":
				$result = $cmd->fetchColumn();
				break;
				
			case "row":
				$result = $cmd->fetch(PDO::FETCH_ASSOC);
				break;
				
			default:				
				$result = $cmd->fetchAll(PDO::FETCH_ASSOC);
		}
		
		if (DEBUG) {
			$end = microtime(true);
			
			YPiDebug::sql_exec($SQL, $start, $end);
		}
		
		$this->error = $cmd->errorInfo();
		
		if($this->error[0] === '00000') {
			return $result;
		}
		else {
			return FALSE;
		}
		
	}


	public function execute($SQL) {
		$this->error = null;

		if (DEBUG) {
			$start = microtime(true);
		}

		$result      = self::$connect[$this->single]->exec($SQL);

		if (DEBUG) {
			$end = microtime(true);
			
			YPiDebug::sql_exec($SQL, $start, $end);
		}

		$this->error = self::$connect[$this->single]->errorInfo();
		
		if($this->error[0] === '00000') {
			return $result;
		}
		else {
			return FALSE;
		}	
	}
	
	
	/**
	 * procdure
	 * 执行存储过程的调用
	 * @param $procdureName 存储过程名称
	 */
	public function procdure($procdureName) {
		$this->error = null;
		$conn = new DBConnect(DB_DRIVER, DB_USER, DB_PASS);
		$cmd = $conn->prepare('call '.$procdureName);
		
		if (func_num_args() > 1) {
			$params = func_get_args();
			for ($i=1; $i<func_num_args(); $i++) {
				$cmd->bindParam($i, $params[$i]);
			}
		}
		
		$cmd->execute();
		$result = $cmd->fechAll(PDO::FETCH_ASSOC);
		
		$this->error = $cmd->errorInfo();
		if($this->error[0] === '00000') {
			return $result;
		}
		else {
			return FALSE;
		}
		
		return $result;
	}
	

	/**
	 * getError ()
	 * 获取错误信息
	 */
	public function getError() {
		return $this->error;
	}

	
	/**
	 * lastInsertId ()
	 * 获取最后插入数据的自动编号(ID)
	 */
	public function lastInsertId () {
		return self::$connect[$this->single]->lastInsertId();
	}
}