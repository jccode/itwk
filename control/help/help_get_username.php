<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-14 14:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title="找回账号".'- '.$_K['html_title'];

$r = $_K['siteurl']."/index.php?do=login";
if (kekezu::submitcheck($formhash)) {
	//获取找回密码的方式
	switch ($accout_type){
		case "email":
			$user_data = db_factory::get_one(" select * from ".TABLEPRE."witkey_member  where email ='$txt_email' ");
			if($user_data){
				$msg = new keke_msg_class();
				$msg->setUid($user_data['uid']);
				$msg->setUsername($user_data['username']);
				$msg->_email = $user_data['email'];
				$msg->_normal_content ="您的账号为：".$user_data['username']."请妥善保管" ;
				$msg->sendmail();
				kekezu::echojson("恭喜您，登录账号已发往您的注册邮箱</br>".$txt_email."</br>请注意查收！",1,$user_data);
				die();
			}else{
				kekezu::echojson('邮箱不存在',3,$user_info);
				die();
			}
			break;
		case "mobile":
			$user_data = db_factory::get_one(" select * from ".TABLEPRE."witkey_member  where email ='$txt_email' ");
			if($user_data){
				$msg = new keke_msg_class();
				$msg_str = $_lang['password_is'] .$pass_info['code']."，". $_lang['your_safe_code_is'] .$pass_info['sec_code'];
				$msg->send_phone_sms("您的账号为：".$user_data['username']."请妥善保管",$msg_str);
				kekezu::echojson($_lang['password_has_send_to_phone'],1,$user_data);
				die();
			}else{
				kekezu::echojson("手机号码不存在",3,$user_info);
				die();
			}
			break;
	}
}

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );