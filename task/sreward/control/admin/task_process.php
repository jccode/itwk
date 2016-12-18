<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-01 11:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 170);
$process_obj=sreward_report_class::get_instance($report_id,$report_info,$obj_info,$user_info,$to_userinfo);//实例化处理对象
if($op_result){
	switch ($type){
		case "rights"://维权
			$res=$process_obj->process_rights($op_result,$type);
			break;
		case "report"://举报
			$res=$process_obj->process_report($op_result,$type);
			break;
		case "complaint"://投诉
			break;
	}
}else{
	$gz_info  =$process_obj->user_role('gz');//雇主信息
	$wk_info  =$process_obj->user_role('wk');//威客信息
	$credit_info=$process_obj->_credit_info;//扣除信誉、能力信息
	$process_can=$process_obj->_process_can;//可以进行的处理动作
	$is_trust   = intval($obj_info['is_trust']);//担保模式
}
require keke_tpl_class::template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_' . $view );