<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-07 11:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 170);
//任务信息
$task_obj = new Keke_witkey_task_class();
$task_obj->setWhere("task_id = '$task_id'");
$task_info = $task_obj->query_keke_witkey_task();
$task_info = $task_info[0];
//客服列表
$custom_list = kekezu::get_table_data('*','witkey_space',"group_id = 3",'','','','uid',null);

//状态获取
$task_status = sreward_task_class::get_task_status ();
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
	
	//扩展信息
	$ext_arr = array();
	$ext_arr['start_time'] = date('Y-m-d H:i:s',$task_info['start_time']);
	$ext_arr['taskmodel'] = "单人悬赏";
	($task_info['task_status']>1&&$task_info['task_status']<9) and $ext_arr['ispay'] = "是" or $ext_arr['ispay'] = "否";
	$ext_arr['task_cash'] = $task_info['task_cash'];
	$ext_arr['task_status'] = $task_status[$task_info['task_status']]; 
	$ext_arr['end_time'] = date('Y-m-d H:i:s',$task_info['end_time']);
	$task_cont = array();
	$task_info['contact'] and $task_cont = unserialize($task_info['contact']);
	$ext_arr['mobile'] = $task_cont['mobile'];
	$ext_arr['qq'] = $task_cont['qq'];
	$ext_arr['msn'] = $task_cont['msn'];
	$ext_arr['email'] = $task_cont['email'];
	$guinfo = kekezu::get_user_info($task_info['uid']);
	$ext_arr['phone'] = $guinfo['phone'];
	$bider_list = db_factory::query("select uid,username from ".TABLEPRE."witkey_task_work where work_status between 1 and 12 and task_id = '$task_id' group by uid ");
	$ext_arr['bider'] = array();
	foreach ($bider_list as $b){
		$ext_arr['bider'][$b['uid']] = $b['username'];
	}
	$ext_arr['bider'] and $ext_arr['bider'] = implode(',',$ext_arr['bider']);
	$task_info['Invoice_status'] and $ext_arr['invoice'] = "是" or $ext_arr['invoice'] = "否";
	$task_info['must_choosework'] and $ext_arr['must_choosework'] = "是" or $ext_arr['must_choosework'] = "否";
	
	$ext_arr and $track_obj->setExt(serialize($ext_arr));
	$res = $track_obj->create_keke_witkey_task_track();
	
	if($res){
		kekezu::admin_system_log("任务 #{$task_id}:创建跟踪记录 ");
		kekezu::admin_show_msg('跟踪记录创建成功',"index.php?do=$do&model_id=$model_id&view=$view&task_id=$task_id",3,'','success');
	}
	else{
		kekezu::admin_show_msg('跟踪记录创建失败',"index.php?do=$do&model_id=$model_id&view=$view&task_id=$task_id",3,'','error');
	}
}



//获取最近1次的跟踪记录
$track_obj = new Keke_witkey_task_track_class();
$track_obj->setWhere("task_id = '$task_id' order by t_id desc");
$track_list = $track_obj->query_keke_witkey_task_track();
$track_info = $track_list[0];





$task_status = sreward_task_class::get_task_status ();
$track_status = keke_glob_class::get_track_status();

require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_track' );
