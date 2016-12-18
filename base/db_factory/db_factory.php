<?php

class keke_db {
	private $_db_provider;
	private $_dbtype;
	public $_mydb;
	private static $dbs = array ('mysql'=>null,'mysqli'=>null,'odbc'=>null);
	public static function &get_instance($dbtype = "mysql") {
		static $obj = null;
		! is_object ( $obj ) && $obj = new keke_db ( $dbtype );
		return $obj;
	}
	function __construct($dbtype = "mysql") {
		if (is_object ( self::$dbs [$dbtype] )) {
			$this->_mydb = self::$dbs [$dbtype];
		} else {
			$this->_mydb = $this->create ( $dbtype );
		}
	}

	public function create($dbtype) {
		if (is_object ( self::$dbs [$dbtype] )) {
			return self::$dbs [$dbtype];
		} else {

			switch ($dbtype) {
				case "odbc" :
					$this->_dbtype = $dbtype;
					if (empty ( self::$dbs [$dbtype] )) {
						kekezu::keke_require_once ( S_ROOT . '/base/db_factory/odbc_driver.php' );
						return self::$dbs [$dbtype] = new odbc_driver ();
					} else {
						return self::$dbs [$dbtype];
					}
					break;
				case "pdo_sqlite" :
					$this->_dbtype = "pdo_sqlite";

					kekezu::keke_require_once ( S_ROOT . '/base/db_factory/sqlite_driver.php' );

					break;
				case "mysqli" :
					$this->_dbtype = "mysqli";
					if (empty ( self::$dbs [$dbtype] )) {

						kekezu::keke_require_once ( S_ROOT . '/base/db_factory/mysqli_driver.php' );
						return self::$dbs [$dbtype] = new mysqli_drver ();
					} else {
						return self::$dbs [$dbtype];
					}
					break;
				default :
					$this->_dbtype = $dbtype;
					if (empty ( self::$dbs [$dbtype] )) {

						kekezu::keke_require_once ( S_ROOT . '/base/db_factory/mysql_driver.php' );
						return self::$dbs [$dbtype] = new mysql_drver ();
					} else {
						return self::$dbs [$dbtype];
					}

					break;
			}
		}

	}

	/**
	 * 通用插入与替换数据方法
	 *
	 * @param $tablename string
	 * @param $insertsqlarr array
	 * 数组
	 * @param $returnid int
	 * @param $replace boolean
	 * @return int lastinsert_id
	 */
	public function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false) {
		return $this->_mydb->insert ( $tablename, $insertsqlarr, $returnid, $replace );
	}
	/**
	 * 通用数据更新方法
	 *
	 * @param $tablename string
	 * @param $setsqlarr array
	 * @param $wheresqlarr array
	 * @return int $affectrows
	 */
	public function updatetable($tablename, $setsqlarr, $wheresqlarr) {
		return $this->_mydb->update ( $tablename, $setsqlarr, $wheresqlarr );
	}
	/**
	 * 执行sql语句
	 *
	 * @param $sql string
	 * @return 返回执行影响的行数
	 */
	public function execute($sql) {
		$res = $this->_mydb->execute ( $sql );
		return $res ? $res : 0;
	}
	public function get_query_num() {
		return $this->_mydb->get_query_num ();
	}
	public function select($fileds = '*', $table, $where = '', $order = '', $group = '', $limit = '', $pk = '') {
		return $this->_mydb->select ( $fileds, $table, $where, $order, $group, $limit, $pk );
	}
	public function getCount($sql, $row = 0, $filed = null) {
		return $this->_mydb->getCount ( $sql, $row, $filed );
	}
	public function get_one($sql) {
		return $this->_mydb->get_one_row ( $sql );
	}
	// 返回查询的结果数组
	public function query($sql, $is_unbuffer = 0) {
		return $this->_mydb->query ( $sql, $is_unbuffer );
	}
	public function __destruct() {
		$this->_mydb->close ();
	}

}
class db_factory {

	private static $db_obj = null;

	public static function init($dbtype = 'mysqli') {
		$db_obj = &keke_db::get_instance ( $dbtype );
		return self::$db_obj = $db_obj;
	}
	public static function execute($sql) {
		self::init ();
		return self::$db_obj->execute ( $sql );

	}
	public static function debug() {
		return self::$db_obj->_mydb->get_all_sql();

	}
	public static function query($sql, $is_cache = 0, $cache_time = 0, $is_unbuffer = 0) {
		$db = self::init ();
		$result='';
		if(IS_CACHE){
			$cache_time > 0||is_null($cache_time) and ($result = self::db_cache ( 1, $sql, $cache_time ) and $cached=1);
			$result or ($result = self::$db_obj->query ( $sql, $is_unbuffer ) and $cached=0);
			if($result&&$result!='QUERY_EMPTY'){
				!$cached&&self::db_set_cache ($sql, $cache_time, $result );
				return $result;
			}else{
				!$cached&&self::db_set_cache ($sql, $cache_time);
				return array();
			}
		}else{
			return $result = self::$db_obj->query ( $sql, $is_unbuffer );
		}


	}
	public static function inserttable($tablename, $insertsqlarr, $returnid = 1, $replace = false) {
		$db = self::init ();
		$result = $db->inserttable ( $tablename, $insertsqlarr, $returnid, $replace );
		return $result == 0 ? true : $result;
	}
	public static function updatetable($tablename, $setsqlarr, $wheresqlarr) {
		$db = self::init ();
		return $db->updatetable ( $tablename, $setsqlarr, $wheresqlarr );
	}
	public static function create($dbtype = "mysql") {
		return self::init ( $dbtype );
	}
	/**
	 * 返回一个一维数组
	 * @param $sql string
	 * @param $cache_time 缓存时间(0表示不缓存)
	 */
	public static function get_one($sql, $cache_time = 0) {
		$db = self::init ();
		if(IS_CACHE){
			$cached = 0;
			$cache_time > 0||is_null($cache_time) and ($result = self::db_cache ( 1, $sql, $cache_time ) and $cached=1);
			$result or 	($result = self::$db_obj->get_one ( $sql) and $cached=0);
			if($result&&$result!='QUERY_EMPTY'){
				!$cached&&self::db_set_cache ($sql, $cache_time, $result );
				return $result;
			}else{
				!$cached&&self::db_set_cache ($sql, $cache_time, $result);
				return array();
			}
		}else{
			return $result = self::$db_obj->get_one ( $sql);
		}

	}
	/**
	 * 返回指定行的指定字段值，没有没有指定。默认取第一行的第一个字段
	 * @param $sql string
	 * @param $row int 行数
	 * @param $filed string 字段名
	 * @param $cache_time 缓存时间, 0表示不缓存
	 */
	public static function get_count($sql, $row = 0, $filed = null, $cache_time = 0) {
		$db = self::init ();
		if(IS_CACHE){
			$cache_time > 0||is_null($cache_time) and ($result = self::db_cache ( 1, $sql, $cache_time ) and $cached=1);
			$result or ($result = $db->getCount ( $sql, $row, $filed ) and $cached=0);
			if($result&&$result!='QUERY_EMPTY'){
				!$cached&&self::db_set_cache ($sql, $cache_time, $result );
				return $result;
			}else{
				!$cached&&self::db_set_cache ($sql, $cache_time);
				return $result=0;
			}
		}else{
			return $result = $db->getCount ( $sql, $row, $filed );
		}

	}
	/**
	 * 存入缓存
	 */
	public static function db_set_cache($sql,$cache_time,$result='QUERY_EMPTY'){
		self::db_cache ( 0, $sql, $cache_time, $result );
	}
	/**
	 * @param bool $is_get 是否获取缓存
	 * @param string $sql sql语句(计算出缓存的key)
	 * @param int $ttl 缓存时间(周期)
	 * @param mixed $result 添加缓存时的结果集
	 * @return 如果$is_get=true 返回array
	 * 如果$is_get=false 返回bool
	 */
	private static function db_cache($is_get, $sql, $ttl, $result='QUERY_EMPTY') {
		global $_K;
		//var_dump(func_get_args());
		$cache_obj = new keke_cache_class ( CACHE_TYPE, $_K ['cache_config'] );
		$key = $cache_obj->generate_id ( $sql );
		//echo $key;
		//$is_get == 1 and $res = $cache_obj->get ( $key ) or $res = $cache_obj->set ( $key, $result, $ttl );
		if($is_get==1){
			$res = $cache_obj->get ( $key );
		}else{
			$res = $cache_obj->set ( $key, $result, $ttl );
		}
		//var_dump($res);
		return $res;
	}
	public static function get_table_data($fileds = '*', $table, $where = '', $order = '', $group = '', $limit = '', $pk = '', $cachetime = 0) {
		global $_K;
		$wh = "";
		if(is_array($where)){
			while ( list ( $k, $v ) = each ( $where ) ) {
				$wh .= " 1=1 and " . $k . " = '$v'";
			}
		}
		$wh and $where = $wh;
		$db = self::init ();
		$sql = $table .$fileds. $where . $pk . $cachetime;
		if(IS_CACHE){
			$cachetime>0||is_null($cachetime) and ($result = self::db_cache ( 1, $sql, $cachetime ) and $cached=1);
			$result or ($result = $db->select ( $fileds, $table, $where, $order, $group, $limit, $pk ) and $cached=0);
			if($result&&$result!='QUERY_EMPTY'){
				!$cached&&self::db_set_cache ($sql, $cachetime, $result );
				return $result;
			}else{
				!$cached&&self::db_set_cache ($sql, $cachetime);
				return array();
			}
		}else{
			return $result = $db->select ( $fileds, $table, $where, $order, $group, $limit, $pk );
		}

	}
}

?>