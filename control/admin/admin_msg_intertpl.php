<?php
/**
 * 短信邮件模板配置管理
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-8-15
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(73);
$msg_obj =new Keke_witkey_msg_tpl_class();
//所有短信类型
$config_msg_arr = $kekezu->get_table_data ( "*", "witkey_msg_config", " 1 = 1 ", "config_id desc ", '', '', 'config_id' );
//当前短信类型
//var_dump($config_msg_arr);

$now_msg_arr = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_msg_config where k='$slt_tpl_code'" );

$now_v = unserialize ( $now_msg_arr ['v'] );
if (isset ( $tpl_code )) {
	$msg_tpl = db_factory::query(" select * from " . TABLEPRE . "witkey_msg_tpl where tpl_code='$tpl_code'" );
	if ($msg_tpl) {
		kekezu::echojson ( '', 1, $msg_tpl );
	} else {
		echo json_encode ( array ("status" => 0 ) );
	}
}

if (isset ( $sbt_edit )) {
//	if ($slt_tpl_code) {
//		$msg_obj->setWhere ( "tpl_code='$slt_tpl_code' and send_type=1" );
//		$msg_obj->setContent($tar_msg_temp_content);
//		$res = $msg_obj->edit_keke_witkey_msg_tpl();
//	}
	
	if ($slt_tpl_code) {
		$has_mobile_tpl=db_factory::get_count(" select * from ".TABLEPRE."witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=1");
		if($has_mobile_tpl){
			$msg_obj->setWhere ( "tpl_code='$slt_tpl_code' and send_type=1" );
			$msg_obj->setContent($tar_msg_temp_content );
			$res .= $msg_obj->edit_keke_witkey_msg_tpl();
		}else{
			$msg_obj->_tpl_id=null;
			$msg_obj->setTpl_code($slt_tpl_code);
			$msg_obj->setContent($tar_msg_temp_content );
			$msg_obj->setSend_type(1);
			$res .= $msg_obj->create_keke_witkey_msg_tpl();
		}	
	}
	
	if ($tar_phone_temp_content_2) { //手机短信
		$has_mobile_tpl=db_factory::get_count(" select * from ".TABLEPRE."witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=2");
		if($has_mobile_tpl){
			$msg_obj = new Keke_witkey_msg_tpl_class();
			$msg_obj->setWhere ( "tpl_code='$slt_tpl_code' and send_type=2" );
			$msg_obj->setContent($tar_phone_temp_content_2 );
			$res .= $msg_obj->edit_keke_witkey_msg_tpl();
		}else{
			$msg_obj->_tpl_id=null;
			$msg_obj->setTpl_code($slt_tpl_code);
			$msg_obj->setContent($tar_phone_temp_content_2 );
			$msg_obj->setSend_type(2);
			$res .= $msg_obj->create_keke_witkey_msg_tpl();
		}	
	}
	
	if ($tar_email_temp_content) { //电子邮件
		$has_email_tpl=db_factory::get_count(" select * from ".TABLEPRE."witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=3");
		if($has_email_tpl){ 
			$msg_obj->setWhere ( "tpl_code='$slt_tpl_code' and send_type=3" );
			$msg_obj->setContent( $tar_email_temp_content );
			$res .= $msg_obj->edit_keke_witkey_msg_tpl();
		}else{ 
			$msg_obj->_tpl_id=null;
			$msg_obj->setTpl_code($slt_tpl_code);
			$msg_obj->setContent( $tar_email_temp_content );
			$msg_obj->setSend_type(3);
			$res .= $msg_obj->create_keke_witkey_msg_tpl();
		}	
	}

	
	if ($res) {
		kekezu::admin_system_log ( $_lang['edit_sms_tpl'] );
		kekezu::admin_show_msg ( $_lang['edit_sms_tpl_success'], 'index.php?do=msg&view=intertpl&slt_tpl_code=' . $slt_tpl_code,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['save_sms_tpl_success'], 'index.php?do=msg&view=intertpl&slt_tpl_code=' . $slt_tpl_code,3,'','success' );
	}
}
$msg_tpl = db_factory::get_one ( "select content from " . TABLEPRE . "witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=1" );
$msg_tpl = $msg_tpl ['content'];
$phone_tpl = db_factory::get_one ( "select content from " . TABLEPRE . "witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=2" );
$phone_tpl = $phone_tpl ['content'];
$email_tpl = db_factory::get_one ( "select content from " . TABLEPRE . "witkey_msg_tpl where tpl_code='$slt_tpl_code' and send_type=3" );
$email_tpl = $email_tpl ['content']; 
//var_dump($msg_tpl);
require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_msg_' . $view );