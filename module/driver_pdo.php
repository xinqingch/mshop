<?php
class driver_pdo {
	
	protected $settings;
	protected static $instance;
	protected static $resource;


	public static function & instance( $settings, $isNew=null ) {
		$ClassName = get_class();
		if ( !is_array($settings) ) {
			throw new Exception( '"invalid database connection params' );
		}

		if ( $isNew ) {
			$resource = new $ClassName( $settings );
			return $resource;
		}
		else {
			if ( !self::$instance ) {
				self::$instance = new $ClassName( $settings );
			}
			return self::$instance;
		}
	}

	
	public function __construct ( $settings ) {
		try {
			if ( is_array($settings) ) {
				$this->settings = $settings;
				$config	= array_rand( $settings );
				extract( $settings[$config] );
			}
			
			$conStr	= "{$data}:host={$host};dbname={$dbname}";
			$init	= array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}'" );
			self::$resource	= new PDO( $conStr, $username, $password, $init );
		}
		catch ( Exception $Error ) {
			exit( $Error->getMessage() );
		}
	}
	

	public function __destruct () {}
	
	
	final public function query ( $SQL, $mode='FetchAll' ) {
		$start	= microtime(true);
		$mode	= strtolower( $mode );
		$fetch	= self::$resource->prepare( $SQL );
		$fetch->execute();
		list( $Err, $Err_code, $Err_info )	= $fetch->errorInfo();

		if ( $Err === '00000' ) {
			switch ( $mode ) {				
				case 'fetch':
				case 'row':
					$result = $fetch->fetch( PDO::FETCH_ASSOC );
					break;
				
				case 'column':
					$result = $fetch->fetchColumn();
					break;
				
				default:
					$result = $fetch->fetchAll( PDO::FETCH_ASSOC );
					break;
			}

			if (DEBUG) {
				$end = microtime(true);					
				YPiDebug::push_sql( $SQL, $start, $end );
			}
			return $result;
		}
		else {
			YPiDebug::push_sql_error( $SQL, $Err_info );
			throw new Exception( "{$Err_code}:{$Err_info}" );
		}
	}
	
	
	final public function exec ( $SQL ) {
		$start	= microtime(true);
		$fetch	= self::$resource->prepare( $SQL );
		$fetch->execute();
		list( $Err, $Err_code, $Err_info )	= $fetch->errorInfo();
		
		if ( $Err === '00000') {
			if (DEBUG) {
				$end = microtime(true);					
				YPiDebug::push_sql( $SQL, $start, $end );
			}
			return $fetch->rowCount();
		}
		else {
			YPiDebug::push_sql_error( $SQL, $Err_info );
			throw new Exception( "{$Err_code}:{$Err_info}" );
		}
	}
	
	
	final public function lastInsertId () {
		return self::$resource->lastInsertId();
	}


	
	//1.创建新的数据库链接
	//2.调用存储过程
	//3.关闭数据库链接
	final public function procedure( $procedure, $mode=null ) {
		$this->settings = $settings;
		$config	= array_rand( $settings );
		$mode	= strtolower( $mode );
		extract( $settings[$config] );

		$conStr	= "{$data}:host={$host};dbname={$dbname}";
		$init	= array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}'" );
		$resource	= new PDO( $conStr, $username, $password, $init );

		$fetch	= $resource->prepare( $SQL );
		$fetch->execute();
		list( $Err, $Err_code, $Err_info )	= self::$resource->errorInfo();

		if ( $Err === '00000' ) {
			switch ( $mode ) {				
				case 'fetch':
				case 'row':
					$result = $fetch->fetch( PDO::FETCH_ASSOC );
					break;
				
				case 'column':
					$result = $fetch->fetchColumn();
					break;
				
				default:
					$result = $fetch->fetchAll( PDO::FETCH_ASSOC );
					break;
			}

			if (DEBUG) {
				$end = microtime(true);					
				YPiDebug::push_sql( $SQL, $start, $end );
			}
			unset( $resource );
			return $result;
		}
		else {
			unset( $resource );
			YPiDebug::push_sql_error( $SQL, $Err_info );
			throw new Exception( "{$Err_code}:{$Err_info}" );
		}
	}
}
