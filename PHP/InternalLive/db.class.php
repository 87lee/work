<?php
/**
 * 	数据库类 / Database Class
 */

class DB {
	// 数据库主机名
	public $hostname;

	// 数据库账号
	public $username;

	// 数据库密码
	public $password;

	// 数据库名称
	public $dbname;

	// 数据库名称
	public $prefix;
	// 连接数据库
	function __construct( $hostname, $username, $password, $dbname,$prefix = '')
	{
		$this->hostname=$hostname; // 初始化数据库主机名
		$this->username=$username; // 初始化数据库用户名
		$this->password=$password; // 初始化数据库密码
		$this->dbname=$dbname; // 初始化数据库名称
		$this->prefix=$prefix; // 数据库表前缀
		$this->connect(); // 执行连接数据库
	}

	function connect()
	{
		// 执行连接
		mysql_connect( $this->hostname, $this->username, $this->password );

		// 统一编码
		mysql_query( 'set names utf8' );

		// 自动选择数据库
		mysql_select_db( $this->dbname );
	}

	function select( $sql )
	{
		// echo $sql;die;
		$data = array();
		$result = mysql_query( $sql );
		while ( $row=mysql_fetch_assoc($result) ) {
			$data[] = $row;
		}

		return $data;
	}

	function getAll( $tableName, $options='' )
	{

		$default = array(
			// isset是为了判断外面有没有传进来条件选项，如果有，那就使用外面的，否则使用默认值
			'where'=>isset($options['where']) ? "WHERE ".$options['where'] : 'WHERE 1',
			'fields'=>isset($options['fields']) ? $options['fields'] : '*',
			'order'=>isset($options['order']) ? 'ORDER BY '.$options['order'] : '',
			'limit'=>isset($options['limit']) ? 'LIMIT '.$options['limit'] : '',
		);

		// W O L
		$sql = "SELECT {$default['fields']} FROM {$this->prefix}{$tableName} {$default['where']} {$default['order']} {$default['limit']}";

		return $this->select( $sql );
	}

	function getOne( $tableName, $options ){
		return current( $this->getAll( $tableName, $options ) );
	}

	function getCount($table, $where=1)
	{
		$sql = "SELECT COUNT(*) FROM {$this->prefix}{$table} WHERE {$where}";
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		
		return $data['COUNT(*)'];
		
		/*
		$data = array(
			'COUNT(*)'=>总数
		)
		*/
	}
	
	function insert( $tableName, $data ){
		$fields = array();
		$values =  array();

		foreach ($data as $key => $value) {
			$fields[] = $key;
			$values[] = $value;
		}

		$fields = implode(',', $fields);
		$values = "'".implode("','", $values)."'";

		$sql = "INSERT INTO {$this->prefix}{$tableName} ($fields) VALUES($values)";

		mysql_query($sql);

		return mysql_insert_id(); //返回插入数据插入的主键id值
	}

	function update( $tableName, $data, $where ){

		$sql = "UPDATE `{$this->prefix}{$tableName}` SET ";

		foreach($data as $k=>$v){
			 $sql=$sql.'`'.$k."`='".$v."',";
		}
		
		$sql= rtrim($sql,',');
		
		$sql=$sql.'WHERE '.$where;
		
		mysql_query($sql);
		
		return mysql_affected_rows();
	}

	function delete( $tableName, $where ){

		$sql = "DELETE FROM {$this->prefix}{$tableName} WHERE $where";

		mysql_query($sql);

		return mysql_affected_rows();//返回删除受影响的行数
	}
}



?>