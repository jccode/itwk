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

$task_obj = preward_task_class::get_instance ( $task_info );

if($task_info['exec_time']&&$task_info['exec_time']<time()){
	$time_obj = new preward_time_class();
	if($time_obj->exec_task($task_id,$task_info))
	$task_info = header("Location:$basic_url");
}

//招标任务的价格区间
//$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");

//访问状态判断
if(in_array($task_info['task_status'],array(-1,0,1,10))&&$uid!=$task_info['uid']&&$uid!=ADMIN_UID&&$userinfo['user_group']!=7){
	$status_arr = $task_obj->get_task_status();
	kekezu::show_msg("拒绝访问","index.php","该任务状态为{$status_arr[$task_info['task_status']]},只有发布者和客服可以访问");
}elseif(!$uid&&strpos(' '.$task_info['pay_item'],'seohide')){
	kekezu::show_msg("拒绝访问","index.php",3,"该任务在您登录后才可以访问");
}


$task_config =$task_obj->_task_config;
$model_id = $task_obj->_model_id;
$task_status = $task_obj->_task_status;
$indus_arr = $kekezu->_indus_c_arr; //子行业集
$indus_p_arr = $kekezu->_indus_p_arr; //父行业集
$status_arr = $task_obj->_task_status_arr; //任务状态数组

//任务时间描述
$time_desc = $task_obj->get_task_timedesc (); 
$stage_desc = $task_obj->get_task_stage_desc (); //任务阶段样式
$related_task = $task_obj->get_task_related ();//获取相关任务
$if_can_hand = intval($task_obj->check_work_if_standard('hand'));//是否还可以交稿
$max_work_num = $task_obj->get_work_count('max');//可交稿件总数
$delay_rule = $task_obj->_delay_rule;//延期规则
$delay_total = sizeof($delay_rule);//可延期次数
$delay_count=intval($task_info['is_delay']);//已延期次数
$process_can = $task_obj->process_can (); //用户操作权限
$process_desc = $task_obj->process_desc (); //用户操作权限中文描述
$task_obj->plus_view_num();//查看加一
$trust_mode=$task_obj->_trust_mode;//担保模式
$time_obj =new  preward_time_class();

//推广链接
$prom_url = $_K['siteurl']."/index.php?do=prom&p=task&task_id=$task_id&epi=$uid";
$prom_content = "#微博赚钱#来接活吧，这里有一个￥{$task_info['task_cash']}元的 {$task_info['task_title']}任务，就快结束了哦，还等什么？轻轻一点，轻松赚钱！[耶] (来自@IT帮手网)";

if ($sw){
}

$g_hide = false;
strpos($task_info['pay_item'],'emphide')!==false and $g_hide=true;//雇主是否因此稿件

//$time_obj->task_hand_end();
//$time_obj->task_choose_end();
//$time_obj->task_vote_end();
//$time_obj->task_notice_end();
//$time_obj->task_agreement_end();


$show_payitem = $task_obj->show_payitem();

$browing_history = $task_obj->browing_history($task_id,$task_info['task_cash']."元",$task_info['task_title']);
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
			$edit_arr = array('task_title'=>$task_title,'task_desc'=>$task_desc,'indus_pid'=>$indus_pid,'indus_id'=>$indus_id);
			$task_obj->edit_task($edit_arr,'','json');
		} else {//调用默认的任务编辑
			$attach_list = $task_obj->get_task_file(0);
			$flie_types = explode ( '|', $basic_config ['file_type'] );
			require keke_tpl_class::template('task/task_edit_default');
		}
		die();
		break;
	case "work_confirm":
		$task_obj->work_confrim($work_id,'','json');
		die();
		
		break;
	case "taskdelay" : //延期
		($uid!=$task_info['uid']||$task_obj->valid_task_status()>3) and kekezu::show_msg("错误",$_K['siteurl']."/index.php?do=task&task_id=$task_id",3,"状态错误或者您不雇主");
		$title = $_lang['task_delay'];
		if($sbt==1){
			$task_obj->task_delay();
		}else{
			$min_count = intval($task_config['min_delay_count']);//配置最新增稿件
			$max_day  = intval($task_config['max_delay']);//配置最大延期天数
			$this_min_count = intval($delay_rule[$delay_count]['defer_rate']);//本次最小新增稿件数
			$min_count>$this_min_count or $min_count = $this_min_count;//真正最少稿件数
			$single_cash  =  floatval($task_info['single_cash']);
			require keke_tpl_class::template("task/preward/tpl/default/task_delay");
		}
		die();
		break;
	case "work_hand" : //交稿
		$title = $_lang['hand_work'];
//		if(intval($user_info['auth_mobile'])!=1||intval($user_info['auth_email'])!=1){
//			$rurl = 'index.php?do=user&view=setting&op=auth';
//			intval($user_info['auth_mobile'])!=1 and $rurl.='&auth_code=mobile' or $rurl.='&auth_code=email';
//			kekezu::show_msg ( "操作提示", $rurl, '2', '只有通过手机认证和邮箱认证的用户才可以参与投稿！', 'alert_error' ) ;
//		}
		if($sbt_edit){
			$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
			$task_obj->work_hand ( $tar_content, $file_ids,$hidemode,$hidework,'json');
		}else{
			if($g_hide==false){
				$payitem  = keke_payitem_class::get_payitem_info ('witkey','sreward',true);//可购买服务
				$payitem  = $payitem['workhide'];//稿件隐藏
				$remain   = keke_payitem_class::payitem_exists($uid,'workhide');
				$dz_credit= keke_user_class::get_credit($uid);//论坛积分
				$isvip    = $user_info['isvip'];
			}
			require keke_tpl_class::template ( 'task/reward_work' );
		}
		die();
		break;
	case "work_choose" : //选稿
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->work_choose ( $work_id, 12,'','json');
		break;
	case "work_out" : //淘汰稿件
		//淘汰暂定只改状态  不通知
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->set_work_status($work_id, 15);
		kekezu::keke_show_msg ( $url,'稿件淘汰成功', "", 'json' );
		die();
		break;
	case "work_remark" : //备选稿件
		//备选稿件只设状态    只是给雇主看的
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->set_work_status($work_id, 14);
		kekezu::keke_show_msg ( $url, '稿件已设为被锁', "", 'json' );
		die();
		break;
//	case "work_vote" : //进行投票
//		$task_obj->set_task_vote($work_id,'','json');
//		break;
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
$ut or strlen($st) or $st = 12;
$w['work_status'] = $st;//稿件状态
$ut and $w['user_type']   = $ut;//用户类型  my自己
$order = max($order,1);//1 降序 asc
$param['page_size'] = 2;
if($view=='work'){
	$work_info = db_factory::get_one(" select a.*,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile,b.isvip,b.seller_credit,b.seller_good_num,b.residency,b.seller_total_num,b.w_level from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid where a.work_id='$work_id'");
	$work_pics = keke_glob_class::work_pics($work_info['work_file']);
	if($work_info['mark_status']){
		$mark_info = db_factory::get_one("select * from ".TABLEPRE."witkey_mark where origin_id = '$task_id' and by_uid = '{$task_info['uid']}' and mark_status>0",180);
	}
}
else{
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
	
	$work_arr = $task_obj->get_work_info ($w, " $ord ", $param ); //稿件信息
	$pages = $work_arr ['pages'];
	$work_info = $work_arr ['work_info'];
	$mark      = $work_arr['mark'];
	$agree_id  = intval($task_obj->_agree_id);
	
	//雇主已查看功能
	$task_obj->edit_work_view_status($work_info);
	
	if(in_array($task_info['task_status'],array(5,7,8))){
		$mark_list = kekezu::get_table_data("mark_status,obj_id,mark_content,mark_time","witkey_mark","origin_id = '$task_id' and by_uid = '{$task_info['uid']}' and mark_status>0 ",'','','','obj_id',180);//3分钟缓存
	}
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
//	    		$comment_arr = array("obj_id"=>$task_id,"origin_id"=>$task_id,"obj_type"=>"task",
//	    		"uid"=>$uid, "username"=>$username,"content"=>$content,"on_time"=>time());
//	    		$res = $comment_obj->save_comment($comment_arr,$task_id); 
	    		
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
	    		db_factory::execute(sprintf(" update %switkey_task set leave_num =leave_num-%d where task_id='%d'",TABLEPRE,$res,$task_id));
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
require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );