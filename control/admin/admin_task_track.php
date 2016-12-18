<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-07 11:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

//任务信息
$task_obj = new Keke_witkey_task_class();
$task_obj->setWhere("task_id = '$task_id'");
$task_info = $task_obj->query_keke_witkey_task();
$task_info = $task_info[0];
//客服列表
$custom_list = kekezu::get_table_data('*','witkey_space',"group_id = 3",'','','','uid',null);

//状态获取
$track_status = keke_glob_class::get_track_status();

if($btn_sbt){
	$track_obj = new Keke_witkey_task_track_class();
	$track_obj->setTask_id($task_id);
	$track_obj->setTask_title($task_info['task_title']);
	$track_obj->setUid($task_info['uid']);
	$track_obj->setUsername($task_info['username']);
	$track_obj->setT_content($tar_t_content);
	$track_obj->setT_uid($slt_t_uid);
	$track_obj->setT_username($custom_list[$slt_t_uid]['username']);
	$track_obj->setT_status($slt_t_status);
	$track_obj->setRemark('');
	$track_obj->setDateline(time());

	$res = $track_obj->create_keke_witkey_task_track();
	
	if($res){
		kekezu::admin_system_log("任务 #{$task_id}:创建跟踪记录 ");
		kekezu::admin_show_msg('跟踪记录创建成功',"index.php?do=$do&view=$view&task_id=$task_id",3,'','success');
	}
	else{
		kekezu::admin_show_msg('跟踪记录创建失败',"index.php?do=$do&view=$view&task_id=$task_id",3,'','error');
	}
}

//获取最近1次的跟踪记录
$track_obj = new Keke_witkey_task_track_class();
$track_obj->setWhere("task_id = '$task_id' order by t_id desc");
$track_list = $track_obj->query_keke_witkey_task_track();
$track_info = $track_list[0];


require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_task_' . $view );
