<?php

/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-27早上9:55:00
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$type or exit ( kekezu::show_msg ( $_lang ['oprerate_notice'], $kekezu->_sys_config ['website_url'] . "/index.php?do=login", 2, $_lang ['type_no_empty'], "warning" ) );
$page_title = '绑定账号- ' . $_K ['html_title'];
// 初始化信息
$oa = new keke_oauth_login_class ( $type ); //var_dump($oa);
$api_name = keke_glob_class::get_open_api ();
$login_obj = new keke_user_login_class ();
$oauth_obj = new Keke_witkey_member_oauth_class ();
//$oauth_url = $kekezu->_sys_config ['website_url'] . "/index.php?do=$do&type=$type";
$oauth_url = $_K ['siteurl'] . "/index.php?do=$do&type=$type";
//获取登录平台
$oauth_type_arr = keke_glob_class::get_oauth_type ();

//oauth登录
if ($type && ! $_SESSION ['auth_' . $type] ['last_key']) {
	if ($type == 'sina' && $error_code == '21330') { //当用户在sina平台上拒绝oauth登录时,给出提示
		kekezu::show_msg ( $_lang ['notice_message'], $kekezu->_sys_config ['website_url'] . '/index.php?do=login', 1, $_lang ['login_in_fail'], "alert_right" );
	}
	$oauth_vericode = $oauth_vericode;
	$oa->login ( $call_back, $oauth_url );
} else {
	$oauth_user_info = $oa->get_login_user_info ();
}
if ($step == 'step2') {
	require keke_tpl_class::template ( 'oauth_step2' );
} else {
	$bind_info = keke_register_class::is_oauth_bind ( $type, $oauth_user_info ['account'] ); //oauth 绑定判断
	if ($oauth_user_info && $bind_info) {
		$user_info = kekezu::get_user_info ( $bind_info ['uid'] );
		$login_user_info = $login_obj->user_login ( $user_info ['username'], $user_info ['password'], null, 1 );
		$login_obj->save_user_info ( $login_user_info, 1 );
	}
	if (kekezu::submitcheck ( $formhash )) {
		$login_user_info = $login_obj->user_login ( $txt_account, md5 ( $pwd_password ), $txt_code,2);
		keke_register_class::register_binding ( $oauth_user_info, $login_user_info, $type );
		$login_obj->save_user_info ( $login_user_info, 1 );
	}
	if (isset ( $check_username ) && ! empty ( $check_username )) {
		$res = kekezu::check_user_by_name ( $check_username, 1 );
		if ($res == 1) {
			$has_bind = db_factory::get_count(' select count(id) from '.TABLEPRE.'witkey_member_oauth where username="'.$check_username.'" and source="'.$type.'"');
			if($has_bind){
				echo '此用户已绑定了'.$oauth_type_arr[$type].'账号';
			}else{
				echo 1;
			}
		} else {
			echo '不存在的IT帮手网账号';
		}
		die ();
	}
	require keke_tpl_class::template ( $do );
}