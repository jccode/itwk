<?php
/**
 * 多人悬赏业务类
 * @method init 任务信息初始化
 * =>任务状态数组信息
 * =>任务基本需求
 * check_if_bided        检测是否中标			 
 * 
 * get_task_stage_desc	        获取任务阶段描述
 * get_task_timedesc 	        获取任务时间描述
 * get_task_work		        获取任务指定状态的稿件信息
 * get_work_info      	        获取任务稿件信息
 *
 * start_vote                   发起投票
 * set_task_vote      			 任务投票进行
 * set_work_status   			 稿件状态变更
 * set_task_sp_end_time			更改任务公示时间
 *
 * dispose_task		   		 任务金额结算
 *
 * auto_choose    	    	          自动选稿
 *
 *时间类
 * time_task_gs   	       	     任务公示
 * time_task_vote     		     任务投票
 * time_task_end      		     任务结束
 *
 * process_can 	    	                当前操作判断
 * work_hand  		      	      任务交稿
 * work_choose 	      	                任务选稿
 */
keke_lang_class::load_lang_class ( 'mreward_task_class' );
class mreward_task_class extends keke_task_class {
	public $_task_status_arr; //任务状态数组
	public $_work_status_arr; //稿件状态数组
	public $_delay_rule; //延期规则
	public $_prize_arr;
	
	public static function get_instance($task_info) {
		static $obj = null;
		if ($obj == null) {
			$obj = new mreward_task_class ( $task_info );
		}
		return $obj;
	}
	public function __construct($task_info) {
		parent::__construct ( $task_info );
		$this->init ();
	}
	public function init() {
		$this->status_init ();
		$this->delay_rule_init ();
		$this->wiki_priv_init ();
		$this->prize_arr_init ();
	}
	
	//动作执行
	public function exec_event($action, $obj_id = null) {
		switch ($action) {
			case 'pub_task' :
				//执行发布任务
				$this->task_begin ();
				
				break;
			case 'task_delay' :
				//读到对应的延期记录
				$delay_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_task_delay where delay_id = '$obj_id'" );
				
				//更新延期记录
				db_factory::execute ( "update " . TABLEPRE . "witkey_task_delay set delay_status=1 where delay_id = '{$delay_info['delay_id']}'" );
				
				//防重复验证和任务状态验证
				if (! $delay_info ['delay_status'] && $this->_task_info ['task_status'] < 4) {
					//修改任务属性
					$delay_time = $delay_info ['delay_day'] * 24 * 3600;
					$task_cash  = $this->_task_info ['task_cash'] + $delay_info ['delay_cash'];
					/**查找此时未放弃的开票申请记录*/
					$iv_info = db_factory::get_one(' select iv_id,iv_status from '.TABLEPRE.'witkey_invoice where task_id='.$this->_task_id.' and iv_tm_status=1 and iv_status>-1');
					if($iv_info){//有此条任务结束前的开票申请,重新计算税金
						$iv_taxes = ($task_cash+$this->_task_info['att_cash'])*0.055;
						db_factory::execute(sprintf(' update %switkey_invoice set iv_taxes="%.2f",iv_price="%.2f" where iv_id=%d',TABLEPRE,$iv_taxes,$task_cash+$this->_task_info['att_cash'],$iv_info['iv_id']));
					}
					if($iv_info&&$iv_info['iv_status']!=2){//不是没通过.
						$real_cash = $task_cash*0.945;//计算实际金额
					}else{//没通过啊
						$real_cash = $task_cash;//实际====总金
					}
					if ($this->_task_info ['task_status'] == 3 && $delay_info ['delay_day']) {
						db_factory::updatetable ( TABLEPRE . 'witkey_task', array ('task_status' => '2', 'sub_time' => time () + $delay_time, 'end_time' => time () + ($this->_task_info ['end_time'] - $this->_task_info ['sub_time']), 'exec_time' => time () + $delay_time, 'task_cash' =>$task_cash, 'real_cash' =>$real_cash, 'is_delay' => $this->_task_info ['is_delay'] + 1 ), array ('task_id' => $this->_task_id ) );
					} else {
						db_factory::updatetable ( TABLEPRE . 'witkey_task', array ('sub_time' => $this->_task_info ['sub_time'] + $delay_time, 'end_time' => $this->_task_info ['end_time'] + $delay_time, 'exec_time' => $this->_task_info ['exec_time'] + $delay_time, 'task_cash' =>$task_cash, 'real_cash' =>$real_cash, 'is_delay' => $this->_task_info ['is_delay'] + 1 ), array ('task_id' => $this->_task_id ) );
					}
					
					//各等奖奖金分配
					$prize_list = db_factory::get_table_data ( 'prize_id,prize,prize_cash', "witkey_task_prize", "task_id='{$this->_task_id}'", "prize", "", "", "prize" );
					$prize_count = count ( $prize_list );
					foreach ( $prize_list as $pz ) {
						$p_add_c = $delay_info ['delay_cash'] * $pz ['prize_cash'] / $this->_task_info ['task_cash'];
						db_factory::execute ( "update " . TABLEPRE . "witkey_task_prize set prize_cash=prize_cash+'$p_add_c' where prize_id = '{$pz['prize_id']}' " );
					}
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
		db_factory::updatetable ( TABLEPRE . 'witkey_task', array ('cash_status'=>1,//赏金托管状态
'task_status' => 2, //任务状态进行中
'sub_time' => $this->_task_info ['sub_time'] + $plus_time, //选稿结束时间补差
'exec_time' => $this->_task_info ['sub_time'] + $plus_time, //自动触发时间补差
'end_time' => $this->_task_info ['end_time'] + $plus_time ), //任务结束时间补差
array ('task_id' => $this->_task_id ) );
		
		$this->union_task_submit();//联盟推广发布
		
		//积分扣除
		if ($this->_task_info ['att_credit']) {
			keke_user_class::credit_out ( $this->_guid, $this->_gusername, 2, '任务#' . $this->_task_id . '的增值服务', $this->_task_info ['att_credit'] );
		}
		if ($this->_task_config ['credit_is_allow'] == 1) {
			//金币返还
			$return_credit = $this->_task_info ['task_cash'] * $this->_task_config ['credit_return_rate'] / 100;
			keke_finance_class::cash_in ( $this->_guid, 0, $return_credit, 'credit_return', '', 'task', $this->_task_id );
		}
		
		//生成动态
		$feed_arr = array ("feed_username" => array ("content" => $this->_task_info ['username'], "url" =>"index.php?do=shop&u_id={$this->_task_info['uid']}" ), "action" => array ("content" => "发布了任务", "url" => "", 'cash' => $this->_task_info ['task_cash'] ), "event" => array ("content" => $this->_task_info ['task_title'], "url" => "index.php?do=task&task_id=" . $this->_task_info ['task_id'] ) );
		kekezu::save_feed ( $feed_arr, $this->_task_info ['uid'], $this->_task_info ['username'], 'pub_task', $this->_task_id );
		
		//消息通知
		$v = array ("用户名" => $this->_task_info ['username'], "任务编号" => $this->_task_id, "任务标题" => $this->_task_title, "任务链接" => '<a href="' . $this->_task_id . '" target="_blank">' . $this->_task_title . '</a>', "任务状态" => $this->_task_status_arr [$this->_task_status], "开始时间" => date ( 'Y-m-d H:i:s', $this->_task_info ['start_time'] ),"投稿结束时间"=>date('Y-m-d H:i:s',$this->_task_info['sub_time']+$plus_time),"选稿结束时间"=>date('Y-m-d H:i:s',$this->_task_info['end_time']+$plus_time ));
		$this->notify_user ( "task_pub", "任务发布消息提示", $v ); // 通知威客
	
		
		
	//同城邀请
	//$this->city_message_send();
	//keke_task_class::city_message_send2($this->_task_info);
	

	//其它操作
	

	}
	
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
	 * 奖项初始化 
	 */
	public function prize_arr_init() {
		$this->_prize_arr = db_factory::get_table_data ( 'prize,prize_cash', "witkey_task_prize", "task_id='{$this->_task_id}'", "prize", "", "", "prize" );
	}
	
	/**
	 * 威客权限动作判断  
	 */
	public function wiki_priv_init() {
		$arr = mreward_priv_class::get_priv ( $this->_task_id, $this->_model_id, $this->_userinfo );
		$this->_priv = $this->user_priv_format ( $arr );
	}
	
	/**
	 * 任务阶段时间描述
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
			case "2" : //投稿中
				$time_desc ['time_desc'] = $_lang ['from_hand_work_deadline']; //时间状态描述
				$time_desc ['time'] = $task_info ['sub_time']; //当前状态结束时间
				$time_desc ['ext_desc'] = $_lang ['task_working_can_hand_work']; //追加描述
				if ($this->_task_config ['open_select'] == 'open') { //开启进行选稿
					$time_desc ['g_action'] = $_lang ['now_employer_can_choose_work']; //雇主追加描述
				}
				break;
			case "3" : //选稿中
				$time_desc ['time_desc'] = $_lang ['from_choose_deadline']; //时间状态描述
				$time_desc ['time'] = $task_info ['end_time']; //当前状态结束时间
				$time_desc ['ext_desc'] = $_lang ['task_choosing_wait_employer_choose']; //追加描述
				break;
			case "4" : //投票中
				$time_desc ['time_desc'] = '距摇奖截止'; //时间状态描述
				$time_desc ['time'] = $task_info ['exec_time']; //当前状态结束时间
				$time_desc ['ext_desc'] = '任务摇奖中，参与就有奖'; //追加描述
				break;
			case "5" : //公示中
				$time_desc ['time_desc'] = $_lang ['from_gs_deadline']; //时间状态描述
				$time_desc ['time'] = $task_info ['sp_end_time']; //当前状态结束时间
				$time_desc ['ext_desc'] = $_lang ['task_haved_choose_bid_and_user_look']; //追加描述
				break;
			case "6" : //交付中
				$time_desc ['time_desc'] = "任务等待交付"; //时间状态描述
				$time_desc ['ext_desc'] = $_lang ['task_in_jf_rate']; //追加描述
				break;
			case "7" : //冻结中
				$time_desc ['time_desc'] = "因到期未处理或存在争议暂时冻结"; //时间状态描述
				$time_desc ['ext_desc'] = "任务冻结中"; //追加描述
				break;
			case "8" : //结束
				$time_desc ['time_desc'] = "任务已完成"; //时间状态描述
				$time_desc ['ext_desc'] = $_lang ['task_haved_complete']; //追加描述
				break;
			case "9" : //失败
				$time_desc ['time_desc'] = "任务失败"; //时间状态描述
				$time_desc ['ext_desc'] = $_lang ['task_timeout_and_no_works_fail']; //追加描述
				break;
			case "11" : //仲裁
				$time_desc ['time_desc'] = "任务等待仲裁中"; //时间状态描述
				$time_desc ['ext_desc'] = $_lang ['task_diffrent_opnion_and_web_in']; //追加描述
				break;
		}
		return $time_desc;
	}
	/**
	 * 获取任务稿件信息  支持分页，用户前端稿件列表
	 * @param array $w 前端查询条件数组
	 * ['work_status'=>稿件状态	
	 * 'user_type'=>用户类型 --有值表示自己
	 * ......]
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
		$sql = " select a.*,b.seller_credit,b.seller_good_num,b.seller_total_num,b.w_level,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$count_sql = " select count(a.work_id) from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$where = " where a.task_id = '$this->_task_id' ";
		
		if (! empty ( $w )) {
			$w ['work_id'] and $where .= " and a.work_id='" . $w ['work_id'] . "'";
			$w ['user_type'] == 'my' and $where .= " and a.uid = '$this->_uid'";
			if (isset ( $w ['work_status'] )) {
				if ($w ['work_status'] == 11) {
					$where .= " and a.work_status between 1 and 9 ";
				} else {
					$where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
				}
			}
		
		//isset ( $w ['work_status'] ) and $where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
		}
		$order and $where.=" order by ".$order or $where .= " order by (CASE WHEN  a.work_status!=0 THEN work_status ELSE work_id END) desc,work_time asc ";
		if (! empty ( $p )) {
			$page_obj = $kekezu->_page_obj;
			$page_obj->setAjax ( 1 );
			$page_obj->setAjaxDom ( "gj_summery" );
			$count = intval ( db_factory::get_count ( $count_sql . $where ) );
			$pages = $page_obj->getPages ( $count, $p ['page_size'], $p ['page'], $p ['url'], $p ['anchor'] );
			$where .= $pages ['where'];
		}
		$work_info = db_factory::query ( $sql . $where );
		$work_info = kekezu::get_arr_by_key ( $work_info, 'work_id' );
		$work_arr ['work_info'] = $work_info;
		$work_arr ['pages'] = $pages;
		$work_info_arr = array_keys ( $work_info );
		if (! empty ( $work_info_arr )) {
			$work_arr ['mark'] = $this->has_mark ( implode ( ',', $work_info_arr ) );
		}
		return $work_arr;
	}
	/**
	 * 任务交稿
	 * @param string $work_desc 交稿描述
	 * @param string $file_ids 稿件附件编号串  eg:1,2,3,4,5
	 * @param $hidemode 隐藏模式
	 * @param int    $hidework 1结束后公开  2永久保密
	 * @see keke_task_class::work_hand()
	 */
	public function work_hand($work_desc, $file_ids,$hidemode = '', $hidework = 0, $output = 'normal') {
		global $_K;
		global $_lang;
		
		if ($this->check_if_can_hand ( $url, $output )&&$this->valid_reg_time($url, $output)) {
			$handed = $this->check_if_handed ();
			$work_obj = new Keke_witkey_task_work_class ();
			
			$valid_hide = keke_glob_class::valid_hide_mode($hidemode);//验证隐藏模式
			
			//提交稿件
			$work_obj->_work_id = null;
			$work_obj->setTask_id ( $this->_task_id );
			$work_obj->setUid ( $this->_uid );
			$work_obj->setUsername ( $this->_username );
			$work_obj->setVote_num ( 0 );
			$work_obj->setWork_status ( 0 );
			$work_obj->setWork_title ( $this->_task_title );
			if($valid_hide){
				$work_obj->setHide_work(intval($hidework));
			}else{
				$work_obj->setHide_work(0);
			}
			//$work_obj->setHide_work ( intval($hide) );
			CHARSET == 'gbk' and $work_desc = kekezu::utftogbk ( $work_desc );
			$work_obj->setWork_desc ( $work_desc );
			$work_obj->setWork_time ( time () );
			
			$file_arr = array_unique ( array_filter ( explode ( ',', $file_ids ) ) );
			$f_ids = implode ( ',', $file_arr ); //附件编号串
			

			//稿件缩略图保存
			$f_ids and $pic_file = db_factory::get_one ( "select save_name,file_ext from " . TABLEPRE . "witkey_file where file_id in ({$f_ids}) and file_ext in ('jpg','gif','png') " );
			
			if ($pic_file) { //如果有缩略图
				$filename = 'exfile_' . work_id . '_' . time () . '.' . $pic_file ['file_ext'];
				$a = UPLOAD_ROOT . 'exfile_' . work_id . '_' . time () . '.' . $pic_file ['file_ext'];
				
				//				keke_img_class::resize_pic(S_ROOT.'./'.$pic_file['save_name'], 217, 150,$a);
				//				$work_obj->setWork_pic('data/uploads/' . UPLOAD_RULE.$filename);
				

				$work_obj->setWork_pic ( $pic_file ['save_name'] );
			}
			
			$f_ids and $work_obj->setWork_file ( $f_ids );
			
			$work_id = $work_obj->create_keke_witkey_task_work ();
			if ($valid_hide&& $work_id) {
				$payitem = keke_payitem_class::get_payitem_info ( 'witkey', 'mreward', true ); //可购买服务
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
				//更新附件表里相应附件的稿件ID
				$f_ids and db_factory::execute ( sprintf ( " update %switkey_file set work_id='%d',task_title='%s',obj_id='%d' where file_id in (%s)", TABLEPRE, $work_id, $this->_task_title, $work_id, $f_ids ) );
				$this->plus_work_num (); //更新任务稿件数量
				$this->plus_take_num (); //更新用户交稿数量
				$handed or $this->plus_wiki_num (); //之前没交过更新任务交稿人数
				$notice_url = "<a href=\"" . $_K ['siteurl'] . "/index.php?do=task&task_id=" . $this->_task_id . "\" target=\"_blank\">" . $this->_task_title . "</a>";
				$g_notice = array ("用户" => $this->_username,"任务标题"=>$this->_task_title,"任务链接"=> $notice_url );
				$this->notify_user ( "task_hand","威客交稿通知", $g_notice ); //通知雇主
				kekezu::keke_show_msg ( $url, $_lang ['congratulate_you_hand_work_success'], "", $output );
			} else {
				kekezu::keke_show_msg ( $url, $_lang ['pity_hand_work_fail'], "error", $output );
			}
		}
	}

	/**
	 * 检查注册时间，新注册用户无法参加
	 */
	public function valid_reg_time($url='',$outrput='normal'){
		// 钓鱼岛之歌特殊任务的处理
		if ($this->_task_info['task_id'] == 191785) {
			return true;
		}
		if($this->_userinfo['reg_time']>$this->_task_info['start_time']){
			kekezu::keke_show_msg($url,'对不起，您暂时还不能参加您注册以前发布的悬赏任务，如需帮助，请联系在线客服','error',$outrput);
		}else{
			return true;
		}
	}
	/**
	 * 任务选稿
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @see keke_task_class::work_choose()
	 */
	public function work_choose($work_id, $to_status, $url = '', $output = 'normal', $trust_response = false) {
		global $_K;
		global $_lang;
		kekezu::check_login ( $url, $output ); //检测登录
		

		if (! in_array ( $this->_task_status ['task_status'], array (2, 3 ) ) || $this->_guid != $this->_uid) {
			kekezu::keke_show_msg ( $url, '任务状态不对,或者您不是雇主', "error", $output );
		}
		
		$status_arr = $this->get_work_status ();
		$task_info = $this->_task_info;
		
		$work_info = $this->get_task_work ( $work_id ); //稿件信息
		

		$choosed = $this->check_if_handed ( $work_info ['uid'], '1,2,3,4,5,13' );
		if ($choosed) {
			kekezu::keke_show_msg ( $url, '该用户已有中奖或入围稿件,无法设置此稿件', "error", $output );
		}
		
		//列表推倒
		$search_condit = $this->get_search_condit();
		//值增
		$search_condit[$to_status]['count']+=1;
		
		//更新状态  用户的中变数量+1
		$this->plus_accepted_num ( $work_info ['uid'] );
		
		$res = $this->set_work_status ( $work_id, $to_status );
		
		$work_count = $search_condit[1]['count']+$work_count = $search_condit[2]['count']+$work_count = $search_condit[3]['count']+$work_count = $search_condit[4]['count']+$work_count = $search_condit[5]['count']+$work_count = $search_condit[13]['count'];
		//结束判断    选稿期内    中标个数   是否等于总数     
		if ($this->_task_status==3&&$this->_task_status&&$work_count >= ($this->_task_info['notice_count']+$this->_task_info['work_count'])){
			//任务可以进入公示了
			db_factory::updatetable(TABLEPRE."witkey_task", array(
				'task_status'=>5,//任务改状态5
				'exec_time'=>time()+($this->_task_config['notice_period']*24*3600),//下次触发时间重设
				'sp_end_time'=>time()+($this->_task_config['notice_period']*24*3600)//公示结束时间
			), array('task_id'=>$this->_task_id));
			
			//获得稿件列表
			$work_list = kekezu::get_table_data ( "work_id,uid,username,work_status,work_time", "witkey_task_work", "task_id='$this->_task_id' and ifnull(work_status,0)!=16", "work_time desc" );
			//状态分组列表
			$work_st_list = array ();
			$bid_uids     = array();//所有得奖威客uid数组
			foreach ( $work_list as $work ) {
				$work ['work_status'] or $work ['work_status'] = 0;
				$work_st_list [$work ['work_status']] [$work ['work_id']] = $work;
				in_array($work['work_status'],array(1,2,3,4,5,13)) and $bid_uids[]=$work['uid'];
			}
				
			$work_choose_count = 0; //已选稿件数量
			$work_st_list [1] and $work_choose_count ++;
			$work_st_list [2] and $work_choose_count ++;
			$work_st_list [3] and $work_choose_count ++;
			$work_st_list [4] and $work_choose_count ++;
			$work_st_list [5] and $work_choose_count ++;
				
			if ($work_choose_count >= $this->_task_info ['work_count']) {
				/**
				 * 互评记录
				 * */
				//获奖的
				$work_st_list [1] or $work_st_list [1] = array ();
				$work_st_list [2] or $work_st_list [2] = array ();
				$work_st_list [3] or $work_st_list [3] = array ();
				$work_st_list [4] or $work_st_list [4] = array ();
				$work_st_list [5] or $work_st_list [5] = array ();
				$list = array_merge ( $work_st_list [1], $work_st_list [2], $work_st_list [3], $work_st_list [4], $work_st_list [5] );
				foreach ( $list as $v ) {
					$rate = (100 - $this->_task_config ['notice_rate']) / 100; //计算比例
					$wc = $this->_task_info ['task_cash']; //威客金额
					$gc = intval ( $this->_task_info ['task_cash'] * $rate / $this->_task_info ['work_count'] ); //雇主金额
					$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
				}
				if ($work_st_list [13]) {
					$mark_count = 0;
					//入围的
					foreach ( $work_st_list [13] as $v ) {
						if ($mark_count < $this->_task_info ['notice_count']) {
							$rate = $this->_task_config ['notice_rate'] / 100; //计算比例
							$gc = $wc = intval ( $this->_task_info ['task_cash'] * $rate ); //雇主金额=威客金额
							$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
						}
						$mark_count ++;
					}
				}
			}
		}elseif($this->_task_status=2&&$this->_task_status&&$work_count >= ($this->_task_info['notice_count']+$this->_task_info['work_count'])){
			//提前选稿，24小时后公示
			db_factory::updatetable ( TABLEPRE . "witkey_task", array ('exec_time' => time () + 24 * 3600 ), array ('task_id' => $this->_task_id ) );//下次触发时间重设到1天后
		}

		$notify_url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank" >' . $this->_task_title . '</a>';
		$v = array ("任务编号"=> $this->_task_id,'任务标题'=>$this->_task_title,'任务链接' => $notify_url,"状态"=>$status_arr[$to_status]);
		//通知威客
		if($to_status>0&&$to_status<11){//中标模板
			$this->notify_user ( "task_bid","稿件".$status_arr[$to_status], $v, 1, $work_info ['uid'] ); 
		}else{//入围模板
			$this->notify_user ( "works_selected","稿件".$status_arr[$to_status], $v, 1, $work_info ['uid'] );
		}
		if ($res) {
			kekezu::keke_show_msg ( $url, $_lang ['work'] . $status_arr [$to_status] . $_lang ['set_success'], "", $output );
		} else {
			kekezu::keke_show_msg ( $url, $_lang ['work'] . $status_arr [$to_status] . $_lang ['set_fail'], "error", $output );
		}
	
	}
	
	public static function task_delay($task_id, $task_cash, $delay_cash) {
		$prize_data = db_factory::query ( sprintf ( " select * from %switkey_task_prize where task_id='%d'", TABLEPRE, $task_id ) );
		foreach ( $prize_data as $v ) {
			$rate = $v ['prize_cash'] / $task_cash;
			$new_cash = $v ['prize_cash'] + $delay_cash * $rate;
			db_factory::execute ( sprintf ( " update %switkey_task_prize set prize_cash='%.2f' where prize_id='%d'", TABLEPRE, $new_cash, $v ['prize_id'] ) );
		}
	}
	/**
	 * 确认付款
	 * @param int $work_id
	 * @param int $to_status
	 * @param $trust_response 担保回调响应
	 * @see keke_task_class::work_choose()
	 */
	public function work_confrim($work_id, $url = '', $output = 'normal') {
		global $kekezu, $_K;
		global $_lang;
		kekezu::check_login ( $url, $output ); //检测登录
		

		$work_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_task_work where work_id='$work_id'" );
		$work_info or kekezu::keke_show_msg ( $url, '找不到该稿件', "error", $output );
		
		if ($work_info ['is_confirmed']) {
			kekezu::keke_show_msg ( $url, '该稿件已确认付款过', "error", $output );
		}
		
		if ($this->_task_status < 5) {
			kekezu::keke_show_msg ( $url, '任务状态不对,或者时间不对', "error", $output );
		}
		
		//改状态 
		$res = db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set is_confirmed=1 where work_id='$work_id'" );
		
		//需要触发结算
		if ($this->_task_status!=5){
			$this->work_pay ( $work_info ['work_price'], $work_id, $work_info );
		
		//事件通知
		//			kekezu::notify_user("确认付款通知", 
		//				'任务<a href="index.php?do=task&task_id='.$this->_task_id.'" target="_blank">'.$this->_task_info['task_title'].'</a>雇主已确认付款，您的中标金额'.$work_info['work_price'].'元已到账。',
		//				$work_info['uid'],$work_info['username']
		//			);
		} else {
			//事件通知
			kekezu::notify_user ( "确认付款通知", '任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a>雇主已确认付款，您的中标金额' . $work_info ['work_price'] . '元，将在公示期结束后到账。', $work_info ['uid'], $work_info ['username'] );
		}
		
		if ($res) {
			kekezu::keke_show_msg ( $url, "确认付款成功", "", $output );
		} else {
			kekezu::keke_show_msg ( $url, "确认付款失败", "error", $output );
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
		$user_info = $this->_userinfo;
		
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
			case "2" : //投稿中
				switch ($g_uid == $uid) { //雇主
					case "1" :
						$task_info ['ext_desc'] or $process_arr ['reqedit'] = true; //补充需求
						$process_arr ['invite'] = true; // 邀请人才
						sizeof ( $this->_delay_rule ) > 0 and $process_arr ['delay'] = true; //延期加价
						if ($config ['open_select'] == 'open') {
							$process_arr ['work_choose'] = true; //开启投稿中选稿
						}
						$process_arr['download'] = true;//文件下载
						$process_arr ['work_comment'] = true; //稿件回复
						break;
					case "0" : //威客
						$process_arr ['work_hand'] = true; //提交稿件
						$process_arr ['task_comment'] = true; //任务回复
						$process_arr ['task_report'] = true; //任务举报
						$process_arr ['work_comment'] = true; //稿件回复
						break;
				}
				$process_arr ['work_report'] = true; //稿件举报
				break;
			case "3" : //选稿中
				switch ($g_uid == $uid) { //雇主
					case "1" :
						$task_info ['must_choosework'] or $process_arr ['choosework'] = true; //保证选稿
						sizeof ( $this->_delay_rule ) > 0 and $process_arr ['delay'] = true; //延期加价
						$process_arr ['work_choose'] = true; //选稿
						$process_arr ['work_comment'] = true; //稿件回复
						$process_arr['download'] = true;//文件下载
						break;
					case "0" : //威客
						$process_arr ['task_comment'] = true; //任务回复
						break;
				}
				$process_arr ['work_report'] = true; //稿件举报
				break;
			case "4" : //投票中   --> 摇奖中
				switch ($g_uid == $uid) { //雇主
					case "1" :
						$process_arr ['work_comment'] = true; //留言回复
						break;
					case "0" :
						$process_arr ['task_lottery'] = true;
						//$process_arr ['task_comment'] = true; //任务回复
						//$process_arr ['task_report'] = true; //任务举报
						break;
				}
				$process_arr ['work_report'] = true; //稿件举报
				$uid and $process_arr ['work_vote'] = true; //进行投票
				break;
			case "5" : //公示中
				switch ($g_uid == $uid) { //雇主
					case "1" :
						$process_arr ['work_comment'] = true; //留言回复
						$process_arr ['work_mark'] = true; //稿件评价
						$process_arr ['confirm_pay'] = true;
						$process_arr['download'] = true;//文件下载
						break;
					case "0" :
						$process_arr ['task_comment'] = true; //任务回复
						$process_arr ['task_mark'] = true; //任务评价
						break;
				}
				$process_arr ['work_report'] = true; //稿件举报
				break;
			case "6" : //交付中
				$process_arr ['task_rights'] = true; //任务维权
				if ($uid == $g_uid) {
					$process_arr ['work_rights'] = true; //雇主发起稿件维权
				}
				$process_arr ['task_agree'] = true; //进入交付
				break;
			case "8" : //已结束
				switch ($g_uid == $uid) { //雇主
					case "1" :
						$process_arr ['work_comment'] = true; //留言回复
						$process_arr ['work_mark'] = true; //稿件评价
						$process_arr ['confirm_pay'] = true;
						$process_arr['download'] = true;//文件下载
						break;
					case "0" :
						$process_arr ['task_comment'] = true; //任务回复
						$process_arr ['task_mark'] = true; //任务评价
						break;
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
		$task_info ['lottery_config'] && $process_arr ['lott_view'] = true;
		$this->_process_can = $process_arr;
		return $process_arr;
	}
	/**
	 * 更改稿件状态
	 * @param int $work_id 稿件编号
	 * @param int $to_status 更新到状态
	 * @return  boolean
	 */
	public function set_work_status($work_id, $to_status) {
		return db_factory::execute ( sprintf ( " update %switkey_task_work set work_status='%d',op_time='%d' where work_id='%d'", TABLEPRE, $to_status, time (), $work_id ) );
	}
	
	/**
	 * 时间触发投稿到期处理
	 * 有稿件：进入选稿
	 * 无稿件：任务冻结等待退款
	 */
	public function time_hand_end() {
		if ($this->_task_status == 2 && $this->_task_info ['sub_time'] < time ()) { //任务投稿时间到
		
			if(strpos(' '.$this->_task_info['pay_item'],'top')){
				//重设增值属性
			 	$payitem = str_replace('top','',$this->_task_info['pay_item']);
				$payitem = implode(',',array_filter(explode(',',$payitem)));
				db_factory::updatetable ( TABLEPRE . "witkey_task", array (//'is_top'=>0,//清空置顶属性
					'pay_item'=>$payitem),array ('task_id' => $this->_task_id ) );
			}

			//获得稿件列表
			$work_list = kekezu::get_table_data ( "work_id,uid,username,work_status,work_time", "witkey_task_work", "task_id='$this->_task_id' and ifnull(work_status,0)!=16" );
			//状态分组列表
			$work_st_list = array ();
			foreach ( $work_list as $work ) {
				$work ['work_status'] or $work ['work_status'] = 0;
				$work_st_list [$work ['work_status']] [$work ['work_id']] = $work;
			}
			
			/*无有效稿件时  冻结退款*/
			if (! $work_list) {
				
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 7, //任务状态改为冻结
'exec_time' => 0 ), array ('task_id' => $this->_task_id ) );
				
				$this->union_task_close(-1);//通知联盟失败
				
				//产生冻结记录
				db_factory::inserttable ( TABLEPRE . "witkey_task_frost", array ('frost_status' => '2', 'task_id' => $this->_task_id, 'frost_time' => time (), 'restore_time' => 0, 'frost_key' => 'reward_return', 'frost_reason' => '该任务投稿期结束，但没有可用的有效稿件' ) );
				
				//消息通知雇主
				kekezu::notify_user ( "您有一个任务进入冻结状态", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已到期，因没有有效稿件进入冻结，请联系本站客服。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				return true;
			}
			
			$work_choose_count = 0; //已选稿件数量
			$work_st_list [1] and $work_choose_count ++;
			$work_st_list [2] and $work_choose_count ++;
			$work_st_list [3] and $work_choose_count ++;
			$work_st_list [4] and $work_choose_count ++;
			$work_st_list [5] and $work_choose_count ++;
			
			/*选稿足够*/
			if ($work_choose_count >= $this->_task_info ['work_count']) {
				
				if ($this->_task_info ['notice_count'] <= $work_st_list [13] [count]) {
					/*入围稿件数量足够    并且再没有可入围的稿件*/
					db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 5, //任务状态改为公示中
'exec_time' => time () + ($this->_task_config ['notice_period'] * 24 * 3600), //下次触发时间重设
'sp_end_time' => time () + ($this->_task_config ['notice_period'] * 24 * 3600) ), //公示结束时间
array ('task_id' => $this->_task_id ) );
					//消息通知雇主
					kekezu::notify_user ( "您的任务进入公示", '您的任务<a href="' . $_K ['siteurl'] . 'index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a>到期结束，进入公示期。', $this->_task_info ['uid'], $this->_task_info ['username'] );
					/**
					 * 互评记录
					 * */
					//获奖的
					$list = array_merge ( $work_st_list [1], $work_st_list [2], $work_st_list [3], $work_st_list [4], $work_st_list [5] );
					foreach ( $list as $v ) {
						$rate = (100 - $this->_task_config ['notice_rate']) / 100; //计算比例
						$wc = $this->_task_info ['task_cash']; //威客金额
						$gc = intval ( $this->_task_info ['task_cash'] * $rate / $this->_task_info ['work_count'] ); //雇主金额
						$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
					}
					$mark_count = 0;
					//入围的
					foreach ( $work_st_list [13] as $v ) {
						if ($mark_count < $this->_task_info ['notice_count']) {
							$rate = $this->_task_config ['notice_rate'] / 100; //计算比例
							$gc = $wc = intval ( $this->_task_info ['task_cash'] * $rate ); //雇主金额=威客金额
							$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
						}
						$mark_count ++;
					}
				
				} else {
					/*入围稿件数量不够*/
					
					db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 3, //任务状态改为选稿期
'exec_time' => $this->_task_info ['end_time'] ), //下次触发时间设置为选稿结束
array ('task_id' => $this->_task_id ) );
					
					//消息通知雇主
					kekezu::notify_user ( "您有未结束的待选稿任务需要处理", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a>入围稿件尚未分配完毕，请选择入围稿件。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				}
			
			} else {
				/*这是有稿件可选但不选中标的*/
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 3, //任务状态改为选稿期
'exec_time' => $this->_task_info ['end_time'] ), //下次触发时间设置为选稿结束
array ('task_id' => $this->_task_id ) );
				
				//消息通知雇主
				kekezu::notify_user ( "您有未结束的待选稿任务需要处理", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 尚未选择中标稿件，请尽快选稿。', $this->_task_info ['uid'], $this->_task_info ['username'] );
			}
		
		}
	}
	
	/**
	 * 时间触发任务选稿到期处理
	 * 选稿到期被触发可能是没有选中标  也可能是没有选入围.
	 * 有稿件：无入围则自动入围、进入公示。。
	 * 无入围自动选入围（前提：优先选备选稿件入围  然后是无操作稿件   淘汰稿件不得入围）。
	 * 无稿件：任务冻结等待退款。
	 */
	public function time_choose_end() {
		
		if ($this->_task_status == 3 && $this->_task_info ['end_time'] < time ()) { //选稿结束
			//获得稿件列表
			$work_list = kekezu::get_table_data ( "work_id,uid,username,work_status,work_time", "witkey_task_work", "task_id='$this->_task_id' and ifnull(work_status,0)!=16", "work_time desc" );
			//状态分组列表
			$work_st_list = array ();
			$bid_uids     = array();//所有得奖威客uid数组
			foreach ( $work_list as $work ) {
				$work ['work_status'] or $work ['work_status'] = 0;
				$work_st_list [$work ['work_status']] [$work ['work_id']] = $work;
				in_array($work['work_status'],array(1,2,3,4,5,13)) and $bid_uids[]=$work['uid'];
			}
			
			$work_choose_count = 0; //已选稿件数量
			$work_st_list [1] and $work_choose_count ++;
			$work_st_list [2] and $work_choose_count ++;
			$work_st_list [3] and $work_choose_count ++;
			$work_st_list [4] and $work_choose_count ++;
			$work_st_list [5] and $work_choose_count ++;
			
			if ($work_choose_count >= $this->_task_info ['work_count']) {
				/**
				 * 互评记录
				 * */
				//获奖的
				$work_st_list [1] or $work_st_list [1] = array ();
				$work_st_list [2] or $work_st_list [2] = array ();
				$work_st_list [3] or $work_st_list [3] = array ();
				$work_st_list [4] or $work_st_list [4] = array ();
				$work_st_list [5] or $work_st_list [5] = array ();
				$list = array_merge ( $work_st_list [1], $work_st_list [2], $work_st_list [3], $work_st_list [4], $work_st_list [5] );
				foreach ( $list as $v ) {
					$rate = (100 - $this->_task_config ['notice_rate']) / 100; //计算比例
					$wc = $this->_task_info ['task_cash']; //威客金额
					$gc = intval ( $this->_task_info ['task_cash'] * $rate / $this->_task_info ['work_count'] ); //雇主金额
					$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
				}
				if ($work_st_list [13]) {
					$mark_count = 0;
					//入围的
					foreach ( $work_st_list [13] as $v ) {
						if ($mark_count < $this->_task_info ['notice_count']) {
							$rate = $this->_task_config ['notice_rate'] / 100; //计算比例
							$gc = $wc = intval ( $this->_task_info ['task_cash'] * $rate ); //雇主金额=威客金额
							$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $v ['uid'], 'username' => $v ['username'], 'cash' => $wc ), $v ['work_id'] );
						}
						$mark_count ++;
					}
				}
				/*有选择中标  自动补全入围进入公示*/
				
				//入围稿件数量
				$notice_count = count ( $work_st_list [13] );
				
				if ($notice_count < $this->_task_info ['notice_count']) {
					/*入围稿件不足*/
					$auto_notice_count = 0; //系统自动操作选稿的数量
					

					$temparr = array ();
					$work_st_list [14] and $temparr = array_merge ( $temparr, $work_st_list [14] ); //备选稿件
					$work_st_list [0] and $temparr = array_merge ( $temparr, $work_st_list [0] ); //优先
					$work_st_list [15] and $temparr = array_merge ( $temparr, $work_st_list [15]); //最后考虑淘汰稿件
					//如果入选稿件未选完   自动选择稿件  按照排列  优先备选 然后普通   已淘汰稿件不在考虑之列
					foreach ( $temparr as $w ) {
						//得奖人不能再入围
						if ($notice_count < $this->_task_info ['notice_count']&&!in_array($w['uid'],$bid_uids)) {
							
							//该稿件自动中标
							db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_status = 13 where work_id='{$w['work_id']}'" );
							//通知威客他中标了
							kekezu::notify_user ( "您有新的入围稿件", '您参与的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已进入公示，因该任务入围稿件不足，您的稿件被系统自动选为入围。', $w ['uid'], $w ['username'] );
							/**
							 * 互评记录。补全
							 * */
							$rate = $this->_task_config ['notice_rate'] / 100; //计算比例
							$gc = $wc = intval ( $this->_task_info ['task_cash'] * $rate ); //雇主金额=威客金额
							$this->create_mark_log ( array ('uid' => $this->_guid, 'username' => $this->_gusername, 'cash' => $gc ), array ('uid' => $w ['uid'], 'username' => $w ['username'], 'cash' => $wc ), $w ['work_id'] );
							
							$notice_count ++;
							$auto_notice_count ++;
						}
					}
				}
				
				if ($auto_notice_count) {
					kekezu::notify_user ( "任务进入公示", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 到期进入公示，因为未选定足够的围稿件，系统自动为您选择了' . $auto_notice_count . '个入围稿件。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				} else {
					kekezu::notify_user ( "任务进入公示", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 到期进入公示。', $this->_task_info ['uid'], $this->_task_info ['username'] );
				}
				$sp_end_time = time () + ($this->_task_config ['notice_period'] * 24 * 3600);
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 5, //任务状态改为公示中
'sp_end_time' => $sp_end_time, 'exec_time' => $sp_end_time ), //下次触发时间设置为公示结束
array ('task_id' => $this->_task_id ) );
			
			} else {
				/*未选择中标稿件  走到冻结*/
				db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 7, //任务状态改为冻结
'exec_time' => 0 ), array ('task_id' => $this->_task_id ) );
				
				$this->union_task_close(-1);//通知联盟失败
				
				//产生冻结记录
				db_factory::inserttable ( TABLEPRE . "witkey_task_frost", array ('frost_status' => '3', 'task_id' => $this->_task_id, 'frost_time' => time (), 'restore_time' => 0, 'frost_key' => 'reward_lottery', 'frost_reason' => '该任务选稿期结束，但雇主没有选稿' ) );
				
				
				//消息通知雇主
				kekezu::notify_user ( "您有一个任务进入冻结状态", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 已到期，因您选稿不足，任务冻结，由客服人工受理。', $this->_task_info ['uid'], $this->_task_info ['username'] );
			}
		
		}
	}
	
	/**
	 * 摇奖结束处理  可以时间触发  也可以参与摇奖触发
	 * 根据摇奖规则    分配摇奖金额触发结束
	 */
	public function lottery_end() {
		//var_dump($this->_task_status,time());die();
		if ($this->_task_status == 4) { //判读是否处于摇奖期
			

			//解出摇奖配置
			$lottery_config = array ();
			$this->_task_info ['lottery_config'] and $lottery_config = unserialize ( $this->_task_info ['lottery_config'] );
			
			//取出任务的摇奖百分比设定
			$main_rate = $this->_task_config ['lottery_main_rate'];
			$other_rate = $this->_task_config ['lottery_other_rate'];
			
			//取出参与摇奖的名单
			$join_list = kekezu::get_table_data ( "task_id,uid,username,l_number", "witkey_task_lottery", "task_id='{$this->_task_id}'", "l_number desc", '', '', 'uid' );
			
			$ec_flag = 0; //执行数量标志
			$join_count = count ( $join_list ); //参与摇奖的总人数
			

			$lottery_config ['main_count'] >= $join_count and $main_rate = $main_rate + $other_rate and $lottery_config ['main_count'] = $join_count; //如果参与摇奖人数只够中奖者   则改变分配比
			

			foreach ( $join_list as $k => $v ) {
				$ec_flag ++; //第x次执行
				if ($ec_flag <= $lottery_config ['main_count']) {
					/*一等奖名单内的处理*/
					
					$his_rate = $main_rate / $lottery_config ['main_count']; //他分到的比重
					$his_get = $this->_task_info ['task_cash'] * $his_rate / 100; //他分到实际金额
					$his_get = number_format ( $his_get, '2' ); //保留2位小数
					

					//消息通知中奖者
					kekezu::notify_user ( "任务#{$this->_task_id}的摇奖结果", '恭喜您，通过摇奖成功中标。<br>
						您参与的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 的摇奖，您的摇奖号码是' . $v ['l_number'] . ',排在第' . $ec_flag . '位。<br>
						本期摇奖总金额为' . $this->_task_info ['task_cash'] . ',产生' . $lottery_config ['main_count'] . '名中标者。<br>
						您获得 <b style="color:red">' . $his_get . '元</b>(' . $his_rate . '%) 。<br>中奖金额将在公示期结束后结算。
						', $v ['uid'], $v ['username'] );
					
					//更新摇奖信息
					db_factory::updatetable ( TABLEPRE . 'witkey_task_lottery', array ('get_ratio' => $his_rate, 'get_cash' => $his_get ), array ('lottery_id' => $v ['lottery_id'] ) );
				} else {
					/*平分者的处理*/
					$his_rate = $other_rate / ($join_count - $lottery_config ['main_count']); //他分到的比重
					$his_get = $this->_task_info ['task_cash'] * $his_rate / 100; //他分到实际金额
					$his_get = number_format ( $his_get, '2' ); //保留2位小数
					

					//消息通知中奖者
					kekezu::notify_user ( "任务#{$this->_task_id}的摇奖结果", '您参与的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a> 的摇奖，您的摇奖号码是' . $v ['l_number'] . ',排在第' . $ec_flag . '位。<br>
						本期摇奖总金额为' . $this->_task_info ['task_cash'] . ',未中标者平分' . $other_rate . '%任务佣金。<br>
						您获得 <b style="color:red">' . $his_get . '元</b> (' . $his_rate . '%)。<br>将在公示期结束后结算。
						', $v ['uid'], $v ['username'] );
					
					//更新摇奖信息
					db_factory::updatetable ( TABLEPRE . 'witkey_task_lottery', array ('get_ratio' => $his_rate, 'get_cash' => $his_get ), array ('lottery_id' => $v ['lottery_id'] ) );
				}
			}
			/*稿件循环处理结束*/
			
			//任务状态转到摇奖公示
			db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 5, //任务状态改为公示
'exec_time' => time () + ($this->_task_config ['lottery_notice_period'] * 24 * 3600), //下次触发时间重设
'sp_end_time' => time () + ($this->_task_config ['lottery_notice_period'] * 24 * 3600) ), //公示结束时间
array ('task_id' => $this->_task_id ) );
		
		}
	}
	
	/**
	 * 时间触发公示结束处理
	 * 进入公示的任务必有中标。
	 * 已确认付款的稿件立刻付款
	 * 未确认付款的任务初始化自动付款时间
	 * 
	 * 注  该状态下中标金额按照每个稿件的奖金单独计算    入围计算为每个稿件的入围叠加
	 * 因为存在未完全选稿的可能   未被结算的任务佣金处理不计入结算    不计入退款   但提示到任务跟踪中给客服处理
	 */
	public function time_notice_end() {
		global $kekezu;
		if ($this->_task_status == 5 && time () > $this->_task_info ['sp_end_time']) { //判读是否处于公式期
			

			if (! $this->_task_info ['lottery_config']) {
				
				//获得稿件列表
				$work_list = kekezu::get_table_data ( "work_id,task_id,uid,username,work_status,work_time,is_confirmed", "witkey_task_work", "task_id='$this->_task_id' and ifnull(work_status,0)!=16" );
				//状态分组列表
				$work_st_list = array ();
				foreach ( $work_list as $work ) {
					$work ['work_status'] or $work ['work_status'] = 0;
					$work_st_list [$work ['work_status']] [$work ['work_id']] = $work;
				}
				
				$prize_arr = $this->_prize_arr;
				
				$cost_tc = 0; //已被结算的佣金数  
				$return_tc = 0; //未被结算的佣金数
				$notice_get = 0; //入围结算金额
				
				$bid_uid = array();//中标威客UIDs数组，需传递给联盟端

				//奖项分级计算
				foreach ( $prize_arr as $k => $v ) {
					//是否有中标者
					if ($work_st_list [$k]) {
						//如果实际金额与奖项金额不想等  则还需要取差   这种情况大概是受开票影响
						if ($this->_task_info ['task_cash'] != $this->_task_info ['real_cash']) {
							$v ['prize_cash'] = $v ['prize_cash'] * $this->_task_info ['real_cash'] / $this->_task_info ['task_cash'];
						}
						
						$cost_tc += $v ['prize_cash'];
						$w_c = count ( $work_st_list [$k] ); //该奖项的中奖人数
						$pay_c = ($v ['prize_cash'] * $this->_task_config ['task_rate']) / 100 / $w_c;
						
						foreach ( $work_st_list [$k] as $work_info ) {
							
							if ($work_info ['is_confirmed']) {
								/*有确认付款*/
								db_factory::updatetable ( TABLEPRE . "witkey_task_work", array ('work_price' => $pay_c, //设置稿件所得金额
'exec_time' => 0 ), //不再有触发设定
array ('work_id' => $work_info ['work_id'] ) );
								
								$work_info ['work_price'] = $pay_c;
								
								//中标结算函数
								$this->work_pay ( $pay_c, $work_info ['work_id'], $work_info );
							} else {
								//设置稿件所得金额
								//触发设定
								db_factory::updatetable ( TABLEPRE . "witkey_task_work", array ('work_price' => $pay_c, 'exec_time' => (time () + ($this->_task_config ['confirm_pay_period'] * 24 * 3600)) ), array ('work_id' => $work_info ['work_id'] ) );
							}
							$bid_uid[] = $work_info['uid'];
						}
					} else {
						//该奖项无中标者跳过结算    记录未结算金额
						$return_tc += $v ['prize_cash']; //未被结算的佣金数
					}
				}
				
				count ( $work_st_list [13] ) and $notice_get = ($cost_tc * $this->_task_config ['notice_rate']) / 100 / count ( $work_st_list [13] );
				
				//结算入围稿件
				if ($work_st_list [13]) {
					/*有入围稿件*/
					foreach ( $work_st_list [13] as $k => $v ) {
						db_factory::updatetable ( TABLEPRE . "witkey_task_work", array ('work_price' => $notice_get, //设置稿件所得金额
'exec_time' => 0 ), //不再有触发设定
array ('work_id' => $v ['work_id'] ) );
						
						$v ['work_price'] = $notice_get;
						
						//中标结算函数
						$this->work_pay ( $notice_get, $v ['work_id'], $v );
					}
				} else {
					/*没有入围稿件*/
				//因为不退款  这里预留暂时不实现  回头可能会有需要加统计代码的
				}
				/**
				 * 结算发布推广
				 */
				$kekezu->init_prom ();
				$kekezu->_prom_obj->dispose_prom_event ( 'pub_task', $this->_task_title, $this->_guid, $this->_task_info ['real_cash'], $this->_task_id );
				
				if(!empty($bid_uid)){
					$this->union_task_close(1,$bid_uid);//通知联盟结束
				}
			} else {
				/*以下为摇奖的公示处理*/
				////摇奖流程的处理不涉及到复用   所以不封装成函数
				

				//摇奖参与名单
				$lottery_list = kekezu::get_table_data ( "task_id,uid,username,l_number,get_ratio,get_cash", "witkey_task_lottery", "task_id='$this->_task_id'" );
				foreach ( $lottery_list as $k => $v ) {
					keke_finance_class::cash_in ( $v ['uid'], $v ['get_cash'], 0, 'task_lettory', null, 'task', $this->_task_id ); //支付费用
					//消息通知
					kekezu::notify_user ( "任务#{$this->_task_id}的佣金结算", '任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank">' . $this->_task_info ['task_title'] . '</a>已公示结束，您通过摇奖获得 的' . $v ['get_cash'] . '元已结算。', $v ['uid'], $v ['username'] );
					
					//动态生成
					$feed_arr = array ("feed_username" => array ("content" => $v ['username'], "url" => "index.php?do=shop&u_id={$v['uid']}" ), "action" => array ("content" => "摇奖中标了", "url" => "", 'cash' => $v ['get_cash'] ), "event" => array ("content" => $this->_task_info ['task_title'], "url" => "index.php?do=task&task_id=" . $this->_task_info ['task_id'], 'cash' => $v ['get_cash'] ) );
					kekezu::save_feed ( $feed_arr, $v ['uid'], $v ['username'], 'lottery_win', $this->_task_id );
				}
			}
		
		}
		db_factory::updatetable ( TABLEPRE . "witkey_task", array ('task_status' => 8, //任务状态改为结束
'cash_status'=>2,//已支付托管
'exec_time' => 0 ), //下次触发时间清零
array ('task_id' => $this->_task_id ) );
	
	}
	
	/**
	 * @return 单个稿件的结算处理
	 */
	public function work_pay($paycash, $work_id, $work_info = null) {
		$task_info = $this->_task_info;
		$work_info or $work_info = db_factory::get_one ( "select work_id,task_id,uid,username,work_status,work_time,work_price from " . TABLEPRE . "witkey_task_work where work_id='$work_id'" );
		$task_info or $task_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_task where task_id='{$work_info['task_id']}'" );
		
		if ( $task_info ['task_id'] != '191785' ) {
		//财务结算
		keke_finance_class::cash_in ( $work_info ['uid'], $paycash, 0,  $work_info['work_status']==13?'task_mark':'task_bid', null, 'task',$work_id); //支付费用
		

		//消息通知
		kekezu::notify_user ( "任务#{$task_info['task_id']}的佣金结算", '任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_info ['task_id'] . '" target="_blank">' . $task_info ['task_title'] . '</a>已公示结束， 您获得的 ' . $paycash . '元已结算。', $work_info ['uid'], $work_info ['username'] );
		
		//动态生成
		$feed_arr = array ("feed_username" => array ("content" => $work_info ['username'], "url" =>"index.php?do=shop&u_id={$work_info['uid']}" ), "action" => array ("content" => "中标了", "url" => "", 'cash' => $paycash ), "event" => array ("content" => $task_info ['task_title'], "url" => $_K ['siteurl'] . "/index.php?do=task&task_id=" . $task_info ['task_id'], 'cash' => $paycash ) );
		kekezu::save_feed ( $feed_arr, $v ['uid'], $v ['username'], 'task_bid', $task_info ['task_id'] );
		
		}
		
		//稿件执行时间清零
		db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set exec_time = 0 where work_id = '$work_id'" );
	}
	
	/**
	 * @return 返回多人悬赏任务状态
	 */
	public static function get_task_status() {
		global $_lang;
		return array ("-1" => "未确认", "0" => $_lang ['task_no_pay'], "1" => $_lang ['task_wait_audit'], "2" => $_lang ['task_vote_choose'], "3" => $_lang ['task_choose_work'], "4" => '摇奖', "5" => $_lang ['task_gs'], "6" => "交付", "7" => $_lang ['freeze'], "8" => $_lang ['task_over'], "9" => $_lang ['fail'], "10" => $_lang ['task_audit_fail'], "11" => $_lang ['arbitrate'], '12' => $_lang ['assure_return_cash'] );
	}
	
	/**
	 * @return 返回多人悬赏稿件状态
	 * 
	 */
	public static function get_work_status() {
		global $_lang;
		return array ('1' => '一等奖', '2' => '二等奖', '3' => '三等奖', '4' => '四等奖', '5' => '五等奖', '13' => '入围', '14' => '备选', '15' => '未采纳', '16' => '不可选标' );
	}
	
	/**
	 * @return 返回多人悬赏稿件奖项
	 * 
	 */
	public function get_work_prize() {
		global $_lang;
		$prize_arr = $this->get_task_prize ();
		switch (count ( $prize_arr )) {
			case 1 :
				return array ('1' => $_lang ['prize_1'] );
				break;
			case 2 :
				return array ('1' => $_lang ['prize_1'], '2' => $_lang ['prize_2'] );
				break;
			case 3 :
				return array ('1' => $_lang ['prize_1'], '2' => $_lang ['prize_2'], "3" => $_lang ['prize_3'] );
				break;
		}
	
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