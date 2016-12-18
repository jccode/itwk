<?php
/**
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-11-1-下午04:50:34
 * @version V2.0
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = 'task';
$basic_url = $_K['siteurl']."/index.php?do=task&task_id=$task_id"; //基本链接
$cash_cove_arr = kekezu::get_cash_cove();
$task_obj = tender_task_class::get_instance ( $task_info ); 

if($task_info['exec_time']&&$task_info['exec_time']<time()){
	$time_obj = new tender_time_class();
	if($time_obj->exec_task($task_id,$task_info))
	$task_info = header("Location:$basic_url");
}
if($task_info['task_type']==2){
	$price_unit  = keke_glob_class::get_price_unit();//价格单位
}
//直接雇佣对应的信息屏蔽
if($task_info['task_type']==3){
	$hire_work = db_factory::get_one("select uid,username,work_id from ".TABLEPRE."witkey_task_work where task_id = '$task_id'");
	if ( !isset($uid) || ($hire_work['uid']!=$uid&&$task_info['uid']!=$uid&&!in_array($user_info['group_id'],$task_rule_group_id))){
		$back_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : "/";
		kekezu::show_msg("直接雇佣任务只有雇佣双方可见",$back_url);//只有双方可以浏览
	}
	$view or $view= 'work';
	$work_id = $hire_work['work_id'];
	
	if($work_info['mark_status']){
		$mark_info = db_factory::get_one("select * from ".TABLEPRE."witkey_mark where origin_id = '$task_id' and by_uid = '{$task_info['uid']}' and mark_status>0",180);
	}
	
}elseif(!$uid&&strpos(' '.$task_info['pay_item'],'seohide')){
	kekezu::show_msg("拒绝访问","index.php",3,"该任务在您登录后才可以访问");
}





//招标任务的价格区间
$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");

//访问状态判断
if(in_array($task_info['task_status'],array(-1,0,1,10))&&$uid!=$task_info['uid']&&!in_array($user_info['group_id'],$task_rule_group_id)&&$uid!=$hire_work['uid']){
	$status_arr = $task_obj->get_task_status();
	kekezu::show_msg("拒绝访问","index.php","该任务状态为{$status_arr[$task_info['task_status']]},只有发布者和客服可以访问");
}



$task_config =$task_obj->_task_config;
$model_id = $task_obj->_model_id;
$task_status = $task_obj->_task_status;
$status_arr = $task_obj->_task_status_arr; //任务状态数组

//任务时间描述
$time_desc = $task_obj->get_task_timedesc (); 
$stage_desc = $task_obj->get_task_stage_desc (); //任务阶段样式
$related_task = $task_obj->get_task_related ();//获取相关任务

$delay_rule = $task_obj->_delay_rule;//延期规则
$delay_total = sizeof($delay_rule);//可延期次数
$delay_count=intval($task_info['is_delay']);//已延期次数
$process_can = $task_obj->process_can (); //用户操作权限
$process_desc = $task_obj->process_desc (); //用户操作权限中文描述
$task_obj->plus_view_num();//查看加一
$trust_mode=$task_obj->_trust_mode;//担保模式
$time_obj =new  tender_time_class();
$g_info   = $task_obj->_g_userinfo;
$contact = unserialize($task_info['contact']);



//推广链接
$prom_url = $_K['siteurl']."/index.php?do=prom&p=task&task_id=$task_id&epi=$uid";
$prom_content = "#微博赚钱#来接活吧，这里有一个￥{$task_info['task_cash']}元的 {$task_info['task_title']}任务，就快结束了哦，还等什么？轻轻一点，轻松赚钱！[耶] (来自@IT帮手网)";


//$time_obj->task_hand_end();
//$time_obj->task_choose_end();
//$time_obj->task_vote_end();
//$time_obj->task_notice_end();
//$time_obj->task_agreement_end();

$show_payitem = $task_obj->show_payitem();

$browing_history = $task_obj->browing_history($task_id,$task_info['task_cash']."元",$task_info['task_title']);
if($task_info['task_type']<3&&$uid&&$uid!=$task_info['uid']){//查找当前用户的报价
	$my_work = db_factory::get_one(sprintf(" select work_id,quote,cycle,work_desc,area from %switkey_task_work where task_id=%d and uid=%d",TABLEPRE,$task_id,$uid));
	
	if(in_array($task_info['task_status'],array(5,7,8))){
		$mark_list = kekezu::get_table_data("mark_status,obj_id,mark_content,mark_time","witkey_mark","origin_id = '$task_id' and by_uid = '{$task_info['uid']}' and mark_status>0 ",'','','','obj_id',180);//3分钟缓存
	}
}
 //操作
switch ($op) {
	case "reqedit" : //需求补充
		$title =$_lang['supply_demand'];
		if ($sbt_edit) {
			$task_obj->set_task_reqedit ( $tar_content, '', 'json' );
		} else{
			$ext_desc = $task_info ['ext_desc'];
			require keke_tpl_class::template ( 'task/task_reqedit' );
		}
		die ();
		break;
	case "edit" : //任务修改
		$title = "修改任务";
		if($sbt_edit){
			$task_obj->edit_task($_REQUEST,'','json');
			
		} else {
			
			if($task_info['task_type']==2){
				$indus_arr =  kekezu::get_table_data ( '*', "witkey_industry", "indus_type=2 ", "listorder asc ", '', '', 'indus_id', NULL );
				$indus_p_arr = kekezu::get_table_data ( '*', "witkey_industry", "indus_type=2 and indus_pid = 0 ", "listorder asc ", '', '', 'indus_id', NULL );
				$indus_c_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=2 and indus_pid >0', 'listorder', '', '', 'indus_id', NULL );
			}
			
			if($task_info['task_type']>1){//直接雇佣和服务类任务调用默认的任务编辑
				$attach_list = $task_obj->get_task_file(0);
				$flie_types = explode ( '|', $basic_config ['file_type'] );
				require keke_tpl_class::template('task/task_edit_default');
			}else{
				if($t=='load'){//异步加载本任务模型的编辑处理页面代码
					$task_day = ceil(($task_info['sub_time']-time())/(24*3600));
					require keke_tpl_class::template('task/tender/tpl/'.$_K['template'].'/task_edit');die();
				}else{//调用公共的任务编辑页面，单，多，招公用
					$attach_list = $task_obj->get_task_file(0);
					$flie_types = explode ( '|', $basic_config ['file_type'] );
					require keke_tpl_class::template('task/task_edit');die();
				}
			}
		}
		break;
	case "work_confirm":
		$task_obj->work_confrim($work_id,'','json');
		die();
		
		break;
	case "taskdelay" : //延期
		$title = $_lang['task_delay'];
		if($sbt_edit){
			$task_obj->set_task_delay($delay_day, $delay_cash,'','json');
		}else{
			$min_cash = intval($task_config['min_delay_cash']);//配置最小延期金额
			$max_day  = intval($task_config['max_delay']);//配置最大延期天数
			$this_min_cash = intval($delay_rule[$delay_count]['defer_rate']*$task_info['task_cash']/100);//本次最小延期金额
			$min_cash>$this_min_cash and $real_min = $min_cash or $real_min = $this_min_cash;//真正最小金额
			$credit_allow =  intval($kekezu->_sys_config ['credit_is_allow']);//金币开启
			require keke_tpl_class::template("task/task_delay");
		}
		die();
		break;
	case "work_hand" : //立即报价
		$task_info['task_type']!=2 and $title = '单人招标报价' or $title = '劳务服务报价'; 
		if($sbt_edit){
			$work_frm['city'] = $city;
			$work_frm['area'] = $area;
			$work_frm['province'] = $province;			
			$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
			$task_obj->work_hand ( $work_frm, $workhide, '', 'json'); //2 $file_ids,
		}else{
			 //检查是否已竞标
			if($op_check && $task_id){
					$task_work_obj = new Keke_witkey_task_work_class();
					$task_work_obj->setWhere ( "task_id = $task_id and uid = $uid" );
					$is_hand = $task_work_obj->count_keke_witkey_task_work (); 
					$is_hand and kekezu::keke_show_msg ( '', $_lang['you_haved_tender'], 'error', $output) or kekezu::keke_show_msg ( '',  $_lang['you_not_tender'] , 'success', $output);die();
			}
			require keke_tpl_class::template ( 'task/work_hand' ); //reward_work  work_hand tender_work
		}
		die();
		break;
	case 'work_modify'://修改报价
		$title = '修改报价'; 
		if($sbt_edit){
			if($work_id==$my_work['work_id']&&$task_obj->valid_task_status()==2){
				$narea    = $province.','.$city.','.$area;
				$t_obj = new Keke_witkey_task_work_class();
				$t_obj->setWhere('work_id='.$work_id.' and uid='.$uid);
				$t_obj->setCycle($work_frm['task_over_time']);
				$t_obj->setQuote($work_frm['txt_cash']);
				$t_obj->setWork_desc($work_frm['tar_content']);
				$t_obj->setArea($narea);
				$t_obj->edit_keke_witkey_task_work();
				kekezu::keke_show_msg('','报价修改成功','success','json');
			}
			kekezu::keke_show_msg('','报价修改失败','error','json');die();
		}else{
			 //检查是否已竞标
			if($op_check && $work_id){
				$work_id!=$my_work['work_id'] and kekezu::keke_show_msg ( '','您无权修改此报价', 'error', $output) or kekezu::keke_show_msg ( '','修改报价' , 'success', $output);die();
			}
			$quote = $my_work['quote'];
			$cycle = $my_work['cycle'];
			$desc  = $my_work['work_desc'];
			$loca  = explode(',',$my_work['area']);
			require keke_tpl_class::template ( 'task/work_hand' ); //reward_work  work_hand tender_work
		}
		die();
		break;
	case "work_choose" : //选稿
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->work_choose ( $work_id, 11,'','json');
		break;
	case "report" : //举报
		$transname = keke_report_class::get_transrights_name($type);
		$title=$transname.$_lang['submit'];
		$ext = kekezu::get_ext_type ();
		//取得举报分类
		$buyer_type = keke_glob_class::get_buyer_report_type();
		$seller_type = keke_glob_class::get_seller_report_type();
		if($sbt_edit){
			if($file_ids){
				$file_info = db_factory::get_one(sprintf("select file_id,save_name from %switkey_file where file_id=%d",TABLEPRE,$file_ids));
			}
			$task_obj->set_report ( $obj, $obj_id, $to_uid,$to_username, $type, $file_info, $tar_content, $report_cate);
		}else{
			require keke_tpl_class::template("report");
		}
		die();
		break;
	case "mark" : //评价
		$title = $_lang['each_mark'];
		$model_code = $task_obj->_model_code;
		require S_ROOT.'control/mark.php';
		die();
		break;
	case "work_del"://稿件删除
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->del_work($work_id,'','json');
		break;
	case "comment" : //相关留言
		switch ($obj_type) {
			case "task" :
				break;
			case "work" :
				$tar_content and $task_obj->set_work_comment ( $obj_type, $obj_id, $tar_content, $p_id, '', 'json' );
				break;
		}
		break;
//	case "task_cancer"://取消任务
//		//任务状态改为失败
//		$uid != $task_info['uid'] and die();
//		$task_info['task_status'] != 2 and $task_info['task_status'] != 0 and die();
//		
//		db_factory::execute ( sprintf ( " update %switkey_task set task_status='%d',exec_time=0 where task_id='%d'", TABLEPRE, 9, $task_id ) );
//		
//		//通知威客交易已取消
//		if ($task_obj->valid_task_status()==3){
//			$bid_info = $task_obj->get_bid_info();
//			if ($bid_info){
//				kekezu::notify_user("任务取消通知",'您参与的招标任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>雇主取消了招标，该任务已关闭。', $bid_info['uid'],$bid_info['username']);
//				db_factory::execute("update ".TABLEPRE."witkey_task_work set work_status = 0 where task_id = '$task_id'");
//			}
//		}
//		kekezu::show_msg("任务取消成功",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,"该任务已成功关闭，如果您需要激活，请重新发布新的任务。");
//		break;
	case "work_cancer"://取消竞标
		
		//任务状态改为失败
		$task_info['task_status'] != 2 and ($task_info['task_status'] != 4||$task_info['cash_status']) and die();
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$work_info = $task_obj->get_task_work($work_id);
		$bid_info = $task_obj->get_bid_info();
		db_factory::execute("delete from ".TABLEPRE."witkey_task_work where work_id = '{$work_info['work_id']}' and uid='$uid'");
		//通知威客交易已取消
		if ($task_info['task_status'] != 2){
			
			if ($bid_info['uid']==$uid&&$bid_info['work_id']==$work_info['work_id']){
				db_factory::execute('update '.TABLEPRE.'witkey_task set w_uid="",w_username="",w_bid_time="" where task_id='.$task_id);
				kekezu::notify_user("投标取消通知",'您发布的招标任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>威客取消了竞标，任务恢复到选标状态，请您重新选标。', $task_info['uid'],$task_info['username']);
				db_factory::execute ( sprintf ( " update %switkey_task set task_status='%d',exec_time=end_time,work_num=work_num-1 where task_id='%d'", TABLEPRE, 2, $task_id ) );
			}
		}
		kekezu::show_msg("竞标取消成功",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,"您的竞标信息已删除，如果还需要参与任务，请重新报价。");
		break;
	case "task_pay":
		//安全验证
		($uid!=$task_info['uid']||$task_obj->valid_task_status()!=4||$task_info['cash_status']) and kekezu::show_msg("错误",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,"状态错误或者您不是雇主");
		
		//是否已存在待支付的订单
		$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='task_pay' and obj_id='$task_id'" );
		//中标着信息  需要影响支付金额
		$bid_info = $task_obj->get_bid_info();
		if($task_info['task_type']==3){//直接雇佣。使用任务金额来结算。
			$cash = $task_info['task_cash'];
		}else{
			$cash = $bid_info['quote'];
		}
		$order_obj = new Keke_witkey_order_class();
		$order_obj->setOrder_amount ($cash);
		$order_obj->setOrder_status ('wait');
		$order_obj->setOrder_name( "任务:{$task_info['task_title']} 托管赏金" );
		$order_obj->setOrder_uid ( $uid );
		$order_obj->setOrder_username ( $username );
		$order_obj->setObj_type ( 'task_pay' );
		$order_obj->setObj_id ( $task_id );
		$order_obj->setTask_id ( $task_id );
		$order_obj->setModel_id ( $task_info ['model_id'] );
		if ($order_exsit){
			$order_obj->setOrder_id ( $order_exsit ['order_id'] );
			$order_obj->edit_keke_witkey_order();
			$order_id = $order_exsit ['order_id'];
		}
		else{
			$order_id = $order_obj->create_keke_witkey_order();
		}
		
		
		//跳转到收银台
		header ( "location:".$_K['siteurl']."/index.php?do=pay&order_id=$order_id&obj_type=task&obj_id=$task_id" );
		
		break;
	case "work_complate":
		$bid_info = $task_obj->get_bid_info();
		$ttask_status=$task_obj->valid_task_status();
		$bid_info['uid']!=$uid && kekezu::show_msg("权限不足","index.php?do=task&task_id=$task_id");
		
		($task_obj->valid_task_status()!=4||!$task_info['cash_status']) && kekezu::show_msg("当前状态无法确认工作","index.php?do=task&task_id=$task_id");
		
		//任务状态改变到验收中
		db_factory::execute("update ".TABLEPRE."witkey_task set task_status = 5 where task_id = '$task_id'");
		//消息通知
		kekezu::notify_user("验收任务",'您的任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>，威客已完成任务，请验收', $task_info['uid'], $task_info['username']);
		
		kekezu::show_msg("任务已完成",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
		die();
		break;
	case "confirm_pay":
		$bid_info = $task_obj->get_bid_info();
		$task_info['uid']!=$uid && kekezu::show_msg("权限不足",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		$task_obj->valid_task_status()!=5&&kekezu::show_msg("操作无效，任务已验收完成",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		db_factory::updatetable ( TABLEPRE . "witkey_task", 
									array ('task_status' => 8, //任务状态改为结束
											'cash_status'=>2,//已支付托管
											'exec_time' => 0 ), //下次触发时间清零
									array ('task_id' => $task_id ) );
		if($task_info['task_type']==3){//直接雇佣。使用任务实际金额来结算。
			$cash = $task_info['real_cash'];
		}else{
			//查找开票记录
			$iv_id = db_factory::get_count(' select iv_id from '.TABLEPRE.'witkey_invoice where task_id='.$task_id.' and iv_status=1');
			$iv_id and $cash = $bid_info['quote']*0.945 or $cash = $bid_info['quote'];//扣除税金
		}
		
		$task_info['part_cash'] and $cash = $cash - $task_info['part_cash'];
		
		
		//消息通知
		kekezu::notify_user("任务佣金结算",'您参与的任务<a href='.$_K['siteurl'].'/"index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>，雇主已确认付款，您收到佣金<span style="color:red">'.$cash.'</span>元', $bid_info['uid'], $bid_info['username']);
		//财务结算
		
		keke_finance_class::cash_in($bid_info['uid'],$cash,0,'task_bid',null,'task',$bid_info['work_id']);
		
		// 写入feed
		$feed_arr = array ("feed_username" => array ("content" => $bid_info ['username'], "url" =>"index.php?do=shop&u_id=$bid_info[uid]" ),
		 "action" => array ("content" => '成功中标了', "url" => "",'cash'=>$cash), "event" => array ("content" => "{$task_info['task_title']}", "url" => "index.php?do=task&task_id={$task_id}" ) );
		kekezu::save_feed ( $feed_arr, $bid_info ['uid'], $bid_info ['username'], 'work_accept', $task_info ['task_id'] );
		/**
		 * 产生互评
		 */
		$task_obj->create_mark_log(
					array('uid'=>$task_info['uid'],'username'=>$task_info['username'],'cash'=>$task_info['real_cash']),
					array('uid'=>$bid_info['uid'],'username'=>$bid_info['username'],'cash'=>$task_info['real_cash']),
					$bid_info['work_id']);
		kekezu::show_msg("任务已验收",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
		die();
		break;
		
	//直接雇佣任务专用
	case "agree_task"://接受雇佣
		$hire_work['uid']!=$uid && kekezu::show_msg("权限不足",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		$task_obj->valid_task_status()!=-1&&kekezu::show_msg("您已接受雇佣,无需再次确认",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		//任务状态改变到待付款
		$task_obj->set_task_status(4);
		db_factory::execute('update '.TABLEPRE.'witkey_task set w_uid='.$hire_work['uid'].',w_username="'.
							$hire_work['username'].'",w_bid_time='.time().' where task_id='.$task_id);
		$task_obj->plus_take_num();
		//消息通知雇主
		kekezu::notify_user("直接雇佣接受",'您发布的任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>，威客已接受雇佣，请您尽快托管佣金。', $task_info['uid'], $task_info['username']);
		//变更稿件为中标
		$task_obj->set_work_status($work_id,11,'work_time');
		$task_obj->plus_wiki_num (); //之前没交过更新任务交稿人数
		kekezu::show_msg("任务已接受雇佣",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
		die();
		break;
	//直接雇佣任务专用
	case "refuse_task"://拒绝雇佣
		$hire_work['uid']!=$uid && kekezu::show_msg("权限不足",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		$task_obj->valid_task_status()==9&&kekezu::show_msg("您已放弃雇佣,无需再次确认",$_K['siteurl']."/index.php?do=task&task_id=$task_id");
		
		//任务状态改变到失败
		$task_obj->set_task_status(9);
		//消息通知雇主
		kekezu::notify_user("直接雇佣拒绝接受",'您发布的任务<a href="'.$_K['siteurl'].'/index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>，威客拒绝接受雇佣，任务已失败。', $task_info['uid'], $task_info['username']);
		
		kekezu::show_msg("任务已拒绝雇佣",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
		die();
		break;
	//分期付款
	case "part_pay":
		
		!$process_can['part_pay'] and kekezu::show_msg("无操作权限",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,"任务状态错误或者您不是雇主");
		//获得中标稿件
		$bid_work = $task_obj->get_bid_info();
		
		$task_cash = $bid_work['quote'];
		
		//直接雇佣以任务金额为准
		$task_info['task_type']==3 and $task_cash = $task_info['task_cash'];
		
		//最低最高验证
		$min_pay = $bid_work['quote']*10/100;
		$max_pay = $bid_work['quote']-$task_info['part_cash'];
		
		if($sbt_edit){
			$pay_cash < $min_pay and kekezu::show_msg("付款金额太低",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
			$pay_cash > $max_pay and kekezu::show_msg("付款金额高于任务佣金",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3);
			
			//记录到任务表
			$res = db_factory::execute("update ".TABLEPRE."witkey_task set part_cash = ifnull(part_cash,0) + $pay_cash where task_id = '$task_id' and uid = '$uid' ");
			//任务付款
			keke_finance_class::cash_in($bid_work['uid'],$pay_cash,0,'task_bid','','task',$bid_work['work_id']);
			
			//消息提示
			kekezu::notify_user("分期付款通知",'您中标的任务 <a href="index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a> 雇主预付了<span style="color:red">'.$pay_cash.'</span>元到您的账户上。', $bid_work['uid'],$bid_work['username']);
			
			kekezu::show_msg("分期付款成功",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,'','success');
			die();
		}
		else{
			require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/part_pay" );
			die();
		}
		
		break;
}

//稿件信息为必读
$search_condit = $task_obj->get_search_condit();
$date_prv = date("Y-m-d",time());//用在雇主回复时的时间前缀部分
$work_status = $task_obj->get_work_status ();//获取稿件状态数组
intval ( $page ) and $param ['page'] = intval ( $page ) or $param ['page']='1';
intval ( $page_size ) and $param ['page_size'] = intval ( $page_size ) or $page_size = $param['page_size']=12;
$param['url'] = $basic_url."&st=$st&ut=$ut&page_size=".$param ['page_size']."&page=".$param ['page'];
$param ['anchor'] = '';
$work_id and $w['work_id'] = $work_id;//稿件编号
$st and $w['work_status'] = $st;//稿件状态
$ut and $w['user_type']   = $ut;//用户类型  my自己
$order = max($order,1);//1 降序 asc

//中标稿件的源文件获取
if($search_condit[11]){
	$temp = db_factory::query("select a.* from ".TABLEPRE."witkey_file a inner join ".TABLEPRE."witkey_task_work b on a.work_id = b.work_id and b.task_id = '$task_id' and b.work_status = 11 and a.obj_type='worksource'");
	$bid_work_source = array();
	foreach ($temp as $tes){
		$bid_work_source[$tes['work_id']][] = $tes;
	}
}

switch($order){
	case 1:
	default:
		$ord='(case when work_status>0 then work_status else 14.5 end),work_id ';
		break;
	case 2:
		$ord='work_id desc';
		break;
	case 3:
		$ord='work_id asc';
		break;
}
if($view=='work'&&$task_info['task_type']!=2){  //详细页
	$work_info = db_factory::get_one(" select a.*,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile,b.isvip,b.seller_credit,b.seller_good_num,b.residency,b.seller_total_num,b.w_level from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid where a.work_id='$work_id'");
	$work_pics = keke_glob_class::work_pics($work_info['work_file']);
	if($task_info['task_type']==3&&$task_info['task_status']==8){//直接雇佣完成后获取互评信息
		$mark = $task_obj->has_mark($work_info['work_id']);
	}
	if($task_info['task_type']==3){
		$task_file = $task_obj->get_task_file (); //任务附件
	}
	
	//翻页用稿件列表信息
	$work_arr = db_factory::query("select work_id from keke_witkey_task_work where task_id=".$task_id." order by ".$ord);
	foreach($work_arr as $k => $v){
		if($v["work_id"]==$work_id){
			$this_num = $k;
			break;
		}
	}
	$back_num = $this_num-1;
	$next_num = $this_num+1;
	if(!is_array($work_arr[$back_num])){
		$back_num = -1;
	}
	if(!is_array($work_arr[$next_num])){
		$next_num = -1;
	}
}else{ //列表页
	
	$work_arr = $task_obj->get_work_info ($w, " $ord ", $param ); //稿件信息  $w条件 $order排序 $p分页
	$pages = $work_arr ['pages'];  
	$work_info = $work_arr ['work_info'];
	$mark      = $work_arr['mark'];
	$agree_id  = intval($task_obj->_agree_id); 
	
	//雇主已查看功能
	$task_obj->edit_work_view_status($work_info);
	
}

///*检测是否有新留言**/
$has_new  = $task_obj->has_new_comment($param ['page'],$param ['page_size']);
switch ($view) {
	case "work" :
		//稿件详细页
		
//		require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_work" );
//		die();
		break;
	case "comment" :
		$comment_obj = keke_comment_class::get_instance('task'); 
		$url = $basic_url."&view=comment";
		intval($page) or $page = 1;
		
	    switch ($op){
	    	case "reply": //回复任务留言
	    		$tmp ='replay_comment';
	    		require keke_tpl_class::template ( "task/comment_reply" );
	    		die();
	    		break;
	    	case "add": //添加任务留言 
	    		
	    		if(!$uid){
	    			echo 'nologin';
					die();
	    		}
	    		
			    if(kekezu::k_match(array($kekezu->_sys_config['ban_content']),$content)){
					echo 'ban';
					die();
				}
	    		
	    		//插入comment记录
	    		$comm_obj = new Keke_witkey_comment_class();
	    		$comm_obj->setObj_id($task_id);
	    		$comm_obj->setOrigin_id($task_id);
	    		$comm_obj->setObj_type('task');
	    		$reply_id and $comm_obj->setP_id($reply_id);
	    		$comm_obj->setUid($uid);
	    		$comm_obj->setUsername($username);
	    		$comm_obj->setContent($content);
	    		$comm_obj->setOn_time(time());
	    		$comment_id = $comm_obj->create_keke_witkey_comment();
	    		
	    		
	    		
	    		//更新留言数量
	    		if ($comment_id){
	    			db_factory::execute(sprintf(" update %switkey_task set leave_num =ifnull(leave_num,0)+1 where task_id='%d'",TABLEPRE,$task_id));
	    		}
	    		
	    		echo $comment_id;
	    		
	    		die();
	    		break;
	    	case "del": 
	    		$comment_info = db_factory::get_one("select uid from ".TABLEPRE."witkey_comment where comment_id = '$comment_id'");
	    		if ($comment_info['uid']!=$uid){
	    			echo 'noaccess';die();
	    		}
	    		$res = db_factory::execute("delete from ".TABLEPRE."witkey_comment where comment_id = '$comment_id' or p_id='$comment_id'");
	    			$res&&db_factory::execute(' update '.TABLEPRE.'witkey_task set leave_num=leave_num-'.intval($res).' where task_id='.$task_id);
	    		echo $res;
	    		die();
	    		break;	
	    }
		
	    $comment_arr = $comment_obj->get_comment_list($task_id, $url, $page); 
		$comment_data = $comment_arr['data'];
		$comment_page = $comment_arr['pages'];
		$reply_arr = $comment_obj->get_reply_info($task_id,true);
		break;
	case "mark":
		$mark_count = $task_obj->get_mark_count();//评价统计
		$mark_count_ext = $task_obj->get_mark_count_ext();//来自评价统计
		intval ( $page ) and $param ['page'] = intval ( $page ) or $param ['page']='1';
		intval ( $page_size ) and $param ['page_size'] = intval ( $page_size ) or $param['page_size']='10';
		$param['url'] = $basic_url."&view=mark&page_size=".$param ['page_size']."&page=".$param ['page'];
		$param ['anchor'] = '';
		$w['model_code'] = $model_code;//互评模型
		$w['origin_id']   = $task_id;//互评源 task_id
		$w['mark_status'] = $st;//评价状态
		//$ut=='my' and $w['uid'] = $uid;//我的评价
		$w['mark_type'] = $ut;//来自的评论
		$mark_arr = keke_user_mark_class::get_mark_info($w,$param,' mark_id desc ',"mark_status>0");
		$mark_info = $mark_arr['mark_info'];
		$pages     = $mark_arr['pages'];
		break;
	default :
		$task_file = $task_obj->get_task_file (); //任务附件
}

// 金额描述
$cash_desc = $task_info["task_cash_coverage"]? $cove_arr[$task_info["task_cash_coverage"]]["cove_desc"]: "￥".$task_info["task_cash"];


//劳务信息
/*
$where = " 1 = 1 ";
if($task_info['task_type']==2){
	$where.=" and task_type=2";
	$where.=" and task_id = '$task_id'";	
}
$task_infolist = db_factory::get_one("select * from ".TABLEPRE."witkey_task where $where");
$user_info = kekezu::get_user_info (11);//获取用户信息
$buyer_level=unserialize($user_info['buyer_level']);
$seller_level=unserialize($user_info['seller_level']);//威客等级
$user_auth=keke_auth_fac_class::get_submit_auth_record($task_infolist['uid'],1);*/
//var_dump($seller_level);exit;
if($task_info['task_type']==2){
	$indus_p_arr = kekezu::get_table_data ( '*', "witkey_industry", "indus_type=2 and indus_pid = 0 ", "listorder asc ", '', '', 'indus_id', NULL );
	$indus_c_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=2 and indus_pid >0', 'listorder', '', '', 'indus_id', NULL );
	$indus_arr = kekezu::get_table_data ( '*', 'witkey_industry', 'indus_type=2', 'listorder', '', '', 'indus_id', NULL );
		
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/service_info" );
}
elseif($task_info['task_type']==3){
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/hire_info" );
}
else{
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );
}
