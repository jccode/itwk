<?php
/**
 * 
 * 任务业务处理基类 ...
 * @author Administrator
 * @method init 任务信息初始化
 * =>模型配置初始化	 	task_config_init
 * =>当前任务信息   		task_info_init
 * =>雇主基本信息		employer_info_init
 * =>当前威客信息		witkey_info_init
 * =>获取随机客服信息	kf_info_init
 * =>任务时间规则
 * =>任务延期规则
 * check_if_favor       检测是否收藏
 * check_if_report      检测是否举报(维权|投诉)
 * check_if_master		  检测是否雇主
 * check_if_over_delay  检测是否超过延期次数    
 * check_if_good_mark   检测是否好评
 * 
 * get_work_fields     获取由于模型对应稿件表使用字段
 * get_search_condit    获取稿件区搜索统计
 * get_task_file		 获取任务附件
 * get_task_related	            获取相关任务
 * get_task_timedesc 	        获取任务时间描述
 * get_comment 			  获取任务(稿件)留言
 * get_user_special      获取用户指定字段信息
 * set_task_status		    任务状态变更
 * set_task_favor		    任务收藏
 * set_task_reqedit		   	    任务需求补充
 * set_task_delay		    任务延期加价
 * set_task_mark		    任务(稿件)评价
 * set_mark				评价
 * set_report             交易维权
 * set_comment          留言回复
 * del_work              稿件删除
 * notify_user      	      消息通知
 * 
 * process_can 		      当前操作判断
 * work_hand    		      任务稿件提交
 * work_choose 		      任务稿件选择		 
 * has_new_comment    稿件有信息的回复
 */
keke_lang_class::load_lang_class ( 'keke_task_class' );
abstract class keke_task_class {
	
	public $_task_obj; //任务对象
	public $_task_id; //任务编号
	public $_task_title; //任务标题
	public $_task_status; //任务状态
	public $_task_config; //任务配置
	public $_task_info; //任务信息
	

	public $_profit_rate; //任务提成比
	public $_fail_rate; //任务失败返金提成比
	

	public $_trust_mode; //担保模式
	public $_must_choosework; //保证选稿
	

	public $_guid; //雇主编号
	public $_gusername; //雇主姓名
	public $_g_userinfo; //雇主信息
	

	public $_uid; //当前用户编号
	public $_username; //当前用户名
	public $_userinfo; //当前用户信息
	public $_priv; //威客操作权限
	

	public $_kf_uid; //客服编号
	public $_kf_userinfo; //客服信息
	

	public $_process_can; //操作状态数组
	

	public $_model_id; //模型编号
	public $_model_code; //模型类型
	public $_model_name; //模型名称
	

	public $_msg_obj; //消息对象
	

	public function __construct($task_info) {
		$this->_task_info = $task_info;
		$task_info ['is_trust'] and $this->_trust_mode = "1" or $this->_trust_mode = "0"; //担保模式
		$task_info ['must_choosework'] and $this->_must_choosework = 1 or $this->_must_choosework = 0; //保证选稿
		$this->_task_id = $task_info ['task_id'];
		$this->_task_title = $task_info ['task_title'];
		$this->_task_status = $task_info ['task_status'];
		$this->_profit_rate = $task_info ['profit_rate'];
		$this->_fail_rate = $task_info ['task_fail_rate'];
		$this->init_task ();
		$this->_task_obj = new Keke_witkey_task_class ();
		$this->_msg_obj = new keke_msg_class ();
	
	}
	
	//动作执行
	public function exec_event($action) {
	
	}
	
	public function init_task() {
		$this->task_config_init ();
		$this->employer_info_init ();
		$this->witkey_info_init ();
		$this->kf_info_init ();
	}
	/**
	 * 任务配置初始化
	 */
	public function task_config_init() {
		global $model_list;
		$model_info = $model_list [$this->_task_info ['model_id']];
		if ($model_info) {
			$this->_model_id = $model_info ['model_id'];
			$this->_model_code = $model_info ['model_code'];
			$this->_model_name = $model_info ['model_name'];
			$model_info ['config'] and $task_config = unserialize ( $model_info ['config'] );
			$this->_task_config = $task_config;
		}
	}
	/**
	 * 雇主信息初始化
	 */
	public function employer_info_init() {
		$task_info = $this->_task_info;
		$this->_guid = $task_info ['uid'];
		$this->_gusername = $task_info ['username'];
		$this->_g_userinfo = kekezu::get_user_info ( $task_info ['uid'] );
	}
	/**
	 * 威客信息初始化
	 */
	public function witkey_info_init() {
		global $user_info;
		$this->_uid = $user_info ['uid'];
		$this->_username = $user_info ['username'];
		$this->_userinfo = $user_info;
	}
	/**
	 * 客服信息初始化
	 */
	public function kf_info_init() {
		$this->_kf_uid = $this->_task_info ['kf_uid'];
		$this->_kf_userinfo = kekezu::get_user_info ( $this->_task_info ['kf_uid'] );
	}
	/**
	 * 获取不同模型下的不同稿件表使用字段。
	 * @return array[tab=>表名,pk=>主键名,st=>状态字段名
	 */
	public function get_work_fields() {
		$mode_code = $this->_model_code;
		$fields_arr = array ();
		/*if ($mode_code == 'dtender' || $mode_code == 'tender') {
			$fields_arr ['tab'] = "witkey_task_bid";
			$fields_arr ['pk'] = "bid_id";
			$fields_arr ['st'] = "bid_status";
		} else {*/
		$fields_arr ['tab'] = "witkey_task_work";
		$fields_arr ['pk'] = "work_id";
		$fields_arr ['st'] = "work_status";
		//}
		return $fields_arr;
	}
	/**
	 * 获取稿件区搜索条件稿件统计。
	 */
	public function get_search_condit() {
		global $kekezu;
		//多少选标完后，对应名次的按钮还在的问题，去除缓存锁
		//$search_condit = $kekezu->_cache_obj->get('taskinfo_search_condit_cache_'.$this->_task_id);
		//if(!$search_condit){
			$search_condit = kekezu::get_table_data ( " count(work_id) count,work_status", "witkey_task_work", " task_id='" . $this->_task_id . "' ", "", 'work_status', "", 'work_status' );
			//$kekezu->_cache_obj->set('taskinfo_search_condit_cache_'.$this->_task_id , $search_condit,3600);
		//}
		return $search_condit;
	}
	/**
	 * 获取任务附件
	 */ 
	public function get_task_file($cache_time = 3600) {
		return kekezu::get_table_data ( "*", "witkey_file", " obj_type = 'task' and work_id='0' and task_id = '" . $this->_task_id . "'", "", "", "", "file_id", $cache_time );
	}
	/**
	 * 获取相关任务信息
	 * @param boolean $same		同类任务
	 * @param boolean $lasted   最新任务
	 * @return array ['same'=>
	 * 'lasted'=>]
	 */
	public function get_task_related($same = true, $lasted = true) {
		$related = array ();
		if ($same) {
			$same_task = db_factory::query ( sprintf ( " select task_title,task_id,task_cash from %switkey_task where indus_id = '%d' and model_id='%d' and task_id!='%d' and task_status in(2,3) and focus_num>0 order by task_cash desc limit 0,5", TABLEPRE, $this->_task_info ['indus_id'], $this->_model_id, $this->_task_id ) );
			$same_task and $related ['same'] = $same_task;
		}
		if ($lasted) {
			$favor_task = db_factory::query ( sprintf ( " select task_title,task_id,task_cash from %switkey_task where indus_id = '%d' and model_id='%d' and task_status in(2,3) and task_id!='%d' order by view_num desc limit 0,5", TABLEPRE, $this->_task_info ['indus_id'], $this->_model_id, $this->_task_id ) );
			$favor_task and $related ['favor'] = $favor_task;
		}
		return $related;
	}
	/**
	 * 获取任务(稿件留言)
	 * @param string $obj_type 留言对象
	 * @param int $obj_id     对象编号
	 * @param int $task_id 任务编号
	 * @param int $work_uid    投稿人
	 */
	
	public static function get_comment($obj_type, $task_id, $obj_id, $work_uid) {
		global $uid;
		$condit = "  from " . TABLEPRE . "witkey_comment where obj_type='$obj_type' and obj_id='$obj_id' and origin_id='$task_id'";
		$comment_arr = db_factory::query ( " select * " . $condit );
		if ($comment_arr && $work_uid == $uid) {
			db_factory::execute ( sprintf ( " update %switkey_comment set status = '1' where obj_id= '%d' and origin_id='$task_id'", TABLEPRE, $obj_id ) );
		}
		return $comment_arr;
	}
	public static function get_task_comment($task_id, $url, $page) {
		$comment_obj = keke_table_class::get_instance ( "witkey_comment" );
		return $comment_arr = $comment_obj->get_grid ( "obj_id='$task_id' and obj_type='task' and p_id=0 order by comment_id desc", $url, $page, 10, null, 1, 'task_leave' );
	
	}
	/**
	 * 任务阶段描述
	 */
	public function get_task_stage_desc() {
		$status_arr = $this->_task_status_arr; //状态数组
		$size = sizeof ( $status_arr );
		$arr = array ();
		for($i = 0; $i <= $size; $i ++) {
			if ($i < $this->_task_status) {
				$arr [$i] = 't_l passed'; //状态样式
			} elseif ($this->_task_status < $i) {
				$arr [$i] = 'no'; //状态样式
			} else {
				$arr [$i] = 'selected'; //状态样式
			}
		}
		return $arr;
	}
	/**
	 * 任务延期加价(投稿中才可延期)
	 * @param int   $delay_day 延期天数
	 * @param float $delay_cash 延期金额
	 * @param string $url 跳转链接
	 * @param string $output 消息输出类型
	 * @param $trust_response 担保回调响应
	 * @return show_msg
	 */
	public function set_task_delay($delay_day, $delay_cash, $url = '', $output = 'normal', $trust_response = false) {
		global $kekezu;
		global $_lang;
		global $uid, $username;
		$basic_config = $kekezu->_sys_config;
		kekezu::check_login ( $url, $output ); //检测登录
		if ($this->check_if_over_delay ( $delay_day, $delay_cash, $url, $output )) {
			$task_info = $this->_task_info;
			if ($this->_trust_mode && $trust_response == false) { //担保模式
				$app_arr ['type'] = "add";
				$app_arr ['data'] ['day'] = $delay_day;
				$app_arr ['data'] ['cash'] = $delay_cash;
				$jump_url = keke_trust_fac_class::trust_task_request ( "append", $this->_model_code, $this->_task_id, $task_info ['trust_type'], $app_arr );
				kekezu::keke_show_msg ( $url, $jump_url, "", $output );
				die ();
			} else {
				$delay_obj = new Keke_witkey_task_delay_class ();
				//延期
				$delay_obj->setDelay_cash ( $delay_cash );
				$delay_obj->setOn_time ( time () );
				$delay_obj->setUid ( $this->_uid );
				$delay_obj->setDelay_day ( $delay_day );
				$delay_obj->setTask_id ( $this->_task_id );
				$delay_obj->setDelay_status ( 0 );
				//或许有上次未付款的延期记录
				$delay_exsit = db_factory::get_one ( "select delay_id from " . TABLEPRE . "witkey_task_delay where task_id = '{$this->_task_id}' and delay_status=0" );
				if ($delay_exsit) {
					$delay_obj->setWhere ( "delay_id = '{$delay_exsit['delay_id']}'" );
					$delay_obj->edit_keke_witkey_task_delay ();
					$delay_id = $delay_exsit ['delay_id'];
				} else {
					$delay_id = $delay_obj->create_keke_witkey_task_delay ();
				}
				
				$task_obj = $this->_task_obj;
				$mycredit = $this->_userinfo ['credit']; //用户金币
				$mycash = $this->_userinfo ['balance']; //用户现金
				$basic_config ['credit_is_allow'] != 1 and $mycredit = '0'; //金币未开启使用。禁用金币
				

				//生成支付订单记录
				$order_obj = new Keke_witkey_order_class ();
				$order_obj->setModel_id ( $this->_task_info ['model_id'] );
				$order_obj->setObj_id ( $delay_id );
				$order_obj->setObj_type ( 'task_delay' );
				$order_obj->setOrder_amount ( $delay_cash );
				$order_obj->setOrder_name ( "任务 {$this->_task_title}的延期加价" );
				$order_obj->setOrder_status ( 'wait' );
				$order_obj->setOrder_time ( time () );
				$order_obj->setOrder_uid ( $uid );
				$order_obj->setOrder_username ( $username );
				$order_obj->setTask_id ( $this->_task_id );
				//重复订单查询
				$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where order_uid = '$uid' and obj_type = 'task_delay' and obj_id = '$delay_id' and order_status='wait'" );
				if ($order_exsit) {
					$order_obj->setWhere ( "order_id = '{$order_exsit['order_id']}'" );
					$order_obj->edit_keke_witkey_order ();
					$order_id = $order_exsit ['order_id'];
				} else {
					$order_id = $order_obj->create_keke_witkey_order ();
				}
				
				header ( "location:index.php?do=pay&order_id=$order_id" );
				die ();
			
			}
		}
	}
	/**
	 * 任务留言，任务留言回复
	 * @param array $comment_arr
	 * @param boolen $is_reply
	 * @return json
	 */
	public static function set_task_comment($comment_arr, $is_reply = false) {
		strtolower ( CHARSET ) == 'gbk' and $comment_arr ['content'] = kekezu::utftogbk ( $comment_arr ['content'] );
		$comment_arr ['content'] = kekezu::escape ( kekezu::str_filter ( $comment_arr ['content'] ) );
		$lid = db_factory::inserttable ( TABLEPRE . "witkey_comment", $comment_arr, 1 );
		//任务留言数加1
		$is_reply or db_factory::execute ( sprintf ( "update %switkey_task set leave_num = leave_num+1 where task_id = '%d'", TABLEPRE, $comment_arr ['obj_id'] ) );
		return $lid;
	}
	
	/**
	 * 
	 * 获取评论详细信息
	 */
	public static function get_comment_info($comment_id) {
		$commnet_obj = new Keke_witkey_comment_class ();
		$commnet_obj->setWhere ( "comment_id = $comment_id" );
		$comment_info = $commnet_obj->query_keke_witkey_comment ();
		$comment_info = $comment_info [0];
		return $comment_info;
	}
	
	/**
	 * 获取用户表中指定字段的信息   主要用于批量处理是获取指定值   
	 * @param unknown_type $uid  用户编号
	 * @param unknown_type $special_fields  特殊字段     可以","分隔   默认为查询手机，邮箱
	 */
	public function get_user_special($uid, $special_fields = 'mobile,email') {
		return db_factory::get_one ( sprintf ( " select %s from %switkey_space where uid = '%d' ", $special_fields, TABLEPRE, $uid ) );
	}
	/**
	 * 获取不同状态的任务稿件信息   用户业务类调取数据
	 * @param int $work_id  稿件编号  当稿件编号存在时  第二参数无效
	 * @param int or string  $work_status 稿件状态  需要查询多种状态信息、可以传递","分隔的字符串
	 */
	public function get_task_work($work_id = '', $work_status = '') {
		$fields_arr = $this->get_work_fields ();
		$tab = $fields_arr ['tab'];
		$pk = $fields_arr ['pk'];
		$st = $fields_arr ['st'];
		if ($work_id) {
			return db_factory::get_one ( sprintf ( " select * from %s%s where %s='%d'", TABLEPRE, $tab, $pk, $work_id ) );
		} else {
			$where = " 1 = 1 and task_id = '" . $this->_task_id . "' ";
			strpos ( $work_status, "," ) == FALSE and $where .= " and $st = '$work_status' " or $where .= " and $st in ($work_status) ";
			return kekezu::get_table_data ( "*", $tab, $where );
		}
	}
	/**
	 * 获取任务稿件信息
	 * * @param int or string $file_ids 单或","分隔字符串
	 */
	public static function get_work_file($file_ids) {
		$sql = " select * from " . TABLEPRE . "witkey_file where obj_type='work' and  file_id ";
		strpos ( $file_ids, "," ) !== FALSE and $sql .= " in (" . $file_ids . ")" or $sql .= " = '$file_ids' ";
		return db_factory::query ( $sql );
	}
	public function get_mark_count() {
		return kekezu::get_table_data ( " count(mark_id) count,mark_status", "witkey_mark", "model_code='" . $this->_model_code . "' and origin_id='$this->_task_id'", "", "mark_status", "", "mark_status", 3600 );
	}
	/**
	 * 获取来自雇主或者威客的评价信息
	 * *
	 */
	public function get_mark_count_ext() {
		return kekezu::get_table_data ( " count(mark_id) count,mark_type", "witkey_mark", "model_code='" . $this->_model_code . "' and origin_id='$this->_task_id' and mark_status>0", "", "mark_type", "", "mark_type", 3600 );
	}
	/**
	 * 更改任务状态
	 * @param int $to_status 变更状态
	 * @return boolean
	 */
	public function set_task_status($to_status) {
		return db_factory::execute ( sprintf ( " update %switkey_task set task_status='%d' where task_id='%d'", TABLEPRE, $to_status, $this->_task_id ) );
	}
	
	/**
	 * 任务补充需求
	 * @param string $ext_desc 补充内容
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return show_msg
	 */
	public function set_task_reqedit($ext_desc, $url = '', $output = 'normal') {
		global $_lang;
		if ($this->check_if_can_reqedit ( $url, $output )) {
			$task_obj = $this->_task_obj;
			$task_obj->setWhere ( "task_id = '$this->_task_id'" );
			CHARSET == 'gbk' and $ext_desc = kekezu::utftogbk ( $ext_desc );
			$task_obj->setExt_desc ( $ext_desc );
			$task_obj->setExt_status(0);
			$res = $task_obj->edit_keke_witkey_task ();
			if ($res == 0 || $res == 1) {
				kekezu::keke_show_msg ( $url, $_lang ['task_need_add_edit_success'], "", $output );
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['task_need_add_edit_fail'], "error", $output );
			}
		}
	}
	
	/**
	 * 任务内容修改
	 * @param array $edit_arr 修改任务的内容数组，键值为字段名
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return show_msg
	 */
	public function edit_task($edit_arr, $url = '', $output = 'normal') {
		global $_lang, $kekezu;
		if ($this->check_if_can_edit ( $url, $output )) {
			$step = $edit_arr ['step'];
			$step or $step = 'step1';
			switch ($step) {
				case 'step1' : //第一步或计件悬赏
					$task_obj = $this->_task_obj;
					$task_obj->setWhere ( "task_id='$this->_task_id'" );
					$task_obj->setTask_title ( $edit_arr ['task_title'] );
					$task_obj->setTask_desc ( $edit_arr ['task_desc'] );
					$task_obj->setIndus_pid ( $edit_arr ['indus_pid'] );
					$task_obj->setIndus_id ( $edit_arr ['indus_id'] );
					$res = $task_obj->edit_keke_witkey_task ();
					break;
				case 'step2' :
					$task_obj = $this->_task_obj;
					$task_config = unserialize ( $kekezu->_model_list [$model_id] ['config'] );
					$model_id = $edit_arr ['model_id'];
					
					$task_obj->setWhere ( " task_id = '{$this->_task_id}'" );
					$task_obj->setStart_time ( time () );
					$sub_time = time () + ($edit_arr ['txt_task_period'] * 24 * 3600);
					$end_time = $sub_time + ($task_config ['choose_time'] * 24 * 3600);
					$task_obj->setEnd_time ( $end_time );
					$task_obj->setSub_time ( $sub_time );
					$task_obj->setModel_id ( $model_id );
					switch ($model_id) {
						case 1 :
							$task_obj->setTask_cash ( $edit_arr ['txt_task_cash'] );
							$task_obj->setReal_cash ( $edit_arr ['txt_task_cash'] );
							$task_obj->setNotice_count ( $edit_arr ['notice_count'] );
							if ($edit_arr ['must_choosework']) {
								$task_obj->setMust_choosework ( 1 );
							} else {
								$task_obj->setMust_choosework ( 0 );
							}
							$task_obj->setTask_status(0);
							//更新订单金额
							db_factory::execute('update '.TABLEPRE.'witkey_order set order_amount='.$edit_arr ['txt_task_cash'].',model_id='.model_id.' where task_id='.$this->_task_id);
							break;
						case 2 :
							$task_obj->setTask_cash ( $edit_arr ['txt_task_cash'] );
							$task_obj->setReal_cash ( $edit_arr ['txt_task_cash'] );
							$task_obj->setNotice_count ( $edit_arr ['notice_count'] );
							$task_obj->setWork_count ( $edit_arr ['prize_count'] ); //用稿件数量代替存储奖项  
							if ($edit_arr ['must_choosework']) {
								$task_obj->setMust_choosework ( 1 );
							} else {
								$task_obj->setMust_choosework ( 0 );
							}
							$task_obj->setTask_status(0);
							//保存价格区间
							db_factory::execute ( "delete from " . TABLEPRE . "witkey_task_prize where task_id='$this->_task_id'" ); //先移除再保存
							for($i = 1; $i <= $edit_arr ['prize_count']; $i ++) {
								db_factory::inserttable ( TABLEPRE . "witkey_task_prize", array ('task_id' => $this->_task_id, 'prize' => $i, 'prize_count' => 1, 'prize_cash' => $edit_arr ['task_prize'] [$i] ) );
							}
							//更新订单金额
							db_factory::execute('update '.TABLEPRE.'witkey_order set order_amount='.$edit_arr ['txt_task_cash'].',model_id='.model_id.' where task_id='.$this->_task_id);
							break;
						case 4 :
							if ($edit_arr ['tender_set_price']) {
								$task_obj->setTask_cash ( $edit_arr ['txt_task_cash'] );
								$task_obj->setTask_cash_coverage ( 0 );
							} else {
								$cash_cove = kekezu::get_cash_cove ( 'tender' );
								$cove = $cash_cove [$edit_arr ['task_cash_coverage']];
								$task_cash = $cove ['end_cove'];
								$task_cash or $task_cash = $cove ['start_cove'];
								$task_obj->setTask_cash ( $task_cash );
								$task_obj->setTask_cash_coverage ( $edit_arr ['task_cash_coverage'] );
							}
							$task_obj->setTask_status(1);
							break;
					}
					$res = $task_obj->edit_keke_witkey_task ();
					break;
			}
			if ($res == 0 || $res == 1) {
				kekezu::keke_show_msg ( $url, $_lang ['task_info_edit_success'], "", $output );
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['task_info_edit_fail'], "error", $output );
			}
		}
	}
	
	/**
	 * 稿件评论
	 * @param string $obj   评论对象  task/work/case/shop/space
	 * @param int $obj_id  对象编号
	 * @param string $content 回复内容
	 * @param int $pid     上级回复编号
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 */
	public function set_work_comment($obj, $work_id, $content, $pid = '', $url = '', $output = 'normal') {
		global $_lang,$_K,$kekezu;
		$comment_desc = kekezu::str_filter ( kekezu::escape ( $content ) );
		CHARSET == 'gbk' and $comment_desc = kekezu::utftogbk ( $comment_desc );
		$obj == 'task' and $str [op] = $_lang ['task'] or $str = $_lang ['workl'];
		$this->_priv ['comment'] ['pass'] or kekezu::keke_show_msg ( $url, $this->_priv ['comment'] ['notice'] . $_lang ['no_reply_rights'], "error", $output );
		kekezu::k_match ( array ($kekezu->_sys_config ['ban_content'] ), $comment_desc ) and kekezu::keke_show_msg ( '', $_lang ['sensitive_word'], 'error', 'json' );
		$comment_obj = new Keke_witkey_comment_class ();
		$comment_obj->_comment_id = null;
		$comment_obj->setContent ( $comment_desc );
		$comment_obj->setObj_id ( intval ( $work_id ) );
		$comment_obj->setOrigin_id ( intval ( $this->_task_id ) );
		$comment_obj->setP_id ( intval ( $pid ) );
		$comment_obj->setOn_time ( time () );
		$comment_obj->setObj_type ( $obj );
		$comment_obj->setUid ( $this->_uid );
		$comment_obj->setUsername ( $this->_username );
		$comment_id = $comment_obj->create_keke_witkey_comment ();
		
		if ($comment_id) {
			$this->plus_work_comment_num ( $work_id );
			$info = db_factory::get_one('select uid,username from '.TABLEPRE.'witkey_task_work where work_id='.$work_id);
			$url  = $_K['siteurl'].'/index.php?do=task&task_id='.$this->_task_id.'&view=work&work_id='.$work_id;
			$content = '您的稿件在 '.date('Y-m-d H:i:s',time()).' 有新的点评。</br>';
			$content.= ' 点评内容为:';
			$content.= $comment_desc;
			$content.= '</br>稿件链接:<a href="'.$url.'">'.$url.'</a>';
			kekezu::notify_user('您有新的稿件点评',$content,$info['uid'],$info['username']);
			kekezu::echojson ( $_lang ['comment_success'], 1, htmlspecialchars ( $comment_desc ) );
			die ();
		
		//kekezu::keke_show_msg ( $url, $str . $_lang['comment_success'], "", $output );
		} else {
			kekezu::echojson ( $_lang ['comment_fail'], 0, htmlspecialchars ( $comment_desc ) );
			die ();
		
		//kekezu::keke_show_msg ( $url, $str . $_lang['comment_fail'], "error", $output );
		}
	
	}
	/**
	 * 任务（稿件）举报
	 * @param string $obj 举报对象
	 * @param $obj_id 对象编号
	 * @param $report_type 举报类型
	 * @param $to_uid 被举报人
	 * @param $to_username 被举报人姓名
	 * @param $file_name 上传文件路径
	 * @return json 
	 */
	public function set_report($obj, $obj_id, $to_uid, $to_username, $report_type, $file_info, $desc, $report_cate) {
		global $_lang;
		$this->_priv ['report'] ['pass'] or kekezu::keke_show_msg ( '', $this->_priv ['report'] ['notice'] . $_lang ['no_report_rights'], "error", 'json' );
		$this->_uid != $this->_guid and $user_type = '1' or $user_type = '2';
		$res = keke_report_class::add_report ( $obj, $obj_id, $to_uid, $to_username, $desc, $report_type, $report_cate, $this->_task_status, $this->_task_id, $user_type, $file_info );
	}
	/**
	 * 任务（稿件）举报
	 * @param int $work_id 稿件编号
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 */
	public function del_work($work_id, $url = '', $output = 'normal') {
		global $user_info;
		global $_lang;
		$user_info ['group_id'] != '7' and kekezu::keke_show_msg ( $url, $_lang ['you_no_rights_delete_manuscript'], "error", $output );
		$fields_arr = $this->get_work_fields ();
		$tab = $fields_arr ['tab'];
		$pk = $fields_arr ['pk'];
		$st = $fields_arr ['st'];
		$res = db_factory::execute ( sprintf ( " delete from %s%s where %s = '%d'", TABLEPRE, $tab, $pk, $work_id ) );
		//$num = db_factory::execute ( sprintf ( "delete from %switkey_comment where origin_id='%d' and obj_id = '%d'", TABLEPRE, $this->_task_id, $work_id ) );
		if ($res) {
			db_factory::execute ( sprintf ( "update %switkey_task set work_num = work_num-1 where task_id = '%d'", TABLEPRE, $this->_task_id ) );
			kekezu::keke_show_msg ( $url, $_lang ['manuscript_delete_success'], "", $output );
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['manuscript_delete_fail'], "error", $output );
		}
	}
	
	/**
	 * 任务操作判断
	 */
	abstract function process_can();
	
	/**
	 * 检测是否有新的回复
	 * @param $page 当前页码
	 * @param $limit 分页条数
	 */
	public function has_new_comment($page, $limit) {
		$new_arr = array ();
		$fields_arr = $this->get_work_fields ();
		$tab = $fields_arr ['tab'];
		$pk = $fields_arr ['pk'];
		$pre = ($page - 1) * $limit;
		$work_ids = kekezu::get_table_data ( $pk, $tab, " task_id ='$this->_task_id'", "", "", " $pre,$limit", $pk );
		if ($work_ids) {
			$new_arr = kekezu::get_table_data ( "count(comment_id) count,obj_id", "witkey_comment", " obj_id in (" . implode ( ",", array_keys ( $work_ids ) ) . ") and status=0 and origin_id='$this->_task_id'", "", " obj_id ", "", "obj_id" );
		}
		return $new_arr;
	}
	/**
	 * 消息通知雇主(威客)
	 * @param string $action  消息动作
	 * @param string $title	    消息标题
	 * @param array  $v_arr   消息内容参数
	 * @param int    $notify_type  1=>威客,2=>雇主
	 * @param int    $uid     接收方UID
	 */
	public function notify_user($action, $title, $v_arr = array(), $notify_type = 2, $uid = null) {
		switch ($notify_type) {
			case 1 :
				! $uid and $user_info = $this->_userinfo or $user_info = kekezu::get_user_info ( $uid ); //默认为当前用户信息
				break;
			case 2 :
				$user_info = $this->_g_userinfo;
				break;
		}
		
		$this->_msg_obj->send_message ( $user_info ['uid'], $user_info ['username'], $action, $title, $v_arr, $user_info ['email'], $user_info ['mobile'] );
	}
	/**
	 * 检测是否超出延期次数
	 * @param int $delay_day 延期天数
	 * @param float $delay_cash 延期金额
	 * @param  string $url    跳转链接
	 * @param  string $output 消息输出类型
	 * @return boolean or show_msg
	 */
	public function check_if_over_delay($delay_day, $delay_cash, $url = '', $output = 'normal') {
		global $_lang;
		$delay_rule = $this->_delay_rule; //延期规则
		$rule_count = sizeof ( $delay_rule ); //延期规则个数
		if ($rule_count == '0') { //没有延期规则
			kekezu::keke_show_msg ( $url, $this->_model_name . $_lang ['task_model_no_open_delay'], "error", $output );
			return false;
		} else {
			if ($this->_process_can ['delay']) { //可以延期
				$delay_count = intval ( $this->_task_info ['is_delay'] );
				$min_cash = intval ( $this->_task_config ['min_delay_cash'] ); //配置最小延期金额
				$max_day = intval ( $this->_task_config ['max_delay'] ); //配置最大延期天数
				$this_min_cash = intval ( $delay_rule [$delay_count] ['defer_rate'] * $this->_task_info ['task_cash'] / 100 ); //本次最小延期金额
				$min_cash > $this_min_cash and $real_min = $min_cash or $real_min = $this_min_cash; //真正最小金额
				if ($delay_count < $rule_count) { //还可延期
					if ($delay_cash < $real_min) { //小于当前延期最小金额
						kekezu::keke_show_msg ( $url, $_lang ['the_delay_money_less_min'] . $real_min . $_lang ['yuan_and_delay_fail'], "error", $output );
						return false;
					} elseif ($delay_day > $max_day) {
						kekezu::keke_show_msg ( $url, $_lang ['the_delay_days_more_max'] . $max_day . $_lang ['tian_and_delay_fail'], "error", $output );
						return false;
					} else {
						return true;
					}
				} else {
					kekezu::keke_show_msg ( $url, $_lang ['task_delay_times_has_reached'], "error", $output );
					return false;
				}
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['task_no_ongoing_no_delay'], "error", $output );
				return false;
			}
		}
	}
	/**
	 * 检测是否为雇主
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_master($url = '', $output = 'normal') {
		global $_lang;
		if ($this->_uid != $this->_guid)
			return TRUE;
		else {
			kekezu::keke_show_msg ( $url, $_lang ['can_not_operate_youself_task'], "error", $output );
			return false;
		}
	}
	/**
	 * 检测是否好评
	 * @param int     $mark_id 互评编号
	 * @param  int  $to_status 变更状态
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_good_mark($mark_id, $to_status, $url = '', $output = 'normal') {
		global $_lang;
		$mark_status = db_factory::get_count ( sprintf ( " select mark_status from %switkey_mark where mark_id='%d'", TABLEPRE, $mark_id ) );
		if ($mark_status == 1 && $mark_status != $to_status) { //阻止好评变更为其他状态
			kekezu::keke_show_msg ( $url, $_lang ['good_values_can_not_update'], "error", $output );
		} else
			return true;
	}
	/**
	 * 检测是否可以进行互评
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_can_mark($url = '', $output = 'normal') {
		global $_lang;
		if ($this->_process_can ['work_mark'] || $this->_process_can ['task_mark']) {
			return true;
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['current_status_can_not_mark'], "error", $output );
		}
	}
	/**
	 * 检测是否可以补充需求
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_can_reqedit($url = '', $output = 'normal') {
		global $_lang;
		if ($this->_process_can ['reqedit']) {
			return true;
		} else {
			kekezu::keke_show_msg ( $url,"当前任务状态无法新增需求或您已补充过需求", "error", $output );
		}
	}
	/**
	 * 检测是否可以修改任务
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_can_edit($url = '', $output = 'normal') {
		global $_lang;
		if ($this->_process_can ['edit']) {
			return true;
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['current_status_not_edit'] . $this->_process_can ['edit'], "error", $output );
		}
	}
	/**
	 * 检测是否可以交稿
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_can_hand($url = '', $output = 'normal') {
		global $_K;
		global $_lang;
		$this->_priv ['work_hand'] ['pass'] or kekezu::keke_show_msg ( $url, $this->_priv ['work_hand'] ['notice'] . $_lang ['no_submit_work_rights'], "error", $output );
		if ($this->_process_can ['work_hand']) {
			return true;
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['current_status_can_not_submit'], "error", $output );
		}
	}
	/**
	 * 检测是否可以选稿
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @return boolean or show_msg
	 */
	public function check_if_can_choose($url = '', $output = 'normal') {
		global $_lang;
		if ($this->_process_can ['work_choose']) {
			return true;
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['current_status_can_not_choose'], "error", $output );
		}
	}
	/**
	 * 检测是否交过稿
	 */
	public function check_if_handed($uid='',$status=''){
		$uid or $uid = $this->_uid;
		$w = '';
		$status!='' and $w = ' and work_status in('.$status.') ';
		 return db_factory::get_count('select work_id from '.TABLEPRE.'witkey_task_work where task_id='.$this->_task_id.' and uid='.$uid.$w);
	}
	/**
	 * 权限格式化
	 */
	public function user_priv_format($priv) {
		$arr = array ();
		if ($this->_uid == $this->_guid) {
			foreach ( $priv as $k => $v ) {
				$arr [$k] ['pass'] = true;
			}
		} else {
			$arr = $priv;
		}
		return $arr;
	}
	/**
	 * 验证任务状态
	 * 	防止用户躲开浏览器。数据不同步
	 */
	public function valid_task_status(){
		return db_factory::get_count('select task_status from '.TABLEPRE.'witkey_task where task_id='.$this->_task_id);
	}
	/**
	 * 验证稿件状态
	 * 	防止用户躲开浏览器。数据不同步
	 */
	public function valid_work_status($work_id){
		return db_factory::get_count('select work_status from '.TABLEPRE.'witkey_task_work where work_id='.$work_id);
	}
	/**
	 * 产生互评记录
	 */
	public function create_mark_log($master, $wiki, $work_id) {
		/*威客记录*/
		keke_user_mark_class::create_mark_log ( $this->_model_code, 1, $wiki ['uid'], $master ['uid'], $work_id, $master ['cash'], $this->_task_id, $wiki ['username'], $master ['username'] );
		/*雇主记录*/
		keke_user_mark_class::create_mark_log ( $this->_model_code, 2, $master ['uid'], $wiki ['uid'], $work_id, $wiki ['cash'], $this->_task_id, $master ['username'], $wiki ['username'] );
		/**互评++*/
		$this->plus_mark_num ();
	}
	/**
	 * 任务查看数量更新
	 * @return boolean
	 */
	public function plus_view_num() {
		if (! $_SESSION ['task_views_' . $this->_task_id . '_' . $this->_uid] && $this->_uid != $this->_guid) {
			$_SESSION ['task_views_' . $this->_task_id . '_' . $this->_uid] = 1;
			return db_factory::execute ( sprintf ( 'update %switkey_task set view_num=view_num+1 where task_id=%d', TABLEPRE, $this->_task_id ) );
		}
	}
	/**
	 * 更新交稿人数
	 */
	public function plus_wiki_num(){
		return db_factory::execute ( sprintf ( "update %switkey_task set wiki_num=wiki_num+1 where task_id ='%d'", TABLEPRE, $this->_task_id ) ); //更新任务稿件数量
	}
	/**
	 * 任务交稿数量更新
	 * @return boolean
	 */
	public function plus_work_num() {
		return db_factory::execute ( sprintf ( "update %switkey_task set work_num=work_num+1 where task_id ='%d'", TABLEPRE, $this->_task_id ) ); //更新任务稿件数量
	}
	/**
	 * 用户中标次数更新
	 * @param int $uid 用户ID
	 * @return boolean
	 */
	public function plus_accepted_num($uid) {
		return db_factory::execute ( sprintf ( "update %switkey_space set accepted_num = accepted_num+1 where uid='%d'", TABLEPRE, intval ( $uid ) ) );
	}
	/**
	 * 用户交稿数量更新
	 * @return boolean
	 */
	public function plus_take_num() {
		return db_factory::execute ( sprintf ( "update %switkey_space set take_num = take_num+1 where uid ='%d'", TABLEPRE, $this->_uid ) );
	}
	/**
	 * 任务留言量更新
	 * @return boolean
	 */
	public function plus_leave_num() {
		return db_factory::execute ( sprintf ( "update %switkey_task set leave_num=leave_num+1 where task_id ='%d'", TABLEPRE, $this->_task_id ) );
	}
	/**
	 * 任务关注数更新
	 * @return boolean
	 */
	public function plus_focus_num() {
		return db_factory::execute ( sprintf ( "update %switkey_task set focus_num=focus_num+1 where task_id ='%d'", TABLEPRE, $this->_task_id ) );
	}
	/**
	 * 悬赏
	 * 稿件留言数更新
	 * @return boolean
	 */
	public function plus_work_comment_num($work_id) {
		return db_factory::execute ( sprintf ( "update %switkey_task_work set comment_num=comment_num+1 where work_id ='%d'", TABLEPRE, $work_id ) );
	}
	/**
	 * 任务评价数更新 每次+2
	 */
	public function plus_mark_num() {
		return db_factory::execute ( sprintf ( "update %switkey_task set mark_num=ifnull(mark_num,0)+2 where task_id ='%d'", TABLEPRE, $this->_task_id ) );
	}
	public static function process_desc() {
		global $_lang;
		return array ("reqedit" => $_lang ['add_need'], "delay" => $_lang ['task_delay'], "work_choose" => $_lang ['task_choose_work'], "task_mark" => $_lang ['task_mark'], "task_pay" => $_lang ['bounty_hosting'], "work_comment" => $_lang ['work_reply'], "work_hand" => $_lang ['i_want_submit_work'], "task_comment" => $_lang ['task_comment'], "task_complaint" => $_lang ['task_complaint'], "task_report" => $_lang ['task_report'], "work_report" => $_lang ['work_report'], "work_choose" => $_lang ['task_choose_work'], "work_complaint" => $_lang ['work_complaint'], "task_vote" => $_lang ['task_vote'], "work_vote" => $_lang ['work_vote'], "work_mark" => $_lang ['work_mark'], "task_rights" => $_lang ['task_rights'], "work_rights" => $_lang ['work_rights'] );
	}
	/**
	 * 
	 * 获评到期
	 * @param $days 互评的最大期限时间
	 */
	public static function hp_timeout($days = 7) {
		$mark_list = db_factory::query ( sprintf ( "select * from %switkey_mark where mark_status=0 and mark_time<'%d'-'%d'", TABLEPRE, time (), 7 * 24 * 3600 ) );
		if (is_array ( $mark_list )) {
			foreach ( $mark_list as $v ) {
				keke_user_mark_class::exec_mark_process ( $v [mark_id], '', 1 );
			}
		}
	}
	/**
	 * 
	 * 更新置顶和加急的结束时间
	 * @param string $payitme_time ------数据库里面存储的时间戳
	 * @param string $top_time  -------- 增加的置顶时间戳
	 * @param string $urgent  -------- 增加的加急时间戳
	 * @return serialize
	 */
	public static function get_payitem($payitem_time, $top_time = false, $urgent_time = false) {
		
		$payite_arr = unserialize ( $payitem_time );
		if ($top_time) {
			$payite_arr [top] > time () and $payite_arr [top] = $payite_arr [top] + $top_time or $payite_arr [top] = time () + $top_time;
		}
		if ($urgent_time) {
			$payite_arr [urgent] > time () and $payite_arr [urgent] = $payite_arr [urgent] + $urgent_time or $payite_arr [urgent] = time () + $urgent_time;
		}
		$new_payitme_time = serialize ( $payite_arr );
		return $new_payitme_time;
	}

	
	public static function city_message_send2($task_info){
		global $_K;
		$city = explode(',',$task_info['city']);
		$city = $city[0];
		$list = db_factory::query(' select a.uid,a.username,a.email,a.mobile,a.city_match from '
				.TABLEPRE.'witkey_space a LEFT join '.TABLEPRE.'witkey_shop b on a.uid=b.uid where a.uid!='
				.$task_info['uid'].' and FIND_IN_SET('.$task_info['indus_id'].',a.skill_ids)>0
				  and a.isvip>0 and CASE WHEN b.global_match=1 THEN 1 = 1 ELSE
				   FIND_IN_SET("'.$city.'",residency)>0 END');//同城VIP列表
		
				
		if($list){
			$cash = $task_info['task_cash'];
			if($task_info['task_cash_coverage']){
				$cove_arr = kekezu::get_cash_cove();	
				$cash	  = $cove_arr[$task_info['task_cash_coverage']]['cove_desc'];
			}
			foreach($list as $v){
				$mail_tpl    = self::format_match_email2($task_info,$city,$v['uid'],$v['username'],$cash);
				kekezu::send_mail($v['email'],'同城任务速配',$mail_tpl);//邮件
				//速配
				$day    = date('Y-m-d',time());
				$m_info = db_factory::get_one(' select que_id,task_ids,task_titles,que_num from '.TABLEPRE.'witkey_citymatch_queue 
							where uid='.$v['uid'].' and que_day="'.$day.'"');
				$c      = intval($m_info['que_num'])+1;//总量+1
				//$match  = "同城速配通知：尊敬的{$v['username']}：截止今日凌晨同城任务共发布了{$c}条，请登录查看详情！";
//				$match  = "尊敬的VIP{$v['username']}您好：恭喜您，{$city}{$task_info[username]}发布了一个任务：{$task_info[task_title]}，请您及时登陆网站查看并报价，祝您工作愉快！IT帮手网";

				$match  = "尊敬的VIP您好：【{$city}】【{$task_info[username]}】发布任务：【{$task_info[task_title]}】，请您及时登录网站查看详情，祝您工作愉快！IT帮手网";
//				$match  = "{$city}{$task_info[username]}发布了一个任务：{$task_info[task_title]}";
				
				if(!$m_info){
					$sqlarr = array(
						'uid'=>$v['uid'],
						'username'=>$v['username'],
						'email'=>$v['email'],//可能换号码。所以更新下	
						'mobile'=>$v['mobile'],//可能换号码。所以更新下
						'content'=>$match,
						'task_ids'=>$task_info['task_id'],
						'task_titles'=>$task_info['task_title'],
						'que_day'=>$day,
						'que_num'=>1	
					);
					$msg_obj = new keke_msg_class ();
					$msg_res = $msg_obj->send_phone_sms ( $v ['mobile'], $match );
					
					if ($msg_res == '发送成功') {
						$sqlarr[que_status] = 1;
					} else {
						$sqlarr[error_status] = $msg_res;
						$sqlarr[que_status] = 2;
					}
					$res = db_factory::inserttable(TABLEPRE.'witkey_citymatch_queue',$sqlarr);
					
				}
				
				
				//if($v['city_match']){//同城速配功能暂时屏蔽。
				//给所有VIP用户发送消息，以后此功能会恢复
					
					//速配开启.计入消息队列
//					$sqlarr = array();
//					if($m_info){
//						$sqlarr = array(
//									'que_id'=>$m_info['que_id'],
//									'email'=>$v['email'],//可能换号码。所以更新下	
//									'mobile'=>$v['mobile'],//可能换号码。所以更新下
//									'content'=>$match,
//									'task_ids'=>$m_info['task_ids'].','.$task_info['task_id'],
//									'task_titles'=>$m_info['task_titles'].','.$task_info['task_title'],
//									'que_num'=>$c	
//						);
//						$replace = true;
//					}else{
//						$sqlarr = array(
//									'uid'=>$v['uid'],	
//									'username'=>$v['username'],
//									'mobile'=>$v['mobile'],
//									'email'=>$v['email'],
//									'content'=>$match,
//									'task_ids'=>$task_info['task_id'],
//									'task_titles'=>$task_info['task_title'],
//									'que_day'=>$day,
//									'que_num'=>$c	
//						);
//						$replace = false;
//					}
//					
//					db_factory::inserttable(TABLEPRE.'witkey_citymatch_queue',$sqlarr,1,$replace);
				//}
			}
		}
		
	}
	static function format_match_email2($task_info,$area,$uid,$username,$cash){
		global $_K,$kekezu;
		//$tpl = "<style>.dasdas{color:red}</style><label class=dasdas>dddddd</label>";
		$tpl = file_get_contents(S_ROOT.'/tpl/default/task/match_email.htm');
		$cont= unserialize($task_info['contact']);
		$contact = '';
		foreach($cont as $k=>$v){
			if($k=='mobile'){
				$contact.='手机';
			}elseif($k=='email'){
				$contact.='邮箱';
			}else{
				$contact.=$k;
			}
			$contact.='('.$v.')&nbsp;&nbsp;&nbsp;';
		}
		$guser_info = kekezu::get_user_info ( $task_info ['uid'] );
		$tpl = strtr($tpl,array(
						'{网站链接}'=>$_K['siteurl'],
						'{速配logo}'=>$_K['siteurl'].'/resource/img/system/match_logo.png',
						'{登录链接}'=>$_K['siteurl'].'/index.php?do=login',
						'{帮助中心}'=>$_K['siteurl'].'/index.php?do=help',
						'{用户名}'=>$username,
						'{城市名}'=>$area,
						'{任务金额}'=>$cash,
						'{任务标题}'=>$task_info['task_title'],
						'{任务地址}'=>$_K['siteurl'].'/index.php?do=task&task_id='.$task_info['task_id'],
						'{速配中心}'=>$_K['siteurl'].'/index.php?do=shop&view=match_list&u_id='.$uid,
						'{分类名}'=>$kekezu->_indus_arr[$task_info['indus_pid']]['indus_name'].'-'.$kekezu->_indus_arr[$task_info['indus_id']]['indus_name'],
						'{发布者}'=>$task_info['username'],
						'{空间链接}'=>$_K['siteurl'].'/index.php?do=shop&u_id='.$task_info['uid'],
						'{联系方式}'=>$contact,
						'{QQ号码}'=>$guser_info['qq'],
						'{手机号码}'=>$guser_info['mobile']
		));
		return strtolower($tpl);
	}
	
	/**
	 * 同城消息发送
	 */
	public function city_message_send(){
		global $_K;
		$city = explode(',',$this->_task_info['city']);
		$city = $city[0];
		$list = db_factory::query(' select a.uid,a.username,a.email,a.mobile,a.city_match from '
				.TABLEPRE.'witkey_space a LEFT join '.TABLEPRE.'witkey_shop b on a.uid=b.uid where a.uid!='
				.$this->_guid.' and FIND_IN_SET('.$this->_task_info['indus_id'].',a.skill_ids)>0
				  and a.isvip>0 and CASE WHEN b.global_match=1 THEN 1 = 1 ELSE
				   FIND_IN_SET("'.$city.'",residency)>0 END');//同城VIP列表
		
				
		if($list){
			$cash = $this->_task_info['task_cash'];
			if($this->_task_info['task_cash_coverage']){
				$cove_arr = kekezu::get_cash_cove();	
				$cash	  = $cove_arr[$this->_task_info['task_cash_coverage']]['cove_desc'];
			}
			foreach($list as $v){
				$mail_tpl    = $this->format_match_email($city,$v['uid'],$v['username'],$cash);
				kekezu::send_mail($v['email'],'同城任务速配',$mail_tpl);//邮件
				//速配
				$day    = date('Y-m-d',time());
				$m_info = db_factory::get_one(' select que_id,task_ids,task_titles,que_num from '.TABLEPRE.'witkey_citymatch_queue 
							where uid='.$v['uid'].' and que_day="'.$day.'"');
				$c      = intval($m_info['que_num'])+1;//总量+1
				$match  = "同城速配通知：尊敬的{$v['username']}：截止今日凌晨同城任务共发布了{$c}条，请登录查看详情！";
				
				//if($v['city_match']){//同城速配功能暂时屏蔽。
				//给所有VIP用户发送消息，以后此功能会恢复
					
					//速配开启.计入消息队列
					$sqlarr = array();
					if($m_info){
						$sqlarr = array(
									'que_id'=>$m_info['que_id'],
									'email'=>$v['email'],//可能换号码。所以更新下	
									'mobile'=>$v['mobile'],//可能换号码。所以更新下
									'content'=>$match,
									'task_ids'=>$m_info['task_ids'].','.$this->_task_id,
									'task_titles'=>$m_info['task_titles'].','.$this->_task_title,
									'que_num'=>$c	
						);
						$replace = true;
					}else{
						$sqlarr = array(
									'uid'=>$v['uid'],	
									'username'=>$v['username'],
									'mobile'=>$v['mobile'],
									'email'=>$v['email'],
									'content'=>$match,
									'task_ids'=>$this->_task_id,
									'task_titles'=>$this->_task_title,
									'que_day'=>$day,
									'que_num'=>$c	
						);
						$replace = false;
					}
					
					db_factory::inserttable(TABLEPRE.'witkey_citymatch_queue',$sqlarr,1,$replace);
				//}
			}
		}
	}
	/**
	 * 获取速配邮件模板
	 */
	function format_match_email($area,$uid,$username,$cash){
		global $_K,$kekezu;
		//$tpl = "<style>.dasdas{color:red}</style><label class=dasdas>dddddd</label>";
		$tpl = file_get_contents(S_ROOT.'/tpl/default/task/match_email.htm');
		$cont= unserialize($this->_task_info['contact']);
		$contact = '';
		foreach($cont as $k=>$v){
			if($k=='mobile'){
				$contact.='手机';
			}elseif($k=='email'){
				$contact.='邮箱';
			}else{
				$contact.=$k;
			}
			$contact.='('.$v.')&nbsp;&nbsp;&nbsp;';
		}
		$tpl = strtr($tpl,array(
						'{网站链接}'=>$_K['siteurl'],
						'{速配logo}'=>$_K['siteurl'].'/resource/img/system/match_logo.png',
						'{登录链接}'=>$_K['siteurl'].'/index.php?do=login',
						'{帮助中心}'=>$_K['siteurl'].'/index.php?do=help',
						'{用户名}'=>$username,
						'{城市名}'=>$area,
						'{任务金额}'=>$cash,
						'{任务标题}'=>$this->_task_title,
						'{任务地址}'=>$_K['siteurl'].'/index.php?do=task&task_id='.$this->_task_id,
						'{速配中心}'=>$_K['siteurl'].'/index.php?do=shop&view=match_list&u_id='.$uid,
						'{分类名}'=>$kekezu->_indus_arr[$this->_task_info['indus_pid']]['indus_name'].'-'.$kekezu->_indus_arr[$this->_task_info['indus_id']]['indus_name'],
						'{发布者}'=>$this->_gusername,
						'{空间链接}'=>$_K['siteurl'].'/index.php?do=shop&u_id='.$this->_guid,
						'{联系方式}'=>$contact,
						'{QQ号码}'=>$this->_g_userinfo['qq'],
						'{手机号码}'=>$this->_g_userinfo['mobile']
		));
		return strtolower($tpl);
	}
	/**
	 * 任务浏览记录
	 * @param int $task_id
	 * @param string $task_cash
	 * @param string $task_title
	 */
	function browing_history($task_id, $task_cash, $task_title) {
		$histroy = array ();
		$_COOKIE ['task_browing_history_' . $this->_uid] and $temp = unserialize ( $_COOKIE ['task_browing_history_' . $this->_uid] ) or $temp = array ();
		$len = sizeof ( $temp );
		if (! $temp [$task_id]) {
			$arr = array ($task_id, $task_cash, $task_title );
			$len >= 5 and array_pop ( $temp );
			array_unshift ( $temp, $arr );
		}
		foreach ( $temp as $v ) {
			$histroy [$v [0]] = $v;
		}
		setcookie ( 'task_browing_history_' . $this->_uid, serialize ( $histroy ) );
		return $histroy;
	}
	/**
	 * 是否有互评记录
	 * @param $obj_ids 
	 */
	function has_mark($obj_ids) {
		global $uid;
		$data = array ();
		if ($uid && $obj_ids) {
			$data = kekezu::get_table_data ( "mark_id,obj_id", "witkey_mark", "mark_status=0 and by_uid='$uid' and origin_id='$this->_task_id' and obj_id in ($obj_ids)", "", "", "", "obj_id", null );
		}
		return $data;
	}
	/**
	 * 显示任务的增值项
	 */
	function show_payitem() {
		global $_K;
		$str = '';
		$item_config = keke_payitem_class::get_payitem_config ( null, null, null, 'item_id' );
		
		$item_arr = array_filter ( explode ( ",", $this->_task_info ['pay_item'] ) );
		$time_arr = unserialize ( $this->_task_info ['payitem_time'] );
		if (! empty ( $item_arr ) && is_array ( $item_arr )) {
			foreach ( $item_arr as $v ) {
				if ($item_config [$v] ['item_code'] == 'top') {
					time () < intval ( $time_arr [top] ) && $item_config [$v] ['small_pic'] and $str .= "<img src='" . $_K ['siteurl'] . '/' . $item_config [$v] ['small_pic'] . "'>";
				
				} elseif ($item_config [$v] ['item_code'] == 'urgent') {
					time () < intval ( $time_arr [urgent] ) && $item_config [$v] ['small_pic'] and $str .= "<img src='" . $_K ['siteurl'] . '/' . $item_config [$v] ['small_pic'] . "'>";
				} else {
					$item_config [$v] ['small_pic'] and $str .= "<img src='" . $_K ['siteurl'] . '/' . $item_config [$v] ['small_pic'] . "'>";
				}
			}
		}
		
		return $str;
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
        
        /**
         * 取指定雇主的各类任务统计数目
         */
        public static function get_all_task_count( $uid ) {
            $ret = array();
            $sql['xsrw'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id BETWEEN 1 AND 2 ", TABLEPRE, $uid);
            $sql['zbrw'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id = 4 AND task_cash_coverage>0 ", TABLEPRE, $uid);
            $sql['jjrw'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id = 3 ", TABLEPRE, $uid);
            $sql['fwxq'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id = 4 AND task_type = 2 ", TABLEPRE, $uid);
            $sql['gyrw'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id = 4 AND task_type = 1 AND (task_cash_coverage IS NULL OR task_cash_coverage <1) ", TABLEPRE, $uid);
            $sql['zjgy'] = sprintf("SELECT COUNT(task_id) FROM %switkey_task WHERE uid=%d AND model_id = 4 AND task_type = 3 ", TABLEPRE, $uid);

            foreach ($sql as $k => $v) {
                $ret[$k] = db_factory::get_count ($v);
            }
            
            return $ret;
        }
        /**
         * 取指定威客的各类任务统计数目
         */
        public static function get_witkey_task_count( $uid ) {
            $ret = array();
            $sql['xsrw'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id between 1 and 2 ", TABLEPRE, TABLEPRE, $uid);
            $sql['zbrw'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id = 4 and task_cash_coverage>0", TABLEPRE, TABLEPRE, $uid);
            $sql['jjrw'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id = 3", TABLEPRE, TABLEPRE, $uid);
            $sql['fwxq'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id = 4 and task_type = 2", TABLEPRE, TABLEPRE, $uid);
            $sql['gyrw'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id = 4 and task_type = 1 and task_cash_coverage<1", TABLEPRE, TABLEPRE, $uid);
            $sql['zjgy'] = sprintf("select COUNT(*) from %switkey_task_work a left join %switkey_task b on a .task_id=b.task_id where a.uid=%d and model_id = 4 and task_type = 3", TABLEPRE, TABLEPRE, $uid);

            foreach ($sql as $k => $v) {
                $ret[$k] = db_factory::get_count ($v);
            }
            
            return $ret;
        }
        /**
         * 取任务跟踪信息
         * 
         */
        public static function get_task_track( $tid ) {
            $sql = sprintf("select * from %switkey_task_track where task_id = %d order by t_id asc limit 1", TABLEPRE, $tid);
            $ret = db_factory::get_one ($sql);
            
            if ( $ret ) {
            	echo '<a title="' . date('Y-m-d H:i:s', $ret['dateline']) .'">' . $ret['t_username'] . '</a>';
            }
        }
        /**
         * 联盟任务提交
         */
        public function union_task_submit(){
        	$url = '';
			if($this->_g_userinfo['union_user']&&!$this->_g_userinfo['union_assoc']){
				$url = keke_union_class::create_task($this->_task_info,false,array(),2,true,'url');
			}else{
				$url = keke_union_class::create_task($this->_task_info,false,array(),2,false,'url');
			}
			kekezu::get_remote_data($url);
        }
        /**
         * 通知联盟任务结束
         * @param $indetify 标识  1：成功  -1：失败
         * @param $bid_uid 中标威客  null/int/array
         */
        public function union_task_close($indetify,$bid_uid=null){
       		if($this->_task_info['task_union']&&$this->_task_info ['task_union']!=2&&in_array($indetify,array(1,-1))) {
				$u = new keke_union_class ( $this->_task_id );
       			$resp = array();
				$resp['r_task_id'] = $u->_r_task_id;
				$resp['indetify']  = $indetify;
				if($indetify==1){
					$resp['bid_uid'] = implode(',',(array)$bid_uid);
				}
				$u->task_close ($resp);
			}
        }
}