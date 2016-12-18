<?php
/** 
 * @copyright keke-tech
 * @version v 2.0
 * @Modify by Chen
 */
keke_lang_class::load_lang_class ( 'keke_admin_class' );
class keke_admin_class {
	public $_uid;
	public function __construct() {
		$_SESSION ['uid'] and $this->_uid = $_SESSION ['uid'];
	}
	static function get_admin_menu() {
		global $kekezu, $_lang;
		$menuset_arr = $kekezu->_cache_obj->get ( 'menu_resource_cache' );
		if (! $menuset_arr) {
			$resource_obj = new Keke_witkey_resource_class ();
			$resource_obj->setWhere ( "1=1 order by listorder asc" );
			$resource_arr = $resource_obj->query_keke_witkey_resource ();
			$resource_submenu_obj = new Keke_witkey_resource_submenu_class ();
			$resource_submenu_obj->setWhere ( "1=1 order by listorder" );
			$resource_sub_arr = $resource_submenu_obj->query_keke_witkey_resource_submenu ();
			
			$temp_arr = array ();
			$temp_arr2 = array ();
			$resource_set_arr = array ();
			$submenu_set_arr = array ();
			foreach ( $resource_arr as $r_tp ) {
				$resource_set_arr [$r_tp ['resource_id']] = $r_tp;
				$temp_arr [$r_tp ['submenu_id']] [] = $r_tp;
			}
			
			foreach ( $resource_sub_arr as $r_tp ) {
				$submenu_set_arr [$r_tp ['submenu_id']] = $r_tp;
				$temp_arr2 [$r_tp ['menu_name']] [] = array ('name' => $r_tp ['submenu_name'], 'items' => $temp_arr [$r_tp ['submenu_id']] );
			}
			
			$resource_arr = $temp_arr2;
			$menuset_arr = array ('navgat' => $navgation_arr, 'menu' => $resource_arr, 'submenu' => $submenu_set_arr, 'resource' => $resource_set_arr );			

			$kekezu->_cache_obj->set ( 'menu_resource_cache', $menuset_arr, null );
		}
		return $menuset_arr;
	}
	
	static function get_user_group() {
		global $kekezu;
		$group_arr = $kekezu->_cache_obj->get ( "member_group_cache" );
		if (! $group_arr) {
			$membergroup_obj = new Keke_witkey_member_group_class ();
			$group_arr = $membergroup_obj->query_keke_witkey_member_group ();
			$temp_arr = array ();
			foreach ( $group_arr as $v ) {
				$temp_arr [$v ['group_id']] = $v;
				$temp_arr [$v ['group_id']] ['group_roles'] = explode ( ',', $v ['group_roles'] );
			}
			$group_arr = $temp_arr;
			$kekezu->_cache_obj->set ( 'member_group_cache', $group_arr, null );
		}
		
		return $group_arr;
	}
	/**
	 * 锁屏
	 */
	function screen_lock() {
		$_SESSION ['lock_screen'] = 1;
	}
	/**
	 * 检测锁屏状态
	 */
	function check_screen_lock() {
		$screen_lock = '0';
		isset ( $_SESSION ['lock_screen'] ) && $_SESSION ['lock_screen'] == 1 and $screen_lock = '1';
		return $screen_lock;
	}
	/**
	 * 解除锁屏
	 * @param $unlock_num 剩余解锁次数
	 * @param $unlock_pwd 解锁密码
	 */
	function screen_unlock($unlock_num, $unlock_pwd) {
		global $kekezu;
		global $_lang;
		($_SESSION ['uid'] && $_SESSION ['auid']) or $kekezu->admin_show_msg ( $_lang ['you_not_login'], 'index.php' );
		if ($unlock_num > 0) { //解锁判断
			/**获取当前登录用户密码**/
			$admin_pwd = db_factory::get_count ( " select password from " . TABLEPRE . "witkey_member where uid = '" . $_SESSION ['uid'] . "'" );
			$unlock_pwd = md5 ( $unlock_pwd ); //解锁密码
			if ($admin_pwd == $unlock_pwd) {
				$_SESSION ['lock_screen'] = '0';
				$kekezu->echojson ( '', '2' );
				die (); //解锁成功
			} else {
				if ($unlock_num > 1) {
					$_SESSION ['allow_times'] = -- $unlock_num;
					$kekezu->echojson ( $_lang ['unlock_fail'] . ".", '1', $unlock_num );
					die (); //解锁失败
				} else { //最后一次操作失败
					$_SESSION ['allow_times'] = '0';
					$_SESSION ['lock_screen'] = '0';
					$_SESSION ['uid'] = '';
					$_SESSION ['username'] = '';
					$kekezu->echojson ( $_lang ['wrong_times_much_login_again'], '0' );
					die ();
				}
			}
		}
	}
	/**
	 * 后台权限判断
	 * @param $roleid 权限编号
	 */
	function admin_check_role($roleid) {
		global $_K, $admin_info;
		$grouplist_arr = self::get_user_group ();
		
		if ($_SESSION ['uid'] != ADMIN_UID && ! in_array ( $roleid, $grouplist_arr [$admin_info ['group_id']] ['group_roles'] )) {
			echo "<script>location.href='index.php?do=main'</script>";
			die ();
		}
	}
	/**
	 * 后台导航搜索
	 * @param $ser_resource 待搜索导航
	 * @todo  支持子导航直接搜索
	 */
	function search_nav($ser_resource) {
		$resource_info = db_factory::query ( " select resource_name,resource_url from " . TABLEPRE . "witkey_resource where INSTR(resource_name,'$ser_resource') > 0 " );
		if ($resource_info)
			return $resource_info;
		else {
			return db_factory::query ( "select resource_name,resource_url from " . TABLEPRE . "witkey_resource a left join " . TABLEPRE . "witkey_resource_submenu b
			 on a.submenu_id = b.submenu_id where INSTR(b.submenu_name,'$ser_resource')>0" );
		}
	}
	/**
	 * 获取快捷方式
	 */
	public function get_shortcuts_list() {
		return db_factory::query ( " select b.resource_id,b.resource_name,b.resource_url from " . TABLEPRE . "witkey_shortcuts a left join " . TABLEPRE . "witkey_resource b on a.resource_id = b.resource_id where a.uid = '$this->_uid' order by a.s_id desc " );
	}
	/**
	 *添加快捷（常用菜单) 
	 *@param $uid  用户id
	 *@param $r_id 导航编号
	 */
	function add_fast_menu($r_id) {
		global $_lang;
		$shortcuts_obj = new Keke_witkey_shortcuts_class ();
		$in_shortcuts_list = db_factory::execute ( " select resource_id from " . TABLEPRE . "witkey_shortcuts where resource_id = '$r_id'" );
		if (! $in_shortcuts_list) {
			$shortcuts_obj->_s_id = null;
			$shortcuts_obj->setUid ( $this->_uid );
			$shortcuts_obj->setResource_id ( $r_id );
			$success = $shortcuts_obj->create_keke_witkey_shortcuts ();
			if ($success) {
				kekezu::echojson ( $_lang ['shortcuts_add_success'], '4' );
				die ();
			} else {
				kekezu::echojson ( $_lang ['shortcuts_add_fail'], '1' );
				die ();
			}
		} else {
			kekezu::echojson ( $_lang ['the_shortcuts_has_exist'], '0' );
			die ();
		}
	}
	/**
	 * 移除快捷菜单 
	 * @param $r_id 导航编号
	 */
	function rm_fast_menu($r_id) {
		global $_lang;
		$shortcuts_obj = new Keke_witkey_shortcuts_class ();
		$shortcuts_list = db_factory::get_one ( " select uid,resource_id from " . TABLEPRE . "witkey_shortcuts where resource_id = '$r_id' and uid = '$this->_uid'" );
		if ($shortcuts_list) {
			if ($shortcuts_list ['uid'] != $this->_uid) {
				kekezu::echojson ( $_lang ['not_delete_others_shortcuts'], '2' );
			} else {
				$success = db_factory::execute ( " delete from " . TABLEPRE . "witkey_shortcuts where resource_id = '$r_id' and uid = '$this->_uid'" );
				if ($success) {
					kekezu::echojson ( $_lang ['shortcuts_delete_success'], '4' );
					die ();
				} else {
					kekezu::echojson ( $_lang ['shortcuts_delete_fail'], '3' );
					die ();
				}
			}
		} else {
			kekezu::echojson ( $_lang ['please_choose_shortcut_menu'], '0' );
			die ();
		}
	}
	/**
	 * 获取文件分类
	 */
	static function get_article_cate() {
		return kekezu::get_table_data ( "*", "witkey_article_category", "", "listorder asc ", "", "", "art_cat_id", null );
	
	}
	/**
	 * 后台登录
	 * @param $username 用户名
	 * @param $password 密码
	 * @param $allow_times 剩余尝试次数
	 */
	public function admin_login($username, $password, $allow_times, $formhash = '') {
		global $_lang;
		global $kekezu;
		
		$login_limit = $_SESSION ['login_limit']; //用户登录限制时间
		$remain_times = $login_limit - time (); //允许再次登录时差
		if ($login_limit && $remain_times > 0) { //存在登录时间限制并且时限未过
			$kekezu->echojson ( "login limit!", "8" );
			die ();
		} else {
			if (! kekezu::submitcheck ( $formhash, true )) { //检测hash值
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$hash = kekezu::formhash ();
				$kekezu->echojson ( $_lang ['repeat_form_submit'], 6, array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			}
			
			$user_info = keke_user_class::user_login ( $username, $password ); //用户信息
			$hash = kekezu::formhash ();
			if ($user_info == - 1) {
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['username_input_error'], "6", array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			} else if ($user_info == - 2) {
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['password_input_error'], "5", array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			}
			if (! $user_info) {
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['login_fail'], "4", array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			} else {
				$user_info = kekezu::get_user_info ( $user_info ['uid'] ); //获取用户信息
			}
			if (! $user_info) {
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['no_rights_login_backstage'], "3", array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			} else if (! $user_info ['group_id'] && $user_info ['uid'] != ADMIN_UID ) {
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['no_rights_login_backstage'], "2", array ('times' => $allow_times, 'formhash' => $hash ) );
				die ();
			} elseif( $user_info ['group_id']==12){//禁止代理商登录
				$_SESSION ['allow_times'] -= 1;
				-- $allow_times == 0 and $this->set_login_limit_time ( '1' );
				$kekezu->echojson ( $_lang ['no_rights_login_backstage'], "2", array ('times' => $allow_times, 'formhash' => $hash ) );
			}else {
				$_SESSION ['auid'] = $_SESSION ['uid'] = $user_info ['uid'];
				$_SESSION ['username'] = $user_info ['username'];
				kekezu::admin_system_log ( $user_info ['username'] . date ( 'Y-m-d H:i:s', time () ) . $_lang ['login_system'] );
				$this->set_login_limit_time ();
				$kekezu->echojson ( $_lang ['login_success'], "1" );
				die ();
			}
		}
	}
	/**
	 * 重置登录限制时间
	 * @param unknown_type $t
	 */
	public function set_login_limit_time($t = '') {
		$t and $_SESSION ['login_limit'] = time () + 3600 or $_SESSION ['login_limit'] = '';
	}
	/**
	 * 尝试次数限制
	 * @param $times 剩余次数
	 */
	public function times_limit($times = null) {
		if (isset ( $times )) {
			$allow_times = $times;
		} else { //初始化
			$_SESSION ['allow_times'] and $allow_times = $_SESSION ['allow_times'] or $allow_times = $_SESSION ['allow_times'] = '5';
		}
		return $allow_times;
	}
	
	/**
	 * 
	 * 获取提现的实际金额
	 * @param folat $cash
	 */
	public static function get_withdraw_cash($cash) {
		$fee = floatval ( $cash );
		/** 站类付款收费标准*/
		$pay_config = kekezu::get_table_data ( "*", "witkey_pay_config", '', '', "", '', 'k' );
		
		$site_per_charge = $pay_config [per_charge] [v];
		$site_per_low = $pay_config [per_low] [v];
		$site_per_high = $pay_config [per_high] [v];
		/** 支付宝付款收费标准*/
		$alipay_per_charge = 0.5;
		$alipay_per_low = 1;
		$alipay_per_high = 25;
		
		if ($fee <= $site_per_low) {
			$pay_cash = $fee;
		} elseif ($fee > $site_per_low && $fee <= 200) {
			$pay_cash = $fee + $alipay_per_low - $site_per_low;
		} elseif ($fee > 200 && $fee < 5000) {
			$pay_cash = number_format ( $fee * (100 - $site_per_charge) / (100 - $alipay_per_charge), 2, ".", "" );
		} else {
			$pay_cash = $fee + $alipay_per_high - $site_per_high;
		}
		
		return $pay_cash;
	
	}
	/**
	 * 每日统计
	 */
	public static function daily_statistics($day=0) {
		
		//计算时间
		$ex_time = time();
		intval($day) and $ex_time += $day*24*3600;
		
		$start_time = strtotime ( date ( 'Y-m-d 00:00:00', $ex_time ) ); //取今天0点0分
		$end_time = $start_time + 24 * 3600;
		
		$submit = array (); //提交统计
		$success = array (); //成功统计
		$conversion = array (); //转换率统计
		$complete = array (); //完成统计
		$additional = array (); //新增统计
		

		//任务统计数据
		//悬赏
		$t_r_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id between 1 and 2 and start_time between $start_time and $end_time " );
		//计件
		$t_p_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 3 and start_time between $start_time and $end_time " );
		//招标
		$t_t_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_type=1 and task_cash_coverage>0 and start_time between $start_time and $end_time " );
		//服务
		$t_s_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_type=2 and start_time between $start_time and $end_time " );
		//雇佣
		$t_h_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_type=1 and ifnull(task_cash_coverage,0)=0 and start_time between $start_time and $end_time " );
		//直接雇佣
		$t_th_c = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_type=3 and start_time between $start_time and $end_time " );
		
		$submit ['reward'] = array ('count' => intval ( $t_r_c ['count'] ),
									'cash' => floatval ( $t_r_c ['cash'] ) 
									);
		$submit ['preward'] = array ('count' => intval ( $t_p_c ['count'] ),
									 'cash' => floatval ( $t_p_c ['cash'] ) 
									);
		$submit ['tender'] = array ('count' => intval ( $t_t_c ['count'] ),
									'cash' => floatval ( $t_t_c ['cash'] )
									 );
		$submit ['service'] = array ('count' => intval ( $t_s_c ['count'] ),
									 'cash' => floatval ( $t_s_c ['cash'] ) 
									);
		$submit ['hire'] = array ('count' => intval ( $t_h_c ['count'] ), 
								  'cash' => floatval ( $t_h_c ['cash'] )
								 );
		$submit ['taskhire'] = array ('count' => intval ( $t_th_c ['count'] ),
								 'cash' => floatval ( $t_th_c ['cash'] ) 
								);
		//总计				
		$submit ['total'] = array ('count' => intval ( $t_r_c ['count']+$t_p_c ['count']+$t_t_c ['count']+$t_s_c ['count']+$t_h_c ['count']+$t_th_c ['count'] ),
								 'cash' => floatval ( $t_r_c ['cash']+$t_p_c ['cash']+$t_t_c ['cash']+$t_s_c ['cash']+$t_h_c ['cash']+$t_th_c ['cash'] ) 
								);
		
		//今天的成功任务
		//悬赏
		$t_r_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id between 1 and 2 and task_status between 2 and 8 and start_time between $start_time and $end_time " );
		//计件
		$t_p_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 3 and task_status between 2 and 8 and start_time between $start_time and $end_time " );
		//招标
		$t_t_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_status between 2 and 8 and task_type=1 and task_cash_coverage>0 and start_time between $start_time and $end_time " );
		//服务
		$t_s_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_status between 4 and 8 and task_type=2 and start_time between $start_time and $end_time " );
		//雇佣
		$t_h_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_status between 2 and 8 and task_type=1 and ifnull(task_cash_coverage,0)=0 and start_time between $start_time and $end_time " );
		//直接雇佣
		$t_th_s = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_status between 4 and 8 and cash_status>0 and task_type=3 and start_time between $start_time and $end_time " );
		//直接雇佣发布成功个数
		$t_th_s_g = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id = 4 and task_status between 4 and 8 and task_type=3 and start_time between $start_time and $end_time " );
		
		//托管赏金的财务记录
		//悬赏
		$m_r_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id between 1 and 2 and a.fina_time between $start_time and $end_time" );
		//计件
		$m_p_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id = 3 and a.fina_time between $start_time and $end_time" );
		//招标
		$m_t_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id = 4 and b.task_type = 1 and b.task_cash_coverage>0 and a.fina_time between $start_time and $end_time" );
		//服务
		$m_s_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id = 4 and b.task_type=2 and a.fina_time between $start_time and $end_time" );
		//雇佣
		$m_h_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id = 4 and b.task_type=1 and ifnull(b.task_cash_coverage,0)=0 and a.fina_time between $start_time and $end_time" );
		//直接雇佣
		$m_th_h = db_factory::get_one ( "SELECT sum(fina_cash) cash FROM " . TABLEPRE . "witkey_finance a left join " . TABLEPRE . "witkey_task b on a.obj_id = b.task_id  where  (fina_action = 'task_pay' or fina_action='pub_task') and b.model_id = 4 and b.task_type=3 and a.fina_time between $start_time and $end_time" );
		
		$success ['reward'] = array ('count' => intval ( $t_r_s ['count'] ),
									'cash' => floatval ( $t_r_s ['cash'] ),
									'host'=> floatval ( $m_r_h ['cash'] )
									);
		$success ['preward'] = array ('count' => intval ( $t_p_s ['count'] ),
									 'cash' => floatval ( $t_p_s ['cash'] ) ,
									 'host'=> floatval ( $m_p_h ['cash'] )
									);
		$success ['tender'] = array ('count' => intval ( $t_t_s ['count'] ),
									'cash' => floatval ( $t_t_s ['cash'] ),
									'host'=> floatval ( $m_t_h ['cash'] )
									 );
		$success ['service'] = array ('count' => intval ( $t_s_s ['count'] ),
									 'cash' => floatval ( $t_s_s ['cash'] ),
									'host'=> floatval ( $m_s_h ['cash'] ) 
									);
		$success ['hire'] = array ('count' => intval ( $t_h_s ['count'] ), 
								  'cash' => floatval ( $t_h_s ['cash'] ),
									'host'=> floatval ( $m_h_h ['cash'] )
								 );
		$success ['taskhire'] = array ('count' => intval ( $t_th_s_g ['count'] ),
								 'cash' => floatval ( $t_th_s ['cash'] ) ,
									'host'=> floatval ( $m_th_h ['cash'] )
								);
		//总计				
		$success ['total'] = array ('count' => intval ( $t_r_s ['count']+$t_p_s ['count']+$t_t_s ['count']+$t_s_s ['count']+$t_h_s ['count']+$t_th_s_g ['count'] ),
								 'cash' => floatval ( $t_r_s ['cash']+$t_p_s ['cash']+$t_t_s ['cash']+$t_s_s ['cash']+$t_h_s ['cash']+$t_th_s ['cash'] ) ,
								 'host' => floatval ( $m_r_h ['cash']+$m_p_h ['cash']+$m_t_h ['cash']+$m_s_h ['cash']+$m_h_h ['cash']+$m_th_h ['cash'] ) 
								);
		/**转换率统计*/
		$r_count = $t_r_c['count']?sprintf('%.2f',100*$t_r_s['count']/$t_r_c['count']):0;
		$r_cash  = $t_r_c['cash']?sprintf('%.2f',100*$t_r_s['cash']/$t_r_c['cash']):0;
		$conversion['reward']  = array('count'=>$r_count,
										'cash'=>$r_cash);
		$p_count = $t_p_c['count']?sprintf('%.2f',100*$t_p_s['count']/$t_p_c['count']):0;
		$p_cash  = $t_p_c['cash']?sprintf('%.2f',100*$t_p_s['cash']/$t_p_c['cash']):0;
		$conversion['preward']  = array('count'=>$p_count,
										'cash'=>$p_cash);
		$t_count = $t_t_c['count']?sprintf('%.2f',100*$t_t_s['count']/$t_t_c['count']):0;
		$t_cash  = $t_t_c['cash']?sprintf('%.2f',100*$t_t_s['cash']/$t_t_c['cash']):0;
		$conversion['tender']  = array('count'=>$t_count,
										'cash'=>$t_cash);
		$s_count = $t_s_c['count']?sprintf('%.2f',100*$t_s_s['count']/$t_s_c['count']):0;
		$s_cash  = $t_s_c['cash']?sprintf('%.2f',100*$t_s_s['cash']/$t_s_c['cash']):0;
		$conversion['service']  = array('count'=>$s_count,
										'cash'=>$s_cash);
		$h_count = $t_h_c['count']?sprintf('%.2f',100*$t_h_s['count']/$t_h_c['count']):0;
		$h_cash  = $t_h_c['cash']?sprintf('%.2f',100*$t_h_s['cash']/$t_h_c['cash']):0;
		$conversion['hire']  = array('count'=>$h_count,
										'cash'=>$h_cash);
		$th_count = $t_th_c['count']?sprintf('%.2f',100*$t_th_s['count']/$t_th_c['count']):0;
		$th_cash  = $t_th_c['cash']?sprintf('%.2f',100*$t_th_s['cash']/$t_th_c['cash']):0;
		$conversion['taskhire']  = array('count'=>$th_count,
										'cash'=>$th_cash);
		$tot_count = $submit['total']['count']?sprintf('%.2f',100*$success['total']['count']/$submit['total']['count']):0;
		$tot_cash  = $submit['total']['cash']?sprintf('%.2f',100*$success['total']['cash']/$submit['total']['cash']):0;
		
		$conversion['total']  = array('count'=>$tot_count,
										'cash'=>$tot_cash);
		//今天完成的任务
		//悬赏
		$t_r_e = db_factory::get_one ( "select count(task_id) count,sum(task_cash) cash from " . TABLEPRE . "witkey_task where model_id between 1 and 2 and sp_end_time between $start_time and $end_time" );
		//计件
		$t_p_e = db_factory::get_one ( "select count(task_id) count,sum(a.task_cash) cash,sum(b.fina_cash) paycash from " . TABLEPRE . "witkey_task a left join " . TABLEPRE . "witkey_finance b on a.task_id = b.obj_id where b.fina_action = 'task_bid' and b.fina_time between $start_time and $end_time and a.model_id=3" );
		//招标
		$t_t_e = db_factory::get_one ( "select count(task_id) count,sum(b.fina_cash) cash from " . TABLEPRE . "witkey_task a left join " . TABLEPRE . "witkey_finance b on a.task_id = b.obj_id where b.fina_action = 'task_bid' and b.fina_time between $start_time and $end_time and a.model_id=4 and a.task_type=1 and a.task_cash_coverage>0" );
		//服务
		$t_s_e = db_factory::get_one ( "select count(task_id) count,sum(b.fina_cash) cash from " . TABLEPRE . "witkey_task a left join " . TABLEPRE . "witkey_finance b on a.task_id = b.obj_id where b.fina_action = 'task_bid' and b.fina_time between $start_time and $end_time and a.model_id=4 and a.task_type=2" );
		//雇佣
		$t_h_e = db_factory::get_one ( "select count(task_id) count,sum(b.fina_cash) cash from " . TABLEPRE . "witkey_task a left join " . TABLEPRE . "witkey_finance b on a.task_id = b.obj_id where b.fina_action = 'task_bid' and b.fina_time between $start_time and $end_time and a.model_id=4 and a.task_type=1 and ifnull(a.task_cash_coverage,0)=0" );
		//直接雇佣
		$t_th_e = db_factory::get_one ( "select count(task_id) count,sum(b.fina_cash) cash from " . TABLEPRE . "witkey_task a left join " . TABLEPRE . "witkey_finance b on a.task_id = b.obj_id where b.fina_action = 'task_bid' and b.fina_time between $start_time and $end_time and a.model_id=4 and a.task_type=3" );
		
		$complete ['reward'] = array ('count' => intval ( $t_r_e ['count'] ),
									'cash' => floatval ( $t_r_e ['cash'] ) 
									);
		$complete ['preward'] = array ('count' => intval ( $t_p_e ['count'] ),
									 'cash' => floatval ( $t_p_e ['cash'] ) 
									);
		$complete ['tender'] = array ('count' => intval ( $t_t_e ['count'] ),
									'cash' => floatval ( $t_t_e ['cash'] )
									 );
		$complete ['service'] = array ('count' => intval ( $t_s_e ['count'] ),
									 'cash' => floatval ( $t_s_e ['cash'] ) 
									);
		$complete ['hire'] = array ('count' => intval ( $t_h_e ['count'] ), 
								  'cash' => floatval ( $t_h_e ['cash'] )
								 );
		$complete ['taskhire'] = array ('count' => intval ( $t_th_e ['count'] ),
								 'cash' => floatval ( $t_th_e ['cash'] ) 
								);
		//总计				
		$complete ['total'] = array ('count' => intval ( $t_r_e ['count']+$t_p_e ['count']+$t_t_e ['count']+$t_s_e ['count']+$t_h_e ['count']+$t_th_e ['count'] ),
								 'cash' => floatval ( $t_r_e ['cash']+$t_p_e ['cash']+$t_t_e ['cash']+$t_s_e ['cash']+$t_h_e ['cash']+$t_th_e ['cash'] ) 
								);
		
		//注册统计
		$today_register_count = db_factory::get_count ( "select count(uid) from " . TABLEPRE . "witkey_space where reg_time between $start_time and $end_time" );
		$total_register_count = db_factory::get_count ( "select count(uid) from " . TABLEPRE . "witkey_member" );
		
		$additional ['register'] = array ('count' => intval ( $today_register_count),
									'total' =>intval ( $total_register_count)
									);
									
		$today_vipbuy_count = db_factory::get_one ( "select count(uid) count,sum(cash_cost) cash from " . TABLEPRE . "witkey_vip_history where start_time between $start_time and $end_time" );
		$total_vipbuy_count = db_factory::get_one ( "select count(uid) count,sum(cash_cost) cash from " . TABLEPRE . "witkey_vip_history" );
		
		$additional ['vipbuy'] = array ('new_count' => intval ( $today_vipbuy_count['count']),
									'total_count' =>intval ( $total_vipbuy_count['count']),
									'new_buy' =>intval ( $today_vipbuy_count['cash']),
									'total_buy' =>intval ( $total_vipbuy_count['cash'])
									);
									
		$today_oauth_count = db_factory::get_count ( "select count(uid) count from " . TABLEPRE . "witkey_member_oauth where on_time between $start_time and $end_time" );
		$total_oauth_count = db_factory::get_count ( "select count(uid) count from " . TABLEPRE . "witkey_member_oauth" );
		
		$additional ['oatuh'] = array ('count' => intval ( $today_oauth_count),
									'total' =>intval ( $total_oauth_count)
									);
									
		$today_mail_count = db_factory::get_count ( "select count(id) count from " . TABLEPRE . "witkey_task_subscribe where status = 1 and booktime between $start_time and $end_time" );
		$total_mail_count = db_factory::get_count ( "select count(id) count from " . TABLEPRE . "witkey_task_subscribe where status = 1" );
		
		$additional ['mail'] = array ('count' => intval ( $today_mail_count),
									'total' =>intval ( $total_mail_count)
									);
		$id = db_factory::get_count ( ' select id from ' . TABLEPRE . 'witkey_statistics where date(from_unixtime(add_time))="' . date ( 'Y-m-d', $ex_time ) . '"' );
		if ($id) {//更新
			db_factory::updatetable(TABLEPRE.'witkey_statistics',
						array('submit'=>serialize($submit),
								'success'=>serialize($success),
								'conversion'=>serialize($conversion),
								'complete'=>serialize($complete),
								'additional'=>serialize($additional),
								'add_time'=>$ex_time),
						array('id'=>$id));
		} else {//新增
			$id = db_factory::inserttable(TABLEPRE.'witkey_statistics',
						array('submit'=>serialize($submit),
								'success'=>serialize($success),
								'conversion'=>serialize($conversion),
								'complete'=>serialize($complete),
								'additional'=>serialize($additional),
								'add_time'=>$ex_time));
		
		}
		return db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_statistics where id='.$id );
	}
}

?>