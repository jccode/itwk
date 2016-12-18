<?php
require ('DataBase.php');
class odbc_driver extends DataBase  {
	
    private $_dbhost;
	private $_dbname;
	private $_dbuser;
	private $_dbpass;
	private $_dbcharset;
	private $_link;
	function __construct() {
		
		$this->_dbhost = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".S_ROOT."data\aaaa.mdb";
		$this->_dbuser = "";
		$this->_dbpass = "";
		 
		if(function_exists("odbc_connect")){
			$this->_link = odbc_connect( $this->_dbhost, $this->_dbuser, $this->_dbpass );
		}else {
			$this->_link = FALSE;
			$this->halt('does not support odbc database connection');
		}
	}
	/**
	 * 数据库连接
	 *
	 * @return unknown
	 */
	public function dbConnection() {
		if (! $this->_link) {
			  $this->halt('DataBase Connection Error') ;
		} else {
			return $this->_link;
		}
	
	}
	/**
	 * 查询结果返回一个数组
	 *
	 * @param unknown_type $sql
	 * @return unknown
	 */
	public function query($sql) {
		$rs = odbc_exec($this->dbConnection(),$sql);
		if(is_resource($rs)){
			while ($result[] = odbc_fetch_array($rs));
			odbc_free_result($rs);
			$this->close();
			return $result;
		}else{
			$this->halt('Database query error',$sql);	
		}	
		
	}
	/**
	 * 查询结果中某行某字段的值
	 *
	 * @param unknown_type $query
	 * @param unknown_type $row
	 * @return unknown
	 */
	public function getCount($sql, $row = 0, $field = null) {
		$query = odbc_exec( $this->dbConnection (),$sql );
		if ($query) {
			$result = odbc_result ( $query, $row, $field );
			return $result;
		} else {
			$this->halt('Database query error',$sql);
		}
	}
	/**
	 * 返回插入的ID
	 *
	 * @param unknown_type $insertSql
	 * @return unknown
	 */
	public function insert_id($insertSql) {
		$query = odbc_execute($this->dbConnection(),$insertSql);
		if($query){
			return $query;
		}else {
			$this->halt('Database query error',$insertSql);
		}
	}
	/**
	 * 更新或删除数据库,返回影响的行数
	 *
	 * @param unknown_type $updatesql
	 * @return unknown
	 */
	public function execute($sql) {
		 
		$query = odbc_do($this->dbConnection(),$sql);
		if ($query) {
			return  $query;
		} else {
			$this->halt('Database query error',$updatesql);
		}
	}
	/**
	 * 关闭数据库连接
	 *
	 * @return unknown
	 */
	public function close() {
		return odbc_close ( $this->_link );
	}
	/**
	 * 获取错误
	 *
	 * @return unknown
	 */
	public function getError() {
		return ($this->_link) ? odbc_error ( $this->dbConnection()) : odbc_errormsg($this->dbConnection());
		
	}
	public function getErrno(){
		return ($this->_link) ? odbc_errormsg ( $this->dbConnection()) : odbc_error($this->dbConnection());
	}
	 
	function halt($message = '', $sql = '') {
		global $_K; 
		$dberror = $this->getError();
		$dberrno = $this->getErrno();
		//if($_K['is_debug'])
		if(1)
		{
		 echo "<div style=\"position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;\">
				<b>ODBCSQL Error</b><br>
				<b>Message</b>: $message<br>
				<b>SQL</b>: $sql<br>
				<b>Error</b>: $dberror<br>
				<b>Error</b>: $dberrno<br> 
				</div>";
		}
		else
		{
			echo "<div style=\"position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;\">
				<b>ODBCSQL	 Error</b><br>
				<b>Message</b>: $message<br>
				 </div>";
		}
		exit();
	}
   public function __destruct()
	{
		is_resource($this->_link) and odbc_close($this->_link);
	}
}

?>