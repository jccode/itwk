<?php
/**
 * 计件悬赏业务类
 * @method init 任务信息初始化
 * =>任务状态数组信息
 * =>任务基本需求
 * check_if_bided        检测是否中标			 
 * 
 * get_task_stage_desc	        获取任务阶段描述
 * get_task_timedesc 	        获取任务时间描述
 * get_task_work		        获取任务指定状态的稿件信息
 * get_work_info      	        获取任务稿件信息 *
 
 * set_work_status   			 稿件状态变更
 
 * dispose_task		   		 任务金额结算
 * dispose_task_return    		 任务金额返还
 *

 *
 *时间类 
 * time_task_end      		     任务结束
 *
 * process_can 	    	                当前操作判断
 * work_hand  		      	      任务交稿
 * work_choose 	      	                任务选稿
 */
keke_lang_class::load_lang_class ( 'preward_task_class' );
class preward_task_class extends keke_task_class {
	
	public $_task_status_arr; //任务状态数组
	public $_work_status_arr; //稿件状态数组
	

	public $_delay_rule; //延期规则
	

	

	protected $_inited = false;
	
	public static function get_instance($task_info) {
		static $obj = null;
		if ($obj == null) {
			$obj = new preward_task_class ( $task_info );
		}
		return $obj;
	}
	public function __construct($task_info) {
		parent::__construct ( $task_info );
		$this->init ();
	
	}
	public function init() {
		if (! $this->_inited) {
			$this->status_init ();
			$this->delay_rule_init ();
			$this->wiki_priv_init ();
		}
		$this->_inited = true;
	
	}
	
	//动作执行
	public function exec_event($action, $obj_id = null) {
		switch ($action) {
			case 'pub_task' :
				//执行发布任务
				

				db_factory::execute ( "update " . TABLEPRE . "witkey_task set cash_status = 1 where task_id = '$this->_task_id'" );
				
				if ($this->_task_config ['xs_audit'] == 1) {
					db_factory::execute ( "update " . TABLEPRE . "witkey_task set task_status=1 where task_id='{$this->_task_id}'" );
				} else {
					$this->task_begin ();
				}
				break;
			case 'task_delay' :
				$delay_cash = db_factory::get_count ( ' select delay_cash from ' . TABLEPRE . 'witkey_task_delay where delay_status=0 and delay_id=' . $obj_id );
				$t_info = $this->_task_info;
				$sin_cash = $t_info ['single_cash'];
				if ($delay_cash && $t_info ['task_status'] < 4) {
					$count = intval ( $delay_cash / $sin_cash );
					db_factory::updatetable ( TABLEPRE . 'witkey_task_delay', array ('delay_status' => 1 ), array ('delay_id' => $obj_id ) );
					//任务数据更新
					db_factory::updatetable ( TABLEPRE . 'witkey_task', array ('is_delay' => ++ $t_info ['is_delay'], //延期+1
'work_count' => $t_info ['work_count'] + $count, //稿件数量
'task_cash' => $t_info ['task_cash'] + $delay_cash, //自动金额
'real_cash' => $t_info ['real_cash'] + $delay_cash ), //任务实际金额
array ('task_id' => $this->_task_id ) );
				}
				break;
		}
	}
	
	/*
	 * 任务开始标志
	 * */
	public function task_begin() {
		//任务开始
		//计算时间差
		$plus_time = time () - $this->_task_info ['start_time'];
		
		//任务数据更新
		db_factory::updatetable ( TABLEPRE . 'witkey_task', array ('task_status' => 2, //任务状态进行中
'sub_time' => $this->_task_info ['sub_time'] + $plus_time, //选稿结束时间补差
'exec_time' => $this->_task_info ['sub_time'] + $plus_time, //自动触发时间补差
'end_time' => $this->_task_info ['end_time'] + $plus_time ), //任务结束时间补差
array ('task_id' => $this->_task_id ) );
		
		$this->union_task_submit();//联盟推广发布
		
		//积分扣除
		if ($this->_task_info ['att_credit']) {
			keke_user_class::credit_out ( $this->_guid, $this->_gusername, 2, '任务#' . $this->_task_id . '的增值服务', $this->_task_info ['att_credit'] );
		}
		
		//生成动态
		$feed_arr = array ("feed_username" => array ("content" => $this->_task_info ['username'], "url" => "index.php?do=shop&u_id={$this->_task_info['uid']}" ), "action" => array ("content" => "发布了任务", "url" => "", 'cash' => $this->_task_info ['task_cash'] ), "event" => array ("content" => $this->_task_info ['task_title'], "url" => "index.php?do=task&task_id=" . $this->_task_info ['task_id'] ) );
		kekezu::save_feed ( $feed_arr, $this->_task_info ['uid'], $this->_task_info ['username'], 'pub_task', $this->_task_id );
		
		//消息通知
		$v = array ("用户名" => $this->_task_info ['username'], "任务编号" => $this->_task_id, "任务标题" => $this->_task_title, "任务链接" => '<a href="' . $this->_task_id . '" target="_blank">' . $this->_task_title . '</a>', "任务状态" => $this->_task_status_arr [$this->_task_status], "开始时间" => date ( 'Y-m-d H:i:s', $this->_task_info ['start_time'] ), "投稿结束时间" => date ( 'Y-m-d H:i:s', $this->_task_info ['sub_time'] + $plus_time ), "选稿结束时间" => date ( 'Y-m-d H:i:s', $this->_task_info ['end_time'] + $plus_time ) );
		$this->notify_user ( "task_pub", "任务发布消息提示", $v ); // 通知威客
	

	//同城邀请
	//$this->city_message_send();
	

	//其它操作
	

	}
	
	/**
	 * 任务，稿件状态数组	 
	 */
	public function status_init() {
		$this->_task_status_arr = $this->get_task_status ();
		$this->_work_status_arr = $this->get_work_status ();
	}
	/**
	 * 任务延期规则
	 */
	public function delay_rule_init() {
		$this->_delay_rule = keke_task_config::get_delay_rule ( $this->_model_id, '3600' );
	
	}
	/**
	 * 威客权限动作判断  
	 */
	public function wiki_priv_init() {
		$arr = preward_priv_class::get_priv ( $this->_task_id, $this->_model_id, $this->_userinfo );
		$this->_priv = $this->user_priv_format ( $arr );
	}
	
	/**
	 * 任务状态说明
	 */
	public function get_task_timedesc() {
		global $_lang;
		$status_arr = $this->_task_status_arr;
		$task_status = $this->_task_status;
		$task_info = $this->_task_info;
		$time_desc = array ();
		switch ($task_status) {
			case "-1" : //待审核
				$time_desc ['time_desc'] = "发布尚未完成"; //时间状态描述
				break;
			case "0" : //待审核
				$time_desc ['time_desc'] = "任务尚未开始"; //时间状态描述
				$time_desc ['ext_desc'] = "等待雇主付款"; //追加描述
				

				$time_desc ['g_action'] = '付款后任务才开始'; //雇主追加描述
				

				break;
			case "1" : //待审核
				$time_desc ['time_desc'] = "任务尚未通过审核"; //时间状态描述
				$time_desc ['ext_desc'] = "等待客服审核"; //追加描述
				

				$time_desc ['g_action'] = "等待客服审核后才能交稿"; //雇主追加描述
				

				break;
			case '2' : //投稿中
				$time_desc ['time_desc'] = $_lang ['from_hand_work_deadline']; //时间状态描述
				$time_desc ['time'] = $task_info ['sub_time']; //当前状态结束时间
				$time_desc ['ext_desc'] = $_lang ['task_working_and_can_hand_work'];
				if ($this->_task_config ['open_select'] == 'open') {
					$time_desc ['g_action'] = $_lang ['present_state_employer_can_choose'];
				}
				break;
			case '3' : //选稿中
				$time_desc ['time_desc'] = $_lang ['from_choose_deadline']; //时间状态描述
				$time_desc ['time'] = $task_info ['end_time'];
				$time_desc ['ext_desc'] = $_lang ['task_choosing_and_wait_employer_choose'];
				break;
			case "7" : //冻结中
				$time_desc ['ext_desc'] = $_lang ['task_diffrent_opnion_and_web_in']; //追加描述
				break;
			case "8" : //结束
				$time_desc ['ext_desc'] = $_lang ['task_haved_complete']; //追加描述
				break;
			case "9" : //失败
				$time_desc ['ext_desc'] = $_lang ['task_timeout_and_no_works_fail']; //追加描述
				break;
			case "10" : // 失败
				$time_desc ['ext_desc'] = "任务审核失败"; // 追加描述
				break;
			case "11" : //仲裁
				$time_desc ['ext_desc'] = $_lang ['task_arbitrating'];
		}
		return $time_desc;
	}
	
	/**
	 * 获取任务稿件信息  支持分页，用户前端稿件列表
	 * @param array $w 前端查询条件数组
	 * ['work_status'=>稿件状态	
	 * 'user_type'=>用户类型 --有值表示自己
	 * ......]
	 * @param string $order 排列条件
	 * @param array $p 前端传递的分页初始信息数组
	 * ['page'=>当前页面
	 * 'page_size'=>页面条数
	 * 'url'=>分页链接
	 * 'anchor'=>分页锚点]
	 * @return array work_list
	 */
	public function get_work_info($w = array(), $order = null, $p = array()) {
		global $kekezu, $_K;
		$work_arr = array ();
		$sql = " select a.*,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile,b.residency,b.seller_credit,b.seller_good_num,b.seller_total_num,b.w_level from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		
		$count_sql = " select count(a.work_id) from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$where = " where a.task_id = '$this->_task_id' ";
		
		if (! empty ( $w )) {
			$w ['work_id'] and $where .= " and a.work_id='" . $w ['work_id'] . "'";
			$w ['user_type'] == 'my' and $where .= " and a.uid = '$this->_uid'";
			$where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
		/**待添加**/
		}
		$order and $where .= " order by " . $order or $where .= " order by (CASE WHEN  a.work_status!=0 THEN work_status ELSE work_id END) desc,work_time asc ";
		
		if (! empty ( $p )) {
			$page_obj = $kekezu->_page_obj;
			$page_obj->setAjax ( 1 );
			$page_obj->setAjaxDom ( "gj_summery" );
			$count = intval ( db_factory::get_count ( $count_sql . $where ) );
			$pages = $page_obj->getPages ( $count, $p ['page_size'], $p ['page'], $p ['url'], $p ['anchor'] );
			$pages ['count'] = $count;
			$where .= $pages ['where'];
		}
		$work_info = db_factory::query ( $sql . $where );
		$work_info = kekezu::get_arr_by_key ( $work_info, 'work_id' );
		$work_arr ['work_info'] = $work_info;
		$work_arr ['pages'] = $pages;
		$work_arr ['mark'] = $this->has_mark ( implode ( ',', array_keys ( $work_info ) ) );
		return $work_arr;
	}
	
	/**
	 * 返回任务稿件数信息	 
	 * 'max'=>'可交稿件最大数'
	 */
	public function get_work_count($where) {
		//总共可交稿件数
		if ($where == 'max') {
			$work_count = intval ( $this->_task_info ['work_count'] );
			$count = $work_count * (1 + intval ( $this->_task_config ['work_percent'] ) / 100);
		} else {
			$count = db_factory::get_count ( sprintf ( "select count(work_id) from %switkey_task_work where %s and task_id='%d'", TABLEPRE, $where, $this->_task_id ) );
		}
		return intval ( $count );
	}
	/**
	 * 判断所交稿件数(合格稿件数)是否达标	 
	 *@param $type 'hand'=>'所交稿件' 'hege'=>合格稿件
	 */
	public function check_work_if_standard($type) {
		$work_count = intval ( $this->_task_info ['work_count'] ); //所需稿件数
		//合格稿件数		
		if ($type == 'hand') {
			//总共可交稿件数
			$totle_count = $this->get_work_count ( "max" );
			//已交稿件数
			$hand_count = $this->get_work_count ( "work_status in(0,12)" );
			if ($hand_count < $totle_count) {
				return true; //可交
			} else {
				return false;
			}
		} elseif ($type == 'hege') {
			$hege_count = $this->get_work_count ( "work_status=12" );
			if ($work_count > $hege_count) {
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * 任务交稿
	 * @param string $work_desc 交稿描述
	 * @param $hidemode 隐藏模式
	 * @param int    $hidework 1结束后公开  2永久保密
	 * @param string $file_ids 稿件附件编号串  eg:1,2,3,4,5
	 * @see keke_task_class::work_hand()
	 */
	public function work_hand($work_desc, $file_ids, $hidemode = '', $hidework = 0, $output = 'normal') {
		global $_lang;
		global $_K;
		if ($this->check_if_can_hand ( $url, $output )) {
			if ($this->check_work_if_standard ( 'hand' )) {
				$handed = $this->check_if_handed ();
				$valid_hide = keke_glob_class::valid_hide_mode ( $hidemode ); //验证隐藏模式
				$work_obj = new Keke_witkey_task_work_class ();
				if ($valid_hide) {
					$work_obj->setHide_work ( intval ( $hidework ) );
				} else {
					$work_obj->setHide_work ( 0 );
				}
				$work_obj->setTask_id ( $this->_task_id );
				$work_obj->setUid ( $this->_uid );
				$work_obj->setUsername ( $this->_username );
				CHARSET == 'gbk' and $work_desc = kekezu::utftogbk ( $work_desc );
				$work_obj->setWork_desc ( $work_desc );
				$work_obj->setWork_status ( 0 );
				$work_obj->setWork_title ( $this->_task_title );
				$work_obj->setWork_time ( time () );
				
				$file_arr = array_unique ( array_filter ( explode ( ',', $file_ids ) ) );
				$f_ids = implode ( ',', $file_arr ); //附件编号串
				

				//稿件缩略图保存
				$f_ids and $pic_file = db_factory::get_one ( "select save_name,file_ext from " . TABLEPRE . "witkey_file where file_id in ({$f_ids}) and file_ext in ('jpg','gif','png') " );
				
				if ($pic_file) { //如果有缩略图
					$filename = 'exfile_' . work_id . '_' . time () . '.' . $pic_file ['file_ext'];
					$a = UPLOAD_ROOT . 'exfile_' . work_id . '_' . time () . '.' . $pic_file ['file_ext'];
					
					//					keke_img_class::resize_pic(S_ROOT.'./'.$pic_file['save_name'], 217, 150,$a);
					//					$work_obj->setWork_pic('data/uploads/' . UPLOAD_RULE.$filename);
					

					$work_obj->setWork_pic ( $pic_file ['save_name'] );
				}
				
				$f_ids and $work_obj->setWork_file ( $f_ids );
				
				$work_id = $work_obj->create_keke_witkey_task_work ();
				if ($valid_hide && $work_id) {
					$payitem = keke_payitem_class::get_payitem_info ( 'witkey', 'preward', true ); //可购买服务
					$payitem = $payitem ['workhide']; //稿件隐藏
					if ($payitem) {
						$pass = false;
						switch ($hidemode) {
							case "no" : //不隐藏
								$pass = true;
								break;
							case "free" : //免费使用
								$payitem ['item_cash'] == 0.00 and $pass = true;
								break;
							case "vip" : //VIP特权
								$this->_userinfo ['isvip'] && $payitem ['vipfree'] and $pass = true;
								break;
							case "remain" : //余额使用
								$remain = keke_payitem_class::payitem_exists ( $this->_uid, 'workhide' );
								if ($remain) {
									$res = keke_payitem_class::payitem_record ( "workhide", '1', 'work', 'spend', $work_id, $this->_task_id );
									$pass = true;
								}
								break;
							case "dzcredit" : //论坛积分
								if ($payitem ['integral_cost']) {
									$dz_credit = keke_user_class::get_credit ( $this->_uid ); //论坛积分
									if ($dz_credit >= $payitem ['integral_cost']) {
										keke_user_class::credit_out ( $this->_uid, $this->_username, 2, '任务#' . $this->_task_id . '使用稿件隐藏', $payitem ['integral_cost'] );
										$pass = true;
									}
								}
								break;
						}
						$pass == false and db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task_work set hide_work=0 where work_id=' . $work_id );
					}
				}
				if ($work_id) {
					//更新附件表信息
					$f_ids and db_factory::execute ( sprintf ( "update %switkey_file set work_id='%d',task_title='%s',obj_id='%d' where file_id in (%s)", TABLEPRE, $work_id, $this->_task_title, $work_id, $f_ids ) );
					//更新任务交稿数
					$this->plus_work_num ();
					//更新用户交稿数
					$this->plus_take_num ();
					$handed or $this->plus_wiki_num (); //之前没交过更新任务交稿人数
					

					//发站内信
					$notice_url = "<a href=\"$_K[siteurl]/index.php?do=task&task_id=.$this->_task_id\" target=\"_blank\">$this->_task_title</a>";
					$g_notice = array ("用户" => $this->_username, "任务标题" => $this->_task_title, "任务链接" => $notice_url ); //雇主
					$this->notify_user ( 'task_hand', "威客交稿通知", $g_notice, '2', $this->_gusername );
					
					kekezu::keke_show_msg ( $url, $_lang ['congratulate_you_hand_work_success'], '', $output );
				} else {
					kekezu::keke_show_msg ( $url, $_lang ['hand_work_fail_and_operate_agian'], 'error', $output );
				}
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['hand_work_fail_for_the_work_full'], 'error', $output );
			}
		
		}
	
	}
	/**
	 * 任务延期
	 */
	public function task_delay() {
		global $_K;
		$_D = $_REQUEST;
		$delay_rule = $this->_delay_rule; //延期规则
		$delay_total = sizeof ( $delay_rule ); //可延期次数
		$task_info = $this->_task_info;
		$delay_count = intval ( $task_info ['is_delay'] ); //已延期次数
		if ($delay_count >= $delay_total) { //延期超量
			kekezu::echojson ( '延期次数超过限制,延期失败', 0 );
			die ();
		}
		$mode = intval ( $_D ['delay_mode'] ); //延期模式 1.增加稿件 2.延长天数
		switch ($mode) {
			case 1 :
				$count = intval ( $_D ['delay_count'] ); //新增稿件数量
				$min_count = intval ( $this->_task_config ['min_delay_count'] ); //配置最新增稿件
				$this_min_count = intval ( $delay_rule [$delay_count] ['defer_rate'] ); //本次最小新增稿件数
				$min_count > $this_min_count or $min_count = $this_min_count; //真正最少稿件数
				if ($count < $min_count) {
					kekezu::echojson ( '本次延期最少需新增' . $min_count . '个稿件,延期失败', 0 );
					die ();
				}
				$single_cash = floatval ( $task_info ['single_cash'] ); //稿件单价
				$cost = $count * $single_cash; //实际花费
				

				$order_obj = new Keke_witkey_order_class ();
				$order_obj->setModel_id ( $task_info ['model_id'] );
				$order_obj->setObj_id ( $this->create_delay ( $cost ) );
				$order_obj->setObj_type ( 'task_delay' );
				$order_obj->setOrder_amount ( $cost );
				$order_obj->setOrder_name ( "任务 {$this->_task_title}的延期加价" );
				$order_obj->setOrder_status ( 'wait' );
				$order_obj->setOrder_time ( time () );
				$order_obj->setOrder_uid ( $task_info ['uid'] );
				$order_obj->setOrder_username ( $task_info ['username'] );
				$order_obj->setTask_id ( $this->_task_id );
				//是否已存在待支付的订单
				$order_id = db_factory::get_count ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='task_delay' and obj_id='$this->_task_id'" );
				if ($order_id) {
					$order_obj->setWhere ( "order_id = '{$order_id}'" );
					$order_obj->edit_keke_witkey_order ();
				} else {
					$order_id = $order_obj->create_keke_witkey_order ();
				}
				kekezu::echojson ( '您此次延期成功,新增稿件' . $count . '个,点击确认跳转至支付页面', 1, $_K ['siteurl'] . '/index.php?do=pay&order_id=' . $order_id );
				die ();
				break;
			case 2 :
				$max_day = intval ( $this->_task_config ['max_delay'] ); //配置最大延期天数
				$day = intval ( $_D ['delay_day'] ); //延长天数
				$delay_id = $this->create_delay ( 0, $day, 2 ); //状态2.待审核
				$delay_id and kekezu::echojson ( '您此次延期成功,延长天数为' . $day . '天,请等待客服审核。', 1, $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id ) or kekezu::echojson ( '系统繁忙,您此次延期失败,详细请咨询客服.', 0, $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id );
				die ();
				break;
		}
	}
	/**
	 * 产生延期记录
	 */
	public function create_delay($cash = 0, $day = 0, $status = 0) {
		$delay_obj = new Keke_witkey_task_delay_class ();
		//延期
		$delay_obj->setDelay_cash ( $cash );
		$delay_obj->setOn_time ( time () );
		$delay_obj->setUid ( $this->_uid );
		$delay_obj->setDelay_day ( $day );
		$delay_obj->setTask_id ( $this->_task_id );
		$delay_obj->setDelay_status ( $status );
		//或许有上次未付款的延期记录
		$delay_id = db_factory::get_count ( "select delay_id from " . TABLEPRE . "witkey_task_delay where task_id = '{$this->_task_id}' and delay_status={$status}" );
		if ($delay_id) {
			$delay_obj->setWhere ( "delay_id = '{$delay_id}'" );
			$delay_obj->edit_keke_witkey_task_delay ();
		} else {
			$delay_id = $delay_obj->create_keke_witkey_task_delay ();
		}
		return $delay_id;
	}
	/**
	 * 任务选稿
	 * @param string $url    消息提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @see keke_task_class::work_choose()
	 */
	public function work_choose($work_id, $to_status, $url = '', $output = 'normal', $trust_response = false) {
		global $_K, $kekezu;
		global $_lang;
		kekezu::check_login ( $_K ['siteurl'] . '/index.php?do=login', $output );
		//是否可以选稿
		$this->check_if_operated ( $work_id, $to_status, $url, $output );
		$work_status_arr = $this->_work_status_arr;
		//test联盟任务改变状态
		if ($this->set_work_status ( $work_id, $to_status )) {
			$title_url = "<a href =" . $_K ['siteurl'] . "/index.php?do=task&task_id=" . $this->_task_id . " target=\"_blank\">" . $this->_task_title . "</a>";
			$work_info = $this->get_task_work ( $work_id );
			if ($to_status == 12) {
				//威客中标一些操作
				$this->work_choosed ( $work_info, $title_url );
				
				//判断当前稿件否是最后一个稿件，如果是改变任务状态						
				if (! $this->check_work_if_standard ( 'hege' )) {
					if (db_factory::execute ( "update " . TABLEPRE . "witkey_task set task_status = 8 ,exec_time = 0 where task_id = '$this->_task_id'" )) {
						if (strpos(' '.$this->_task_info['pay_item'],'top')) {
							//重设增值属性
							$payitem = str_replace ( 'top', '', $this->_task_info ['pay_item'] );
							$payitem = implode ( ',', array_filter ( explode ( ',', $payitem ) ) );
							db_factory::updatetable ( TABLEPRE . "witkey_task", array (//'is_top' => 0, //清空置顶属性
								'pay_item' => $payitem ), array ('task_id' => $this->_task_id ) );
						}
						/**
						 * 结算发布推广
						 */
						$kekezu->init_prom ();
						$kekezu->_prom_obj->dispose_prom_event ( 'pub_task', $this->_task_title, $this->_guid, $this->_task_info ['real_cash'], $this->_task_id );
						
						if ($this->_task_info ['task_union']>1) {//通知联盟
							$bid_uid = array();
							$ids = db_factory::query('select uid from '.TABLEPRE.'witkey_task_work where work_status=12 and task_id='.$this->_task_id);
							foreach($ids as $v){
								$bid_uid[] = $v['uid'];
							}
							!empty($bid_uid) and $this->union_task_close(1,$bid_uid);//通知联盟结束
						}
						kekezu::notify_user ( $_lang ['task_over_notice'], $_lang ['you_pub_task'] . $title_url . $_lang ['haved_pefect_over'], $this->_guid, $this->_gusername );
					}
				}
			}
			
			kekezu::keke_show_msg ( $url, $_lang ['work'] . $work_status_arr [$to_status] . $_lang ['set_success'], '', $output );
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['work'] . $work_status_arr [$to_status] . $_lang ['set_fail'], 'error', $output );
		}
	
	}
	
	/**
	 * 威客中标后打钱操作	 
	 * @param array $work_info
	 */
	public function work_choosed($work_info, $title_url) {
		global $_K;
		global $_lang;
		
		//给威客打钱
		$single_cash = floatval ( $this->_task_info ['single_cash'] );
		$real_cash = $single_cash * $this->_task_config ['task_rate'] / 100;
		$profit_cash = floatval ( $single_cash - $real_cash );
		
		keke_finance_class::cash_in ( $work_info ['uid'], $real_cash, 0, 'task_bid', '', 'task', $work_info ['work_id'], $profit_cash );
		
		//通知威客				
		$url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_title . '</a>';
		$v = array ($_lang ['username'] => $work_info ['username'], $_lang ['website_name'] => $kekezu->_sys_config ['website_name'], $_lang ['task_id'] => "#" . $this->_task_id, $_lang ['task_title'] => $url );
		//$this->notify_user ( "task_bid", $_lang['work_bid'], $v, '1', $work_info ['uid'] );
		kekezu::notify_user ( $_lang ['work_bid'], $_lang ['you_submit_to'] . $title_url . $_lang ['task_work_bid_and_get_cash'] . $real_cash . $_lang ['yuan'], $work_info ['uid'] );
		//写入feed表 
		$feed_arr = array ("feed_username" => array ("content" => $work_info ['username'], "url" => "index.php?do=shop&u_id=$work_info[uid]" ), "action" => array ("content" => $_lang ['success_bid_haved'], "url" => "", 'cash' => $real_cash ), "event" => array ("content" => "$this->_task_title", "url" => "index.php?do=task&task_id=$this->_task_id", 'cash' => $real_cash ) );
		kekezu::save_feed ( $feed_arr, $work_info ['uid'], $work_info ['username'], 'work_accept', $this->_task_id );
		
		//更改威客稿件被采纳次数
		$this->plus_accepted_num ( $work_info ['uid'] );
		/*$this->plus_mark_num (); //更新互评次数; 计件无互评
		/**威客对雇主记录**/
	//keke_user_mark_class::create_mark_log ( $this->_model_code, '1', $work_info ['uid'], $this->_guid, $work_info ['work_id'], $this->_task_info ['single_cash'], $this->_task_id, $work_info ['username'], $this->_gusername );
	/**雇主对威客记录**/
	//keke_user_mark_class::create_mark_log ( $this->_model_code, '2', $this->_guid, $work_info ['uid'], $work_info ['work_id'], $real_cash, $this->_task_id, $this->_gusername, $work_info ['username'] );
	

	}
	
	/**
	 * 判断是否可以选稿
	 * 任务是否处于选稿状态，合格稿件是否达到所需要稿件数,当前稿件是否可以被操作 
	 */
	public function check_if_operated($work_id, $to_status, $url = '', $output = 'normal') {
		global $_lang;
		if ($this->check_if_can_choose ( $url, $output ) && $this->valid_work_status ( $work_id ) != $to_status) {
			$work_status = db_factory::get_count ( sprintf ( "select work_status from %switkey_task_work where work_id='%d'", TABLEPRE, $work_id ) );
			switch (intval ( $work_status )) {
				case 0 :
					if ($to_status == 12) {
						if ($this->check_work_if_standard ( 'hege' )) {
							return true;
						} else {
							kekezu::keke_show_msg ( $url, $_lang ['task_hg_work_full_and_not_operate_bid_work'], 'error', $output );
						}
					} else {
						return true;
					}
					break;
				case 6 :
					kekezu::keke_show_msg ( $url, $_lang ['task_bid_work_full_and_not_operate_choose_work'], 'error', $output );
					break;
				case 7 :
					kekezu::keke_show_msg ( $url, $_lang ['task_not_recept_work_full_and_not_operate_choose_work'], 'error', $output );
					break;
				case 8 :
					kekezu::keke_show_msg ( $url, $_lang ['task_not_operate_work_and_not_operate_choose_work'], 'error', $output );
					break;
			}
		} else {
			kekezu::keke_show_msg ( $url, "稿件当前状态无法操作", "error", $output );
		}
	}
	
	/**
	 * 操作判断
	 * //注意用户权限的判断   
	 * 雇主不受威客权限的限制、、拥有威客的所有权限
	 * 威客严格受到条件约束
	 * 威客限制：查看任务       
	 * 留言        
	 * 举报	
	 * @see keke_task_class::process_can()
	 */
	public function process_can() {
		$wiki_priv = $this->_priv; //威客权限数组		
		$process_arr = array ();
		$status = intval ( $this->_task_status );
		$task_info = $this->_task_info;
		$config = $this->_task_config;
		$g_uid = $this->_guid;
		$uid = $this->_uid;
		switch ($status) {
			case "-1" :
				if ($g_uid == $uid) {
					$process_arr ['edit'] = true; //修改任务
					$process_arr ['publish'] = true; //继续发布
				}
				break;
			case "0" :
				if ($g_uid == $uid) {
					$process_arr ['edit'] = true; //修改任务
					$process_arr ['task_pay'] = true;
				}
				break;
			case "1" : //任务未开始
				if ($g_uid == $uid) {
					$process_arr ['edit'] = true; //修改任务
				}
				break;
			case '2' : //交稿期
				if ($uid == $g_uid) {
					$task_info ['ext_desc'] or $process_arr ['reqedit'] = true; //补充需求
					$process_arr ['invite'] = true; // 邀请人才
					sizeof ( $this->_delay_rule ) > 0 and $process_arr ['delay'] = true; //延期加价
					if ($config ['open_select'] == 'open' && $this->check_work_if_standard ( 'hege' )) {
						$process_arr ['work_choose'] = true; //开启投稿中选稿
					}
					$process_arr ['download'] = true; //文件下载
					$process_arr ['work_comment'] = true; //稿件回复
				} else {
					$process_arr ['work_hand'] = true; //交稿
					$process_arr ['task_comment'] = true; //任务评论
					$process_arr ['work_comment'] = true; //稿件回复
				}
				
				$process_arr ['work_report'] = true; //稿件举报 
				$process_arr ['work_cancel'] = true; //取消稿件中标
				break;
			case '3' : //选稿期
				if ($uid == $g_uid) {
					if ($this->check_work_if_standard ( 'hege' )) {
						$process_arr ['work_choose'] = true;
						$process_arr ['work_comment'] = true; //稿件留言
					}
					$process_arr ['download'] = true; //文件下载
				} else {
					$process_arr ['task_comment'] = true;
				}
				$process_arr ['work_report'] = true;
				$process_arr ['work_cancel'] = true; //取消稿件中标
				break;
			case '8' : //任务结束
				if ($uid == $g_uid) {
					$process_arr ['work_comment'] = true; //稿件留言
					$process_arr ['download'] = true; //文件下载
				} else {
					$process_arr ['task_comment'] = true;
				
				}
				break;
		
		}
		if ($status > 1) {
			$process_arr ['interactive'] = true; //互动
			$process_arr ['onekey'] = true; //发布类似需求
			if ($uid != g_uid) {
				$process_arr ['task_report'] = true; // 任务举报
				$process_arr ['task_favor'] = true; // 任务收藏
				$process_arr ['task_complaint'] = true; // 任务投诉
			} else {
				$process_arr ['work_complaint'] = true; // 稿件投诉
			}
		}
		if ($status > 4) {
			$process_arr ['work_mark'] = true; //稿件评论
			$process_arr ['task_mark'] = true; //任务评价
		}
		$this->_process_can = $process_arr;
		return $process_arr;
	}
	/**
	 * 时间触发投稿到期处理
	 * 有稿件：进入选稿
	 * 无稿件：任务冻结等待退款
	 */
	public function time_hand_end() {
		global $kekezu;
		if ($this->_task_status == 2 && $this->_task_info ['sub_time'] < time ()) { //任务投稿时间到
			

			if (strpos(' '.$this->_task_info['pay_item'],'top')) {
				//重设增值属性
				$payitem = str_replace ( 'top', '', $this->_task_info ['pay_item'] );
				$payitem = implode ( ',', array_filter ( explode ( ',', $payitem ) ) );
				db_factory::updatetable ( TABLEPRE . "witkey_task", array (//'is_top' => 0, //清空置顶属性
'pay_item' => $payitem ), array ('task_id' => $this->_task_id ) );
			}
			//获得稿件列表
			$work_list = kekezu::get_table_data ( "work_id,uid,username,work_status,work_time", "witkey_task_work", "task_id='$this->_task_id' and work_status!=16" );
			//状态分组列表
			$work_st_list = array ();
			foreach ( $work_list as $work ) {
				$work_st_list [$work ['work_status']] [$work ['work_id']] = $work;
			}
			
			/*无有效稿件时  冻结退款*/
			if (! $work_list) {
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 7, //任务状态改为冻结
'exec_time' => 0 ), array ('task_id' => $this->_task_id ) );
				
				//产生冻结记录
				db_factory::inserttable ( TABLEPRE . "witkey_task_frost", array ('frost_status' => '2', 'task_id' => $this->_task_id, 'frost_time' => time (), 'restore_time' => 0, 'frost_key' => 'preward_overtime', 'frost_reason' => '该任务投稿期结束，但没有可用的有效稿件' ) );
				
				$this->union_task_close(-1);//通知联盟失败
				
				//消息通知雇主
				kekezu::notify_user ( "您有一个任务进入冻结状态", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已到期，因没有有效稿件进入冻结，请联系本站客服。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				return true;
			}
			
			$work_count = db_factory::get_count ( "select count(*) from " . TABLEPRE . "witkey_task_work where task_id='$this->_task_id' and work_status = 12" );
			if ($work_count >= $this->_task_info ['work_count']) {
				//选稿已足够
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 8, //任务状态改为成功结束
'exec_time' => 0 ), //下次触发时间清零
array ('task_id' => $this->_task_id ) );
				
				/**
				 * 结算发布推广
				 */
				$kekezu->init_prom ();
				$kekezu->_prom_obj->dispose_prom_event ( 'pub_task', $this->_task_title, $this->_guid, $this->_task_info ['real_cash'], $this->_task_id );
				/**
				 * 通知联盟
				 */
				if ($this->_task_info ['task_union']>1) {
					$bid_uid = array();
					$ids = db_factory::query('select uid from '.TABLEPRE.'witkey_task_work where work_status=12 and task_id='.$this->_task_id);
					foreach($ids as $v){
						$bid_uid[] = $v['uid'];
					}
					!(empty($bid_uid)) and $this->union_task_close(1,$bid_uid);//通知联盟
				}
				//消息通知雇主
				kekezu::notify_user ( "您的任务#" . $this->_task_id . "结束", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已结束。', $this->_task_info ['uid'], $this->_task_info ['username'] );
			} else {
				
				if ($work_st_list [0] || $work_st_list [14]) {
					/*这是有稿件可选但不选中标的*/
					db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 3, //任务状态改为选稿期
'exec_time' => $this->_task_info ['end_time'] ), //下次触发时间设置为选稿结束
array ('task_id' => $this->_task_id ) );
					
					//消息通知雇主
					kekezu::notify_user ( "您有未结束的待选稿任务需要处理", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 还有未处理的稿件，请尽快处理。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				
				} else {
					/*这是表示已经没有操作的稿件了*/
					db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 7, //任务状态改为冻结
'exec_time' => 0 ), //下次触发时间设置为选稿结束
array ('task_id' => $this->_task_id ) );
					
					$this->union_task_close(-1);//通知联盟失败
					
					//产生冻结记录
					db_factory::inserttable ( TABLEPRE . "witkey_task_frost", array ('frost_status' => '2', 'task_id' => $this->_task_id, 'frost_time' => time (), 'restore_time' => 0, 'frost_key' => 'preward_overtime', 'frost_reason' => '该任务投稿期结束，待选的稿件不足，又没有未操作的稿件' ) );
					
					//消息通知雇主
					kekezu::notify_user ( "您的任务#" . $this->_task_id . "结束", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已到期结束，因合格稿件不足，任务暂时冻结，您可以联系客服确定是否退款或者延期。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				}
			}
		}
	}
	
	/**
	 * 时间触发任务选稿到期处理
	 * 选稿到期被触发可能是没有选中标  也可能是没有选入围.
	 * 无稿件：任务冻结等待退款。
	 */
	public function time_choose_end() {
		global $kekezu;
		if ($this->_task_status == 3 && $this->_task_info ['end_time'] < time ()) { //选稿结束
			

			//读取稿件数量
			$work_count = db_factory::get_count ( "select count(*) from " . TABLEPRE . "witkey_task_work where task_id='$this->_task_id' and work_status = 12" );
			if ($work_count >= $this->_task_info ['work_count']) {
				//选稿已足够
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 8, //任务状态改为成功结束
'cash_status' => 2, //已支付托管
'exec_time' => 0 ), //下次触发时间清零
array ('task_id' => $this->_task_id ) );
				/**
				 * 结算发布推广
				 */
				$kekezu->init_prom ();
				$kekezu->_prom_obj->dispose_prom_event ( 'pub_task', $this->_task_title, $this->_guid, $this->_task_info ['task_cash'], $this->_task_id );
				/**
				 * 通知联盟
				 */
				if ($this->_task_info ['task_union']>1) {
					$bid_uid = array();
					$ids = db_factory::query('select uid from '.TABLEPRE.'witkey_task_work where work_status=12 and task_id='.$this->_task_id);
					foreach($ids as $v){
						$bid_uid[] = $v['uid'];
					}
					!empty($bid_uid) and $this->union_task_close(1,$bid_uid);//通知联盟失败
				}
				//消息通知雇主
				kekezu::notify_user ( "您的任务#" . $this->_task_id . "结束", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已结束。', $this->_task_info ['uid'], $this->_task_info ['username'] );
			} else {
				//选稿不足
				

				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 7, //任务状态改为冻结
'exec_time' => 0 ), //下次触发时间设置为选稿结束
array ('task_id' => $this->_task_id ) );
				
				$this->union_task_close(-1);//通知联盟失败
				
				//产生冻结记录
				db_factory::inserttable ( TABLEPRE . "witkey_task_frost", array ('frost_status' => '2', 'task_id' => $this->_task_id, 'frost_time' => time (), 'restore_time' => 0, 'frost_key' => 'preward_overtime', 'frost_reason' => '该选稿期结束，但所选稿件不足' ) );
				
				//消息通知雇主
				kekezu::notify_user ( "您的任务#" . $this->_task_id . "结束", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已到期结束，因合格稿件不足，任务暂时冻结，您可以联系客服确定是否退款或者延期。', $this->_task_info ['uid'], $this->_task_info ['username'] );
			
			}
		}
	}
	
	/**
	 * @return 返回计件悬赏任务状态
	 */
	
	public static function get_task_status() {
		global $_lang;
		return array ("-1" => "未确认", "0" => $_lang ['task_no_pay'], "1" => $_lang ['task_wait_audit'], "2" => $_lang ['task_vote_choose'], "3" => $_lang ['task_choose_work'], "7" => $_lang ['freeze'], "8" => $_lang ['task_over'], "9" => $_lang ['fail'], "10" => $_lang ['task_audit_fail'], "11" => $_lang ['arbitrate'] );
	}
	
	/**
	 * @return 返回计件悬赏稿件状态
	 * 
	 */
	public static function get_work_status() {
		global $_lang;
		return array ('11' => '中标', '12' => '合格', '13' => '入围', '14' => '备选', '15' => '未采纳', '16' => '不可选标' );
	}
	/**
	 * 设置稿件状态
	 */
	public function set_work_status($work_id, $to_status) {
		return db_factory::execute ( sprintf ( " update %switkey_task_work set work_status='%d',op_time='%s' where work_id='%d'", TABLEPRE, $to_status, time (), $work_id ) );
	}
	/**
	 * 
	 * 订单处理
	 * @param int $order_id //订单id
	 */
	public function dispose_order($order_id) {
		global $_K;
		global $_lang;
		//后台配置
		$task_config = $this->_task_config;
		$task_info = $this->_task_info; //任务信息
		$url = $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id;
		$task_status = $this->_task_status;
		$order_info = db_factory::get_one ( sprintf ( "select order_amount,order_status from %switkey_order where order_id='%d'", TABLEPRE, intval ( $order_id ) ) );
		$order_amount = $order_info ['order_amount'];
		if ($order_info ['order_status'] == 'ok') {
			$task_status == 1 && $notice = $_lang ['task_pay_success_and_wait_admin_audit'];
			$task_status == 2 && $notice = $_lang ['task_pay_success_and_task_pub_success'];
			return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $notice, $url, 'success' );
		} else {
			$res = keke_finance_class::cash_out ( $this->_task_info ['uid'], $order_amount, 'pub_task' ); //支付费用
			if ($res) { //支付成功
				//更改订单状态到已付款状态
				db_factory::updatetable ( TABLEPRE . "witkey_order", array ("order_status" => "ok" ), array ("order_id" => "$order_id" ) );
				if ($order_amount < $task_config ['audit_cash']) { //如果订单的金额比发布任务时配置的最小金额要小
					$this->set_task_status ( 1 ); //状态更改为审核状态
					return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_success_and_wait_admin_audit'], $url, 'success' );
				} else {
					$this->set_task_status ( 2 ); //状态更改为进行状态
					return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_success_and_task_pub_success'], $url, 'success' );
				}
			} else { //支付失败
				$pay_url = $_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id"; //支付跳转链接
				return pay_return_fac_class::struct_response ( $_lang ['operate_notice'], $_lang ['task_pay_error_and_please_repay'], $pay_url, 'warning' );
			}
		}
	}
	
	/**
	 * 修改稿件查看状态
	 * @param int $work_arr  稿件列表
	 */
	public function edit_work_view_status($work_arr) {
		if ($this->_uid == $this->_guid) {
			if (is_array ( $work_arr )) {
				foreach ( $work_arr as $k => $v ) {
					$work_ids .= $v [work_id] . ',';
				}
			}
			if ($work_ids) {
				$work_ids = substr ( $work_ids, 0, strlen ( $work_ids ) - 1 );
				db_factory::execute ( sprintf ( "update  %switkey_task_work set view_status = 1 where work_id in ($work_ids)", TABLEPRE ) );
			}
		}
	}

}