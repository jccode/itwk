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

$task_obj = sreward_task_class::get_instance ( $task_info );

if($task_info['exec_time']&&$task_info['exec_time']<time()&&$task_info['task_union']!=2){
	$time_obj = new sreward_time_class();
	if($time_obj->exec_task($task_id,$task_info))
	$task_info = header("Location:$basic_url");
}

//招标任务的价格区间
//$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");

//访问状态判断
if(in_array($task_info['task_status'],array(-1,0,1,10))&&$uid!=$task_info['uid']&&!in_array($user_info['group_id'],$task_rule_group_id)){
	$status_arr = $task_obj->get_task_status();
	kekezu::show_msg("拒绝访问","index.php","该任务状态为{$status_arr[$task_info['task_status']]},只有发布者和客服可以访问",3);
}elseif(!$uid&&strpos(' '.$task_info['pay_item'],'seohide')){
	kekezu::show_msg("拒绝访问","index.php",3,"该任务在您登录后才可以访问");
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
$time_obj =new  sreward_time_class();

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
	case "set_mustchoose"://保证选稿
		if($task_info['uid']==$uid){
			db_factory::execute("update ".TABLEPRE."witkey_task set must_choosework = 1 where task_id = '$task_id'");
		}
		die();
		break;
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
			if($t=='load'){//异步加载本任务模型的编辑处理页面代码
				$task_day = ceil(($task_info['sub_time']-time())/(24*3600));
				require keke_tpl_class::template('task/sreward/tpl/'.$_K['template'].'/task_edit');die();
			}else{//调用公共的任务编辑页面，单，多，招公用
				$attach_list = $task_obj->get_task_file(0);
				$flie_types = explode ( '|', $basic_config ['file_type'] );
				require keke_tpl_class::template('task/task_edit');die();
			}
		}
		die();
		break;
	case "work_confirm":
		$task_obj->work_confrim($work_id,'','json');
		die();
		
		break;
	case "taskdelay" : //延期
		$title = $_lang['task_delay'];
		if($sbt_edit){
			$task_obj->set_task_delay($delay_day, $delay_cash);
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
	case "work_hand" : //交稿
		$title = $_lang['hand_work'];

		if(intval($user_info['auth_mobile'])!=1||intval($user_info['auth_email'])!=1){
			$rurl = 'index.php?do=user&view=setting&op=auth';
			intval($user_info['auth_mobile'])!=1 and $rurl.='&auth_code=mobile' or $rurl.='&auth_code=email';
			kekezu::show_msg ( "操作提示", $rurl, '2', '友情提示:参加任务请先通过手机和邮箱认证！', 'alert_error' ) ;
		}
		
		$task_obj->valid_reg_time($_SERVER['HTTP_REFERER']);
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
		$task_obj->work_choose ( $work_id, 11,'','json');
		break;
	case "work_notice" : //选入围
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		$task_obj->work_choose ( $work_id, 13,'','json');
		break;
	case "work_out" : //淘汰稿件
		//淘汰暂定只改状态  不通知
		$task_obj->set_work_status($work_id, 15);
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		kekezu::keke_show_msg ( $url,'稿件淘汰成功', "", 'json' );
		die();
		break;
	case "work_remark" : //备选稿件
		//备选稿件只设状态    只是给雇主看的
		$task_obj->set_work_status($work_id, 14);
		$kekezu->_cache_obj->del('taskinfo_search_condit_cache_'.$task_id);
		kekezu::keke_show_msg ( $url, '稿件设置成功', 1, 'json' );
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
//排序规则
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

if($view=='work'){
	$work_info = db_factory::get_one(" select a.*,b.auth_bank,b.auth_realname,b.auth_email,b.auth_mobile,b.isvip,b.seller_credit,b.seller_good_num,b.residency,b.seller_total_num,b.w_level from " . TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_space b on a.uid=b.uid where a.work_id='$work_id'");
	$work_pics = keke_glob_class::work_pics($work_info['work_file']);
	if($work_info['mark_status']){
		$mark_info = db_factory::get_one("select * from ".TABLEPRE."witkey_mark where origin_id = '$task_id' and by_uid = '{$task_info['uid']}' and mark_status>0",180);
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
}
else{
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

	// 对特殊任务的处理
	require_once S_ROOT . '/task/mreward/control/task_info_ex.php';
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
};

require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );