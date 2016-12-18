<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2012-06-29 11:57
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(82);
$report_info = keke_report_class::get_report_info ( $report_id );
$report_info or kekezu::admin_show_msg ( $_lang['parameters_error_not_exist'] . $action_arr [$type] [1] . $_lang['record'], "index.php?do=trans&view=$type",3,'','warning' );
$user_info = kekezu::get_user_info ( $report_info ['uid'] ); //举报方信息
$to_userinfo = kekezu::get_user_info ( $report_info ['to_uid'] ); //对方信息

$obj_info = keke_report_class::obj_info_init ( $report_info,$user_info);

if($sbt_op){
	$report_obj = new Keke_witkey_report_class();
	$report_obj->setWhere("report_id=".$report_id);
	$report_obj->setReport_status(2);
	$report_obj->setOp_uid($admin_info['uid']);
	$report_obj->setOp_username($admin_info['username']);
	$report_obj->setOp_time(time());
	$report_obj->setOp_result($op_result);
	$res = $report_obj->edit_keke_witkey_report();
	if($res){
		kekezu::admin_system_log('处理举报信息：#'.$report_id);
		kekezu::admin_show_msg('操作提示','index.php?do=trans&view=report',3,'处理举报信息成功！','success');
	}else{
		kekezu::admin_show_msg('操作提示','index.php?do=trans&view=report_info&report_id='.$report_id,3,'处理举报信息失败！','warning');
	}
}

require $template_obj->template ( 'control/admin/tpl/admin_trans_' . $view );