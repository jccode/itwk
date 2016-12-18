<?php
/**
 * @copyright keke-tech
 * @author wrh
 * @version v 2.0
 * 2012-06-18 15:40:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 170);
$work_id and $w ['work_id'] = $work_id; //稿件编号
$st and $w ['work_status'] = $st; //稿件状态

//取表数据
$task_info = db_factory::get_one ( sprintf ( " select * from %switkey_task where task_id='%d'", TABLEPRE, $task_id ) );

$task_obj = sreward_task_class::get_instance ( $task_info );
$search_condit = $task_obj->get_search_condit ();
$task_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_task where model_id = '$model_id' and task_id = '$task_id'" );
$work_status = $task_obj->get_work_status (); //获取稿件状态数组
$task_obj = new Keke_witkey_task_class ();
$task_config = kekezu::get_task_config($model_id);//读出任务配置   需要读取中奖者分配比例

if ($sbt_act) { 
//	$work_memobj = $_POST ['work_status'];
//	foreach ( $work_memobj as $v ) {
//		$task_work_obj->setWhere ( "work_status ='$v' " );
//		$task_work_obj->setWhere ( "task_id='$task_id'" );
//		$work_obj = $task_work_obj->query_keke_witkey_task_work ();
//		foreach ( $work_obj as $vk ) {
//			$join_member = $vk ['uid'];
//		}
//	}

	$lottery_allow_member = array(); //被邀请参与摇奖的用户列表
	//读取合格的稿件列表
	if($_POST ['work_status']){
		$member_list = db_factory::query("select a.uid,a.username,b.email,b.mobile from ".TABLEPRE."witkey_task_work a left join ".TABLEPRE."witkey_space b on a.uid=b.uid where task_id = '$task_id' and work_status in (".implode(',',$_POST ['work_status']).")");
		if($member_list)
		foreach ($member_list as $m){
			if($lottery_allow_member[$m['uid']]){continue;}
			//因为1个用户可能发送多个稿件，这里用uid做主键以清除重复列
			$lottery_allow_member[$m['uid']] = $m['uid'];
			
			//编辑摇奖配置是不会发送用户邀请的
			if(!$task_info['lottery_config']){
				$msg_obj = new keke_msg_class();
				$msg_obj->config_init('lottery_invite');
				$msg_obj->setUid($m['uid']);
				$msg_obj->setUsername($m['username']);
				$msg_obj->setValue("任务名称",$task_info['task_title']);
				$msg_obj->setValue('任务编号',$task_id);
				$msg_obj->setValue("摇奖链接", "<a href=".$_K['siteurl']."/index.php?do=lottery&view=info&task_id=".$task_id.">".$task_info['task_title']."</a>");
				$m['email'] and $msg_obj->setEmail($m['email']);
				$m['mobile'] and $msg_obj->setMobile($m['mobile']);
				$msg_obj->send();
			}
		}
	}
	
	
	$join_count = count($lottery_allow_member);
	$join_count<=$mainno and kekezu::show_msg("可参与摇奖的人数不足");//参与人数验证 
	
	$join_member = implode(',',$lottery_allow_member);
	
	$lottery_arr = array ();
	$lottery_arr ['real_cash'] = $realcash;
	$lottery_arr ['main_count'] = $mainno;
	$lottery_arr ['join_member'] = $join_member;
	$lottery_arr ['lottery_time'] = time();
	$lottery_arr ['lottery_reason'] = $lottery_reason;
	$task_obj->setTask_id($task_id);
	$task_obj->setLottery_config ( serialize ( $lottery_arr ) );
    $res =$task_obj->edit_keke_witkey_task ();
    
    //冻结状态解冻
     
    //任务状态改到摇奖中
    $lottery_end_time = time()+24*3600*$task_config['lottery_period'];
    $txt_edit_endtime and $lottery_end_time = strtotime($txt_edit_endtime);
    db_factory::execute("update ".TABLEPRE."witkey_task set task_status =4,exec_time='$lottery_end_time' where task_id='$task_id' ");
    
    if($res==0 || $res==1){
    kekezu::admin_system_log("配置任务#{$task_id}的摇奖规则");
	kekezu::admin_show_msg ( '摇奖配置成功', "index.php?do=model&model_id=$model_id&view=list", 3, '', 'success' );
    }
}
if ($task_info['lottery_config']){
	
 $lottery_info=unserialize($task_info['lottery_config'] ); 
 
}



require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_lottery' );





