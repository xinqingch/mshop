<?php
class DBModule extends Connect {
	
	protected $tableName;
	protected $_errorInfo=array();

	public function getErrorEx () {
		return self::$db->getError();
	}

	public function getError () {
		return $this->_errorInfo;
	}


	/**
	 * data findAll ($condition="", $order="", $limit="", $fields='*')
	 * 高级数据库查询功能
	 * @param string $condition		自定义查询条件
	 * @param string $order			排序方式
	 * @param string $limit			查询列表范围
	 * @param string $fields		列表字段名称
	 */
	final public function findAll ($condition="", $order="", $limit="", $fields='*') {
		
		//before触发器
		if ( method_exists($this, 'findAll_before') ) {
			$this->findAll_before();
		}
		
		if ($condition) {
			$condition = 'WHERE ' . $condition;
		}

		if ($order) {
			$order = 'ORDER BY ' . $order;
		}

		if ($limit) {
			$limit = 'LIMIT ' . $limit;
		}

		$SQL	= "SELECT {$fields} FROM `{$this->tableName}` {$condition} {$order} {$limit}";
		$result	= self::$db->query($SQL);
		
		//after触发器
		if ( method_exists( $this, 'findAll_after' ) ) {
			$this->findAll_after();
		}
		
		return $result;
	}



	/**
	 * data find ($condition="", $order="", $fields='*')
	 * 高级数据库单条记录查询功能
	 * @param $condition	WhereString
	 * @param $order		OrderString
	 * @param $fields		FieldString
	 */
	final public function find ( $condition="", $order="", $fields='*' ) {
		
		//before触发器
		if ( method_exists($this, 'find_before') ) {
			$this->find_before();
		}
		
		if ( $condition ) {
			$condition = 'WHERE ' . $condition;
		}

		if ( $order ) {
			$order = 'ORDER BY ' . $order;
		}

		$SQL = "SELECT {$fields} FROM `{$this->tableName}` {$condition} {$order} LIMIT 1";
		$result	= self::$database->query($SQL, 'row');
		
		//after触发器
		if ( method_exists( $this, 'find_after' ) ) {
			$this->find_after();
		}
		
		return $result;
	}

	/**
	 * lastInsertId()
	 * 获取最后插入内容的自动编号
	 * @return string
	 */
	final public function lastInsertId () {
		return self::$database->lastInsertId();
	}
	
	/**
	 * table ( $tablename )
	 * 设置关联查询的数据表名称
	 * @param $tablename TableName
	 */
	final public function table ( $tablename ) {
		$this->found['table'] = $tablename;
		return $this;
	}
	
	/**
	 * 设置关联查询条件
	 */
	final public function where ( $condition ) {
		$this->found["where"] = $condition;
		return $this;
	}

	
	/**
	 * 设置关联查询排序方式
	 */
	final public function order ( $condition ) {
		$this->found["order"] = $condition;
		return $this;
	}


	/**
	 * 设置关联查询记录偏移量
	 */
	final public function limit ( $condition ) {
		$this->found["limit"] = $condition;
		return $this;
	}


	/**
	 * 设置关联查询字段
	 */
	final public function field ( $condition ) {
		$this->found["field"] = $condition;
		return $this;
	}


	/**
	 * 执行连贯查询操作
	 */
	final public function go ( $RowType=null ) {
		
		//事件触发器before
		if (method_exists($this, 'go_before')) {
			$this->go_before();
		}
		
		$table	= ( $this->found["table"])? $this->tableName : $this->found["table"];
		$field	= ( $this->found["field"] )? $this->found["field"] : '*';
		$where	= ( $this->found["where"] )? 'WHERE ' . $this->found["where"] : '';
		$order	= ( $this->found["order"] )? 'ORDER BY ' . $this->found["order"] : '';
		$limit	= ( $this->found["limit"] )? 'LIMIT ' . $this->found["limit"] : '';

		$SQL = "SELECT {$field} FROM `{$this->tableName}` {$where} {$order} {$limit}";
		$this->found = array( 'table'=>null, 'where'=>null, 'order'=>'', 'limit'=>null, 'field'=>null );
		$result = self::$db->query($SQL, $RowType);
		
		//事件触发器after
		if (method_exists($this, 'go_after')) {
			$this->go_before();
		}

		return $result;
	}


	final protected function error_append( $key, $msg ) {
		if ( !array_key_exists( $key, $this->_errorInfo ) ) {
			$this->_errorInfo[$key] = $msg;
		}
	}
}
