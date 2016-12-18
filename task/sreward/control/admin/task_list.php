<?php
/**
 * 后台单人悬赏任务管理列表
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 170);
//任务配置
$task_config = unserialize ( $model_info ['config'] );

$model_list = $kekezu->_model_list;
//任务状态
$status_arr = sreward_task_class::get_task_status ();

$table_obj = keke_table_class::get_instance ( 'witkey_task' );

$page and $page = intval ( $page ) or $page = 1;
$page_size and $page_size = intval ( $page_size ) or $page_size = 10;

$wh = " model_id={$model_info['model_id']} ";

$url_str = "index.php?do=model&model_id=1&view=list&page=$page&page_size=$page_size";
$condit  = keke_task_config::condit_format($wh,$url_str,$ord);
$url_str = $condit['url'];
$wh      = $condit['w'];


//查询
$table_arr = $table_obj->get_grid ( $wh, $url_str, $page, $page_size, null );
$task_arr = $table_arr ['data'];
$pages = $table_arr ['pages'];
if ($task_id) {
	$task_audit_arr = get_task_info ( $task_id );
	$start_time = date ( "Y-m-d H:i:s", $task_audit_arr ['start_time'] );
	$end_time = date ( "Y-m-d H:i:s", $task_audit_arr ['end_time'] );
	$url = "<a href =\"$_K[siteurl]/index.php?do=task&task_id=$task_audit_arr[task_id]\" target=\"_blank\" >" . $task_audit_arr [task_title] . "</a>";

}
switch ($ac) {
	case "del" : //删除
		$task_title = db_factory::get_count ( sprintf ( "select task_title from %switkey_task where task_id='%d' ", TABLEPRE, $task_id ) );
		kekezu::admin_system_log ( $_lang ['task_del'] . "：{$task_title}(" . $_lang ['single_reward'] . ")" );
		$res = $table_obj->del ( 'task_id', $task_id, $url_str );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['delete_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['delete_fail'], "warning" );
		break;
	case "pass" : //通过审核
		

		$res = keke_task_config::task_audit_pass ( $task_id );
		$v_arr = array ("用户名" => "{$task_audit_arr['username']}", "任务链接" => $url, "开始时间" => $start_time, "结束时间" => $end_time, "任务编号" => "#" . $task_id );
		keke_shop_class::notify_user ( $task_audit_arr ['uid'], $task_audit_arr ['username'], 'task_auth_success', $task_audit_arr ['task_title'], $v_arr );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['examine_successfully'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['nopass'], "warning" );
		break;
	case "nopass" : //不通过审核
		$res = keke_task_config::task_audit_nopass ( $task_id );
		$v_arr = array ("用户名" => "{$task_audit_arr['username']}", $_lang ['task_title'] => $url, "网站名称" => "$kekezu->_sys_config['website_name']" );
		keke_shop_class::notify_user ( $task_audit_arr ['uid'], $task_audit_arr ['username'], 'task_auth_fail', $task_audit_arr ['task_title'], $v_arr );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['operate_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['operate_fail'], "warning" );
		break;
	//	case "freeze" : //冻结任务
	//		if($sbt_edit){
	//			$res =keke_task_config::task_freeze ( $task_id );
	//			$v_arr = array($_lang['username']=>"{$task_audit_arr['username']}",$_lang['task_title']=>$url);
	//			keke_shop_class::notify_user($task_audit_arr['uid'], $task_audit_arr['username'], 'task_freeze', $task_audit_arr['task_title'],$v_arr);
	//	
	//			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['task_frooze_successfully'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['task_frooze_fail'],"warning");
	//		}
	//		else{
	//			require keke_tpl_class::template ( 'control/admin/tpl/admin_task_freeze' );
	//			die();
	//		}
	//		break;
	case "unfreeze" : //任务解冻
		$res = keke_task_config::task_unfreeze ( $task_id );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_unfrooze_successfully'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_unfrooze_fail'], "warning" );
		break;
	case "recommend" : //任务推荐
		$res = keke_task_config::task_recommend ( $task_id );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_recommend_successfully'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_recommend_fail'], "warning" );
		break;
	case "unrecommend" : //取消任务推荐
		$res = keke_task_config::task_unrecommend ( $task_id );
		$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_unrecommend_successfully'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['task_unrecommend_fail'], "warning" );
		break;
}

//批量删除
if ($sbt_action == $_lang ['mulit_delete'] && ! empty ( $ckb )) {
	keke_task_config::task_del ( $ckb ) and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_delete_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_delete_fail'], "warning" );
}
//批量审核
if ($sbt_action == $_lang ['mulit_pass'] && ! empty ( $ckb )) {
	keke_task_config::task_audit_pass ( $ckb ) and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_pass_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_pass_fail'], "warning" );
}
//批量冻结
//if ($sbt_action==$_lang['mulit_freeze']&&!empty($ckb)) {
//	keke_task_config::task_freeze($ckb) and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_freeze_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['mulit_freeze_fail'],"warning");
//}
//批量解冻
if ($sbt_action == $_lang ['mulit_unfreeze'] && ! empty ( $ckb )) {
	keke_task_config::task_unfreeze ( $ckb ) and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_unfreeze_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['mulit_unfreeze_fail'], "warning" );
}
function get_task_info($task_id) {
	$task_obj = new Keke_witkey_task_class ();
	$task_obj->setWhere ( "task_id = $task_id" );
	$task_info = $task_obj->query_keke_witkey_task ();
	$task_info = $task_info ['0'];
	return $task_info;

}
require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_' . $view );