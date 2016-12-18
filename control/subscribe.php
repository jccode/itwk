<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-3 15:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title ="任务周报订阅" . '-' . $_K ['html_title'];

$subscribe_obj = new Keke_witkey_task_subscribe_class(); 

if($op == 'cancel'){
	$subscribe_obj->setWhere("uid = '{$uid}'");
	$res = $subscribe_obj->del_keke_witkey_task_subscribe();
	$res and kekezu::echojson ( '邮箱订阅取消成功。', "1" );	
	//exit();
}

if(isset($formhash)){ //提交表单 isset($formhash)&&kekezu::submitcheck($formhash)
	$old_sub = db_factory::get_count(sprintf("select id from %switkey_task_subscribe where email='%s'",TABLEPRE,$email));
	
	if($old_sub){ 
		kekezu::show_msg("操作提示","{$kekezu->_sys_config[website_url]}/index.php?do=subscribe",2,"您已经订阅过任务周报，请勿重复订阅。","warning");
	}else{//保存邮件订阅信息
		$email_code = kekezu::randomkeys(6);//生成随机码
		$email_code_md5 = md5($email_code);
		
		$subscribe_obj->setEmail($email);
		$subscribe_obj->setTypeid(1);
		$subscribe_obj->setStatus(0);
		$subscribe_obj->setUid($uid);
		$subscribe_obj->setUsername($username);
		$subscribe_obj->setCheckcode($email_code);
		$subscribe_obj->setBooktime(time());
		$res = $subscribe_obj->create_keke_witkey_task_subscribe();
		if($res){ 
			//发送验证邮件
			$title = $kekezu->_sys_config ['website_name']."--邮件订阅";
			$body = <<<EOT
			<font color="red">{$kekezu->_sys_config['website_name']}--邮件订阅</font><br><br>
			&nbsp;&nbsp;&nbsp;感谢您订阅IT帮手网任务周报，请点击此地址验证您的邮箱：
			{$kekezu->_sys_config[website_url]}/index.php?do=subscribe&view=step3&email_code={$email_code_md5}&id=$res
EOT;

/*<a href="{$kekezu->_sys_config[website_url]}/index.php?do=subscribe&view=step3&email_code={$email_code_md5}&id=$res" traget="_blank">
			点击验证邮箱
			</a> */
			kekezu::send_mail ( $email, $title, $body );
		}else{
			kekezu::show_msg("操作提示","{$kekezu->_sys_config[website_url]}/index.php?do=subscribe",2,"任务周报订阅失败，请联系客服人员。","warning");
		}
	}
}

if($view=='step3'){
	if($id){
		$subscribe_info = db_factory::get_one(sprintf("select * from %switkey_task_subscribe where id=%d",TABLEPRE,$id));
		if($subscribe_info){
			$md5_code = md5($subscribe_info['checkcode']);
			if($subscribe_info['status']==0){
				if($email_code==$md5_code){
					$subscribe_obj->setWhere("id=".$id);
					$subscribe_obj->setStatus(1);
					$subscribe_obj->setChecktime(time());
					$res=$subscribe_obj->edit_keke_witkey_task_subscribe();
					if(!$res){
						kekezu::show_msg("操作提示","index.php",2,"邮箱验证失败，请联系客服人员。","warning");
					}
				}else{
					kekezu::show_msg("操作提示","index.php",2,"邮箱验证失败，验证码不正确，请联系客服人员。","warning");
				}
			}else{
				kekezu::show_msg("操作提示","index.php",2,"您已经验证过邮箱，请不要重复验证。","warning");
			}
		}else{
			kekezu::show_msg("操作提示","index.php",2,"没有找到您申请的邮件订阅记录，请联系客服人员。","warning");
		}
	}else{
		kekezu::show_msg("操作提示","index.php",2,"邮箱验证参数错误，请重试。","warning");
	}
}

if($email){
	$arr = explode ( "@", $email );
	$mail_url = "http://mail." . $arr [1];
}

if($view){
	require keke_tpl_class::template ( $do ."_". $view);
}else{
	require keke_tpl_class::template ( $do );
}
