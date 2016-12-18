<?php
/**
 * 后台单人悬赏任务管理列表
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 174);
//任务配置
$task_config = unserialize ( $model_info ['config'] );

$model_list = $kekezu->_model_list;
//任务状态
$status_arr = preward_task_class::get_task_status ();

$table_obj = keke_table_class::get_instance ( 'witkey_task' );


$page and $page=intval ( $page ) or $page = 1;
$page_size and $page_size=intval($page_size) or $page_size =10;

$wh = "model_id=3";

$url_str = "index.php?do=model&model_id=3&view=list&page=$page&page_size=$page_size";
$condit  = keke_task_config::condit_format($wh,$url_str,$ord);
$url_str = $condit['url'];
$wh      = $condit['w'];
//查询
$table_arr = $table_obj->get_grid ( $wh, $url_str, $page, $page_size, null);
$task_arr = $table_arr ['data'];
$pages = $table_arr ['pages'];
if($task_id){ 
	$task_audit_arr = get_task_info($task_id);
	$start_time = date("Y-m-d H:i:s",$task_audit_arr['start_time']);
	$end_time = date("Y-m-d H:i:s",$task_audit_arr['end_time']);
	$url = "<a href =\"{$_K['siteurl']}/index.php?do=task&task_id={$task_audit_arr['task_id']}\" target=\"_blank\" >" . $task_audit_arr['task_title']. "</a>";

}
	switch ($ac) {
		case "del" : //删除
			$task_title = db_factory::get_count(sprintf("select task_title from %switkey_task where task_id='%d' ",TABLEPRE,$task_id));
			kekezu::admin_system_log($_lang['delete_task'].":{$task_title}(".$_lang['piece_reward'].")");
			$res = $table_obj->del ( 'task_id', $task_id, $url_str );
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['delete_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['delete_fail'],"warning");
			break;
		case "pass" : //通过审核
			$res =keke_task_config::task_audit_pass ( $task_id );
			//$v_arr = array($_lang['username']=>$task_audit_arr['username'],$_lang['task_link']=>$url,$_lang['start_time']=>$start_time,$_lang['end_time']=>$end_time,$_lang['task_id']=>"#".$task_id); 
			//keke_shop_class::notify_user($task_audit_arr['uid'], $task_audit_arr['username'], 'task_auth_success', $task_audit_arr['task_title'],$v_arr);
			
			kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['audit_success'],'success');
			break;
		case "nopass" : //审核失败
			$res =keke_task_config::task_audit_nopass ( $task_id );
			$v_arr = array("用户名"=>$task_audit_arr['username'],"任务标题"=>$url,"网站名称"=>$kekezu->_sys_config['website_name']); 
			//keke_shop_class::notify_user($task_audit_arr['uid'], $task_audit_arr['username'], 'task_auth_fail', $task_audit_arr['task_title'],$v_arr);
			
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['operate_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['operate_fail'],"warning");
			break;
//		case "freeze" : //冻结任务
//			$res =keke_task_config::task_freeze ( $task_id );
//			$v_arr = array($_lang['username']=>$task_audit_arr['username'],$_lang['task_title']=>$url); 
//			keke_shop_class::notify_user($task_audit_arr['uid'], $task_audit_arr['username'], 'task_freeze', $task_audit_arr['task_title'],$v_arr);
//		
//			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['freeze_task_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['freeze_task_fail'],"warning");
//			break;
		case "unfreeze" : //任务解冻
			$res =keke_task_config::task_unfreeze ( $task_id );
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['unfreeze_task_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['unfreeze_task_fail'],"warning");
			break;
		case "recommend"://任务推荐
			$res =keke_task_config::task_recommend($task_id);
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['task_recommend_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['task_recommend_fail'],"warning");
			break;
		case "unrecommend"://取消任务推荐
			$res = keke_task_config::task_unrecommend($task_id);
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['cancel_recommend_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['cancel_recommend_fail'],"warning");
			break;
		case 'delay'://延期
			$day = intval($day);
			switch($t){
				case 'pass':
					if($task_audit_arr['task_status']<4){
						$res =db_factory::execute('update '.TABLEPRE.'witkey_task_delay set delay_status=1 where task_id='.$task_id);
						if($res){
							if($task_audit_arr['task_status']==3){
								db_factory::updatetable(TABLEPRE.'witkey_task',array(
									'task_status'=>2,//状态修正
									'sub_time'=>time()+$day*24*3600,
									'end_time'=>time()+($task_audit_arr['end_time']-$task_audit_arr['start_time'])+$day*24*3600,
									'exec_time'=>time()+$day*24*3600,
									'is_delay'=>++$task_audit_arr['is_delay']),
									array('task_id'=>$task_id));
							}else{
								db_factory::updatetable(TABLEPRE.'witkey_task',array(
									'sub_time'=>$task_audit_arr['sub_time']+$day*24*3600,
									'end_time'=>$task_audit_arr['end_time']+$day*24*3600,
									'exec_time'=>$task_audit_arr['exec_time']+$day*24*3600,
									'is_delay'=>++$task_audit_arr['is_delay']),
									array('task_id'=>$task_id));
							}
							
							kekezu::notify_user('任务延期审核通过','恭喜,您关于任务'.$url.'的延期'.$day.'天申请已通过审核',$task_audit_arr['uid'],$task_audit_arr['username']);
							kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,'延期审核成功','success');
						}else{
							kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,'延期审核失败','warning');
						}
					}else{
						db_factory::execute('update '.TABLEPRE.'witkey_task_delay set delay_status=3 where task_id='.$task_id);
						kekezu::notify_user('任务延期审核失败','对不起,您关于任务'.$url.'的延期'.$day.'天，由于任务当前状态无法延期,申请没有通过审核',$task_audit_arr['uid'],$task_audit_arr['username']);
						kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,'延期审核成功,由于任务当前状态无法延期,延期申请被终止','success');
					}
					break;
				case 'nopass':
					$res = db_factory::execute('update '.TABLEPRE.'witkey_task_delay set delay_status=3 where task_id='.$task_id);
					if($res){
						kekezu::notify_user('任务延期审核失败','对不起,您关于任务'.$url.'的延期'.$day.'天申请没有通过审核',$task_audit_arr['uid'],$task_audit_arr['username']);
						kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,'延期审核成功','success');
					}else{
						kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,'延期审核失败','warning');
					}
					break;
			}
			break;
	}


//批量删除
if ($sbt_action==$_lang['mulit_delete']&&!empty($ckb)) {	
	keke_task_config::task_del($ckb) and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_delete_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['tips_about_delete_failure'],"warning");
}
//批量审核
if ($sbt_action==$_lang['mulit_pass']&&!empty($ckb)) {
	keke_task_config::task_audit_pass($ckb) and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_pass_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_pass_fail'],"warning");
}
////批量冻结
//if ($sbt_action==$_lang['mulit_freeze']&&!empty($ckb)) {
//	keke_task_config::task_freeze($ckb) and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_freeze_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_freeze_fail'],"warning");
//}
//批量解冻
if ($sbt_action==$_lang['mulit_unfreeze']&&!empty($ckb)) {
	keke_task_config::task_unfreeze($ckb) and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_unfreeze_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_unfreeze_fail'],"warning");
}
function get_task_info($task_id){
	$task_obj = new Keke_witkey_task_class();
	$task_obj->setWhere("task_id = $task_id");
	$task_info = $task_obj->query_keke_witkey_task();
	$task_info = $task_info ['0'];
	return $task_info;

}
/**
 * 延
 */
function is_delay($task_id){
	return db_factory::get_count('select delay_day from '.TABLEPRE.'witkey_task_delay where delay_status=2 and task_id='.$task_id);
}

require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_' . $view );