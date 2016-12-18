<?php
/**
 * 悬赏任务编辑 
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 170);
intval ( $task_id ) or kekezu::admin_show_msg ( $_lang['param_error'], 'index.php?do=model&model_id=' . $model_id . '&view=list',3,'','warning' );
$task_info = db_factory::get_one ( sprintf ( " select * from %switkey_task where task_id='%d'", TABLEPRE, $task_id ) );

//任务推荐操作
if($sbt_recmmend){
	$res = db_factory::execute(sprintf("update %switkey_task set is_top=1 where task_id='%d' ",TABLEPRE,$task_id));
	$res and kekezu::admin_show_msg ( $_lang['task_operate_successfully'], "index.php?do=model&model_id=$model_id&view=list",3,'','success' ) or kekezu::admin_show_msg ( $_lang['task_operate_fail'], "index.php?do=model&model_id=$model_id&view=list",3,'','warning');
}
if ($sbt_edit) {//编辑
	$task_obj = new Keke_witkey_task_class ();
	$task_obj->setWhere(" task_id ='$task_id'");
	if($recommend){
		//$task_obj->setIs_recommend(1);
		$task_obj->setIs_top(1);
	}else{
		//$task_obj->setIs_recommend(0);
		$task_obj->setIs_top(0);
	}
	$task_obj->setTask_title (kekezu::escape($task_title) );
	$task_obj->setIndus_id ( $slt_indus_id );
	$task_obj->setTask_cash($task_cash);
	if($task_info['real_cash']!=$task_info['task_cash']){//小于任务金额。说明受到了开票影响
		$iv_taxes = $task_info['task_cash']-$task_info['real_cash'];
		$task_obj->setReal_cash($task_cash-$iv_taxes);//可用佣金
	}else{
		$task_obj->setReal_cash($task_cash*(1-$task_info['profit_rate']/100));//可用佣金
	}
	if($rdo_must_choosework){
		$task_obj->setMust_choosework(1);
	}else{
		$task_obj->setMust_choosework(0);
	}
	$task_obj->setTask_desc ( $task_desc );
	$task_obj->setWorklist_viewtype($worklist_viewtype);
	$task_obj->setExt_desc($ext_desc);//描述补充和显示状态
	$task_obj->setExt_status($ext_status);

	// 任务联系方式
	$task_obj->setcontact( serialize($contact) );

	//牵涉到时间触发时间的问题 设标志变量以确保触发时间不会被重置
	$exec_valid_flag = false;
	
	
	//时间更改操作
	switch ($task_info['task_status']){
		case 2:
			if(strtotime($txt_sub_time)!=$task_info['sub_time']){
				$task_obj->setSub_time(strtotime($txt_sub_time));
				$task_obj->setExec_time(strtotime($txt_sub_time));
				$task_info['exec_time'] and $exec_valid_flag = true;
			}
			strtotime($txt_end_time)!=$task_info['end_time'] and $task_obj->setEnd_time(strtotime($txt_end_time));
			break;
		case 3:
			if(strtotime($txt_end_time)!=$task_info['end_time']){
				$task_obj->setEnd_time(strtotime($txt_end_time));
				$task_obj->setExec_time(strtotime($txt_end_time));
				$task_info['exec_time'] and $exec_valid_flag = true;
			}
			if(strtotime($txt_sub_time)!=$task_info['sub_time']&&strtotime($txt_sub_time)>time()&&strtotime($txt_sub_time)<strtotime($txt_end_time)){
				$task_obj->setSub_time(strtotime($txt_sub_time));
				$task_obj->setExec_time(strtotime($txt_sub_time));
				$task_obj->setTask_status(2);
				$task_info['exec_time'] and $exec_valid_flag = true;
			}
			break;
		case 4:
			if(strtotime($txt_exec_time)!=$task_info['exec_time']){
				$task_obj->setExec_time(strtotime($txt_exec_time));
				$task_info['exec_time'] and $exec_valid_flag = true;
			}
			break;
		case 5:
			if(strtotime($txt_sp_end_time)!=$task_info['sp_end_time']){
				$task_obj->setSp_end_time(strtotime($txt_sp_end_time));
				$task_obj->setExec_time(strtotime($txt_sp_end_time));
				$task_info['exec_time'] and $exec_valid_flag = true;
			}
			break;
	}
	
	$exec_valid_flag and $task_obj->setWhere("task_id='$task_id' and exec_time='{$task_info['exec_time']}'");//exec为0时表示在编辑生效之前已经被自动触发了
	
	if($_FILES['fle_task_pic']['name']){
		$task_pic = keke_file_class::upload_file("fle_task_pic");
	}else{
		$task_pic = $task_pic_path;
	}
	$task_obj->setTask_pic($task_pic);
	kekezu::admin_system_log ( $_lang['edit_task'].":{$task_title}" );	//生成日志
	$res=$task_obj->edit_keke_witkey_task ();
	if($res){
		kekezu::notify_user ( $_lang['system_message'], $_lang['admin'] . $myinfo_arr ['username'] . $_lang['edit_your_task'].'<b><a href="index.php?do=task&task_id=' . $task_info ['task_id'] . '">' . $task_info ['task_title'] . '</a></b>(id' . $task_id . ') 。', $task_info ['uid'], $task_info ['username'] );
	}
} elseif($sbt_act){
	switch ($sbt_act){
		case "freeze"://冻结
			if($is_submit){
				$res=keke_task_config::task_freeze ( $task_id , $reason_content);
				//kekezu::admin_show_msg("冻结成功","index.php?do=model&model_id={$v['model_id']}&view=edit&task_id={$v['task_id']}");
				require keke_tpl_class::template ( 'control/admin/tpl/admin_header');
				echo '<script type="text/javascript">
					 	$(document).ready(function(){
							art.dialog.open.api.close();
							//window.parent.location.href = \'index.php?do=model&model_id='.$task_info['model_id'].'&view=edit&task_id='.$task_info['task_id'].'\';
						});
					</script>';
				
				require keke_tpl_class::template ( 'control/admin/tpl/admin_footer');
				die();
			}
			else{
				$status_arr = sreward_task_class::get_task_status ();
				require keke_tpl_class::template ( 'control/admin/tpl/admin_task_freeze' );
			}
			die();
			break;
		case "tochoosestatus":
			$e_time = time()+24*3600;
			db_factory::execute("update ".TABLEPRE."witkey_task set task_status = 3 ,end_time ='$e_time',exec_time='$e_time' where task_id = $task_id ");
			kekezu::show_msg("操作成功","index.php?do=model&model_id={$model_id}&view=edit&task_id={$task_id}");
			break;
		case "unfreeze"://解冻
			$res=keke_task_config::task_unfreeze ( $task_id );
			break;
		case "returncash"://关闭并退还佣金
			//退还佣金
			keke_finance_class::cash_in($task_info['uid'],$task_info['task_cash'],0, 'task_fail',null,'task',$task_id);
			//消息通知雇主
			kekezu::notify_user("任务#{$task_id}退款", '您的任务<a href="index.php?do=task&task_id='.$task_id.'" target="_blank">'.$task_info['task_title'].'</a>被退款关闭，任务金额'.$task_info['task_cash'].'已退还到您的个人账户上。', $task_info['uid'],$task_info['username']);
			$temp_str = "退款并";
			//不写break 以执行关闭动作
		case "close"://关闭  
			//任务状态改为'失败'
			db_factory::execute("update ".TABLEPRE."witkey_task set task_status = 9 where task_id = '$task_id'");
			kekezu::admin_system_log ( $temp_str."关闭了任务了".":{$task_info['task_title']}");
			kekezu::empty_cache('data');
			kekezu::show_msg("操作成功","index.php?do=model&model_id={$model_id}&view=edit&task_id={$task_id}");
			break;
		case "pass"://通过
			$res=keke_task_config::task_audit_pass ( array($task_id));
			break;
		case "nopass"://不通过
			$res=keke_task_config::task_audit_nopass ( $task_id );
			break;
	}
	
}else {
	$process_arr = keke_task_config::can_operate ( $task_info ['task_status'] );
	$file_list = db_factory::query ( sprintf ( " select * from %switkey_file where task_id='%d'", TABLEPRE, $task_id ) );
	$status_arr = sreward_task_class::get_task_status ();
	
	$payitem_list=keke_payitem_class::get_payitem_config('employer');
	/*行业*/
	$indus_arr = $kekezu->_indus_arr;
	$temp_arr = array ();
	$indus_option_arr = $indus_arr;
	kekezu::get_tree ( $indus_option_arr, $temp_arr, "option", $task_info ['indus_id'] );
	$indus_option_arr = $temp_arr;
}
if($res){
	kekezu::admin_show_msg ( $_lang['task_operate_successfully'], "index.php?do=model&model_id=$model_id&view=list",3,'','success' );
}

//冻结状态下取冻结信息
$task_info['task_status'] == 7 and $frost_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task_frost where task_id = '$task_id' order by frost_id desc");
//摇奖状态下读取摇奖配置
$task_info['task_status'] == 4 and $task_info['lottery_config'] and $lottery_config = unserialize($task_info['lottery_config']);

require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_edit' );