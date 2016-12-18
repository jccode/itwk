<?php
/**
 * 增值服务处理类
 * 1.增值服务配置信息获取
 * 2.增值服务购买、使用记录获取   可根据服务类型、使用类型、对象类型、用户编号 来组合获取
 * 3.增值服务安装、卸载
 * 4.增值服务配置编辑
 * 5.增值服务购买、使用
 * @author Administrator
 *
 */
keke_lang_class::load_lang_class ( 'keke_payitem_class' );
class keke_payitem_class {
	
	public static function get_table_obj($table = 'witkey_payitem') {
		return keke_table_class::get_instance ( $table );
	}
	
	/**
	 * 获取增值项配置 
	 * @param string $user_type 用户类型 employer雇主，witkey威客
	 * @param string $model_code 模型code
	 * @param string $item_code 增值项类型
	 */
	public static function get_payitem_config($user_type = null, $model_code = null, $item_code = null, $pk = null, $is_open = '') {
		global $kekezu;
		intval($is_open) and $where = " and is_open=".intval($is_open);
		$pk or $pk = "item_code";
		
		$payitem_list = kekezu::get_table_data ( "*", "witkey_payitem", "1=1 $where ", "", "", "", $pk, 3600 );
		
		if ($user_type) {
			foreach ( $payitem_list as $k => $v ) {
				if ($v ['user_type'] != $user_type)
					unset ( $payitem_list [$k] );
			}
		}
		if ($model_code) {
			foreach ( $payitem_list as $k => $v ) {
				if (strpos ( $v ['model_code'], $model_code ) === FALSE) {
					unset ( $payitem_list [$k] );
				}
			}
		}
		
		if ($item_code) {
			$payitem_list [$item_code];
			
			return $payitem_list [$item_code];
		
		} else {
			
			return $payitem_list;
		}
	}
	/**
	 * 获取增值服务记录
	 * @param array $w 传入的搜索条件数组
	 * ['item_code'=>
	 * 'use_type'=>
	 * 'uid'=>    
	 * 'record_id'=>
	 * 'order'=>  ]
	 * @param array $p 外部传入的分页初始信息
	 * @return array ['page'=>,'list'=>]
	 */
	public static function get_payitem_record($w = array(), $order = null, $p = array()) {
		global $kekezu;
		$record_obj = new Keke_witkey_payitem_record_class ();
		$record_arr = array ();
		$where = " 1 = 1 ";
		
		if (! empty ( $w )) {
			$w ['item_code'] and $where .= " and item_code = '" . $w ['item_code'] . "'";
			$w ['use_type'] and $where .= " and use_type = '" . $w ['use_type'] . "' ";
			$w ['uid'] and $where .= " and uid = '" . $w ['uid'] . "' ";
		}
		$order and $where .= "  order by $order " or $where .= "  order by record_id desc  ";
		
		if (! empty ( $p )) { //需要执行分页
			$page_obj = $kekezu->_page_obj;
			intval ( $p ['page'] ) and $page = intval ( $p ['page'] ) or $page = '1';
			intval ( $p ['page_size'] ) and $page_size = intval ( $p ['page_size'] ) or $page_size = "10";
			$p ['url'] and $url = $p ['url'] or $url = $_SERVER ['HTTP_REFERER'];
			$p ['anchor'] and $anchor = $p ['anchor'];
			$record_obj->setWhere ( $where );
			$count = intval ( $record_obj->count_keke_witkey_payitem_record () );
			$pages = $page_obj->getPages ( $count, $page_size, $page, $url, "#" . $anchor );
			$where .= $pages ['where'];
		}
		$record_obj->setWhere ( $where );
		$record_list = $record_obj->query_keke_witkey_payitem_record ();
		
		$record_arr ['page'] = $pages ['page'];
		$record_arr ['list'] = $record_list;
		return $record_arr;
	}
	/**
	 * 增值项安装
	 * @param string $item_code 增值项类型
	 * @return boolen
	 */
	public static function payitem_install($item_code) {
		
		$obj = self::get_table_obj ();
		$info = $obj->get_table_info ( "item_code", $item_code );
		if ($info) { //此服务已存在
			return false;
		} else { //添加服务
			if (file_exists ( S_ROOT . "./control/payitem/$item_code/control/init_config.php" )) {
				require_once S_ROOT . "./control/payitem/$item_code/control/init_config.php";
				return $obj->save ( $init_info );
			} else {
				return false;
			}
		}
	}
	/**
	 * 增值项编辑
	 * @param int   $item_id   增值项编号
	 * @param array $item_info 编辑内容
	 */
	public static function payitem_edit($item_id, $item_info = array()) {
		$obj = self::get_table_obj ();
		return $obj->save ( $item_info, array ("item_id" => $item_id ) );
	}
	/**
	 * 增值项卸载
	 * @param int $item_id  增值项编号 
	 */
	public static function payitem_uninstall($item_id) {
		$obj = self::get_table_obj ();
		return $obj->del ( "item_id", $item_id );
	}
	/**
	 * 增值服务花费、购买记录产生
	 * @param string $item_code 增值服务类型 workhide
	 * @param string $use_type	使用类型  buy/spend
	 * @param string $obj_type  对象类型 task/work。。。
	 * @param string $use_num   使用、购买量
	 * @param int    $obj_id    对象编号
	 * @param int    $origin_id 源编号
	 */
	public static function payitem_record($item_code, $use_num = '1', $obj_type = false, $use_type = 'buy', $obj_id = null, $origin_id = null) {
		global $uid, $username;
		global $_lang;
		//使用花费直接根据购买量有配置算出
		$item = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_payitem where item_code='" . $item_code . "'" );
		if ($item) {
			$use_cash = $item ['item_cash'] * $use_num;
			$record_obj = new Keke_witkey_payitem_record_class ();
			$record_obj->_record_id = null;
			$record_obj->setItem_code ( $item_code );
			$record_obj->setUid ( $uid );
			$record_obj->setUsername ( $username );
			$record_obj->setUse_type ( $use_type );
			$record_obj->setUse_cash ( $use_cash );
			$record_obj->setUse_num ( intval ( $use_num ) );
			$record_obj->setObj_type ( $obj_type );
			$record_obj->setObj_id ( $obj_id );
			$record_obj->setOrigin_id ( $origin_id );
			$record_obj->setOn_time ( time () );
			$record_obj->setStatus ( 0 ); //购买状态
			$record_id = $record_obj->create_keke_witkey_payitem_record ();
			return $record_id;
		} else {
			kekezu::show_msg ( $item_info ['item_name'] . $_lang ['buy_fail'], $_SERVER ['HTTP_REFERER'], "3", "此服务已被网站关闭,无法购买", "warning" );
		}
	}
	/**
	 * 增值服务订单生成
	 * @param $item 增值项配置
	 */
	public static function payitem_order($item,$record_id,$use_num=1) {
		global $uid, $username;
		$order_obj = new Keke_witkey_order_class ();
		$order_obj->setOrder_name ( $username . "购买" . $item ['item_name'] . "服务" );
		$order_obj->setOrder_amount ( $item ['item_cash'] * $use_num );
		$order_obj->setOrder_status ( 'wait' );
		$order_obj->setOrder_uid ( $uid );
		$order_obj->setOrder_username ( $username );
		$order_obj->setObj_type ( 'payitem' );
		$order_obj->setObj_id ( $record_id );
		$order_obj->setTask_id ( 0 );
		$order_obj->setModel_id ( 0 );
		$order_obj->setOrder_time ( time () );
		//因为完成付款之前可能会有重复操作
		$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='paitem_buy' and obj_id='$record_id'" );
		if ($order_exsit) {
			$order_obj->setOrder_id ( $order_exsit ['order_id'] );
			$order_obj->edit_keke_witkey_order ();
			$order_id = $order_exsit ['order_id'];
		} else {
			$order_id = $order_obj->create_keke_witkey_order ();
		}
		return $order_id;
	}
	/**
	 * 获取订单号
	 */
	public static function get_order_id($record_id){
		return db_factory::get_count('select order_id from '.TABLEPRE.'witkey_order where obj_id='.$record_id);
	}

	/**
	 * 增值服务记录删除
	 * @param int $record_id
	 */
	public static function payitem_del($record_id) {
		return db_factory::execute ( sprintf ( " delete frm %switkey_payitem_record where record_id='%d'", TABLEPRE, $record_id ) );
	}
	/**
	 * 收费标准
	 */
	public static function payitem_standard() {
		global $_lang;
		return array ("times" => $_lang ['times'], "day" => $_lang ['day'], "month" => $_lang ['month'], "year" => $_lang ['year'] );
	}
	/**
	 * 获取用户增值项是否剩余
	 * @param  使用者 $uid
	 * @param 增值服务类型  $item_code
	 * @param 增值服务对象  $obj_type
	 */
	public static function payitem_exists($uid, $item_code = false, $obj_type = false, $payitem_arr = false) {
		
		if ($payitem_arr) {
			
			foreach ( $payitem_arr as $k => $v ) {
				$buy_count = db_factory::get_count ( sprintf ( " select sum(use_num) from %switkey_payitem_record where uid = '%d'  and item_code = '%s' and use_type = 'buy'", TABLEPRE, $uid, $v [item_code] ) );
				$use_count = db_factory::get_count ( sprintf ( " select sum(use_num) from %switkey_payitem_record where uid = '%d'  and item_code = '%s' and use_type = 'spend'", TABLEPRE, $uid, $v [item_code] ) );
				$payitem_info [$v [item_code]] = intval ( $buy_count - $use_count );
			
			}
		
		} else {
			$buy_count = db_factory::get_count ( sprintf ( " select sum(use_num) from %switkey_payitem_record where uid = '%d' and item_code = '%s' and use_type = 'buy'", TABLEPRE, $uid, $item_code ) );
			$use_count = db_factory::get_count ( sprintf ( " select sum(use_num) from %switkey_payitem_record where uid = '%d'  and item_code = '%s' and use_type = 'spend'", TABLEPRE, $uid, $item_code ) );
			
			$payitem_info = intval ( $buy_count - $use_count );
		}
		return $payitem_info;
	}
	/**
	 * 获取用户的相关增值服务使用信息
	 * @param int $uid
	 * @param string $use_type  使用类型    为空所有/buy 购买/spend 花费
	 * @param string $obj_type  对象类型
	 * @param int $obj_id		对象编号	
	 */
	public static function get_user_payitem($uid, $use_type = null, $obj_type = null, $obj_id = null) {
		$sql = " select a.use_cash,a.item_code,b.item_name,b.small_pic,b.item_desc from " . TABLEPRE . "witkey_payitem_record a left join " . TABLEPRE . "witkey_payitem b
			 on a.item_code = b.item_code where a.uid = '$uid' ";
		$use_type and $sql .= " and a.use_type = '$use_type' ";
		$obj_type and $sql .= " and a.obj_type = '$obj_type' ";
		$obj_id and $sql .= " and a.obj_id = '$obj_id' ";
		
		return db_factory::query ( $sql );
	}
	
	/**
	 * 
	 * 获取某个对象的增值服务项
	 * @param string $model_code
	 */
	public static function get_payitem_info($user_type, $model_code = '', $sort = false) {
		$where = sprintf ( " (user_type='%s' or user_type='universal') and is_open = 1", $user_type );
		$model_code && $where .= sprintf ( " and find_in_set('%s',model_code)", $model_code );
		$payitem_arr = kekezu::get_table_data ( "*", "witkey_payitem", "$where", "", "", "", "item_id" );
		if ($sort) {
			$payitem_arr = kekezu::get_arr_by_key ( array_merge ( $payitem_arr ), 'item_code' );
		}
		return $payitem_arr;
	}
	
	/**
	 * 
	 * 更新服务的增值服务时间
	 */
	public static function update_service_payitem_time($payitem_time, $add_time, $service_id) {
		$service_payitem_arr = unserialize ( $payitem_time ); //改变增值服务的时间 
		$service_payitem_arr [top] = $add_time + $service_payitem_arr [top];
		$new_payitem_time = serialize ( $service_payitem_arr );
		$res = db_factory::execute ( sprintf ( "update %switkey_service set payitem_time='%s' where service_id=%d", TABLEPRE, $new_payitem_time, $service_id ) );
		return $res;
	}
	
	/**
	 * 
	 * 设置增值服务结束时间
	 * @param array $payitem_arr
	 * @param int $obj_id
	 * @param string $obj_type
	 */
	public static function set_payitem_time($payitem_arr, $obj_id, $obj_type) {
		$payitem_end_time = serialize ( $payitem_arr );
		switch ($obj_type) { //便于以后的扩展，所以这个地方用的switch
			case "task" :
				$sql = sprintf ( "update %switkey_task set payitem_time='%s' where task_id=%d", TABLEPRE, $payitem_end_time, $obj_id );
				break;
			case "service" :
				$sql = sprintf ( "update %switkey_service set payitem_time='%s' where service_id=%d", TABLEPRE, $payitem_end_time, $obj_id );
				break;
		}
		$res = db_factory::execute ( $sql );
		return $res;
	}

}