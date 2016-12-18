<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-6-13上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 161 );

!$send_uid and kekezu::admin_show_msg ( '该用户不存在', 'index.php?do=task&view=unpublished_list',3,'','warning' );

$space_obj = new Keke_witkey_space_class(); 
$space_obj->setWhere ( " uid = " . $send_uid );
$space_arr = $space_obj->query_keke_witkey_space ();	
$space_arr = $space_arr[0];  

/** 身份证认证 **/
$auth_realname_obj = new Keke_witkey_auth_realname_class(); 
$auth_realname_obj->setWhere ( " uid = " . $send_uid );
$auth_realname_arr = $auth_realname_obj->query_keke_witkey_auth_realname ();	
$space_arr['auth_realname_arr'] = $auth_realname_arr[0];  

/** 银行认证 **/
$auth_bank_obj = new Keke_witkey_auth_bank_class(); 
$auth_bank_obj->setWhere ( " uid = " . $send_uid );
$space_arr['auth_bank_arr'] = $auth_bank_obj->query_keke_witkey_auth_bank ();	
$bank = keke_glob_class::get_bank();  //银行类型

/**
 * 发布号码
 */
$send_tid or $send_tid = -1;
$contact = db_factory::get_count(' select contact from '.TABLEPRE.'witkey_task where task_id='.$send_tid);
$contact = unserialize($contact);
function contact($k){
	$t = array('mobile'=>'手机','email'=>'邮箱','qq'=>'QQ','msn'=>'MSN');
	return $t[$k];
}
require $template_obj->template ( 'control/admin/tpl/admin_task_' . $view);