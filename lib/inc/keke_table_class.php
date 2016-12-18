<?php
/**
 * 
 * 单表，增，删，查，改.
 * 注：该方法里面的数组的键均为数据库对应字段名
 * @author Administrator
 *
 */
class keke_table_class{
	public $_table_name;
	public $_table_obj;
	public $_page_obj;
	public $_count;
	public $_pre = 'keke_';
	public $_db;
	public $_dbop;
	public static function get_instance($table_name) {
		return new keke_table_class ( $table_name );
	}
	
	function __construct($table_name) {
		global $kekezu;
		$this->_page_obj = $kekezu->_page_obj;
		$this->_table_name = $table_name;
		$table_class = ucfirst($this->_pre).$table_name . "_class";
		$this->_table_obj = new $table_class (); 
		$this->_db = new db_factory ( );
		$this->_dbop = $this->_db->create(DBTYPE);
	}
	
	/**
	 * 
	 * 查询
	 * @param array $where_arr    -------二维条件数组
	 * $where_arr 数组的标准 $where_arr[w][f]=f为表的字段 y为该字段的值 
	 * $where[ord][f]=desc/asc  x为排序的字段
	 * @param string $url_str     -------url地址
	 * @param int $page
	 * @param int $ajax 是否使用AJAX翻页
	 * @param string $ajax_dom 异步加载DOM的ID
	 * @return array(data,pages);
	 */
	
	function get_grid($wh = '1=1', $url_str, $page, $page_size = 10, $order = null,$ajax=0,$ajax_dom=null) {
		$page_obj = $this->_page_obj;
		if($ajax){
			$page_obj->setAjax('1');
			$page_obj->setAjaxDom($ajax_dom);
		}
		if (is_array ( $wh )) {
			$where = " 1 = 1";
			$wh [w] = array_filter ( $wh [w] );
			foreach ( $wh [w] as $k => $v ) {
				$where .= " and $k = '$v'";
			}
			
			foreach ( $wh [ord] as $k => $v ) {
				$where .= " order by $k $v";
			}
		
		} else {
			$where = $wh;
		}
		$this->_table_obj->setWhere ( $where );
		$count_query = "count_" . $this->_pre . $this->_table_name;
		$this->_count = $count = $this->_table_obj->$count_query ();
		
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url_str ); 
		$where .= $order .= $pages [where];
		$this->_table_obj->setWhere ( $where );
		$query = "query_" . $this->_pre . $this->_table_name;
		$res_info [data] = $this->_table_obj->$query ();

		$res_info [pages] = $pages;
		
		if ($res_info) {
			return $res_info;
		} else {
			return false;
		}
	
	}
	/**
	 * 多表查询,使用联接方式，默认为inner join
	 * @param string $field 要查询的字段
	 * @param array $table 查询表-----array('s'=>'witkey_space','m'=>'witkey_member')当键值为string时，则为表简称
	 * @param array $join 联接类型---array('left join','right join','inner join')此与表参数对应,空值是默认为inner join
	 * @param array $on 联接条件---array('s.user_id=m.user_id')
	 * @param string $where -------查询条件
	 * @param string $order ---排序字段
	 * @param int $page---当前页
	 * @param int $page_size----每页显示记录数
	 * @param string $url_str     -------url地址
	 * @param int $ajax 是否使用AJAX翻页
	 * @param string $ajax_dom 异步加载DOM的ID
	 * @return array(data,pages);
	 */
	
	function get_multi_grid($field="*",$table=null,$join=null,$on=null,$where =null,$order = null,$page, $page_size = 10,$url_str,$ajax=0,$ajax_dom=null) {
		$page_obj = $this->_page_obj;
		if($ajax){
			$page_obj->setAjax('1');
			$page_obj->setAjaxDom($ajax_dom);
		}
		//读取出table ,并连接join与on成string类型
		if(is_array($table)){
			$i=0;
			$tb_leng=count($table)-1;
			foreach($table as $k=>$v){
				$str_table.=$this->_pre .$v;
				is_string($k)and $str_table.=" as ".$k;
				if($i<$tb_leng ){
					isset($join[$i])?$join[$i]=" ".$join[$i]." ":$join[$i]=' inner join ';
				}
				if($i<=$tb_leng){
					isset($on[$i-1])?$on[$i-1]=' on '.$on[$i-1]:$on[$i-1]='';
					$str_table.=$join[$i].$on[$i-1];
				}
				$i++;
			}
		}else{
			$str_table=$this->_pre .$table;
		}
		if($order){
			$order=' order by '.$order;
		}else{
			$order=null;
		}
		$sql="select ".$field." from ".$str_table;
		$where and $sql.=" where ".$where;
		$this->_count = $count = $this->_dbop->execute($sql);
	
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url_str );
		$sql.=$order .= $pages [where];
		$res_info [data] = $this->_dbop->query ($sql);
		$res_info [pages] = $pages;
		if ($res_info) {
			return $res_info;
		} else {
			return false;
		}
	}
	/**
	 * 编辑/添加
	 * @param array $fileds   ----------要编辑字段数组 $fds['字段名']
	 * @param array $pk  --------编辑条件array("id"=>$id)
	 */
	function save($fields, $pk = array()) {
		foreach ( $fields as $k => $v ) {
			$kk = ucfirst ( $k );
			$set_query = "set" . $kk;
			$this->_table_obj->$set_query ( $v );
		}
		$keys = array_keys ( $pk );
		$key = $keys [0];
		//编辑
		if (! empty ( $pk [$key] )) {
			$this->_table_obj->setWhere ( " $key = '" . $pk [$key] . "'" );
			$edit_query = "edit_" . $this->_pre . $this->_table_name;
			$res = $this->_table_obj->$edit_query ();
		} else {
			$create_query = "create_" . $this->_pre . $this->_table_name;
			$res = $this->_table_obj->$create_query ();
		}
		if ($res) {
			return $res;
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * 批量删除
	 * @param string $pk ------表的主键
	 * @param array $val ------批量删除数组必须为主键id的数组,或者字符串
	 * @return int 影响的行数
	 */
	function del($pk, $val, $url = null) {
		
		if (! $val) {
			return false;
		}
		if (is_array ( $val ) && ! empty ( $val )) {
			$ids = implode ( ',', $val );
			$this->_table_obj->setWhere ( " $pk in ($ids)" );
		} elseif ($val) {
			$this->_table_obj->setWhere ( "$pk = " . $val );
		}
		$del_query = "del_" . $this->_pre . $this->_table_name;
		return $this->_table_obj->$del_query ();
	}
	
	/**
	 * 
	 * 获取编辑页面的详细信息
	 * @param string $index_key    -----主键名
	 * @param string $index_val		------主键值
	 */
	function get_table_info($index_key, $index_val) {
		$this->_table_obj->setWhere ( " $index_key = '$index_val'" );
		$query = "query_" . $this->_pre . $this->_table_name;
		$table_info = $this->_table_obj->$query ();
		$table_info = $table_info [0];
		if ($table_info) {
			return $table_info;
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * 
	 * 生成条件数据集 ，可以代替in()的写法
	 * @param string $fname   --- 字段名 
	 * @param sting $str	----数组 /数字(如果为数字，则会生成1-x的数据集,如果为数组则生成数据集)
	 */
	public static function generate_row($fname, $str) {
		$a = "";
		if (is_numeric ( $str )) {
			$a .= "select 1 as $fname";
			for($i = 2; $i <= $str; $i ++) {
				$a .= " union all select $i";
			}
		} elseif (is_array ( $str )) {
			foreach ( $str as $k => $v ) {
				
				if ($k == 0) {
					$a .= " select $v as $fname";
				} else {
					$a .= " union all select $v ";
				}
			}
		}
		return $a;
	}
	 
	
	/**
	 * 
	 * update更新语句 支持字段运算    
	 * @param string $table_name ----表名
	 * @param array $fiedsarr  ----字段数组
	 * @param array $wherearr  ------ 条件数组
	 */
	public static function updateself($table_name, $fiedsarr, $wherearr) {
		
		$size = sizeof ( $fiedsarr );
		$keys = array_keys ( $fiedsarr );
		for($i = 0; $i < $size; $i ++) {
			//判断是否是最后一个字段,最后一个字段不加逗号
			stristr ( $fiedsarr [$keys [$i]], '`' ) != false and $value = $fiedsarr [$keys [$i]] or $value = "'{$fiedsarr[$keys[$i]]}'";
			$i == $size - 1 and $set_value .= "`$keys[$i]` = $value " or $set_value .= " `$keys[$i]` = $value,";
		}
		$size = sizeof ( $wherearr );
		$keys = array_keys ( $wherearr );
		$where = " 1=1 ";
		for($i = 0; $i < $size; $i ++) {
			$where .= " and  `$keys[$i]` = '{$wherearr[$keys[$i]]}'";
		}
	 
		return db_factory::execute ( " update ".TABLEPRE. $table_name . " set $set_value where $where" );
	}
	/**
	 * 查询条件组合  
	 * @param unknown_type $where
	 * @param unknown_type $order
	 * @param unknown_type $w
	 * @param unknown_type $p
	 * @return multitype:Ambigous <string, unknown> unknown
	 */
	public static function format_condit_data($where,$order,$w=array(),$p = array()){
		global $kekezu;
		$arr = array();
		if (! empty ( $w )) {
			$w = array_filter ( $w );
			foreach ( $w as $k => $v ) {
				$where .= " and $k = '$v' ";
			}
		}
		$order and $where.=" order by $order ";
		if (! empty ( $p )) {
			$page_obj = $kekezu->_page_obj;
			$count = intval ( db_factory::execute ($where ));
			$pages = $page_obj->getPages ( $count, $p ['page_size'], $p ['page'], $p ['url'], $p ['anchor'] );
			$where .= $pages ['where'];
		}
		$arr['where']  = $where;
		$arr['pages']  = $pages;
		return $arr;
	}
	
	
	
	/**
	 * 
	 * 根据主建获取表的详细信息
	 * @param string $table_name
	 * @param string $pk_field
	 */
	public static function all_table_info($table_name,$arr){ 
		 list($key,$val)= each($arr);
		 $sql = sprintf("select * from %s where %s='%s'",TABLEPRE.$table_name,$key,$val);
		 return db_factory::query($sql);
	}
	
	
	
	
	
	
	
}