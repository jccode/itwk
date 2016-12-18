<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-14 14:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title="找回密码".'- '.$_K['html_title'];

$api_name = keke_glob_class::get_open_api();
$r = $_K['siteurl']."/index.php?do=login";
if (kekezu::submitcheck($formhash)) {
	//判断账号是否存在
	$user_info = kekezu::get_user_info($txt_account,true);
	//获取找回密码的方式
	switch ($accout_type){
		case "email":
			//$auth_arr = keke_auth_fac_class::auth_check ( 'email', $user_info['uid'] );
			
			//if($auth_arr){
				if($user_info['email']==$txt_email){
					$pass_info = reset_set_password($user_info);
					$v_arr = array("用户名"=>$user_info['username'],"网站名称"=>$kekezu->_sys_config['website_name'],"密码"=>$pass_info['code'],"安全码"=>$pass_info['sec_code'] );
					keke_shop_class::notify_user($user_info['uid'], $user_info['username'], 'get_password',"账号找回通知",$v_arr);
					//kekezu::show_msg($_lang['friendly_notice'],$r,3,$_lang['your_new_password_in_email']);
					kekezu::echojson($_lang['your_new_password_in_email']."<br />".$txt_email."请注意查收！",1,$user_info);
					die();
				}else{
					$user_info['txtemail']=$txt_email;
					$user_info['authemail']=$user_info['email'];
					kekezu::echojson($_lang['email_accout_error'],2,$user_info);
					die();
				}
			/* }else{
				kekezu::echojson($_lang['no_email_auth_no_back'],3,$user_info);
				die();
			} */
			break;
		case "mobile":
			//$mobile_auth = keke_auth_fac_class::auth_check ( 'mobile', $user_info['uid'] );
			//if($mobile_auth){
				if($user_info['mobile']==$txt_mobile){
					$pass_info = reset_set_password($user_info);
					$msg = new keke_msg_class();
					$msg_str = $_lang['password_is'] .$pass_info['code']."，". $_lang['your_safe_code_is'] .$pass_info['sec_code'];
					$msg->send_phone_sms($user_info['mobile'],$msg_str);
					kekezu::echojson($_lang['password_has_send_to_phone'],1,$user_info);
					die();
					//kekezu::show_msg($_lang['friendly_notice'],$r,3,$_lang['password_has_send_to_phone']);
				}else{
					kekezu::echojson($_lang['mobile_accout_error'],2,$user_info);
					die();
				}
			/* }else{
				kekezu::echojson($_lang['no_phone_auth_no_back'],3,$user_info);
				die();
			} */
			break;
	}

}
//重置密码
function reset_set_password($user_info){
	$code = kekezu::randomkeys(6);
	//生成密码
	$user_code = md5($code);
	//生成随即数
	$slt = kekezu::randomkeys(6);
	//生成安全码
	$user_seccode = keke_user_class::get_password($slt, $slt);
	//更新密码信息
	$sql = "update %switkey_member set password = '%s' , rand_code = '%s' where uid=%d";
	$sql = sprintf($sql,TABLEPRE,$user_code,$slt,$user_info['uid']);
	$res = db_factory::execute($sql);

	$sql = "update %switkey_space set  password = '%s' , sec_code = '%s' where uid=%d";
	$sql = sprintf($sql,TABLEPRE,$user_code,$user_seccode,$user_info['uid']);
	db_factory::execute($sql);
	$pass_info ['code'] = $code;
	$pass_info ['sec_code'] = $slt;
	return $pass_info;
}

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );