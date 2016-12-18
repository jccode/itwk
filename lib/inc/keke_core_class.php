<?php
/**
 * this not free,powered by keke-tech
 * @version 2.0
 * @auther xujie
 * 
 */

(defined ( "IN_KEKE" ) || defined ( 'ADMIN_KEKE' )) or die ( "Access Denied" );
ini_get ( "session.save_path" ) or ini_set ( "session.save_path", S_ROOT . '/data/session/' );
class keke_core_class extends keke_base_class {
	
	/**
	 * 用于后台页面跳转提示
	 * 
	 * @param string $type
	 * {'info'=>默认,'success'=>'成功','warning'=>'警告'}
	 */
	static function admin_show_msg($title = "", $url = "", $time = 3, $content = "", $type = "info") {
		global $_K, $_lang;
		$url ? $url : $_K ['refer'];
		require keke_tpl_class::template ( 'control/admin/tpl/show_msg' );
		die ();
	}
	/**
	 * 用于页面跳转提示
	 * 
	 * @param string $type
	 * inajax
	 * {'alert_info'=>'提示','alert_right'=>'成功','confirm_info'=>'确认','alert_error'=>'错误'}
	 * 非ajax {'info'=>默认,'success'=>'成功','warning'=>'警告'}
	 */
	static function show_msg($title = "", $url = "", $time = 3, $content = "", $type = 'info') {
		global $_K, $basic_config, $username, $uid, $nav_list, $_lang;
		
		$r = $_REQUEST;
		$msgtype = $type;
		require keke_tpl_class::template ( 'show_msg' );
		die ();
	}
	
	// 权限检查函数 需要将权限编号传递过来
	static function admin_check_role($roleid) {
		global $_K, $admin_info;
		$grouplist_arr = keke_admin_class::get_user_group ();
		if ($_SESSION ['uid'] != ADMIN_UID && ! in_array ( $roleid, $grouplist_arr [$admin_info ['group_id']] ['group_roles'] )) {
			echo "<script>location.href='index.php?do=main'</script>";
			die ();
		}
	}
	/**
	 * 清除全局缓存
	 */
	static function empty_cache($type='') {
		$file_obj = new keke_file_class ();
		TPL_CACHE&&$tpl = true;
		IS_CACHE&&$data = true;
		switch($type){
			case 'tpl':
				$data = false;
				break;
			case 'data':
				$tpl = false;
				break;
		}
		$tpl and $file_obj->delete_files ( S_ROOT . "/data/tpl_c" );
		$data and $file_obj->delete_files ( S_ROOT . "/data/data_cache" );
	}
	/**
	 * 获取金币，现金消耗
	 * 现金+金币足额前提;
	 */
	static function get_cash_consume($total) {
		global $kekezu, $user_info;
		$tmp = array ();
		$credit_allow = $kekezu->_sys_config ['credit_is_allow'];
		$ba = $user_info ['balance'];
		$cr = $user_info ['credit'];
		switch ($credit_allow) {
			case "1" : //开
				if ($cr >= $total) {
					$credit = $total;
					$cash = 0;
				} else {
					$credit = $cr;
					$ba >= $total - $cr and $cash = $total - $cr or $cash = - 1;
				}
				break;
			case "2" : //关
				$ba >= $total and $cash = $total or $cash = - 1;
				$credit = 0;
				break;
		}
		$tmp ['cash'] = floatval ( $cash );
		$tmp ['credit'] = floatval ( $credit );
		return $tmp;
	}
	/**
	 * 安全码SESSION重置
	 * 
	 * @param boolean $verify
	 * 是否需验证
	 */
	static function reset_secode_session($verify) {
		global $uid;
		if ($verify) { // 需验证。不管之前是否验证过。强制重新验证
			unset ( $_SESSION ['check_secode_' . $uid] );
			return TRUE;
		} else { // 不需验证
			if ($_SESSION ['check_secode_' . $uid]) { //
				return FALSE;
			} else { // 虽然外部指示不需验证。但是由于安全码session不存在。此时强制需验证
				return TRUE;
			}
		}
	}
	/**
	 * 获取showWindw的弹窗链接
	 */
	static function get_window_url() {
		global $_K;
		$post_url = $_SERVER ['QUERY_STRING'];
		preg_match ( '/(.*)&infloat/U', $post_url, $match );
		return $_K ['siteurl'] . '/index.php?' . $match ['1'];
	}
	// 系统日志
	static function admin_system_log($msg) {
		global $_K, $admin_info;
		$system_log_obj = new Keke_witkey_system_log_class ();
		$system_log_obj->setLog_content ( $msg );
		$system_log_obj->setLog_ip ( kekezu::get_ip () );
		$system_log_obj->setLog_time ( time () );
		$system_log_obj->setUser_type ( $admin_info ['group_id'] );
		$system_log_obj->setUid ( $admin_info ['uid'] ? $admin_info ['uid'] : $_SESSION ['uid'] );
		$system_log_obj->setUsername ( $admin_info ['username'] ? $admin_info ['username'] : $_SESSION ['username'] );
		$system_log_obj->create_keke_witkey_system_log ();
	}
	/**
	 * 盖楼函数
	 * 
	 * @param $int $nodeid
	 * -- 顶级父ID的值
	 * @param array $arTree
	 * -- 数组
	 */
	static function sort_tree($nodeid, $data_arr, $pid = "indus_pid", $id = "indus_id") {
		$res = array ();
		for($i = 0; $i < sizeof ( $data_arr ); $i ++)
			if ($data_arr [$i] ["$pid"] == $nodeid) {
				array_push ( $res, $data_arr [$i] );
				$subres = self::sort_tree ( $data_arr [$i] ["$id"], $data_arr, $pid, $id );
				for($j = 0; $j < sizeof ( $subres ); $j ++)
					array_push ( $res, $subres [$j] );
			}
		return $res;
	}
	
	/**
	 * 收藏
	 * 
	 * @param string $pk
	 * 收藏对象表的主键 用于更新收藏记录用
	 * @param string $keep_type
	 * 收藏类型 task=>任务,work=>稿件,service=>商品,case=>案例,'shop'=>店铺
	 * @param string $model_code
	 * 收藏对象所属模型
	 * @param int $obj_uid
	 * 收藏对象的UID
	 * @param int $obj_id
	 * 收藏对象编号
	 * @param string $obj_name
	 * 收藏对象名称
	 * @param int $origin_id
	 * 对象源编号 eg:收藏稿件则表示任务编号
	 * @param string $url
	 * 操作提示链接 具体参见 kekezu::keke_show_msg
	 * @param string $output
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return show_msg
	 */
	public static function set_favor($pk, $keep_type, $model_code, $obj_uid, $obj_id, $obj_name, $origin_id, $url = '', $output = 'normal') {
		global $uid, $username;
		global $_lang;
		self::check_login ( $url, $output ); // 检测登录
		self::check_if_favor ( $uid, $obj_uid, $pk, $keep_type, $model_code, $obj_id, $url, $output ); // 检测收藏
		$favor_type = keke_glob_class::get_favor_type ();
		$favor_obj = new Keke_witkey_favorite_class ();
		
		$favor_obj->_f_id = NULL;
		CHARSET == 'gbk' and $obj_name = kekezu::utftogbk ( $obj_name );
		$favor_obj->setKeep_type ( $keep_type );
		$favor_obj->setObj_type ( $model_code );
		$favor_obj->setObj_id ( intval ( $obj_id ) );
		$favor_obj->setObj_name ( $obj_name );
		$favor_obj->setOrigin_id ( intval ( $origin_id ) );
		$favor_obj->setUid ( $uid );
		$favor_obj->setUsername ( $username );
		$favor_obj->setOn_date ( time () );
		
		$f_id = $favor_obj->create_keke_witkey_favorite ();
		if ($f_id) {
			if (in_array ( $keep_type, array ('service', 'task', 'shop' ) )) {
				$up_tab = TABLEPRE . "witkey_" . $keep_type;
				db_factory::execute ( sprintf ( "update %s set focus_num = focus_num+1 where %s='%d'", $up_tab, $pk, $obj_id ) );
			}
			// kekezu::update_score_value ( $this->_uid, 'collect_task', 3
			// );//更新经验值
			kekezu::keke_show_msg ( $url, $favor_type [$keep_type] . $_lang ['collection_success'], "", $output );
		} else
			kekezu::keke_show_msg ( $url, $favor_type [$keep_type] . $_lang ['collection_fail'], "error", $output );
	}
	
	/**
	 * 检测是否登录
	 * 
	 * @param string $url
	 * 操作提示链接 具体参见 kekezu::keke_show_msg
	 * @param string $output
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public static function check_login($url = 'index.php?do=login', $output = 'normal', $type = 'warning') {
		global $uid;
		global $_lang;
		if ($uid) {
			return TRUE;
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['you_not_login_not_operate'], $type, $output );
			return false;
		}
	}
	/*
	 * //登录检查 static function check_login($jump_url = 'index.php?do=login') { if
	 * ($_SESSION [uid]) { return true; } else { die ( kekezu::show_msg (
	 * '访问拒绝', $jump_url, 3, '您访问的页面需要登录' ) ); } }
	 */
	/**
	 * 检测是否收藏
	 * 
	 * @param int $uid
	 * 用户
	 * @param int $obj_uid
	 * 收藏对象的UID
	 * @param string $pk
	 * 收藏对象的主键
	 * @param string $keep_type
	 * 收藏类型 任务/稿件/店铺/案例/商品
	 * @param string $model_code
	 * 模型code
	 * @param int $obj_id
	 * 对象编号
	 * @param string $url
	 * 操作提示链接 具体参见 kekezu::keke_show_msg
	 * @param string $output
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public static function check_if_favor($uid, $obj_uid, $pk, $keep_type, $model_code, $obj_id, $url = '', $output = 'normal') {
		global $_lang;
		$favor_type = keke_glob_class::get_favor_type ();
		$favor_tab = TABLEPRE . "witkey_" . $keep_type;
		if ($obj_uid == $uid) {
			kekezu::keke_show_msg ( $url, $_lang ['you_can_not_collection_self'] . $favor_type [$keep_type] . "！", "error", $output );
		} else {
			$if_favor = db_factory::get_count ( sprintf ( " select f_id from %switkey_favorite where keep_type='%s' and obj_type='%s' and obj_id='%s' and uid='%d'", TABLEPRE, $keep_type, $model_code, $obj_id, $uid ) );
			if (! $if_favor) {
				return TRUE;
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['you_has_collection_this'] . $favor_type [$keep_type] . "," . $_lang ['no_need_continue_collection'], "error", $output );
				return false;
			}
		}
	}
	/**
	 *
	 *
	 * 获取任务金额条件
	 * 
	 * @param string $field
	 * ------字段名
	 * @param float $min_cash        	
	 * @param float $max_cash        	
	 * @return string $where --------- 一个含有and 的where条件
	 */
	
	public static function get_between_where($field, $min_cash, $max_cash) {
		$where = " and $field >$min_cash and $field<$max_cash";
		return $where;
	}
	
	/**
	 * 发送邮件
	 *
	 * @param String $time,String
	 * $title,String $body
	 * @return String Time Elapsed
	 * @author shangjinglong
	 * @copyright keke-tech
	 */
	static function send_mail($address, $title, $body) {
		global $_K, $kekezu;
		$basicconfig = $kekezu->_sys_config;
		$mail = new Phpmailer_class ();
		if ($basicconfig ['mail_server_cat'] == "smtp") {
			
			$mail->IsSMTP ();
			$mail->SMTPAuth = true;
			$mail->CharSet = strtolower ( $_K ['charset'] );
			// $mail->SMTPSecure = "tsl";
			$mail->Host = $basicconfig ['smtp_url'];
			$mail->Port = $basicconfig ['mail_server_port'];
			$mail->Username = $basicconfig ['post_account'];
			$mail->Password = base64_decode ( $basicconfig ['account_pwd'] );
		
		} else {
			$mail->IsMail ();
		}
		//$mail->IsHTML(true);
		// $mail->CharSet = $mail_charset;
		

		$mail->SetFrom ( $basicconfig ['post_account'], $basicconfig ['website_name'] );
		
		if ($basicconfig ['mail_replay'])
			$mail->AddReplyTo ( $basicconfig ['mail_replay'], $basicconfig ['website_name'] );
		
		$mail->Subject = $title;
		
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML ( kekezu::k_input ( $body ) );
		
		$mail->AddAddress ( $address, $basicconfig ['website_name'] );
		
		return $mail->Send ();
	}
	
	/**
	 * 悬赏根据金额来计算显示时间
	 *
	 * @param Float $cash        	
	 * @return String Time Elapsed
	 * @author shangjinglong
	 * @copyright keke-tech
	 */
	
	static function get_show_day($cash = 0, $model_id = '') {
		global $_K;
		// 悬赏金额时间规则
		$reward_day_rule = keke_task_config::get_time_rule ( $model_id, '3600' );
		$count = count ( $reward_day_rule );
		for($i = 0; $i <= $count; $i ++) {
			
			if ($cash >= $reward_day_rule [$i] [rule_cash] && $cash < $reward_day_rule [$i + 1] [rule_cash]) {
				return $reward_day_rule [$i] [rule_day];
			} elseif ($cash < $reward_day_rule [0] [rule_cash]) {
				return ceil ( $reward_day_rule [0] [rule_day] / 2 );
			} elseif ($cash >= $reward_day_rule [count ( $reward_day_rule ) - 1] [rule_cash]) {
				return $reward_day_rule [count ( $reward_day_rule ) - 1] [rule_day];
			}
		}
	}
	/**
	 * 获取随即客服
	 * 
	 * @return $kf_info
	 */
	static function get_rand_kf() {
		$kf_arr = kekezu::get_table_data ( '*', 'witkey_space', ' group_id = 7', '', '', '', '', null );
		$kf_arr_count = count ( $kf_arr );
		$randno = rand ( 0, $kf_arr_count - 1 );
		$kf_arr [$randno] [uid] and $kf_uid = $kf_arr [$randno] [uid] or $kf_uid = ADMIN_UID;
		$kf_info = kekezu::get_user_info ( $kf_uid );
		return $kf_info;
	}
	static function get_user_info($uid, $isusername = 0) {
		return keke_user_class::get_user_info ( $uid, $isusername );
	}
	
	static function check_user_by_name($user, $isusername = 0) {
		global $_K;
		$member_obj = new Keke_witkey_member_class ();
		if ($isusername) {
			$member_obj->setWhere ( "username='{$user}'" );
		} else {
			$member_obj->setWhere ( "uid='{$user}'" );
		}
		$user_count = $member_obj->count_keke_witkey_member ();
		return $user_count;
	}
	
	static function get_format_size($bytes) {
		$units = array (0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log ( $bytes, 1024 );
		$power = ( int ) $log;
		$size = pow ( 1024, $log - $power );
		return round ( $size, 2 ) . ' ' . $units [$power];
	
	}
	/**
	 * 将很长的数字转换成 xx万
	 * 
	 * @param int、float.. $number
	 * 数字
	 * @param string $unit
	 * 单位
	 */
	static function pretty_format($number, $unit = '') {
		global $_lang;
		$unit == '' && $unit = $_lang ['million'];
		if ($number < 10000) {
			return $number;
		}
		return ((round ( $number / 1000 )) / 10) . $unit; // round四舍五入 ceil进一法取整 floor舍去法取整
	}
	
	/**
	 *
	 *
	 * 存储feed
	 * 
	 * @param array $content_arr
	 * $feed_arr{
	 * "feed_username"=>array("content"=>"","url"=>""),
	 * "action"=>array("content"=>"","url"=>""),
	 * "event"=>array("content"=>"","url"=>""),
	 * }
	 * @param int $uid        	
	 * @param string $username        	
	 * @param string $feedtype        	
	 * @param int $obj_id        	
	 * @param string $obj_link        	
	 * @param string $icon        	
	 */
	static function save_feed($feed_arr, $uid, $username, $feedtype = "", $obj_id = 0, $obj_link = "", $icon = '') {
		$title = serialize ( $feed_arr );
		$sql = " insert into %switkey_feed (feed_id,icon,feed_time,feedtype,obj_link,obj_id,title,uid,username) 
				values('','%s','%s','%s','%s','%d','%s','%d','%s')";
		return db_factory::execute ( sprintf ( $sql, TABLEPRE, $icon, time (), $feedtype, $obj_link, $obj_id, $title, $uid, $username ) );
	}
	
	/**
	 *
	 *
	 * 获取feed信息
	 * 
	 * @param array $where_arr
	 * ---- 此数组为键值对数组
	 * @param int $limit        	
	 */
	static function get_feed($where_arr, $order, $limit) {
		$feed_arr = kekezu::get_table_data ( "*", "witkey_feed", $where_arr, $order, "", $limit, "feed_id" );
		$feed_new_arr = array ();
		foreach ( $feed_arr as $k => $v ) {
			$title_arr = unserialize ( $v ['title'] );
			// $title_arr = kekezu::gbktoutf($title_arr);
			if (is_array ( $title_arr )) {
				foreach ( $title_arr as $k1 => $v1 ) {
					$v [$k1] = $v1;
				}
			}
			
			$feed_new_arr [] = $v;
		}
		return $feed_new_arr;
	}
	
	/**
	 *
	 *
	 * 生成feed 的时间描述
	 * 
	 * @param int $feed_time
	 * -------- 时间戳
	 */
	static function feed_time($feed_time) {
		global $_lang;
		$time = time () - $feed_time;
		$time_desc = kekezu::time2Units ( $time, 'hour' );
		if ($time_desc) {
			return $_lang ['in'] . $time_desc . $_lang ['before'];
		} else {
			return $_lang ['just'];
		}
	}
	
	/**
	 * 通知
	 * 
	 * @param unknown_type $title        	
	 * @param unknown_type $content        	
	 * @param to $uid        	
	 * @param to $username        	
	 */
	static function notify_user($title, $content, $uid, $username = "") {
		if (! $username) {
			$userinfo = kekezu::get_user_info ( $uid );
			$username = $userinfo ['username'];
		}
		
		$message_obj = new Keke_witkey_msg_class ();
		$message_obj->setTitle ( $title );
		$message_obj->setContent ( $content );
		$message_obj->setOn_time ( time () );
		$message_obj->setTo_uid ( $uid );
		$message_obj->setTo_username ( $username );
		$message_obj->create_keke_witkey_msg ();
	}
	
	// 获取店铺信息
	static function get_shop_info($uid) {
		$shop_obj = new Keke_witkey_shop_class ();
		$shop_obj->setWhere ( " uid = $uid" );
		$shop_info = $shop_obj->query_keke_witkey_shop ();
		if ($shop_info) {
			return $shop_info [0];
		} else {
			return FALSE;
		}
	}
	// 删除上传的文件
	static function del_att_file($fid = 0) {
		keke_file_class::del_att_file ( $fid );
	}
	
	// secode 判断
	static function check_secode($secode) {
		global $_lang;
		$img = new Secode_class ();
		$res_code = $img->check ( $secode, 1 );
		if (! $res_code) {
			return $_lang ['verification_code_input_error'];
		} else {
			return true;
		}
	
	}
	
	public static function autoload($class_name) {
		try {
			$file1 = S_ROOT . '/model/' . $class_name . '.php';
			$file2 = S_ROOT . '/lib/inc/' . $class_name . '.php';
			$file3 = S_ROOT . '/lib/helper/' . $class_name . '.php';
			$file4 = S_ROOT . '/lib/sys/' . $class_name . '.php';
			if (is_file ( $file1 )) {
				self::keke_require_once ( $file1, $class_name );
				return class_exists ( $file1, false ) || interface_exists ( $file1, false );
			} elseif (is_file ( $file2 )) {
				self::keke_require_once ( $file2, $class_name );
				return class_exists ( $file2, false ) || interface_exists ( $file2, false );
			} elseif (is_file ( $file3 )) {
				self::keke_require_once ( $file3, $class_name );
				return class_exists ( $file3, false ) || interface_exists ( $file3, false );
			} elseif (is_file ( $file4 )) {
				self::keke_require_once ( $file4, $class_name );
				return class_exists ( $file4, false ) || interface_exists ( $file4, false );
			}
			self::keke_require_once ( S_ROOT . '/base/db_factory/db_factory.php', 'db_facotry' );
			
			global $i_model, $_K, $kekezu;
			
			if (! $i_model && isset ( $kekezu->_model_list )) {
				$model_arr = $kekezu->_model_list;
				foreach ( $model_arr as $value ) {
					$dir = $value ['model_code'];
					$type = $value ['model_type'];
					$f1 = S_ROOT . '/' . $type . '/' . $dir . '/lib/' . $class_name . '.php';
					$f2 = S_ROOT . '/' . $type . '/' . $dir . '/model/' . $class_name . '.php';
					if (file_exists ( $f1 )) {
						self::keke_require_once ( $f1, $class_name );
						return class_exists ( $f1, false ) || interface_exists ( $f1, false );
					}
					if (file_exists ( $f2 )) {
						self::keke_require_once ( $f2, $class_name );
						return class_exists ( $f2, false ) || interface_exists ( $f2, false );
					}
				}
				$auth_item = self::get_table_data ( 'auth_code,auth_dir', 'witkey_auth_item', '', 'listorder asc ', '', '', 'auth_code', null );
				foreach ( $auth_item as $v ) {
					$auth_dir = $v ['auth_dir'];
					$f3 = S_ROOT . '/auth/' . $auth_dir . '/lib/' . $class_name . '.php';
					if (file_exists ( $f3 )) {
						self::keke_require_once ( $f3, $class_name );
						return class_exists ( $f3, false ) || interface_exists ( $f3, false );
					}
				}
			}
		} catch ( Exception $e ) {
			keke_exception::handler ( $e );
		}
		return true;
	}
	/**
	 * 消息提示方法
	 * 
	 * @param string $content
	 * 内容 （以json输出时表示$data）
	 * @param string $url
	 * 跳转链接 （以json输出时无实际意义）
	 * @param string $type
	 * 提示类型 （error/正常 json输出时error表示status='0' 为空表示1）
	 * @param string $output
	 * 输出类型 （消息提示格式 json/show_msg）
	 */
	public static function keke_show_msg($url, $content, $type = 'success', $output = 'normal') {
		global $_lang;
		switch ($output) {
			case "normal" :
				$type == 'success' or $type = 'warning';
				kekezu::show_msg ( $_lang ['operate_notice'], $url, '3', $content, $type );
				break;
			case "json" :
				
				$type == 'error' or $status = '1'; // 非错误提示,即正确
				kekezu::echojson ( $_lang ['operate_notice'], intval ( $status ), $content );
				die ();
				break;
		}
	}
	
	public static function register_autoloader($callback = null) {
		spl_autoload_unregister ( array ('keke_core_class', 'autoload' ) );
		isset ( $callback ) and spl_autoload_register ( $callback );
		spl_autoload_register ( array ('keke_core_class', 'autoload' ) );
	}
	public static function keke_require_once($filename, $class_name = null) {
		isset ( $GLOBALS ['class'] [$filename] ) or (($GLOBALS ['class'] [$filename] = 1) and require $filename);
	}
	public static function get_config($configtype) {
		$v = "Keke_witkey_{$configtype}_config_class";
		$q = "query_keke_witkey_{$configtype}_config";
		$config_obj = new $v ();
		$config_arr = $config_obj->$q ( 1, null );
		return $config_arr [0];
	}
	/**
	 * $fileds,$where可以为数组 , $pk为@return数组的key , 对db_factory -> select()的改进,添加缓存
	 * 
	 * @return array($pk => data)
	 */
	public static function get_table_data($fileds = '*', $table, $where = '', $order = '', $group = '', $limit = '', $pk = '', $cachetime = 0) {
		return db_factory::get_table_data ( $fileds, $table, $where, $order, $group, $limit, $pk, $cachetime );
	}
	
	public static function get_task_config($model_id = '') {
		global $kekezu;
		if ($model_id) {
			$where = " where model_id= '$model_id' ";
		}
		$model_config = db_factory::query ( ' select model_id,config from ' . TABLEPRE . "witkey_model $where ", true, 60 * 20 );
		if ($model_id) {
			
			$m_config = unserialize ( $model_config [0] ['config'] );
			if ($m_config) {
				$model_config [0] = array_merge ( $model_config [0], $m_config );
			}
			return $model_config [0];
		} else {
			$temp = array ();
			foreach ( $model_config as $mod ) {
				if (is_array ( $mod ) && is_array ( $mod ['config'] )) {
					$temp [$mod ['model_id']] = array_merge ( $mod, unserialize ( $mod ['config'] ) );
				}
			}
			return $temp;
		}
	}
	public static function get_pay_item() {
		global $kekezu;
		$pay_item = $kekezu->_cache_obj->get ( "task_pay_item" );
		if (! $pay_item) {
			$pay_item = array ();
			$item = db_factory::query ( " select a.*,b.model_id from " . TABLEPRE . "witkey_pay_item as a left " . TABLEPRE . "witkey_model as b on a.model_code =b.model_dir" );
			foreach ( $item as $v ) {
				$pay_item [$v [model_code]] [$v [pay_item_id]] = $v;
			}
			$kekezu->_cache_obj->set ( "task_pay_item", $pay_item );
		}
		return $pay_item;
	}
	public static function get_payment_config($paymentname = "", $pay_type = 'online', $pay_status = null) {
		if ($paymentname) {
			if ($pay_type != 'offline') {
				if (! file_exists ( S_ROOT . "/payment/" . $paymentname . "/pay_config.php" )) {
					return FALSE;
				} else {
					require_once S_ROOT . "/payment/" . $paymentname . "/pay_config.php";
				}
			}
			// var_dump($tenpay_basic);
			$list = kekezu::get_table_data ( '*', "witkey_pay_api", "payment='$paymentname' and type='$pay_type'", "", '', '', '', null );
			if ($list) {
				$pay_config = $pay_basic;
				$pay_config ['payment'] = $list [0] ['payment'];
				$pay_config ['config'] = $list [0] ['config'];
				$pay_config ['type'] = $list [0] ['type'];
				$config = unserialize ( $pay_config ['config'] );
				$config and $pay_config = array_merge ( $pay_config, $config );
				$list = $pay_config;
				if (isset ( $pay_status )) {
					if ($list ['pay_status'] == intval ( $pay_status )) {
						return $list;
					}
				} else {
					return $list;
				}
			}
		} else {
			if ($pay_type == 'offline') {
				$list = kekezu::get_table_data ( 'payment', "witkey_pay_api", " type='offline'", '', '', '', '', null );
				$i = 0;
				while ( list ( $k, $v ) = each ( $list ) ) {
					$paymentlist [$v ['payment']] = self::get_payment_config ( $v ['payment'], $pay_type, $pay_status );
					$i = $i + 1;
				}
			} else {
				$filepath = S_ROOT . "/payment";
				$handle = opendir ( $filepath );
				$i = 0;
				while ( $file = readdir ( $handle ) ) {
					if (($file != ".") and ($file != ".." and file_exists ( S_ROOT . "/payment/" . $file . "/pay_config.php" ))) {
						$paymentlist [$file] = self::get_payment_config ( $file, $pay_type, $pay_status );
						$i = $i + 1;
					}
				}
				closedir ( $handle );
			}
			return array_filter ( $paymentlist );
		}
	}
	public static function get_config_rule($ruletype, $nokey = '') {
		global $kekezu;
		return kekezu::get_table_data ( "*", $ruletype, "", "", "", "", "", $nokey, null );
	
	}
	
	public static function get_industry($pid = NULL, $cache = NULL) {
		
		! is_null ( $pid ) and $where = " indus_pid = '" . intval ( $pid ) . "'";
		
		$indus_arr = self::get_table_data ( '*', "witkey_industry", $where, "listorder", '', '', 'indus_id', $cache );
		
		return $indus_arr;
	
	}
	
	public static function get_indus_by_index($indus_type = "1", $pid = NULL) {
		global $kekezu;
		$indus_index_arr = $kekezu->_cache_obj->get ( 'indus_index_arr' . $indus_type . '_' . $pid );
		if (! $indus_index_arr) {
			$indus_arr = kekezu::get_industry ( $pid );
			$indus_index_arr = array ();
			foreach ( $indus_arr as $indus ) {
				$indus_index_arr [$indus ['indus_pid']] [$indus ['indus_id']] = $indus;
			}
			$kekezu->_cache_obj->set ( 'indus_index_arr' . $indus_type . '_' . $pid, $indus_index_arr, 3600 );
		}
		return $indus_index_arr;
	}
	
	public static function get_cash_cove($model_code = 'tender') {
		return self::get_table_data ( '*', "witkey_task_cash_cove", " model_code ='$model_code'", "start_cove", '', '', 'cash_rule_id', null );
	}
	
	/**
	 * 获取附件类型
	 */
	public static function get_ext_type() {
		global $kekezu;
		$basic_config = $kekezu->_sys_config;
		$flie_types = explode ( '|', $basic_config ['file_type'] );
		
		foreach ( $flie_types as $k => $v ) {
			$k and $ext .= ";";
			$ext .= '*.' . $v;
		}
		return $ext;
	}
	public static function get_skill() {
		global $kekezu;
		$skill_arr = $kekezu->_cache_obj->get ( "keke_witkey_skill" );
		if (! $skill_arr) {
			$indus_arr = $kekezu->_indus_arr;
			$skill_obj = new Keke_witkey_skill_class ();
			$skill_obj->setWhere ( ' 1 = 1 order by listorder asc ' );
			$skill_arr = $skill_obj->query_keke_witkey_skill ();
			$temparr = array ();
			foreach ( $skill_arr as $inarr ) {
				$indus_id = $inarr ['indus_id'];
				$indus_pid = $indus_arr [$inarr ['indus_id']] ['indus_pid'];
				$indus_pid > 0 and $temparr [$indus_pid] [] = $inarr or $temparr [$indus_id] [] = $inarr;
			}
			$skill_arr = $temparr;
			$kekezu->_cache_obj->set ( "keke_witkey_skill", $skill_arr, 3600 );
		}
		return $skill_arr;
	}
	public static function get_tpl() {
		$sql = sprintf ( "select tpl_title,tpl_pic from %switkey_template where is_selected = '1' limit 1", TABLEPRE );
		
		$res = db_factory::get_one ( $sql, null );
		//var_dump ( $res );
		die ();
		return $res;
	}
	public static function get_tag($mode = '') {
		$tag_obj = new Keke_witkey_tag_class ();
		$taginfo = $tag_obj->query_keke_witkey_tag ( 1, null );
		
		$temp_arr = array ();
		if (! $mode) {
			foreach ( $taginfo as $tag ) {
				$temp_arr [$tag ['tagname']] = $tag;
			}
			$taginfo = $temp_arr;
		} else if ($mode == 1) {
			foreach ( $taginfo as $tag ) {
				$temp_arr [$tag ['tag_id']] = $tag;
			}
			$taginfo = $temp_arr;
		}
		return $taginfo;
	}
	static function get_ad($adname = null, $limit_num = null) {
		is_null ( $adname ) or $where = "and target_id ='$adname'";
		$limit_num > 0 and $limit = $limit_num;
		return self::get_table_data ( '*', 'witkey_ad', '1=1  and is_allow =1 ' . $where, 'listorder', '', $limit, '', 3600 );
	}
	
	static function execute_time() {
		if (function_exists ( 'xdebug_time_index' )) {
			$ex_time = xdebug_time_index ();
		} else {
			$stime = explode ( ' ', SYS_START_TIME );
			$etime = explode ( ' ', microtime () );
			$ex_time = number_format ( ($etime [1] + $etime [0] - $stime [1] - $stime [0]), 6 );
		}
		return $ex_time;
	
	}
	
	static function lang($key) {
		return keke_lang_class::lang ( $key );
	}
	
	// 获取用户最后操作时间
	static function update_oltime() {
		global $_K, $kekezu;
		$res = null;
		$login_uid = $kekezu->_uid;
		$user_oltime = db_factory::get_one ( sprintf ( "select last_op_time from %switkey_member_oltime where uid = '%d'", TABLEPRE, $login_uid ) );
		if ((SYS_START_TIME - $user_oltime ['last_op_time']) > $_K ['timespan']) {
			$res = db_factory::execute ( sprintf ( "update %switkey_member_oltime set online_total_time = online_total_time+%d,last_op_time = '%d' where uid = '%d'", TABLEPRE, $_K ['timespan'], SYS_START_TIME, $login_uid ) );
		}
		return $res;
	}
	
	/**
	 *
	 *
	 * 判断用户是否在线
	 * 
	 * @param unknown_type $uid        	
	 */
	static function get_user_online($uid) {
		$user_oltime = db_factory::get_one ( sprintf ( "select last_op_time from %switkey_member_oltime where uid = '%d'", TABLEPRE, $uid ) );
		if ((SYS_START_TIME - $user_oltime ['last_op_time']) > 1200) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 错误监听
	 */
	static function error_handler($code, $error, $file = NULL, $line = NULL) {
		if (error_reporting () && $code !== 8) {
			ob_get_level () and ob_clean ();
			keke_exception::handler ( new ErrorException ( $error, $code, 0, $file, $line ) );
		}
		return TRUE;
	}
	/**
	 * 异常监听
	 */
	static function shutdown_handler() {
		if (KEKE_DEBUG and $error = error_get_last () and in_array ( $error ['type'], array (E_PARSE, E_ERROR, E_USER_ERROR ) )) {
			ob_get_level () and ob_clean ();
			keke_exception::handler ( new ErrorException ( $error ['message'], $error ['type'], 0, $error ['file'], $error ['line'] ) );
			exit ( 1 );
		}
	}
	
	/**
	 * 限制IP
	 */
	static function limit_ip($ip) {
		global $_K;
		$this->_basic_arr = $basic_arr = db_factory::query ( 'select config_id,k,v,type,listorder from ' . TABLEPRE . 'witkey_basic_config', 1, null );
		$config_arr = array ();
		$size = sizeof ( $basic_arr );
		for($i = 0; $i < $size; $i ++) {
			$config_arr [$basic_arr [$i] ['k']] = $basic_arr [$i] ['v'];
		}
		$_K ['limit_ip'] = $config_arr ['limit_ip'];
		$current_ip = kekezu::get_ip ();
		if (! $_K ['limit_ip'] != $current_ip) {
			kekezu::show_msg ( "IP被限制，暂时无法访问！", ' ', 10, "您IP被限制或IP不在服务范围内请联系站点管理员", 'warning' );
			exit ( 1 );
		}
	}
	
	/**
	 * 检查用户信息是否完善
	 */
	static function check_user_info() {
		global $user_info;
		if ($user_info ['truename']) {
			$i += 1;
		}
		if ($user_info ['residency'] || $user_info ['address'] || $user_info ['point']) {
			$i += 1;
		}
		if ($user_info ['mobile']) {
			$i += 1;
		}
		if ($user_info ['qq']) {
			$i += 1;
		}
		if ($user_info ['email']) {
			$i += 1;
		}
		if ($i < 3) {
			kekezu::show_msg ( "操作提示！", "index.php?do=user&view=setting&op=basic", 1, "请完善个人资料！", 'warning' );
		}
	}
	
	static function get_City($province) {
		global $_K;
		for($x = 0; $x <= 34; $x ++) {
			if ($_K ['arr_provinces'] [$x] [0] == $province) {
				$city_arr = $_K ['arr_provinces'] [$x] [1];
			}
		}
		return $city_arr;
	}
	
	static function get_Area($province, $city) {
		global $_K;
		for($x = 0; $x <= 34; $x ++) {
			if ($_K ['arr_provinces'] [$x] [0] == $province) {
				for($y = 0; $y <= count ( $_K ['arr_provinces'] [$x] [1] ); $y ++) {
					if ($_K ['arr_provinces'] [$x] [1] [$y] == $city) {
						$area_arr = $_K ['arr_provinces'] [$x] [2] [$y];
					}
				}
			}
		}
		return $area_arr;
	}
	
	//友情链接位置运算(解)
	static function link_parse_tags($tag) {
		$tag = intval ( $tag );
		$tags = array ();
		for($i = 1; $i <= 12; $i ++) {
			$k = pow ( 2, $i - 1 );
			$tags [$i] = ($tag & $k) ? 1 : 0;
		}
		
		return $tags;
	}
	
	//友情链接位置运算(计算)
	static function link_make_tag($tags) {
		$tags = ( array ) $tags;
		$tag = 0;
		for($i = 1; $i <= 12; $i ++) {
			if (! empty ( $tags [$i] )) {
				$tag += pow ( 2, $i - 1 );
			}
		}
		
		return $tag;
	}
	
	//获取当前自己订阅的邮箱地址
	static function get_task_subscribe_email($uid = '') {
		if (! $uid)
			return false;
		$subscribe_info = db_factory::get_one ( " select email from " . TABLEPRE . "witkey_task_subscribe where uid='$uid'" );
		
		return $subscribe_info ['email'];
	}
	
	//查询数据
	static function list_search($list, $condition, $row = '') {
		if (is_string ( $condition ))
			parse_str ( $condition, $condition );
		
		$resultSet = array ();
		foreach ( $list as $key => $data ) {
			$find = false;
			foreach ( $condition as $field => $value ) {
				if (isset ( $data [$field] )) {
					if (0 === strpos ( $value, '/' )) {
						$find = preg_match ( $value, $data [$field] );
					} elseif ($data [$field] == $value) {
						$find = true;
					}
				}
			}
			if ($find) {
				if ($row == 1) {
					$resultSet = &$list [$key];
					break;
				} else {
					$resultSet [] = &$list [$key];
				}
			
			}
		
		}
		
		return $resultSet;
	}
	
	/*
	 * 获取感兴趣任务
	 * @param $indus_pid 父行业编号
	 */
	static function get_own_interest($task_id, $indus_pid) {
		$where = ' task_id !=' . $task_id . ' and task_status=2';
		$run ['inter'] = db_factory::get_table_data ( '*', 'witkey_task', $where . ' and indus_pid = ' . $indus_pid, 'start_time desc', '', '0,5' );
		//最新
		$run ['istop'] = db_factory::get_table_data ( '*', 'witkey_task', $where, 'start_time desc', '', '0,5' );
		//重金
		$run ['heavy'] = db_factory::get_table_data ( '*', 'witkey_task', $where . ' and task_cash>=200', 'task_cash desc', '', '0,5' );
		//var_dump($run);die;
		return $run;
	}
	/*static function get_task_subscribe_email(){
		global $uid;
		return db_factory::get_count(sprintf("select id from %switkey_task_subscribe where uid=%d",TABLEPRE,$uid));
	}*/
	
	public static function search_word_format($keyword) {
		global $_K;
		//查询过滤
		$keyword = trim ( str_replace ( '|', '', $keyword ) );
		if (! $keyword) {
			return false;
		}
		
		$cache_obj = new keke_cache_class ( CACHE_TYPE, $_K ['cache_config'] );
		$temp_indus_str = $cache_obj->get ( 'talent_indushash_cache' );
		
		if (! $temp_indus_str) {
			$indus_arr = kekezu::get_industry ();
			$temp_indus_arr = array ();
			foreach ( $indus_arr as $v ) {
				$temp_indus_arr [] = "{$v['indus_name']}={$v['indus_id']}";
			}
			$temp_indus_str = implode ( "|", $temp_indus_arr );
			$cache_obj->set ( 'talent_indushash_cache', $temp_indus_str );
		}
		
		$r = array ();
		
		//寻找匹配点
		$f_point = strpos ( " $temp_indus_str", $keyword ); //追加空格以
		if ($f_point) {
			//以关键词为基准切割
			$test_arr = explode ( $keyword, $temp_indus_str );
			for($i = 0; $i < (count ( $test_arr ) - 1); $i ++) {
				$pre_str = $test_arr [$i];
				//分隔符截取
				if (strpos ( " $pre_str", '|' )) {
					$pre_str_t = explode ( '|', $pre_str );
					$pre_str = $pre_str_t [count ( $pre_str_t ) - 1];
				}
				$nex_str = $test_arr [$i + 1];
				
				if (strpos ( " $nex_str", '|' )) {
					$nex_str_t = explode ( '|', $nex_str );
					$nex_str = $nex_str_t [0];
				}
				$result = $pre_str . $keyword . $nex_str;
				$result = explode ( '=', $result );
				$result = $result [1];
				$r [] = $result;
			}
			
			return implode ( ',', $r );
		} else {
			return false;
		}
	}
	
	/*
	 * utf8方式计算字符串长度
	 */
	public static function strlen_utf8($str) {
		$i = 0;
		$count = 0;
		$len = strlen ($str);
		while ($i < $len) {
			$chr = ord ($str[$i]);
			$count++;
			$i++;
			if($i >= $len) break;
			if($chr & 0x80) {
				$chr <<= 1;
				while ($chr & 0x80) {
					$i++;
					$chr <<= 1;
				}
			}
		}
		return $count;
	}

}
class kekezu extends keke_core_class {
	public $_inited = false;
	public $_sys_config;
	public $_basic_arr;
	public $_uid;
	public $_username;
	public $_userinfo;
	public $_template;
	public $_model_list;
	public $_nav_list;
	public $_user_group;
	public $_tpl_obj;
	public $_cache_obj;
	public $_page_obj;
	public $_session_obj;
	public $_mark;
	public $_finance;
	public $_tag;
	public $_messagecount;
	public $_indus_p_arr;
	public $_indus_c_arr;
	public $_indus_arr;
	public $_service_indus_p_arr;
	public $_service_indus_c_arr;
	public $_service_indus_arr;
	public $_prom_obj;
	public $_weibo_list;
	public $_api_open;
	public $_lang;
	public $_lang_list;
	public $_style_path;
	public $_weibo_attent;
	public $_attent_api_open;
	public $_is_allow_fxx = 1;
	public $_website_session;
	public $_arr_provinces;
	
	public static function &get_instance() {
		static $obj = null;
		if ($obj == null) {
			$obj = new kekezu ();
		}
		return $obj;
	}
	function __construct() {
		$this->init ();
		keke_lang_class::loadlang ( 'public', 'public' );
	}
	
	function init() {
		global $close_allow_fxx, $_K, $_lang;
		
		define ( "S_ROOT", substr ( dirname ( __FILE__ ), 0, - 7 ) );
		include (S_ROOT . '/config/config.inc.php');
		include (S_ROOT . '/config/keke_version.php');
		include (S_ROOT . '/lib/sys/keke_debug.php');
		if (! $this->_inited) {
			$this->init_session ();
			$this->init_config ();
			$this->init_user ();
			$this->init_area ();
			$this->_cache_obj = new keke_cache_class ( CACHE_TYPE, $_K ['cache_config'] );
			
			$this->_tpl_obj = new keke_tpl_class ();
			$this->_page_obj = new keke_page_class ();
			// $this->_tag = kekezu::get_tag ();
			$close_allow_fxx == 1 or $this->init_out_put ();
			$this->init_model ();
			$this->init_industry ();
			$this->init_oauth ();
			$this->init_lang ();
			$this->init_weibo_attent ();
			if ($this->_sys_config ['is_close'] == 1 && substr ( $_SERVER ['PHP_SELF'], - 24 ) != '/control/admin/index.php') {
				kekezu::show_msg ( $_lang ['site_is_close_notice'], 'index.php', 20, $_lang ['site_close_reason_notice'] . $this->_sys_config ['close_reason'] . '！', 'warning' );
			}
		}
		$this->_inited = true;
	}
	
	function init_area() {
		global $_K;
		$arr_provinces [0] = array ("上海市", array ("市辖区", "市辖县" ), array ("黄浦区,卢湾区,徐汇区,长宁区,静安区,普陀区,闸北区,虹口区,杨浦区,闵行区,宝山区,嘉定区,浦东新区,金山区,松江区,青浦区,南汇区,奉贤区", "崇明县" ) );
		$arr_provinces [1] = array ("北京市", array ("市辖区", "市辖县" ), array ("东城区,西城区,崇文区,宣武区,朝阳区,丰台区,石景山区,海淀区,门头沟区,房山区,通州区,顺义区,昌平区,大兴区,怀柔区,平谷区", "密云县,延庆县" ) );
		$arr_provinces [2] = array ("天津市", array ("市辖区", "市辖县" ), array ("和平区,河东区,河西区,南开区,河北区,红桥区,塘沽区,汉沽区,大港区,东丽区,西青区,津南区,北辰区,武清区,宝坻区", "宁河县,静海县,蓟县" ) );
		$arr_provinces [3] = array ("河北省", array ("石家庄市", "唐山市", "秦皇岛市", "邯郸市", "邢台市", "保定市", "张家口市", "承德市", "沧州市", "廊坊市", "衡水市" ), array ("市辖区,长安区,桥东区,桥西区,新华区,井陉矿区,裕华区,井陉县,正定县,栾城县,行唐县,灵寿县,高邑县,深泽县,赞皇县,无极县,平山县,元氏县,赵县,辛集市,藁城市,晋州市,新乐市,鹿泉市", "市辖区,路南区,路北区,古冶区,开平区,丰南区,丰润区,滦县,滦南县,乐亭县,迁西县,玉田县,唐海县,遵化市,迁安市", "市辖区,海港区,山海关区,北戴河区,青龙满族自治县,昌黎县,抚宁县,卢龙县", "市辖区,邯山区,丛台区,复兴区,峰峰矿区,邯郸县,临漳县,成安县,大名县,涉县,磁县,肥乡县,永年县,邱县,鸡泽县,广平县,馆陶县,魏县,曲周县,武安市", "市辖区,桥东区,桥西区,邢台县,临城县,内丘县,柏乡县,隆尧县,任县,南和县,宁晋县,巨鹿县,新河县,广宗县,平乡县,威县,清河县,临西县,南宫市,沙河市", "市辖区,新市区,北市区,南市区,满城县,清苑县,涞水县,阜平县,徐水县,定兴县,唐县,高阳县,容城县,涞源县,望都县,安新县,易县,曲阳县,蠡县,顺平县,博野县,雄县,涿州市,定州市,安国市,高碑店市", "市辖区,桥东区,桥西区,宣化区,下花园区,宣化县,张北县,康保县,沽源县,尚义县,蔚县,阳原县,怀安县,万全县,怀来县,涿鹿县,赤城县,崇礼县", "市辖区,双桥区,双滦区,鹰手营子矿区,承德县,兴隆县,平泉县,滦平县,隆化县,丰宁满族自治县,宽城满族自治县,围场满族蒙古族自治县", "市辖区,新华区,运河区,沧县,青县,东光县,海兴县,盐山县,肃宁县,南皮县,吴桥县,献县,孟村回族自治县,泊头市,任丘市,黄骅市,河间市", "市辖区,安次区,广阳区,固安县,永清县,香河县,大城县,文安县,大厂回族自治县,霸州市,三河市", "市辖区,桃城区,枣强县,武邑县,武强县,饶阳县,安平县,故城县,景县,阜城县,冀州市,深州市" ) );
		$arr_provinces [4] = array ("山西省", array ("太原市", "大同市", "阳泉市", "长治市", "晋城市", "朔州市", "晋中市", "运城市", "忻州市", "临汾市", "吕梁市" ), array ("市辖区,小店区,迎泽区,杏花岭区,尖草坪区,万柏林区,晋源区,清徐县,阳曲县,娄烦县,古交市", "市辖区,城区,矿区,南郊区,新荣区,阳高县,天镇县,广灵县,灵丘县,浑源县,左云县,大同县", "市辖区,城区,矿区,郊区,平定县,盂县", "市辖区,城区,郊区,长治县,襄垣县,屯留县,平顺县,黎城县,壶关县,长子县,武乡县,沁县,沁源县,潞城市", "市辖区,城区,沁水县,阳城县,陵川县,泽州县,高平市", "市辖区,朔城区,平鲁区,山阴县,应县,右玉县,怀仁县", "市辖区,榆次区,榆社县,左权县,和顺县,昔阳县,寿阳县,太谷县,祁县,平遥县,灵石县,介休市", "市辖区,盐湖区,临猗县,万荣县,闻喜县,稷山县,新绛县,绛县,垣曲县,夏县,平陆县,芮城县,永济市,河津市", "市辖区,忻府区,定襄县,五台县,代县,繁峙县,宁武县,静乐县,神池县,五寨县,岢岚县,河曲县,保德县,偏关县,原平市", "市辖区,尧都区,曲沃县,翼城县,襄汾县,洪洞县,古县,安泽县,浮山县,吉县,乡宁县,大宁县,隰县,永和县,蒲县,汾西县,侯马市,霍州市", "市辖区,离石区,文水县,交城县,兴县,临县,柳林县,石楼县,岚县,方山县,中阳县,交口县,孝义市,汾阳市" ) );
		$arr_provinces [5] = array ("内蒙古自治区", array ("呼和浩特市", "包头市", "乌海市", "赤峰市", "通辽市", "鄂尔多斯市", "呼伦贝尔市", "巴彦淖尔市", "乌兰察布市", "兴安盟", "锡林郭勒盟", "阿拉善盟" ), array ("市辖区,新城区,回民区,玉泉区,赛罕区,土默特左旗,托克托县,和林格尔县,清水河县,武川县", "市辖区,东河区,昆都仑区,青山区,石拐区,白云矿区,九原区,土默特右旗,固阳县,达尔罕茂明安联合旗", "市辖区,海勃湾区,海南区,乌达区", "市辖区,红山区,元宝山区,松山区,阿鲁科尔沁旗,巴林左旗,巴林右旗,林西县,克什克腾旗,翁牛特旗,喀喇沁旗,宁城县,敖汉旗", "市辖区,科尔沁区,科尔沁左翼中旗,科尔沁左翼后旗,开鲁县,库伦旗,奈曼旗,扎鲁特旗,霍林郭勒市", "东胜区,达拉特旗,准格尔旗,鄂托克前旗,鄂托克旗,杭锦旗,乌审旗,伊金霍洛旗", "市辖区,海拉尔区,阿荣旗,莫力达瓦达斡尔族自治旗,鄂伦春自治旗,鄂温克族自治旗,陈巴尔虎旗,新巴尔虎左旗,新巴尔虎右旗,满洲里市,牙克石市,扎兰屯市,额尔古纳市,根河市", "市辖区,临河区,五原县,磴口县,乌拉特前旗,乌拉特中旗,乌拉特后旗,杭锦后旗", "市辖区,集宁区,卓资县,化德县,商都县,兴和县,凉城县,察哈尔右翼前旗,察哈尔右翼中旗,察哈尔右翼后旗,四子王旗,丰镇市", "乌兰浩特市,阿尔山市,科尔沁右翼前旗,科尔沁右翼中旗,扎赉特旗,突泉县", "二连浩特市,锡林浩特市,阿巴嘎旗,苏尼特左旗,苏尼特右旗,东乌珠穆沁旗,西乌珠穆沁旗,太仆寺旗,镶黄旗,正镶白旗,正蓝旗,多伦县", "阿拉善左旗,阿拉善右旗,额济纳旗" ) );
		$arr_provinces [6] = array ("辽宁省", array ("沈阳市", "大连市", "鞍山市", "抚顺市", "本溪市", "丹东市", "锦州市", "营口市", "阜新市", "辽阳市", "盘锦市", "铁岭市", "朝阳市", "葫芦岛市" ), array ("市辖区,和平区,沈河区,大东区,皇姑区,铁西区,苏家屯区,东陵区,新城子区,于洪区,辽中县,康平县,法库县,新民市", "市辖区,中山区,西岗区,沙河口区,甘井子区,旅顺口区,金州区,长海县,瓦房店市,普兰店市,庄河市", "市辖区,铁东区,铁西区,立山区,千山区,台安县,岫岩满族自治县,海城市", "市辖区,新抚区,东洲区,望花区,顺城区,抚顺县,新宾满族自治县,清原满族自治县", "市辖区,平山区,溪湖区,明山区,南芬区,本溪满族自治县,桓仁满族自治县", "市辖区,元宝区,振兴区,振安区,宽甸满族自治县,东港市,凤城市", "市辖区,古塔区,凌河区,太和区,黑山县,义县,凌海市,北宁市", "市辖区,站前区,西市区,鲅鱼圈区,老边区,盖州市,大石桥市", "市辖区,海州区,新邱区,太平区,清河门区,细河区,阜新蒙古族自治县,彰武县", "市辖区,白塔区,文圣区,宏伟区,弓长岭区,太子河区,辽阳县,灯塔市", "市辖区,双台子区,兴隆台区,大洼县,盘山县", "市辖区,银州区,清河区,铁岭县,西丰县,昌图县,调兵山市,开原市", "市辖区,双塔区,龙城区,朝阳县,建平县,喀喇沁左翼蒙古族自治县,北票市,凌源市", "市辖区,连山区,龙港区,南票区,绥中县,建昌县,兴城市" ) );
		$arr_provinces [7] = array ("吉林省", array ("长春市", "吉林市", "四平市", "辽源市", "通化市", "白山市", "松原市", "白城市", "延边朝鲜族自治州" ), array ("市辖区,南关区,宽城区,朝阳区,二道区,绿园区,双阳区,农安县,九台市,榆树市,德惠市", "市辖区,昌邑区,龙潭区,船营区,丰满区,永吉县,蛟河市,桦甸市,舒兰市,磐石市", "市辖区,铁西区,铁东区,梨树县,伊通满族自治县,公主岭市,双辽市", "市辖区,龙山区,西安区,东丰县,东辽县", "市辖区,东昌区,二道江区,通化县,辉南县,柳河县,梅河口市,集安市", "市辖区,八道江区,抚松县,靖宇县,长白朝鲜族自治县,江源县,临江市", "市辖区,宁江区,前郭尔罗斯蒙古族自治县,长岭县,乾安县,扶余县", "市辖区,洮北区,镇赉县,通榆县,洮南市,大安市", "延吉市,图们市,敦化市,珲春市,龙井市,和龙市,汪清县,安图县" ) );
		$arr_provinces [8] = array ("黑龙江省", array ("哈尔滨市", "齐齐哈尔市", "鸡西市", "鹤岗市", "双鸭山市", "大庆市", "伊春市", "佳木斯市", "七台河市", "牡丹江市", "黑河市", "绥化市", "大兴安岭地区" ), array ("市辖区,道里区,南岗区,道外区,香坊区,动力区,平房区,松北区,呼兰区,依兰县,方正县,宾县,巴彦县,木兰县,通河县,延寿县,阿城市,双城市,尚志市,五常市", "市辖区,龙沙区,建华区,铁锋区,昂昂溪区,富拉尔基区,碾子山区,梅里斯达斡尔族区,龙江县,依安县,泰来县,甘南县,富裕县,克山县,克东县,拜泉县,讷河市", "市辖区,鸡冠区,恒山区,滴道区,梨树区,城子河区,麻山区,鸡东县,虎林市,密山市", "市辖区,向阳区,工农区,南山区,兴安区,东山区,兴山区,萝北县,绥滨县", "市辖区,尖山区,岭东区,四方台区,宝山区,集贤县,友谊县,宝清县,饶河县", "市辖区,萨尔图区,龙凤区,让胡路区,红岗区,大同区,肇州县,肇源县,林甸县,杜尔伯特蒙古族自治县", "市辖区,伊春区,南岔区,友好区,西林区,翠峦区,新青区,美溪区,金山屯区,五营区,乌马河区,汤旺河区,带岭区,乌伊岭区,红星区,上甘岭区,嘉荫县,铁力市", "市辖区,永红区,向阳区,前进区,东风区,郊区,桦南县,桦川县,汤原县,抚远县,同江市,富锦市", "市辖区,新兴区,桃山区,茄子河区,勃利县", "市辖区,东安区,阳明区,爱民区,西安区,东宁县,林口县,绥芬河市,海林市,宁安市,穆棱市", "市辖区,爱辉区,嫩江县,逊克县,孙吴县,北安市,五大连池市", "市辖区,北林区,望奎县,兰西县,青冈县,庆安县,明水县,绥棱县,安达市,肇东市,海伦市", "呼玛县,塔河县,漠河县" ) );
		$arr_provinces [9] = array ("江苏省", array ("南京市", "无锡市", "徐州市", "常州市", "苏州市", "南通市", "连云港市", "淮安市", "盐城市", "扬州市", "镇江市", "泰州市", "宿迁市" ), array ("市辖区,玄武区,白下区,秦淮区,建邺区,鼓楼区,下关区,浦口区,栖霞区,雨花台区,江宁区,六合区,溧水县,高淳县", "市辖区,崇安区,南长区,北塘区,锡山区,惠山区,滨湖区,江阴市,宜兴市", "市辖区,鼓楼区,云龙区,九里区,贾汪区,泉山区,丰县,沛县,铜山县,睢宁县,新沂市,邳州市", "市辖区,天宁区,钟楼区,戚墅堰区,新北区,武进区,溧阳市,金坛市", "市辖区,沧浪区,平江区,金阊区,虎丘区,吴中区,相城区,常熟市,张家港市,昆山市,吴江市,太仓市", "市辖区,崇川区,港闸区,海安县,如东县,启东市,如皋市,通州市,海门市", "市辖区,连云区,新浦区,海州区,赣榆县,东海县,灌云县,灌南县", "市辖区,清河区,楚州区,淮阴区,清浦区,涟水县,洪泽县,盱眙县,金湖县", "市辖区,亭湖区,盐都区,响水县,滨海县,阜宁县,射阳县,建湖县,东台市,大丰市", "市辖区,广陵区,邗江区,维扬区,宝应县,仪征市,高邮市,江都市", "市辖区,京口区,润州区,丹徒区,丹阳市,扬中市,句容市", "市辖区,海陵区,高港区,兴化市,靖江市,泰兴市,姜堰市", "市辖区,宿城区,宿豫区,沭阳县,泗阳县,泗洪县" ) );
		$arr_provinces [10] = array ("浙江省", array ("杭州市", "宁波市", "温州市", "嘉兴市", "湖州市", "绍兴市", "金华市", "衢州市", "舟山市", "台州市", "丽水市" ), array ("市辖区,上城区,下城区,江干区,拱墅区,西湖区,滨江区,萧山区,余杭区,桐庐县,淳安县,建德市,富阳市,临安市", "市辖区,海曙区,江东区,江北区,北仑区,镇海区,鄞州区,象山县,宁海县,余姚市,慈溪市,奉化市", "市辖区,鹿城区,龙湾区,瓯海区,洞头县,永嘉县,平阳县,苍南县,文成县,泰顺县,瑞安市,乐清市", "市辖区,秀城区,秀洲区,嘉善县,海盐县,海宁市,平湖市,桐乡市", "市辖区,吴兴区,南浔区,德清县,长兴县,安吉县", "市辖区,越城区,绍兴县,新昌县,诸暨市,上虞市,嵊州市", "市辖区,婺城区,金东区,武义县,浦江县,磐安县,兰溪市,义乌市,东阳市,永康市", "市辖区,柯城区,衢江区,常山县,开化县,龙游县,江山市", "市辖区,定海区,普陀区,岱山县,嵊泗县", "市辖区,椒江区,黄岩区,路桥区,玉环县,三门县,天台县,仙居县,温岭市,临海市", "市辖区,莲都区,青田县,缙云县,遂昌县,松阳县,云和县,庆元县,景宁畲族自治县,龙泉市" ) );
		$arr_provinces [11] = array ("安徽省", array ("合肥市", "芜湖市", "蚌埠市", "淮南市", "马鞍山市", "淮北市", "铜陵市", "安庆市", "黄山市", "滁州市", "阜阳市", "宿州市", "巢湖市", "六安市", "亳州市", "池州市", "宣城市" ), array ("市辖区,瑶海区,庐阳区,蜀山区,包河区,长丰县,肥东县,肥西县", "市辖区,镜湖区,弋江区,鸠江区,三山区,芜湖县,繁昌县,南陵县", "市辖区,龙子湖区,蚌山区,禹会区,淮上区,怀远县,五河县,固镇县", "市辖区,大通区,田家庵区,谢家集区,八公山区,潘集区,凤台县", "市辖区,金家庄区,花山区,雨山区,当涂县", "市辖区,杜集区,相山区,烈山区,濉溪县", "市辖区,铜官山区,狮子山区,郊区,铜陵县", "市辖区,迎江区,大观区,宜秀区,怀宁县,枞阳县,潜山县,太湖县,宿松县,望江县,岳西县,桐城市", "市辖区,屯溪区,黄山区,徽州区,歙县,休宁县,黟县,祁门县", "市辖区,琅琊区,南谯区,来安县,全椒县,定远县,凤阳县,天长市,明光市", "市辖区,颍州区,颍东区,颍泉区,临泉县,太和县,阜南县,颍上县,界首市", "市辖区,埇桥区,砀山县,萧县,灵璧县,泗县", "市辖区,居巢区,庐江县,无为县,含山县,和县", "市辖区,金安区,裕安区,寿县,霍邱县,舒城县,金寨县,霍山县", "市辖区,谯城区,涡阳县,蒙城县,利辛县", "市辖区,贵池区,东至县,石台县,青阳县", "市辖区,宣州区,郎溪县,广德县,泾县,绩溪县,旌德县,宁国市" ) );
		$arr_provinces [12] = array ("福建省", array ("福州市", "厦门市", "莆田市", "三明市", "泉州市", "漳州市", "南平市", "龙岩市", "宁德市" ), array ("市辖区,鼓楼区,台江区,仓山区,马尾区,晋安区,闽侯县,连江县,罗源县,闽清县,永泰县,平潭县,福清市,长乐市", "市辖区,思明区,海沧区,湖里区,集美区,同安区,翔安区", "市辖区,城厢区,涵江区,荔城区,秀屿区,仙游县", "市辖区,梅列区,三元区,明溪县,清流县,宁化县,大田县,尤溪县,沙县,将乐县,泰宁县,建宁县,永安市", "市辖区,鲤城区,丰泽区,洛江区,泉港区,惠安县,安溪县,永春县,德化县,金门县,石狮市,晋江市,南安市", "市辖区,芗城区,龙文区,云霄县,漳浦县,诏安县,长泰县,东山县,南靖县,平和县,华安县,龙海市", "市辖区,延平区,顺昌县,浦城县,光泽县,松溪县,政和县,邵武市,武夷山市,建瓯市,建阳市", "市辖区,新罗区,长汀县,永定县,上杭县,武平县,连城县,漳平市", "市辖区,蕉城区,霞浦县,古田县,屏南县,寿宁县,周宁县,柘荣县,福安市,福鼎市" ) );
		$arr_provinces [13] = array ("江西省", array ("南昌市", "景德镇市", "萍乡市", "九江市", "新余市", "鹰潭市", "赣州市", "吉安市", "宜春市", "抚州市", "上饶市" ), array ("市辖区,东湖区,西湖区,青云谱区,湾里区,青山湖区,南昌县,新建县,安义县,进贤县", "市辖区,昌江区,珠山区,浮梁县,乐平市", "市辖区,安源区,湘东区,莲花县,上栗县,芦溪县", "市辖区,庐山区,浔阳区,九江县,武宁县,修水县,永修县,德安县,星子县,都昌县,湖口县,彭泽县,瑞昌市", "市辖区,渝水区,分宜县", "市辖区,月湖区,余江县,贵溪市", "市辖区,章贡区,赣县,信丰县,大余县,上犹县,崇义县,安远县,龙南县,定南县,全南县,宁都县,于都县,兴国县,会昌县,寻乌县,石城县,瑞金市,南康市", "市辖区,吉州区,青原区,吉安县,吉水县,峡江县,新干县,永丰县,泰和县,遂川县,万安县,安福县,永新县,井冈山市", "市辖区,袁州区,奉新县,万载县,上高县,宜丰县,靖安县,铜鼓县,丰城市,樟树市,高安市", "市辖区,临川区,南城县,黎川县,南丰县,崇仁县,乐安县,宜黄县,金溪县,资溪县,东乡县,广昌县", "市辖区,信州区,上饶县,广丰县,玉山县,铅山县,横峰县,弋阳县,余干县,鄱阳县,万年县,婺源县,德兴市" ) );
		$arr_provinces [14] = array ("山东省", array ("济南市", "青岛市", "淄博市", "枣庄市", "东营市", "烟台市", "潍坊市", "济宁市", "泰安市", "威海市", "日照市", "莱芜市", "临沂市", "德州市", "聊城市", "滨州市", "菏泽市" ), array ("市辖区,历下区,市中区,槐荫区,天桥区,历城区,长清区,平阴县,济阳县,商河县,章丘市", "市辖区,市南区,市北区,四方区,黄岛区,崂山区,李沧区,城阳区,胶州市,即墨市,平度市,胶南市,莱西市", "市辖区,淄川区,张店区,博山区,临淄区,周村区,桓台县,高青县,沂源县", "市辖区,市中区,薛城区,峄城区,台儿庄区,山亭区,滕州市", "市辖区,东营区,河口区,垦利县,利津县,广饶县", "市辖区,芝罘区,福山区,牟平区,莱山区,长岛县,龙口市,莱阳市,莱州市,蓬莱市,招远市,栖霞市,海阳市", "市辖区,潍城区,寒亭区,坊子区,奎文区,临朐县,昌乐县,青州市,诸城市,寿光市,安丘市,高密市,昌邑市", "市辖区,市中区,任城区,微山县,鱼台县,金乡县,嘉祥县,汶上县,泗水县,梁山县,曲阜市,兖州市,邹城市", "市辖区,泰山区,岱岳区,宁阳县,东平县,新泰市,肥城市", "市辖区,环翠区,文登市,荣成市,乳山市", "市辖区,东港区,岚山区,五莲县,莒县", "市辖区,莱城区,钢城区", "市辖区,兰山区,罗庄区,河东区,沂南县,郯城县,沂水县,苍山县,费县,平邑县,莒南县,蒙阴县,临沭县", "市辖区,德城区,陵县,宁津县,庆云县,临邑县,齐河县,平原县,夏津县,武城县,乐陵市,禹城市", "市辖区,东昌府区,阳谷县,莘县,茌平县,东阿县,冠县,高唐县,临清市", "市辖区,滨城区,惠民县,阳信县,无棣县,沾化县,博兴县,邹平县", "市辖区,牡丹区,曹县,单县,成武县,巨野县,郓城县,鄄城县,定陶县,东明县" ) );
		$arr_provinces [15] = array ("河南省", array ("郑州市", "开封市", "洛阳市", "平顶山市", "安阳市", "鹤壁市", "新乡市", "焦作市", "濮阳市", "许昌市", "漯河市", "三门峡市", "南阳市", "商丘市", "信阳市", "周口市", "驻马店市" ), array ("市辖区,中原区,二七区,管城回族区,金水区,上街区,惠济区,中牟县,巩义市,荥阳市,新密市,新郑市,登封市", "市辖区,龙亭区,顺河回族区,鼓楼区,禹王台区,金明区,杞县,通许县,尉氏县,开封县,兰考县", "市辖区,老城区,西工区,廛河回族区,涧西区,吉利区,洛龙区,孟津县,新安县,栾川县,嵩县,汝阳县,宜阳县,洛宁县,伊川县,偃师市", "市辖区,新华区,卫东区,石龙区,湛河区,宝丰县,叶县,鲁山县,郏县,舞钢市,汝州市", "市辖区,文峰区,北关区,殷都区,龙安区,安阳县,汤阴县,滑县,内黄县,林州市", "市辖区,鹤山区,山城区,淇滨区,浚县,淇县", "市辖区,红旗区,卫滨区,凤泉区,牧野区,新乡县,获嘉县,原阳县,延津县,封丘县,长垣县,卫辉市,辉县市", "市辖区,解放区,中站区,马村区,山阳区,修武县,博爱县,武陟县,温县,济源市,沁阳市,孟州市", "市辖区,华龙区,清丰县,南乐县,范县,台前县,濮阳县", "市辖区,魏都区,许昌县,鄢陵县,襄城县,禹州市,长葛市", "市辖区,源汇区,郾城区,召陵区,舞阳县,临颍县", "市辖区,湖滨区,渑池县,陕县,卢氏县,义马市,灵宝市", "市辖区,宛城区,卧龙区,南召县,方城县,西峡县,镇平县,内乡县,淅川县,社旗县,唐河县,新野县,桐柏县,邓州市", "市辖区,梁园区,睢阳区,民权县,睢县,宁陵县,柘城县,虞城县,夏邑县,永城市", "市辖区,浉河区,平桥区,罗山县,光山县,新县,商城县,固始县,潢川县,淮滨县,息县", "市辖区,川汇区,扶沟县,西华县,商水县,沈丘县,郸城县,淮阳县,太康县,鹿邑县,项城市", "市辖区,驿城区,西平县,上蔡县,平舆县,正阳县,确山县,泌阳县,汝南县,遂平县,新蔡县" ) );
		$arr_provinces [16] = array ("湖北省", array ("武汉市", "黄石市", "十堰市", "宜昌市", "襄樊市", "鄂州市", "荆门市", "孝感市", "荆州市", "黄冈市", "咸宁市", "随州市", "恩施土家族苗族自治州", "省直辖行政单位" ), array ("市辖区,江岸区,江汉区,硚口区,汉阳区,武昌区,青山区,洪山区,东西湖区,汉南区,蔡甸区,江夏区,黄陂区,新洲区", "市辖区,黄石港区,西塞山区,下陆区,铁山区,阳新县,大冶市", "市辖区,茅箭区,张湾区,郧县,郧西县,竹山县,竹溪县,房县,丹江口市", "市辖区,西陵区,伍家岗区,点军区,猇亭区,夷陵区,远安县,兴山县,秭归县,长阳土家族自治县,五峰土家族自治县,宜都市,当阳市,枝江市", "市辖区,襄城区,樊城区,襄阳区,南漳县,谷城县,保康县,老河口市,枣阳市,宜城市", "市辖区,梁子湖区,华容区,鄂城区", "市辖区,东宝区,掇刀区,京山县,沙洋县,钟祥市", "市辖区,孝南区,孝昌县,大悟县,云梦县,应城市,安陆市,汉川市", "市辖区,沙市区,荆州区,公安县,监利县,江陵县,石首市,洪湖市,松滋市", "市辖区,黄州区,团风县,红安县,罗田县,英山县,浠水县,蕲春县,黄梅县,麻城市,武穴市", "市辖区,咸安区,嘉鱼县,通城县,崇阳县,通山县,赤壁市", "市辖区,曾都区,广水市", "恩施市,利川市,建始县,巴东县,宣恩县,咸丰县,来凤县,鹤峰县", "仙桃市,潜江市,天门市,神农架林区" ) );
		$arr_provinces [17] = array ("湖南省", array ("长沙市", "株洲市", "湘潭市", "衡阳市", "邵阳市", "岳阳市", "常德市", "张家界市", "益阳市", "郴州市", "永州市", "怀化市", "娄底市", "湘西土家族苗族自治州" ), array ("市辖区,芙蓉区,天心区,岳麓区,开福区,雨花区,长沙县,望城县,宁乡县,浏阳市", "市辖区,荷塘区,芦淞区,石峰区,天元区,株洲县,攸县,茶陵县,炎陵县,醴陵市", "市辖区,雨湖区,岳塘区,湘潭县,湘乡市,韶山市", "市辖区,珠晖区,雁峰区,石鼓区,蒸湘区,南岳区,衡阳县,衡南县,衡山县,衡东县,祁东县,耒阳市,常宁市", "市辖区,双清区,大祥区,北塔区,邵东县,新邵县,邵阳县,隆回县,洞口县,绥宁县,新宁县,城步苗族自治县,武冈市", "市辖区,岳阳楼区,云溪区,君山区,岳阳县,华容县,湘阴县,平江县,汨罗市,临湘市", "市辖区,武陵区,鼎城区,安乡县,汉寿县,澧县,临澧县,桃源县,石门县,津市市", "市辖区,永定区,武陵源区,慈利县,桑植县", "市辖区,资阳区,赫山区,南县,桃江县,安化县,沅江市", "市辖区,北湖区,苏仙区,桂阳县,宜章县,永兴县,嘉禾县,临武县,汝城县,桂东县,安仁县,资兴市", "市辖区,零陵区,冷水滩区,祁阳县,东安县,双牌县,道县,江永县,宁远县,蓝山县,新田县,江华瑶族自治县", "市辖区,鹤城区,中方县,沅陵县,辰溪县,溆浦县,会同县,麻阳苗族自治县,新晃侗族自治县,芷江侗族自治县,靖州苗族侗族自治县,通道侗族自治县,洪江市", "市辖区,娄星区,双峰县,新化县,冷水江市,涟源市", "吉首市,泸溪县,凤凰县,花垣县,保靖县,古丈县,永顺县,龙山县" ) );
		$arr_provinces [18] = array ("广东省", array ("广州市", "韶关市", "深圳市", "珠海市", "汕头市", "佛山市", "江门市", "湛江市", "茂名市", "肇庆市", "惠州市", "梅州市", "汕尾市", "河源市", "阳江市", "清远市", "东莞市", "中山市", "潮州市", "揭阳市", "云浮市" ), array ("市辖区,荔湾区,越秀区,海珠区,天河区,白云区,黄埔区,番禺区,花都区,南沙区,萝岗区,增城市,从化市", "市辖区,武江区,浈江区,曲江区,始兴县,仁化县,翁源县,乳源瑶族自治县,新丰县,乐昌市,南雄市", "市辖区,罗湖区,福田区,南山区,宝安区,龙岗区,盐田区", "市辖区,香洲区,斗门区,金湾区", "市辖区,龙湖区,金平区,濠江区,潮阳区,潮南区,澄海区,南澳县", "市辖区,禅城区,南海区,顺德区,三水区,高明区", "市辖区,蓬江区,江海区,新会区,台山市,开平市,鹤山市,恩平市", "市辖区,赤坎区,霞山区,坡头区,麻章区,遂溪县,徐闻县,廉江市,雷州市,吴川市", "市辖区,茂南区,茂港区,电白县,高州市,化州市,信宜市", "市辖区,端州区,鼎湖区,广宁县,怀集县,封开县,德庆县,高要市,四会市", "市辖区,惠城区,惠阳区,博罗县,惠东县,龙门县", "市辖区,梅江区,梅县,大埔县,丰顺县,五华县,平远县,蕉岭县,兴宁市", "市辖区,城区,海丰县,陆河县,陆丰市", "市辖区,源城区,紫金县,龙川县,连平县,和平县,东源县", "市辖区,江城区,阳西县,阳东县,阳春市", "市辖区,清城区,佛冈县,阳山县,连山壮族瑶族自治县,连南瑶族自治县,清新县,英德市,连州市", "东莞市", "中山市", "市辖区,湘桥区,潮安县,饶平县", "市辖区,榕城区,揭东县,揭西县,惠来县,普宁市", "市辖区,云城区,新兴县,郁南县,云安县,罗定市" ) );
		$arr_provinces [19] = array ("广西壮族自治区", array ("南宁市", "柳州市", "桂林市", "梧州市", "北海市", "防城港市", "钦州市", "贵港市", "玉林市", "百色市", "贺州市", "河池市", "来宾市", "崇左市" ), array ("市辖区,兴宁区,青秀区,江南区,西乡塘区,良庆区,邕宁区,武鸣县,隆安县,马山县,上林县,宾阳县,横县", "市辖区,城中区,鱼峰区,柳南区,柳北区,柳江县,柳城县,鹿寨县,融安县,融水苗族自治县,三江侗族自治县", "市辖区,秀峰区,叠彩区,象山区,七星区,雁山区,阳朔县,临桂县,灵川县,全州县,兴安县,永福县,灌阳县,龙胜各族自治县,资源县,平乐县,荔蒲县,恭城瑶族自治县", "市辖区,万秀区,蝶山区,长洲区,苍梧县,藤县,蒙山县,岑溪市", "市辖区,海城区,银海区,铁山港区,合浦县", "市辖区,港口区,防城区,上思县,东兴市", "市辖区,钦南区,钦北区,灵山县,浦北县", "市辖区,港北区,港南区,覃塘区,平南县,桂平市", "市辖区,玉州区,容县,陆川县,博白县,兴业县,北流市", "市辖区,右江区,田阳县,田东县,平果县,德保县,靖西县,那坡县,凌云县,乐业县,田林县,西林县,隆林各族自治县", "市辖区,八步区,昭平县,钟山县,富川瑶族自治县", "市辖区,金城江区,南丹县,天峨县,凤山县,东兰县,罗城仫佬族自治县,环江毛南族自治县,巴马瑶族自治县,都安瑶族自治县,大化瑶族自治县,宜州市", "市辖区,兴宾区,忻城县,象州县,武宣县,金秀瑶族自治县,合山市", "市辖区,江洲区,扶绥县,宁明县,龙州县,大新县,天等县,凭祥市" ) );
		$arr_provinces [20] = array ("海南省", array ("海口市", "三亚市", "省直辖县级行政单位" ), array ("市辖区,秀英区,龙华区,琼山区,美兰区", "市辖区", "五指山市,琼海市,儋州市,文昌市,万宁市,东方市,定安县,屯昌县,澄迈县,临高县,白沙黎族自治县,昌江黎族自治县,乐东黎族自治县,陵水黎族自治县,保亭黎族苗族自治县,琼中黎族苗族自治县,西沙群岛,南沙群岛,中沙群岛的岛礁及其海域" ) );
		$arr_provinces [21] = array ("重庆市", array ("市辖区", "市辖县", "县级市" ), array ("万州区,涪陵区,渝中区,大渡口区,江北区,沙坪坝区,九龙坡区,南岸区,北碚区,万盛区,双桥区,渝北区,巴南区,黔江区,长寿区", "綦江县,潼南县,铜梁县,大足县,荣昌县,璧山县,梁平县,城口县,丰都县,垫江县,武隆县,忠县,开县,云阳县,奉节县,巫山县,巫溪县,石柱土家族自治县,秀山土家族苗族自治县,酉阳土家族苗族自治县,彭水苗族土家族自治县", "江津市,合川市,永川市,南川市" ) );
		$arr_provinces [22] = array ("四川省", array ("成都市", "自贡市", "攀枝花市", "泸州市", "德阳市", "绵阳市", "广元市", "遂宁市", "内江市", "乐山市", "南充市", "眉山市", "宜宾市", "广安市", "达州市", "雅安市", "巴中市", "资阳市", "阿坝藏族羌族自治州", "甘孜藏族自治州", "凉山彝族自治州" ), array ("市辖区,锦江区,青羊区,金牛区,武侯区,成华区,龙泉驿区,青白江区,新都区,温江区,金堂县,双流县,郫县,大邑县,蒲江县,新津县,都江堰市,彭州市,邛崃市,崇州市", "市辖区,自流井区,贡井区,大安区,沿滩区,荣县,富顺县", "市辖区,东区,西区,仁和区,米易县,盐边县", "市辖区,江阳区,纳溪区,龙马潭区,泸县,合江县,叙永县,古蔺县", "市辖区,旌阳区,中江县,罗江县,广汉市,什邡市,绵竹市", "市辖区,涪城区,游仙区,三台县,盐亭县,安县,梓潼县,北川羌族自治县,平武县,江油市", "市辖区,市中区,元坝区,朝天区,旺苍县,青川县,剑阁县,苍溪县", "市辖区,船山区,安居区,蓬溪县,射洪县,大英县", "市辖区,市中区,东兴区,威远县,资中县,隆昌县", "市辖区,市中区,沙湾区,五通桥区,金口河区,犍为县,井研县,夹江县,沐川县,峨边彝族自治县,马边彝族自治县,峨眉山市", "市辖区,顺庆区,高坪区,嘉陵区,南部县,营山县,蓬安县,仪陇县,西充县,阆中市", "市辖区,东坡区,仁寿县,彭山县,洪雅县,丹棱县,青神县", "市辖区,翠屏区,宜宾县,南溪县,江安县,长宁县,高县,珙县,筠连县,兴文县,屏山县", "市辖区,广安区,岳池县,武胜县,邻水县,华蓥市", "市辖区,通川区,达县,宣汉县,开江县,大竹县,渠县,万源市", "市辖区,雨城区,名山县,荥经县,汉源县,石棉县,天全县,芦山县,宝兴县", "市辖区,巴州区,通江县,南江县,平昌县", "市辖区,雁江区,安岳县,乐至县,简阳市", "汶川县,理县,茂县,松潘县,九寨沟县,金川县,小金县,黑水县,马尔康县,壤塘县,阿坝县,若尔盖县,红原县", "康定县,泸定县,丹巴县,九龙县,雅江县,道孚县,炉霍县,甘孜县,新龙县,德格县,白玉县,石渠县,色达县,理塘县,巴塘县,乡城县,稻城县,得荣县", "西昌市,木里藏族自治县,盐源县,德昌县,会理县,会东县,宁南县,普格县,布拖县,金阳县,昭觉县,喜德县,冕宁县,越西县,甘洛县,美姑县,雷波县" ) );
		$arr_provinces [23] = array ("贵州省", array ("贵阳市", "六盘水市", "遵义市", "安顺市", "铜仁地区", "黔西南布依族苗族自治州", "毕节地区", "黔东南苗族侗族自治州", "黔南布依族苗族自治州" ), array ("市辖区,南明区,云岩区,花溪区,乌当区,白云区,小河区,开阳县,息烽县,修文县,清镇市", "钟山区,六枝特区,水城县,盘县", "市辖区,红花岗区,汇川区,遵义县,桐梓县,绥阳县,正安县,道真仡佬族苗族自治县,务川仡佬族苗族自治县,凤冈县,湄潭县,余庆县,习水县,赤水市,仁怀市", "市辖区,西秀区,平坝县,普定县,镇宁布依族苗族自治县,关岭布依族苗族自治县,紫云苗族布依族自治县", "铜仁市,江口县,玉屏侗族自治县,石阡县,思南县,印江土家族苗族自治县,德江县,沿河土家族自治县,松桃苗族自治县,万山特区", "兴义市,兴仁县,普安县,晴隆县,贞丰县,望谟县,册亨县,安龙县", "毕节市,大方县,黔西县,金沙县,织金县,纳雍县,威宁彝族回族苗族自治县,赫章县", "凯里市,黄平县,施秉县,三穗县,镇远县,岑巩县,天柱县,锦屏县,剑河县,台江县,黎平县,榕江县,从江县,雷山县,麻江县,丹寨县", "都匀市,福泉市,荔波县,贵定县,瓮安县,独山县,平塘县,罗甸县,长顺县,龙里县,惠水县,三都水族自治县" ) );
		$arr_provinces [24] = array ("云南省", array ("昆明市", "曲靖市", "玉溪市", "保山市", "昭通市", "丽江市", "思茅市", "临沧市", "楚雄彝族自治州", "红河哈尼族彝族自治州", "文山壮族苗族自治州", "西双版纳傣族自治州", "大理白族自治州", "德宏傣族景颇族自治州", "怒江傈僳族自治州", "迪庆藏族自治州" ), array ("市辖区,五华区,盘龙区,官渡区,西山区,东川区,呈贡县,晋宁县,富民县,宜良县,石林彝族自治县,嵩明县,禄劝彝族苗族自治县,寻甸回族彝族自治县,安宁市", "市辖区,麒麟区,马龙县,陆良县,师宗县,罗平县,富源县,会泽县,沾益县,宣威市", "市辖区,红塔区,江川县,澄江县,通海县,华宁县,易门县,峨山彝族自治县,新平彝族傣族自治县,元江哈尼族彝族傣族自治县", "市辖区,隆阳区,施甸县,腾冲县,龙陵县,昌宁县", "市辖区,昭阳区,鲁甸县,巧家县,盐津县,大关县,永善县,绥江县,镇雄县,彝良县,威信县,水富县", "市辖区,古城区,玉龙纳西族自治县,永胜县,华坪县,宁蒗彝族自治县", "市辖区,翠云区,普洱哈尼族彝族自治县,墨江哈尼族自治县,景东彝族自治县,景谷傣族彝族自治县,镇沅彝族哈尼族拉祜族自治县,江城哈尼族彝族自治县,孟连傣族拉祜族佤族自治县,澜沧拉祜族自治县,西盟佤族自治县", "市辖区,临翔区,凤庆县,云县,永德县,镇康县,双江拉祜族佤族布朗族傣族自治县,耿马傣族佤族自治县,沧源佤族自治县", "楚雄市,双柏县,牟定县,南华县,姚安县,大姚县,永仁县,元谋县,武定县,禄丰县", "个旧市,开远市,蒙自县,屏边苗族自治县,建水县,石屏县,弥勒县,泸西县,元阳县,红河县,金平苗族瑶族傣族自治县,绿春县,河口瑶族自治县", "文山县,砚山县,西畴县,麻栗坡县,马关县,丘北县,广南县,富宁县", "景洪市,勐海县,勐腊县", "大理市,漾濞彝族自治县,祥云县,宾川县,弥渡县,南涧彝族自治县,巍山彝族回族自治县,永平县,云龙县,洱源县,剑川县,鹤庆县", "瑞丽市,潞西市,梁河县,盈江县,陇川县", "泸水县,福贡县,贡山独龙族怒族自治县,兰坪白族普米族自治县", "香格里拉县,德钦县,维西傈僳族自治县" ) );
		$arr_provinces [25] = array ("西藏自治区", array ("拉萨市", "昌都地区", "山南地区", "日喀则地区", "那曲地区", "阿里地区", "林芝地区" ), array ("市辖区,城关区,林周县,当雄县,尼木县,曲水县,堆龙德庆县,达孜县,墨竹工卡县", "昌都县,江达县,贡觉县,类乌齐县,丁青县,察雅县,八宿县,左贡县,芒康县,洛隆县,边坝县", "乃东县,扎囊县,贡嘎县,桑日县,琼结县,曲松县,措美县,洛扎县,加查县,隆子县,错那县,浪卡子县", "日喀则市,南木林县,江孜县,定日县,萨迦县,拉孜县,昂仁县,谢通门县,白朗县,仁布县,康马县,定结县,仲巴县,亚东县,吉隆县,聂拉木县,萨嘎县,岗巴县", "那曲县,嘉黎县,比如县,聂荣县,安多县,申扎县,索县,班戈县,巴青县,尼玛县", "普兰县,札达县,噶尔县,日土县,革吉县,改则县,措勤县", "林芝县,工布江达县,米林县,墨脱县,波密县,察隅县,朗县" ) );
		$arr_provinces [26] = array ("陕西省", array ("西安市", "铜川市", "宝鸡市", "咸阳市", "渭南市", "延安市", "汉中市", "榆林市", "安康市", "商洛市" ), array ("市辖区,新城区,碑林区,莲湖区,灞桥区,未央区,雁塔区,阎良区,临潼区,长安区,蓝田县,周至县,户县,高陵县", "市辖区,王益区,印台区,耀州区,宜君县", "市辖区,渭滨区,金台区,陈仓区,凤翔县,岐山县,扶风县,眉县,陇县,千阳县,麟游县,凤县,太白县", "市辖区,秦都区,杨凌区,渭城区,三原县,泾阳县,乾县,礼泉县,永寿县,彬县,长武县,旬邑县,淳化县,武功县,兴平市", "市辖区,临渭区,华县,潼关县,大荔县,合阳县,澄城县,蒲城县,白水县,富平县,韩城市,华阴市", "市辖区,宝塔区,延长县,延川县,子长县,安塞县,志丹县,吴起县,甘泉县,富县,洛川县,宜川县,黄龙县,黄陵县", "市辖区,汉台区,南郑县,城固县,洋县,西乡县,勉县,宁强县,略阳县,镇巴县,留坝县,佛坪县", "市辖区,榆阳区,神木县,府谷县,横山县,靖边县,定边县,绥德县,米脂县,佳县,吴堡县,清涧县,子洲县", "市辖区,汉滨区,汉阴县,石泉县,宁陕县,紫阳县,岚皋县,平利县,镇坪县,旬阳县,白河县", "市辖区,商州区,洛南县,丹凤县,商南县,山阳县,镇安县,柞水县" ) );
		$arr_provinces [27] = array ("甘肃省", array ("兰州市", "嘉峪关市", "金昌市", "白银市", "天水市", "武威市", "张掖市", "平凉市", "酒泉市", "庆阳市", "定西市", "陇南市", "临夏回族自治州", "甘南藏族自治州" ), array ("市辖区,城关区,七里河区,西固区,安宁区,红古区,永登县,皋兰县,榆中县", "市辖区", "市辖区,金川区,永昌县", "市辖区,白银区,平川区,靖远县,会宁县,景泰县", "市辖区,秦城区,北道区,清水县,秦安县,甘谷县,武山县,张家川回族自治县", "市辖区,凉州区,民勤县,古浪县,天祝藏族自治县", "市辖区,甘州区,肃南裕固族自治县,民乐县,临泽县,高台县,山丹县", "市辖区,崆峒区,泾川县,灵台县,崇信县,华亭县,庄浪县,静宁县", "市辖区,肃州区,金塔县,安西县,肃北蒙古族自治县,阿克塞哈萨克族自治县,玉门市,敦煌市", "市辖区,西峰区,庆城县,环县,华池县,合水县,正宁县,宁县,镇原县", "市辖区,安定区,通渭县,陇西县,渭源县,临洮县,漳县,岷县", "市辖区,武都区,成县,文县,宕昌县,康县,西和县,礼县,徽县,两当县", "临夏市,临夏县,康乐县,永靖县,广河县,和政县,东乡族自治县,积石山保安族东乡族撒拉族自治县", "合作市,临潭县,卓尼县,舟曲县,迭部县,玛曲县,碌曲县,夏河县" ) );
		$arr_provinces [28] = array ("青海省", array ("西宁市", "海东地区", "海北藏族自治州", "黄南藏族自治州", "海南藏族自治州", "果洛藏族自治州", "玉树藏族自治州", "海西蒙古族藏族自治州" ), array ("市辖区,城东区,城中区,城西区,城北区,大通回族土族自治县,湟中县,湟源县", "平安县,民和回族土族自治县,乐都县,互助土族自治县,化隆回族自治县,循化撒拉族自治县", "门源回族自治县,祁连县,海晏县,刚察县", "同仁县,尖扎县,泽库县,河南蒙古族自治县", "共和县,同德县,贵德县,兴海县,贵南县", "玛沁县,班玛县,甘德县,达日县,久治县,玛多县", "玉树县,杂多县,称多县,治多县,囊谦县,曲麻莱县", "格尔木市,德令哈市,乌兰县,都兰县,天峻县" ) );
		$arr_provinces [29] = array ("宁夏回族自治区", array ("银川市", "石嘴山市", "吴忠市", "固原市", "中卫市" ), array ("市辖区,兴庆区,西夏区,金凤区,永宁县,贺兰县,灵武市", "市辖区,大武口区,惠农区,平罗县", "市辖区,利通区,盐池县,同心县,青铜峡市", "市辖区,原州区,西吉县,隆德县,泾源县,彭阳县", "市辖区,沙坡头区,中宁县,海原县" ) );
		$arr_provinces [30] = array ("新疆维吾尔自治区", array ("乌鲁木齐市", "克拉玛依市", "吐鲁番地区", "哈密地区", "昌吉回族自治州", "博尔塔拉蒙古自治州", "巴音郭楞蒙古自治州", "阿克苏地区", "克孜勒苏柯尔克孜自治州", "喀什地区", "和田地区", "伊犁哈萨克自治州", "塔城地区", "阿勒泰地区", "省直辖行政单位" ), array ("市辖区,天山区,沙依巴克区,新市区,水磨沟区,头屯河区,达坂城区,东山区,乌鲁木齐县", "市辖区,独山子区,克拉玛依区,白碱滩区,乌尔禾区", "吐鲁番市,鄯善县,托克逊县", "哈密市,巴里坤哈萨克自治县,伊吾县", "昌吉市,阜康市,米泉市,呼图壁县,玛纳斯县,奇台县,吉木萨尔县,木垒哈萨克自治县", "博乐市,精河县,温泉县", "库尔勒市,轮台县,尉犁县,若羌县,且末县,焉耆回族自治县,和静县,和硕县,博湖县", "阿克苏市,温宿县,库车县,沙雅县,新和县,拜城县,乌什县,阿瓦提县,柯坪县", "阿图什市,阿克陶县,阿合奇县,乌恰县", "喀什市,疏附县,疏勒县,英吉沙县,泽普县,莎车县,叶城县,麦盖提县,岳普湖县,伽师县,巴楚县,塔什库尔干塔吉克自治县", "和田市,和田县,墨玉县,皮山县,洛浦县,策勒县,于田县,民丰县", "伊宁市,奎屯市,伊宁县,察布查尔锡伯自治县,霍城县,巩留县,新源县,昭苏县,特克斯县,尼勒克县", "塔城市,乌苏市,额敏县,沙湾县,托里县,裕民县,和布克赛尔蒙古自治县", "阿勒泰市,布尔津县,富蕴县,福海县,哈巴河县,青河县,吉木乃县", "石河子市,阿拉尔市,图木舒克市,五家渠市" ) );
		$arr_provinces [31] = array ("香港特别行政区", array ("香港" ), array ("香港特别行政区" ) );
		$arr_provinces [32] = array ("澳门特别行政区", array ("澳门" ), array ("澳门特别行政区" ) );
		$arr_provinces [33] = array ("台湾省", array ("台北市", "高雄市", "基隆市", "台中市", "台南市", "新竹市", "嘉义市", "县" ), array ("中正区,大同区,中山区,松山区,大安区,万华区,信义区,士林区,北投区,内湖区,南港区,文山区", "新兴区,前金区,芩雅区,盐埕区,鼓山区,旗津区,前镇区,三民区,左营区,楠梓区,小港区", "仁爱区,信义区,中正区,中山区,安乐区,暖暖区,七堵区", "中区,东区,南区,西区,北区,北屯区,西屯区,南屯区", "中西区,东区,南区,北区,安平区,安南区", "东区,北区,香山区", "东区,西区", "台北县(板桥市),宜兰县(宜兰市),新竹县(竹北市),桃园县(桃园市),苗栗县(苗栗市),台中县(丰原市),彰化县(彰化市),南投县(南投市),嘉义县(太保市),云林县(斗六市),台南县(新营市),高雄县(凤山市),屏东县(屏东市),台东县(台东市),花莲县(花莲市),澎湖县(马公市)" ) );
		$arr_provinces [34] = array ("其它", array ("亚洲", "非洲", "欧洲", "美洲", "大洋洲" ), array ("阿富汗,巴林,孟加拉国,不丹,文莱,缅甸,塞浦路斯,印度,印度尼西亚,伊朗,伊拉克,日本,约旦,朝鲜,科威特,老挝,马尔代夫,黎巴嫩,马来西亚,以色列,蒙古,尼泊尔,阿曼,巴基斯坦,巴勒斯坦,菲律宾,沙特阿拉伯,新加坡,斯里兰卡,叙利亚,泰国,柬埔寨,土耳其,阿联酋,越南,也门,韩国,中国,中国香港,中国澳门,中国台湾", "阿尔及利亚,安哥拉,厄里特里亚,法罗群鸟,加那利群岛(西)(拉斯帕尔马斯),贝宁,博茨瓦纳,布基纳法索,布隆迪,喀麦隆,加那利群岛(西)(圣克鲁斯),佛得角,中非,乍得,科摩罗,刚果,吉布提,埃及,埃塞俄比亚,赤道几内亚,加蓬,冈比亚,加纳,几内亚,南非,几内亚比绍,科特迪瓦,肯尼亚,莱索托,利比里亚,利比亚,马达加斯加,马拉维,马里,毛里塔尼亚,毛里求斯,摩洛哥,莫桑比克,尼日尔,尼日利亚,留尼旺岛,卢旺达,塞内加尔,塞舌尔,塞拉利昂,索马里,苏丹,斯威士兰,坦桑尼亚,圣赤勒拿,多哥,突尼斯,乌干达,扎伊尔,赞比亚,津巴布韦,纳米比亚,迪戈加西亚,桑给巴尔,马约特岛,圣多美和普林西比", "阿尔巴尼亚,安道尔,奥地利,比利时,保加利亚,捷克,丹麦,芬兰,法国,德国,直布罗陀(英),希腊,匈牙利,冰岛,爱尔兰,意大利,列支敦士登,斯洛伐克,卢森堡,马耳他,摩纳哥,荷兰,挪威,波兰,葡萄牙,马其顿,罗马尼亚,南斯拉夫,圣马力诺,西班牙,瑞典,瑞士,英国,科罗地亚,斯洛文尼亚,梵蒂冈,波斯尼亚和塞哥维那,俄罗斯联邦,亚美尼亚共和国,白俄罗斯共和国,格鲁吉亚共和国,哈萨克斯坦共和国,吉尔吉斯坦共和国,乌兹别克斯坦共和国,塔吉克斯坦共和国,土库曼斯坦共和国,乌克兰,立陶宛,拉脱维亚,爱沙尼亚,摩尔多瓦,阿塞拜疆", "安圭拉岛,安提瓜和巴布达,阿根廷,阿鲁巴岛,阿森松,巴哈马,巴巴多斯,伯利兹,百慕大群岛,玻利维亚,巴西,加拿大,开曼群岛,智利,哥伦比亚,多米尼加联邦,哥斯达黎加,古巴,多米尼加共和国,厄瓜多尔,萨尔瓦多,法属圭亚那,格林纳达,危地马拉,圭亚那,海地,洪都拉斯,牙买加,马提尼克(法),墨西哥,蒙特塞拉特岛,荷属安的列斯群岛,尼加拉瓜,巴拿马,巴拉圭,秘鲁,波多黎哥,圣皮埃尔岛密克隆岛(法),圣克里斯托弗和尼维斯,圣卢西亚,福克兰群岛,维尔京群岛(英),圣文森特岛(英),维尔京群岛(美),苏里南,特立尼达和多巴哥,乌拉圭,美国,委内瑞拉,格陵兰岛,特克斯和凯科斯群岛,瓜多罗普", "澳大利亚,科克群岛,斐济,法属波里尼西亚、塔希提,瓦努阿图,关岛,基里巴斯,马里亚纳群岛,中途岛,瑙鲁,新咯里多尼亚群岛,新西兰,巴布亚新几内亚,东萨摩亚,西萨摩亚,所罗门群岛,汤加,对诞岛,威克岛,科科斯岛,夏威夷,诺福克岛,帕劳,纽埃岛,图瓦卢,托克鲁,密克罗尼西亚,马绍尔群岛,瓦里斯加富士那群岛" ) );
		$this->_arr_provinces = $arr_provinces;
		
		$_K ['arr_provinces'] = $arr_provinces;
	
	}
	
	/**
	 * 初始化配置信息
	 */
	function init_config() {
		global $i_model, $_lang, $_K;
		$this->_basic_arr = $basic_arr = db_factory::query ( 'select config_id,k,v,type,listorder from ' . TABLEPRE . 'witkey_basic_config', 1, null );
		$config_arr = array ();
		$size = sizeof ( $basic_arr );
		for($i = 0; $i < $size; $i ++) {
			$config_arr [$basic_arr [$i] ['k']] = $basic_arr [$i] ['v'];
		}
		$mtime = explode ( ' ', microtime () );
		$nav_list = kekezu::get_table_data ( '*', 'witkey_nav', 'ishide!=1', 'listorder', '', '', "nav_id", null );
		$this->_nav_list = $nav_list;
		$_K ['timestamp'] = $mtime [1];
		$_K ['charset'] = CHARSET;
		$_K ['template'] = "default";
		$_K ['theme'] = "simple";
		$_K ['sitename'] = $config_arr ['website_name'];
		$_K ['siteurl'] = $config_arr ['website_url'];
		$_K ['inajax'] = 0;
		$_K ['block_search'] = array ();
		$_K ['is_rewrite'] = $config_arr ['is_rewrite'];
		$_K ['timespan'] = '600';
		$_K ['i'] = 0;
		$_K ['refer'] = "index.php";
		$_K ['limit_ip'] = $config_arr ['limit_ip'];
		$_K ['block_search'] = $_K ['block_replace'] = array ();
		$_lang = array ();
		@include (S_ROOT . '/config/lic.php');
		$config_arr ['seo_title'] and $_K ['html_title'] = $config_arr ['seo_title'] or $_K ['html_title'] = $config_arr ['website_name'];
		define ( 'SKIN_PATH', 'tpl/' . $_K ['template'] );
		define ( 'UPLOAD_ROOT', S_ROOT . '/data/uploads/' . UPLOAD_RULE ); // 附件保存物理路径
		define ( 'UPLOAD_ALLOWEXT', '' . $config_arr ['file_type'] ); // 允许上传的文件后缀，多个后缀用“|”分隔
		define ( 'UPLOAD_MAXSIZE', '' . $config_arr ['max_size'] * 1024 * 1024 ); // 允许上传的附件最大值
		define ( "CREDIT_NAME", $config_arr ['credit_rename'] ? $config_arr ['credit_rename'] : $_lang ['credit'] );
		define ( "EXP_NAME", $config_arr ['exp_rename'] ? $config_arr ['exp_rename'] : $_lang ['experience'] );
		define ( 'FORMHASH', kekezu::formhash () );
		$this->_sys_config = $config_arr;
		$this->_style_path = $_K ['siteurl'] . "/" . SKIN_PATH;
		if (( int ) KEKE_DEBUG == 1) {
			set_error_handler ( array ('keke_core_class', 'error_handler' ) );
			set_exception_handler ( array ('keke_exception', 'handler' ) );
		}
		register_shutdown_function ( array ('keke_core_class', 'shutdown_handler' ) );
	}
	/**
	 * 初始化用户
	 */
	function init_user() {
		global $kekezu, $_K;
		if (isset ( $_SESSION ['uid'] )) {
			
			$this->_uid = $_SESSION ['uid'];
			$this->_username = $_SESSION ['username'];
			$this->_userinfo = keke_user_class::get_user_info ( $this->_uid );
			$this->_user_group = $this->_userinfo ['group_id'];
			$sql = "select count(msg_id) from %switkey_msg where to_uid = '%d' and view_status=0 and msg_status!=1";
			$this->_messagecount = db_factory::get_count ( sprintf ( $sql, TABLEPRE, $this->_uid ) );
			$login_obj = &keke_user_login_class::get_instance();
			$login_obj->check_status ( $this->_userinfo );
		}
	}
	function init_prom() {
		$this->_prom_obj = keke_prom_class::get_instance ();
	}
	function init_industry() {
		$this->_indus_p_arr = kekezu::get_table_data ( '*', "witkey_industry", "indus_type=1 and indus_pid = 0 ", "listorder asc ", '', '', 'indus_id', NULL );
		$this->_indus_c_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=1 and indus_pid >0', 'listorder', '', '', 'indus_id', NULL );
		$this->_indus_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=1', 'listorder', '', '', 'indus_id', NULL );
        
        $this->init_industry_map();
		
		$this->_service_indus_p_arr = kekezu::get_table_data ( '*', "witkey_industry", "indus_type=2 and indus_pid = 0 ", "listorder asc ", '', '', 'indus_id', NULL );
		$this->_service_indus_c_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=2 and indus_pid >0', 'listorder', '', '', 'indus_id', NULL );
		$this->_service_indus_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=2', 'listorder', '', '', 'indus_id', NULL );
	
	}
    
    function init_industry_map() {
        $map = array();
        foreach ($this->_indus_p_arr as $indus) {
            $map[$indus["indus_id"]] = $indus;
        }
        $this->_indus_map = $map;
        
        // $this->_construct_industry_map(&$this->_indus_map, $this->_indus_c_arr, 3);
        $this->_construct_industry_map($this->_indus_map, $this->_indus_c_arr, 3);
    }
    
    private function _construct_industry_map(&$ret_map, $remain_arr, $level) {
        if($level <= 1 || sizeof($remain_arr) == 0) return;
        
        $rest = array();
        foreach($remain_arr as $indus) {
            $indus_pid = $indus["indus_pid"];
            // if ($ret_map[$indus_pid]) {
            if (array_key_exists($indus_pid, $ret_map)) {
                // if(!$ret_map[$indus_pid]["children"]) $ret_map[$indus_pid]["children"] = array();
                if(!array_key_exists("children", $ret_map[$indus_pid])) $ret_map[$indus_pid]["children"] = array();
                array_push($ret_map[$indus_pid]["children"], $indus);
            } else {
                $found_flag = FALSE;
                foreach ($ret_map as $indus_id => &$indus2) {
                // foreach ($ret_map as $indus_id => $indus2) {
                    if(array_key_exists('children', $indus2)) {
                        // $found = $this->_lookforIndustry(&$indus2["children"], $indus);
                        $found = $this->_lookforIndustry($indus2["children"], $indus);
                        if($found) {
                            $found_flag = TRUE;                            
                            break;
                        }
                    }
                }
                if(!$found_flag) {
                    array_push($rest, $indus);
                }
            }
        }
        
        if(sizeof($rest) > 0) {
            // $this->_construct_industry_map(& $ret_map, $rest, $level-1);
            $this->_construct_industry_map($ret_map, $rest, $level-1);
        }
    }
    
    private function _lookforIndustry(&$indusList, $indus) {
        if(!$indusList || count($indusList) == 0) return false;
        
        $indus_pid = $indus["indus_pid"];
        foreach($indusList as &$indus_i) {
        // foreach($indusList as $indus_i) {
            if($indus_i["indus_id"] == $indus_pid) {
                // if(!$indus_i["children"]) $indus_i["children"] = array();
                if(!array_key_exists("children", $indus_i)) $indus_i["children"] = array();
                array_push($indus_i["children"], $indus);
                return true;
            }
            else {
                // if($indus_i["children"] && count($indus_i["children"]) > 0) {
                if(array_key_exists("children", $indus_i) && count($indus_i["children"]) > 0) {
                    // $found = $this->_lookforIndustry(&$indus_i["children"], $indus);
                    $found = $this->_lookforIndustry($indus_i["children"], $indus);
                    if($found) 
                        return true;
                }
            }
        }
        return false;
    }

    public function findIndusById($id) {
        if(array_key_exists($id, $this->_indus_map)) {
            return $this->_indus_map[$id];
        }
        else {
            foreach ($this->_indus_map as $indus_id => $indus) {
                if(array_key_exists("children", $indus)) {
                    $ret = $this->_findIndusByIdInList($indus["children"], $id);
                    if($ret) {
                        return $ret;
                    }
                }
            }
            return null;
        }
    }
    
    private function _findIndusByIdInList($indusList, $id) {
        foreach ($indusList as $indus) {
            if($indus["indus_id"] == $id) {
                return $indus;
            }
            elseif (array_key_exists("children", $indus)) {
                $ret = $this->_findIndusByIdInList($indus["children"], $id);
                if($ret) return $ret;
            }
        }
        return false;
    }

    /**
     * 向上查找,连同父节点一起查找出来.  类似"面包屑"的结构
     */
    public function findIndusWithParentById($id) {
        $indus = $this->findIndusById($id);
        if(!$indus) return null;
        $retList = array($indus);
        if($indus["indus_pid"] == "0") {
            return $retList;
        }
        else {
            while($indus && $indus["indus_pid"] != "0") {
                $indus = $this->findIndusById($indus["indus_pid"]);
                if($indus)
                    array_push($retList, $indus);
            }
            return $retList;
        }
    }
    
    public function get_indus_ids_below_except_leaf($indus) {
	    	$ids = array();
	    	$this->_get_indus_ids_below_except_leaf($indus, $ids);
	    	return $ids;
    }
    
    private function _get_indus_ids_below_except_leaf($indus, &$ids) {
	    	if($indus['children']) {
	    		array_push($ids, $indus['indus_id']);
	    		foreach ($indus['children'] as $key => $value) {
	    			$this->_get_indus_ids_below_except_leaf($value, $ids);
	    	}
	    }
    }
	
	function init_oauth() {
		
		foreach ( $this->_basic_arr as $k => $v ) {
			($v ['type'] == 'weibo' || $v ['type'] == 'interface') and $this->_weibo_list [$v ['k']] = $v ['v'];
		}
		$this->_api_open = unserialize ( $this->_sys_config ['oauth_api_open'] );
	
	}
	/**
	 * 微博关注
	 */
	function init_weibo_attent() {
		foreach ( $this->_basic_arr as $k => $v ) {
			$v ['type'] == 'attention' and $this->_weibo_attent [$v ['k']] = $v ['v'];
		}
		$this->_attent_api_open = unserialize ( $this->_sys_config ['attent_api_open'] );
	}
	function init_lang() {
		$this->_lang_list = keke_lang_class::lang_type ();
		$this->_lang = keke_lang_class::get_lang ();
	}
	function init_model() {
		$model_arr = db_factory::query ( 'select * from ' . TABLEPRE . 'witkey_model where 1=1 order by  model_id asc', 0, null );
		$this->_model_list = kekezu::get_arr_by_key ( $model_arr, 'model_id' );
	}
	/**
	 * 初始化标签
	 */
	function init_tag() {
		$this->_tag or $this->_tag = kekezu::get_tag ();
	}
	function init_session() {
		keke_session_class::get_instance ();
		session_id () == '' and session_start ();
	}
	function init_out_put() {
		($_SERVER ['REQUEST_METHOD'] == 'GET' && ! empty ( $_SERVER ['REQUEST_URI'] )) and kekezu::filter_xss ();
		ob_start ();
		header ( "Content-Type:text/html; charset=" . CHARSET );
	
	}
	/**
	 * 格式化网址, 输出绝对网址
	 */
	public static function format_url( $url ) {
		global $_K;
		
		if ( ! preg_match('/^http/i', $url) ) {
			$url = $_K ['siteurl'] . '/' . $url;
		}

		return $url;
	}
	public static function do_post_request($url, $postdata)
	{
		$php_errormsg="连接失败";
		$data = "";
		$boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);
	
		//Collect Postdata
		foreach($postdata as $key => $val)
		{
			$data .= "--$boundary\n";
			$data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n";
		}
	
		$data .= "--$boundary\n";
	
		$params = array('http' => array(
				'method' => 'POST',
				'content' => $data
		));
	
		$ctx = stream_context_create($params);
		$fp = fopen($url, 'rb', false, $ctx);
	
		if (!$fp) {
			throw new Exception("从$url读取失败");
		}
	
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("从$url读取失败");
		}
		return $response;
	}
}

$ipath = dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "keke_kppw_install.lck";
file_exists ( $ipath ) == true or header ( "Location: install/index.php" );
kekezu::register_autoloader ();
$kekezu = &kekezu::get_instance ();
keke_lang_class::load_lang_class ( 'keke_core_class' );
$_cache_obj = $kekezu->_cache_obj;
$page_obj = $kekezu->_page_obj;
$template_obj = $kekezu->_tpl_obj;
if (! $kekezu->_uid) {
	$login_uid = $_COOKIE ['epautologin'];
	if ($login_uid) {
		$login_obj = &keke_user_login_class::get_instance();
		$login_obj->_login_type=1;
		$user_info = kekezu::get_user_info ( keke_encrypt_class::decode ( $login_uid ) );
		$login_obj->check_status ( $user_info );
		$login_obj->save_user_info ( $user_info, 1);
	}
}
