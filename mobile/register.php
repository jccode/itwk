<?php
/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' )&&defined('ISWAP')&&ISWAP or kekezu::echojson ($wap_msg, 0);
$reg_obj = new keke_register_class(2);//初始化对象
 
if($account){
	if(kekezu::utf8_strlen(kekezu::gbktoutf($account))<4 ||kekezu::utf8_strlen(kekezu::gbktoutf($account))>12){
		kekezu::echojson ( array ('r' => '用户名长度4-16字符' ), 2 );
	}
}

if($password){
	if(kekezu::utf8_strlen(kekezu::gbktoutf($password))<4 ||kekezu::utf8_strlen(kekezu::gbktoutf($password))>16){
		kekezu::echojson ( array ('r' => '密码长度4-16字符' ), 2 );
	}
}

if($email){
	if(!kekezu::is_email($email)){
		kekezu::echojson ( array ('r' => '邮箱格式xxx@xxx.com' ), 2 );
	}
}

$reg_uid = $reg_obj->user_register ( $account, md5 ( $password ), $email, '', 0, $password );//用户注册
$user_info = kekezu::get_user_info ( $reg_uid );
wap_base_class::update_load_status($user_info['uid']);//更新登陆状态
//kekezu::echojson ($user_info, 0);
$reg_obj->register_login ( $user_info );//用户登录