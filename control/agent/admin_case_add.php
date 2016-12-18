<?php
/**
 * 案例添加，有对象关联就关联对象，没有直接添加
 * @copyright keke-tech
 * @author S
 * @version kppw 2.0
 * 2011-12-14
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$case_obj = new Keke_witkey_case_class ();
$task_obj = new Keke_witkey_task_class ();
 
$case_id and $case_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_case where case_id ='$case_id'" );

$txt_task_id and $case_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_task where task_id = '$txt_task_id'" );

$url ="index.php?do=case&view=list" ;

//查询案例编号
if ($ac == 'ajax' && $id&&$obj) {
	case_obj_exists ( $id, $obj ) and kekezu::echojson ( $_lang['echojson_msg'],1 ) or kekezu::echojson ( $_lang['echojosn_erreor_msg'],0 );
}

//编辑、添加案例
if (isset ( $sbt_edit )) { //编辑
	if ($hdn_case_id) {
		$case_obj->setCase_id ( $hdn_case_id );
	}else{
			if (case_obj_exists($fds['obj_id'],$case_type)) {
			$case_obj->setObj_id ( $fds ['obj_id'] );
			}
	}
	$case_obj->setObj_type ( $case_type );
	$case_obj->setCase_auther ( $fds ['case_auther'] );
	$case_obj->setCase_price ( $fds ['case_price'] );
	$case_obj->setCase_desc ( kekezu::escape($fds ['case_desc']) );
	$case_obj->setCase_title ( kekezu::escape($fds ['case_title']) );
	$case_obj->setOn_time ( time () );
	//如果有上传文件优先选择上传文件
	($case_img = keke_file_class::upload_file ( "fle_case_img" )) or $case_img = $hdn_case_img;
	$case_obj->setCase_img ($case_img );//上传图片
	if ($hdn_case_id) {//编辑
		$res = $case_obj->edit_keke_witkey_case ();
		kekezu::admin_system_log ( $_lang['edit_case'].':' . $hdn_case_id ); //日志记录
		$res and kekezu::admin_show_msg ( $_lang['modify_case_success'], 'index.php?do=case&view=lise',3,'','success' ) or kekezu::admin_show_msg ( $_lang['modify_case_fail'], 'index.php?do=case&view=lise',3,'','warning' );
	}else{//添加
		$res = $case_obj->create_keke_witkey_case ();
		kekezu::admin_system_log ( $_lang['add_case'] ); //日志记录
		$res and kekezu::admin_show_msg ( $_lang['add_case_success'],'index.php?do=case&view=lise',3,'','success' ) or kekezu::admin_show_msg ( $_lang['add_case_fail'],'index.php?do=case&view=add',3,'','warning' );
	}
	
}

/**
 * 判断id是否存在
 * @param int $id	案例id
 * @param string $obj 案例类型（任务，商品）
 */
function case_obj_exists($id, $obj = 'task') {
	if ($obj == 'task') {
		$search_obj = db_factory::get_count ( sprintf ( "select count(task_id) from %switkey_task where task_id='%d' ", TABLEPRE, $id ) );
	} elseif ($obj =='service') {
		$search_obj = db_factory::get_count ( sprintf ( "select count(service_id) from %switkey_service where service_id='%d' ", TABLEPRE, $id ) );
	}
	if ($search_obj) {
		return true;
	} else {
		return false;
	}
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );