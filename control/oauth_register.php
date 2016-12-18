<?php

/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-27早上9:55:00
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
// 初始化对象
$type or exit ( kekezu::show_msg ( $_lang ['operate_notice'], $kekezu->_sys_config ['website_url'] . "/index.php?do=login", 2, $_lang ['type_no_empty'], "warning" ) );
$page_title = "完善个人资料-" . $_K ['html_title'];
$open_api_arr = keke_glob_class::get_open_api ();
//获取登录平台
$oauth_type_arr = keke_glob_class::get_oauth_type ();
$oauth_url = $kekezu->_sys_config ['website_url'] . "/index.php?do=$do&type=$type";
//aouth登录


$oa = new keke_oauth_login_class ( $type );
if ($type && ! $_SESSION ['auth_' . $type] ['last_key']) {
	
	$oauth_vericode = $oauth_vericode;
	$oa->login ( $call_back, $oauth_url );
} else {
	$oauth_user_info = $oa->get_login_user_info ();
}
$oauth_type = $type;
if ($step == 'step2') {
	require keke_tpl_class::template ( 'oauth_step2' );
} else {
	if ($oauth_user_info && $formhash) {
		
		if (! $bind_info = keke_register_class::is_oauth_bind ( $type, $oauth_user_info ['account'] )) {
			$reg_obj = new keke_register_class ();
			//用户注册
			$reg_uid = $reg_obj->user_register ( $txt_account, md5 ( $pwd_password ), $txt_email, '', 'oauth', $txt_code );
			if ($reg_uid) {
				$user_info = keke_user_class::get_user_info ( $reg_uid );
				$reg_obj->register_binding ( $oauth_user_info, $user_info, $type );
				$reg_obj->register_login ( $user_info, 'oauth' );
			} else {
				kekezu::show_msg ( $_lang ['operate_notice'], "", '2', $_lang ['login_account_fail'] );
			}
		} else {
			kekezu::show_msg ( $_lang ['operate_notice'], "", '2', $_lang ['now_three_account_bind'] );
		}
	}
	if (isset ( $check_username ) && ! empty ( $check_username )) {
		$res = keke_user_class::check_username ( $check_username );
		echo $res;
		die ();
	}
	//异步检查
	if (isset ( $check_email ) && ! empty ( $check_email )) {
		$res = keke_user_class::check_email ( $check_email );
		echo $res;
		die ();
	}
	require keke_tpl_class::template ( $do );
}