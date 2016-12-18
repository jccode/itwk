<?php
/**
 * 注册邮箱激活
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$user_info = kekezu::get_user_info($excite_uid);

if($user_info){
	if($user_info['status']=='3'){
		$md5_code = md5($user_info['uid'].','.$user_info['username'].','.$user_info['email']);
		if($md5_code==$excite_code){
			$res = db_factory::execute(sprintf("update %switkey_space set status='1' where uid='%d'",TABLEPRE,$excite_uid));
			//添加邮箱认证记录
			$email_obj = new Keke_witkey_auth_email_class();
			$email_obj->setUid($user_info['uid']);
			$email_obj->setUsername($user_info['username']);
			$email_obj->setEmail($user_info['email']);
			$email_obj->setCash(0);
			$email_obj->setAuth_time(time());
			$email_obj->setStart_time(time());
			$email_obj->setEnd_time(time());
			$email_obj->setAuth_status(1);
			$email_obj->create_keke_witkey_auth_email();
			//添加record表记录
			$record_obj = new Keke_witkey_auth_record_class();
			$record_obj->setAuth_code('email');
			$record_obj->setUid($user_info['uid']);
			$record_obj->setUsername($user_info['username']);
			$record_obj->setEnd_time(time());
			$record_obj->setAuth_status(1);
			$record_obj->create_keke_witkey_auth_record();
			//修改space表邮箱认证状态
			$space_obj = new Keke_witkey_space_class();
			$space_obj->setWhere('uid='.$excite_uid);
			$space_obj->setAuth_email(1);
			$space_obj->edit_keke_witkey_space();
			//用户登录
			$_SESSION['uid'] = $user_info['uid'];
			$_SESSION['username'] = $user_info['username'];
			
			$res and kekezu::show_msg($_lang['operate_notice'],$_K['siteurl'].'/index.php?do=register&type=email&step=step3&id='.$user_info['uid'],3,$_lang['your_username_has_activation_seccess'],'success') or kekezu::show_msg($_lang['operate_notice'],'index.php',3,$_lang['your_username_activation_fail'],'warning');
		}else{
			kekezu::show_msg($_lang['operate_notice'],'index.php',3,$_lang['user_activation_number_error'],'warning');
		}
	}else{
		kekezu::show_msg($_lang['operate_notice'],'index.php',3,$_lang['your_username_activated_not_repeat'],'warning');
	}
}else{
	kekezu::show_msg($_lang['operate_notice'],'index.php',3,$_lang['not_exist_wait_activation_user'],'warning');
}