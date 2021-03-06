<?php
/**
 * 普通招标业务类
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
 * dispose_task_return    		 任务金额返还
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
keke_lang_class::load_lang_class ( 'tender_task_class' );
class tender_task_class extends keke_task_class {
	
	public $_task_status_arr; // 任务状态数组
	public $_work_status_arr; // 稿件状态数组
	public $_delay_rule; // 延期规则
	protected $_inited = false;
	
	public static function get_instance($task_info) {
		static $obj = null;
		if ($obj == null) {
			$obj = new tender_task_class ( $task_info );
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
	public function exec_event($action){
		switch($action){
			case 'pub_task':
				
				//执行发布任务
				if($this->_task_config['zb_audit']==1){
					db_factory::execute("update ".TABLEPRE."witkey_task set task_status=1 where task_id='{$this->_task_id}'");
				}
				else{
					$this->task_begin();
				}
				
				break;
			case 'task_pay':
				$bid_info = $this->get_bid_info();
				
				//因异步可能  容错判断
				if ($this->_task_info['task_status']==4&&!$this->_task_info['cash_status']){		
					//更新任务状态
					db_factory::execute("update ".TABLEPRE."witkey_task set task_status = 4,cash_status = 1 where task_id = '$this->_task_id'");
					//消息通知威客
					kekezu::notify_user("赏金托管通知",'您中标的任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$this->_task_id.'" target="_blank">'.$this->_task_title.'</a>.雇主已托管赏金，您可以开始工作了', $bid_info['uid'],$bid_info['username']);
				}
				else{
					//可能在付款完成之前威客取消了   那么该扣除的佣金立刻退还
					keke_finance_class::cash_in($task_info['uid'],$bid_info['quote'],0, 'task_fail',null,'task',$this->_task_id);
				}
				break;
		}
	}
	/*
	 * 任务开始标志
	 * */
	public function task_begin(){ 
		//任务开始
		//计算时间差
		$plus_time = time()-$this->_task_info['start_time'];
		
		//任务数据更新
		db_factory::updatetable(TABLEPRE.'witkey_task', 
			array(
				'task_status'=>2,//任务状态进行中
				'start_time'=>time(),//开始时间变为现在
				'sub_time'=>$this->_task_info['sub_time']+$plus_time,//选稿结束时间补差
				'end_time'=>$this->_task_info['end_time']+$plus_time,//任务结束时间补差
				'exec_time'=>$this->_task_info['sub_time']+$plus_time,//任务下次自动执行时间设置
			),
			array('task_id'=>$this->_task_id)
		);
		
		//积分扣除
		if ($this->_task_info['att_credit']){
			keke_user_class::credit_out($this->_guid,$this->_gusername,2,'任务#'.$this->_task_id.'的增值服务',$this->_task_info['att_credit']);
		}
		
		//生成动态
		$feed_arr = array ("feed_username" => array ("content" => $this->_task_info['username'], "url" =>"index.php?do=shop&u_id={$this->_task_info['uid']}" ),
		 "action" => array ("content" => "发布了任务", "url" => "",'cash'=>$this->_task_info['task_cash'],"model_id"=>$this->_task_info['model_id'],"task_cash_coverage"=>$this->_task_info['task_cash_coverage']), "event" => array ("content" => $this->_task_info['task_title'] , "url" => "index.php?do=task&task_id=".$this->_task_info['task_id']) );
		kekezu::save_feed ($feed_arr,$this->_task_info['uid'],$this->_task_info['username'],'pub_task',$this->_task_id);
		
		//消息通知
		$v = array (
			"用户名" => $this->_task_info['username'],
			"任务编号"=>$this->_task_id,
			"任务标题"=>$this->_task_title,
			"任务链接"=>'<a href="'.$this->_task_id.'" target="_blank">'.$this->_task_title.'</a>',
			"任务状态"=>$this->_task_status_arr[$this->_task_status],
			"开始时间"=>date('Y-m-d H:i:s',$this->_task_info['start_time']),
			"投稿结束时间"=>date('Y-m-d H:i:s',$this->_task_info['sub_time']+$plus_time),
			"选稿结束时间"=>date('Y-m-d H:i:s',$this->_task_info['end_time']+$plus_time)
		);
		$this->notify_user ( "task_pub","任务发布消息提示", $v); // 通知雇主
		
		
		//同城邀请
		$this->_task_info['task_type']!=3&&keke_task_class::city_message_send2 ( $this->_task_info );//直接雇佣不需要通知
		
		//其它操作
		
	}
	
	
	/**
	 * 任务(稿件)状态数组信息
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
		$arr = tender_priv_class::get_priv ( $this->_task_id, $this->_model_id, $this->_userinfo );
		$this->_priv = $this->user_priv_format ( $arr );
	}
	

	/**
	 * 任务交稿(任务竞标)
	 * @Auther Aaron
	 * @param $work_frm string
	 * 表单信息
	 * @param $hidework int
	 * 稿件隐藏 1=>隐藏,2=>不隐藏 默认为不隐藏
	 * @param $url string
	 * 消息提示链接 具体参见 kekezu::keke_show_msg
	 * @param $output string
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @see keke_task_class::work_hand()
	 */
	public function work_hand($work_frm, $hidework = '2', $url = '', $output = 'normal') { //2 $hdn_att_file, 
		global $kekezu, $_K;
		global $_lang;
		
		if ($this->check_if_can_hand ( $url, $output )) { 
			$task_work_obj = new Keke_witkey_task_work_class();
			$task_work_obj->setWhere ( "task_id = $this->_task_id and uid = $this->_uid" );
			$is_hand = $task_work_obj->count_keke_witkey_task_work (); 
			$is_hand and kekezu::keke_show_msg ( '', $_lang['you_haved_tender'], 'error', $output );

			$task_work_obj->setTask_id ( $this->_task_id );
			$task_work_obj->setUid ( $this->_uid );
			$task_work_obj->setUsername ( $this->_username );
			$task_work_obj->setArea ( $work_frm['province'].','.$work_frm['city'].','.$work_frm['area'] );   //所在地
			$task_work_obj->setCycle ( $work_frm['task_over_time'] );  //开发周期
			$task_work_obj->setQuote ( $work_frm['txt_cash'] );        //报价
			$task_work_obj->setWork_title($this->_task_title);						
			$task_work_obj->setWork_time ( time () );                //提交时间
			$task_work_obj->setWork_status(0);                       
			$task_work_obj->setWork_desc($work_frm['tar_content']);
			$res = $task_work_obj->create_keke_witkey_task_work ();

			$hidework == 1 and keke_payitem_class::payitem_record ( "workhide", '1', 'work', 'spend', $res, $this->_task_id );
		
			 //通知雇主有人交稿
			$this->plus_work_num (); //更新任务稿件数量
			
			$this->plus_take_num (); //更新用户交稿数量
			
			$this->plus_wiki_num (); //之前没交过更新任务交稿人数
			
			$url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '">' . $this->_task_title . '</a>';
			$v_arr = array ($_lang['username'] => "$this->_gusername", $_lang['user'] => $this->_username, $_lang['call'] => $_lang['you'], $_lang['task_title'] => $url, $_lang['website_name'] => $kekezu->_sys_config ['website_name'] );
			//keke_shop_class::notify_user ( $this->_guid, $this->_gusername, 'task_hand', $_lang['hand_work_notice'], $v_arr );
			
			kekezu::notify_user ( $_lang['hand_work_notice'], $_lang['you_pub_tender_task_'] . '<a href='.$_K['siteurl'].'/index.php?do=task&task_id=' . $this->_task_id . '&work_id='. $res .'&view=work>' . $this->_task_info ['task_title'] . '</a>' . $_lang['have_new_work'], $this->_guid );
		
			kekezu::keke_show_msg ( $url, $_lang['tender_success'], 'right', $output );
			
		}
	}
	
	/**
	 * 任务交稿
	 * 
	 * @param $work_desc string
	 * 交稿描述
	 * @param $hidework int
	 * 稿件隐藏 1=>隐藏,2=>不隐藏 默认为不隐藏
	 * @param $work_file string
	 * 稿件附件编号串 eg:1,2,3,4,5
	 * @param $mobile string
	 * 用户手机 通过手机认证的不支持修改
	 * @param $qq string
	 * 用户QQ
	 * @param $url string
	 * 消息提示链接 具体参见 kekezu::keke_show_msg
	 * @param $output string
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @see keke_task_class::work_hand()
	 */
	public function tender_work_hand($work_info, $url = '', $output = 'normal') {
		global $kekezu, $_K;
		global $_lang;
		if ($this->check_if_can_hand ( $url, $output )) {
			// 判断是否已交稿
			$this->_task_bid_obj->setWhere ( "task_id = $this->_task_id and uid = $this->_uid and bid_status=0" );
			$is_hand = $this->_task_bid_obj->count_keke_witkey_task_bid ();
			$is_hand and kekezu::keke_show_msg ( '', $_lang['you_haved_tender'], 'error', $output );
			$this->_task_bid_obj->setUid ( $this->_uid );
			$this->_task_bid_obj->setUsername ( $this->_username );
			$this->_task_bid_obj->setArea ( $work_info ['area'] );
			$this->_task_bid_obj->setCycle ( $work_info ['task_over_time'] );
			$this->_task_bid_obj->setQuote ( $work_info ['txt_cash'] );
			$this->_task_bid_obj->setTask_id ( $this->_task_id );
			$this->_task_bid_obj->setBid_time ( time () );
			$this->_task_bid_obj->setHidden_status ( $work_info ['workhide'] );
			$this->_task_bid_obj->setMessage ( $work_info ['tar_content'] );
			$res = $this->_task_bid_obj->create_keke_witkey_task_bid ();
			if ($res) {
				// 是否是联盟任务
				if ($this->_task_info ['task_union'] == '1') {
					$union_obj = new keke_union_class ( $this->_task_id );
					$union_obj->work_hand ( $res );
				}
			}
			$work_info ['workhide'] == 1 and keke_payitem_class::payitem_cost ( "workhide", '1', 'work', 'spend', $res, $this->_task_id );
			// 通知雇主有人交稿
			$this->plus_work_num (); // 更新任务稿件数量
			$this->plus_take_num (); // 更新用户交稿数量
			
			kekezu::notify_user ( $_lang['hand_work_notice'], $_lang['you_pub_tender_task_'] . '<a href='.$_K['siteurl'].'/index.php?do=task&task_id=' . $this->_task_id . '&view=work>' . $this->_task_info ['task_title'] . '</a>' . $_lang['have_new_work'], $this->_guid );
			kekezu::keke_show_msg ( $url, $_lang['tender_success'], 'right', $output );
		
		}
	}
	
	/**
	 * 中标
	 * 
	 * @param $url string
	 * 消息提示链接 具体参见 kekezu::keke_show_msg
	 * @param $output string
	 * 消息输出方式 具体参见 kekezu::keke_show_msg
	 * @see keke_task_class::work_choose()
	 */
	public function work_choose($work_id, $to_status, $url = '', $output = 'json', $trust_response = false) {
		global $_K;
		global $_lang;
		
		//验证是否已在中标状态
		if ($this->get_bid_info()){
			kekezu::keke_show_msg ( $url, "该任务已有中标稿件", 'error', $output );
		}
		
		$status_arr = $this->get_work_status ();
		// 改变稿件状态值
		if ($this->set_work_status ( $work_id, $to_status )) {
			$work_info = $this->get_task_work ( $work_id );
			if($to_status==11){
				db_factory::execute('update '.TABLEPRE.'witkey_task set w_uid='.$work_info['uid'].',w_username="'.
							$work_info['username'].'",w_bid_time='.time().',task_status=4,real_cash ='.$work_info['quote'].' where task_id='.$this->_task_id);
				if(strpos(' '.$this->_task_info['pay_item'],'top')){
					//重设增值属性
				 	$payitem = str_replace('top','',$this->_task_info['pay_item']);
					$payitem = implode(',',array_filter(explode(',',$payitem)));
					db_factory::updatetable ( TABLEPRE . "witkey_task", array (//'is_top'=>0,//清空置顶属性
						'pay_item'=>$payitem),array ('task_id' => $this->_task_id ) );
				}
			}
			//$this->set_task_status ( 0 ); // *更改任务状态为等待付款**/
			$this->plus_accepted_num ( $work_info ['uid'] );
			
			//更改稿件状态
			//db_factory::execute ( sprintf ( " update %switkey_task_work set work_status='%d' where work_id='%d'", TABLEPRE, $to_status, $work_id ) );
			
			
			// 通知威客
			$notify_url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id . '" target="_blank" >' . $this->_task_title . '</a>';
			
			$v = array ("任务编号"=> $this->_task_id,'任务标题'=>$this->_task_title,'任务链接' => $notify_url,"状态"=>$status_arr[$to_status]);
			$this->notify_user ( "task_bid","稿件".$status_arr[$to_status], $v, 1, $work_info ['uid'] ); //通知威客
			kekezu::keke_show_msg ( $url, $_lang['choose_tender_success'], '', $output );
		} else {
			kekezu::keke_show_msg ( $url, $_lang['choose_tender_fail'], 'error', $output );
		}
	}
	
	/**
	 * 设置稿件为淘汰
	 */
	public function set_work_eliminate($work_id, $to_status, $url = '', $output = 'normal') {
		global $_lang;
		$bid_info = $this->select_bid_check ( $work_id, $url );
		// 判读是否已有中标稿件
		$this->_task_bid_obj->setWhere ( " task_id = " . intval ( $this->_task_id ) . " and bid_status = 4" );
		$count = $this->_task_bid_obj->count_keke_witkey_task_bid ();
		$count > 0 and kekezu::keke_show_msg ( $url, $_lang['haved_bid_work'], '', $output );
		// 改变稿件状态值
		$res = $this->set_work_status ( $work_id, $to_status );
		// 通知威客
		kekezu::notify_user ( $_lang['work_notice'], $_lang['you_join_normal_tender_task'] . '<a href='.$_K['siteurl'].'/index.php?do=task&task_id=' . $this->_task_id . ">" . $this->_task_info ['task_title'] . "</a>" . $_lang['work_out'], $bid_info ['uid'] );
		// kekezu::feed_add ( '<a target="_blank"
		// href="index.php?do=space&member_id=' . $bid_info ['uid'] . '">' .
		// $bid_info ['username'] . '</a>成功中标了任务<a
		// href="index.php?do=task&task_id=' . $this->_task_info ['task_id'] .
		// '">' . $this->_task_title . '</a>', $bid_info ['uid'], $bid_info
		// ['username'], 'work_accept' );
		// 更新中标次数
		$this->plus_accepted_num ( $bid_info ['uid'] );
		if ($res) {
			kekezu::keke_show_msg ( $url, $_lang['operate_success'], '', $output );
		} else {
			kekezu::keke_show_msg ( $url, $_lang['operate_fail'], 'error', $output );
		}
	}
	
	
	
	/**
	 * 获取任务稿件信息 支持分页，用户前端稿件列表
	 * 
	 * @param $w array
	 * 前端查询条件数组
	 * ['work_status'=>稿件状态
	 * 'user_type'=>用户类型 --有值表示自己
	 * ......]
	 * @param $p array
	 * 前端传递的分页初始信息数组
	 * ['page'=>当前页面
	 * 'page_size'=>页面条数
	 * 'url'=>分页链接
	 * 'anchor'=>分页锚点]
	 * @return array work_list
	 */
	public function get_work_info($w = array(), $order = null, $p = array()) {
		global $kekezu, $_K;
		$work_arr = array ();
		
		$sql = " select a.*,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile,b.residency,b.w_level,b.seller_total_num,b.seller_good_num,b.isvip from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		
		$count_sql = " select count(a.work_id) from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid";
		$where = " where a.task_id = '$this->_task_id' ";
		
		if (! empty ( $w )) {
			$w ['user_type'] == 'my' and $where .= " and a.uid = '$this->_uid'";
			isset ( $w ['work_status'] ) and $where .= " and a.work_status = '" . intval ( $w ['work_status'] ) . "'";
		/**
		 * 待添加*
		 */
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
		$work_arr ['mark'] = $this->has_mark ( implode ( ',', array_keys ( $work_info ) ) );
		$work_arr ['pages'] = $pages;
		return $work_arr;
	}
	
	/**
	 * 设置稿件状态
	 */
	
	function set_work_status($work_id, $status,$col='op_time') {
		$col=='op_time' or $col = 'work_time';
		return db_factory::execute ( sprintf ( " update %switkey_task_work set work_status='%d',%s='%d' where work_id='%d'", TABLEPRE, $status,$col,time(), $work_id ) );
		
	}
	
	/**
	 * 操作判断
	 * //注意用户权限的判断
	 * 雇主不受威客权限的限制、、拥有威客的所有权限
	 * 威客严格受到条件约束
	 * 威客限制：查看任务
	 * 留言
	 * 举报
	 * 
	 * @see keke_task_class::process_can()
	 */
	public function process_can() {
		$wiki_priv = $this->_priv; // 威客权限数组
		$process_arr = array ();
		$status = intval ( $this->_task_status );
		$task_info = $this->_task_info;
		$config = $this->_task_config;
		$g_uid = $this->_guid;
		$uid = $this->_uid;
		$user_info = $this->_userinfo;
		
		switch ($status) {
			case "-1":
				if($g_uid == $uid){
					$process_arr['edit'] = true; //修改任务
					$process_arr['publish'] = true; //继续发布
					//$process_arr ['task_cancer'] = true;
				}
				else{
					$process_arr ['work_hand'] = true;
				}
				break;
			case "1" : //任务未开始
				if($g_uid == $uid){
					$process_arr['edit'] = true; //修改任务
					//$process_arr ['task_cancer'] = true;
				}
				break;
			case "2" : // 投标中
				switch ($g_uid == $uid) { // 雇主
					case "1" :
						$task_info['ext_desc'] or $process_arr ['reqedit'] = true; // 补充需求
						$task_info['task_type']!=3&&$process_arr ['invite'] = true; // 邀请人才
						//$this->_task_info['task_status']<4&&$process_arr ['task_cancer'] = true;
						if ($config ['open_select'] == 'open') {
							$process_arr ['work_choose'] = true; // 开启投稿中选稿
						}
						$process_arr ['work_comment'] = true; // 稿件回复
						break;
					case "0" : // 威客
						$process_arr ['work_hand'] = true; // 提交稿件
						$process_arr ['task_comment'] = true; // 任务回复
						$process_arr ['work_comment'] = true; // 稿件回复
						break;
				}
				
				break;
			case "3" : // 投标中
				switch ($g_uid == $uid) { // 雇主
					case "1" :
						$process_arr ['work_choose'] = true; // 开启投稿中选稿
						$process_arr ['work_comment'] = true; // 稿件回复
						break;
					case "0" : // 威客
						$process_arr ['task_comment'] = true; // 任务回复
						$process_arr ['work_comment'] = true; // 稿件回复
						break;
				}
				
				break;
			case "0" : // 待付款
				
				//付款有增值服务付款和任务赏金托管的区别  需要加以判断
				$bid_info = $this->get_bid_info();
				if(!$bid_info){
					if($g_uid == $uid){
						$process_arr ['pub_task'] = true;
					}
					else{
						$process_arr['cantviewyet'] = true;
					}
				}
				
//				switch ($g_uid == $uid) { // 雇主
//					case "1" :
//						//$process_arr ['work_choose'] = true; // 选稿
//						$process_arr ['work_comment'] = true; // 稿件回复
//						//$process_arr ['task_cancer'] = true;
//						$process_arr ['task_pay'] = true;
//						//$process_arr['edit'] = true; //修改任务
//						break;
//					case "0" : // 威客
//						$process_arr ['task_comment'] = true; // 任务回复
//						$process_arr ['work_cancer'] = true; // 取消交稿
//						break;
//				}
				break;
			case "4" : // 工作中
				
				//赏金已托管
				if ($this->_task_info['cash_status']){
					$bid_info = $this->get_bid_info ();
					switch ($g_uid == $uid) { // 雇主
						case "1" :
							$process_arr['download'] = true;//文件下载
							$process_arr ['part_pay'] = true;
							$process_arr ['work_comment'] = true; // 留言回复
							$bid_info ['ext_status'] == 1 and $process_arr ['work_over'] = true;
							break;
						case "0" :
							
							$process_arr['upload_source'] = true;//附件上传
							$process_arr['work_complate'] = true;//确认工作
							$process_arr ['task_comment'] = true; // 任务回复
							$bid_info ['ext_status'] != 1 and $process_arr ['pub_agreement'] = true;
							break;
					}
					$process_arr ['work_report'] = true; // 稿件举报
				}
				else{
					//赏金未托管
					switch ($g_uid == $uid) { // 雇主
					case "1" :
						//$process_arr ['work_choose'] = true; // 选稿
						$process_arr ['work_comment'] = true; // 稿件回复
						//$process_arr ['task_cancer'] = true;
						$process_arr ['task_pay'] = true;
						//$process_arr['edit'] = true; //修改任务
						$process_arr['download'] = true;//文件下载
						break;
					case "0" : // 威客
						
						$process_arr ['task_comment'] = true; // 任务回复
						$process_arr ['work_cancer'] = true; // 取消交稿
						break;
					}
				}
				
				
				break;
			
			case "5" : // 威客交付中
				$bid_info = $this->get_bid_info ();
				
				switch ($g_uid == $uid) { // 雇主
					case "1" :
						$process_arr ['part_pay'] = true;
						$process_arr['confirm_pay'] = true;//确认付款
						$bid_info ['ext_status'] == 1 and $process_arr ['work_over'] = true;
						$process_arr['download'] = true;//文件下载
						break;
					case "0" :
						$process_arr['upload_source'] = true;//附件上传
						$this->_uid == $bid_info ['uid'] && $bid_info ['ext_status'] != 1 and $process_arr ['pub_agreement'] = true;
						break;
				}
				break;
			case "8" : // 已结束
				switch ($g_uid == $uid) { // 雇主
					case "1" :
						$process_arr ['work_comment'] = true; // 留言回复
						$process_arr ['work_mark'] = true; // 稿件评价
						$process_arr['download'] = true;//文件下载
						break;
					case "0" :
						$process_arr ['task_comment'] = true; // 任务回复
						$process_arr ['task_mark'] = true; // 任务评价
						break;
				}
				break;
		}
		if($status>1){
			$process_arr['interactive'] = true;//互动
			$process_arr['onekey']      = true;//发布类似需求
			if($uid!=g_uid){
				$process_arr ['task_report'] = true; // 任务举报
				$process_arr ['task_favor'] = true; // 任务收藏
				$process_arr ['task_complaint'] = true; // 任务投诉
			}else{
				$process_arr ['work_complaint'] = true; // 稿件投诉
			}
		}
		$this->_process_can = $process_arr;
		return $process_arr;
	}
	/**
	 *
	 * @return 返回普通招标任务状态
	 */
	public static function get_task_status() {
		global $_lang;
		return array ("-1"=>"未确认","0" => $_lang['task_no_pay'], "1" => $_lang['task_wait_audit'], "2" => $_lang['tendering'], "3" => $_lang['choose_tendering'], "4" => $_lang['working'], "5" => $_lang['jfing'], "7" => $_lang['freeze'], "8" => $_lang['task_over'], "9" => $_lang['fail'], "10" => $_lang['task_audit_fail'], "11" => $_lang['arbitrate'] );
	
	}
	
	/**
	 *
	 * @return 返回普通招标稿件状态
	 *
	 */
	public static function get_work_status() {
		global $_lang;
		return array ('4' => $_lang['task_bid'], '7' => $_lang['task_out'], '8' => $_lang['task_can_not_choose_bid'] );
	
	}
	
	/**
	 *
	 * @return 返回任务英文状态
	 */
	public static function get_task_union_status() {
		return array ('0' => "wait", '1' => "audit", '2' => "sub", '3' => "choose", '4' => "vote", '5' => "notice", '6' => 'deliver', '7' => "freeze", '8' => "end", '9' => "failure", '10' => "audit_fail", '11' => "arbitrate" );
	}
	/**
	 *
	 * @return 返回稿件英文状态
	 */
	public static function get_work_union_status() {
		return array ('0' => 'wait', '4' => 'bid', '8' => 'no_optional' );
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
			case "-1" :
				if($this->_task_info['task_type']<3){
					$time_desc ['time_desc'] = "发布尚未完成"; //时间状态描述
				}
				else{
					$time_desc ['ext_desc'] = "威客未接受";
					$time_desc ['time_desc'] = "需要等待威客同意承接，雇主再托管赏金";
				}
				break;
			case "0" :
				if($this->_task_info['task_type']<3){
					$time_desc ['ext_desc'] = "待付款";
				}
				else{
					$time_desc ['ext_desc'] = "待付款";
					$time_desc ['time_desc'] = "任务金额或者附加服费未尚未支付";
				}
				break;
			case "1" :
				$time_desc ['ext_desc'] = $_lang['task_auditing'];
				break;
			case "2" : // 投标中
				$time_desc ['time_desc'] = "距投标截止"; // 时间状态描述
				$time_desc ['time'] = $task_info ['sub_time']; // 当前状态结束时间
				$time_desc ['ext_desc'] = $_lang['task_doing_can_tender']; // 追加描述
				if ($this->_task_config ['open_select'] == 'open') { // 开启进行选稿
					$time_desc ['g_action'] = $_lang['present_state_employer_can_choose']; // 雇主追加描述
				}
				break;
			case "3" : // 选标中
				$time_desc ['time_desc'] = "距选标截止"; // 时间状态描述
				$time_desc ['time'] = $task_info ['end_time']; // 当前状态结束时间
				$time_desc ['ext_desc'] = $_lang['task_choosing_tender']; // 追加描述
				$time_desc ['g_action'] = $_lang['present_state_employer_can_choose']; // 雇主追加描述
				break;
			case "4" : // 工作中
				if($this->_task_info['cash_status']){
					$time_desc ['time_desc'] = "雇主已选标，威客工作中"; 
					$time_desc ['ext_desc'] = "进行中"; // 追加描述
				}
				else{
					$time_desc ['time_desc'] = "等待雇主托管赏金后，威客开始工作"; 
					$time_desc ['ext_desc'] = "等待雇主付款"; // 追加描述
				}
				break;
			case "5" : // 交付中
				$time_desc ['time_desc'] = "威客已完成，等待雇主验收"; 
				$time_desc ['ext_desc'] = "待验收"; // 追加描述
				break;
			case "7" : // 冻结中
				$time_desc ['ext_desc'] = $_lang['task_diffrent_opnion_and_web_in']; // 追加描述
				break;
			case "8" : // 结束
				$time_desc ['ext_desc'] = $_lang['task_haved_complete']; // 追加描述
				break;
			case "9" : // 失败
				$time_desc ['ext_desc'] = "雇主或管理员关闭了任务"; // 追加描述
				break;
			case "10" : // 失败
				$time_desc ['ext_desc'] = "任务审核失败"; // 追加描述
				break;
			case "11" : // 仲裁
				$time_desc ['ext_desc'] = $_lang['task_arbitrating']; // 追加描述
				break;
		}
		
		return $time_desc;
	}
	
	// 获取中标稿件
	function get_bid_info() {
		return db_factory::get_one ( sprintf ( " select * from %switkey_task_work where task_id='%d' and work_status = 11", TABLEPRE, $this->_task_id) );
	}
	
	// 改变稿件状态
	function set_bid_status($bid_id, $bid_status) {
		$this->_task_bid_obj->setWhere ( " bid_id = $bid_id" );
		$this->_task_bid_obj->setBid_status ( $bid_status );
		$res = $this->_task_bid_obj->edit_keke_witkey_task_bid ();
		if ($res) {
			return $res;
		} else {
			return false;
		}
	
	}
	
	// 改变协议状态
	function set_agreement_status($bid_id, $status) {
		$this->_task_bid_obj->setWhere ( " bid_id = $bid_id" );
		$this->_task_bid_obj->setExt_status ( $status );
		$res = $this->_task_bid_obj->edit_keke_witkey_task_bid ();
		if ($res) {
			return $res;
		} else {
			return false;
		}
	}
	
	public function dispose_order($order_id) {
		global $_K;
		global $_lang;
		// 后台配置
		$task_config = $this->_task_config;
		$task_info = $this->_task_info; // 任务信息
		$url = $_K ['siteurl'] . '/index.php?do=task&task_id=' . $this->_task_id;
		$task_status = $this->_task_status;
		$order_info = db_factory::get_one ( sprintf ( "select order_amount,order_status from %switkey_order where order_id='%d'", TABLEPRE, intval ( $order_id ) ) );
		$order_amount = $order_info ['order_amount'];
		if ($order_info ['order_status'] == 'ok') {
			$task_status == 1 && $notice = $_lang['task_pay_success_and_wait_admin_audit'];
			$task_status == 2 && $notice = $_lang['task_pay_success_and_task_pub_success'];
			return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $notice, $url, 'success' );
		} else {
			$res = keke_finance_class::cash_out ( $task_info ['uid'], $order_amount, 'pub_task',$task_info['task_cash']); // 支付费用
			switch ($res == true) {
				case "1" : // 支付成功
					// 更改订单状态到已付款状态
					db_factory::updatetable ( TABLEPRE . "witkey_order", array ("order_status" => "ok" ), array ("order_id" => "$order_id" ) );
					if ($order_amount < $task_config ['audit_cash']) { // 如果订单的金额比发布任务时配置的审核金额要小
						$this->set_task_status ( 1 ); // 状态更改为审核状态
						return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_success_and_wait_admin_audit'], $url, 'success' );
					} else {
						$this->set_task_status ( 2 ); // 状态更改为进行状态
						return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_success_and_task_pub_success'], $url, 'success' );
					}
					break;
				case "0" : // 支付失败
					$pay_url = $_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id"; // 支付跳转链接
					return pay_return_fac_class::struct_response ( $_lang['operate_notice'], $_lang['task_pay_error_and_please_repay'], $pay_url, 'warning' );
					break;
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