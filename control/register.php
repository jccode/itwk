<?php
/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-26早上11:49:00
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//注册类型
$types = array('reg','email','mobile');
(!empty($type) && in_array($type,$types)) or $type='reg';
//注册步骤
!isset($step) and $step='';
(isset($step) && $step!='' && $type=='reg') and $step='step3';
//如果是管理员添加用户就不作判断,第三步允许停留在当前页面
if($step != 'step3'){
	($uid && !isset($_SESSION['auid'])) and kekezu::show_msg ( $_lang['friendly_notice'], 'index.php', 3, $_lang['you_has_login'],'warning');
	$page_title=$_lang['register'].'-'.$_K['html_title'];
}
//初始化对象
$reg_obj = new keke_register_class();
$api_name = keke_glob_class::get_open_api();
if (isset($formhash)&&kekezu::submitcheck($formhash)){ 
	//用户注册
	isset($txt_mobile) or $txt_mobile='';
	$reg_uid = $reg_obj->user_register($txt_account, md5($pwd_password), $txt_email,$txt_mobile,$type,$txt_code,1,$pwd_password);
	$user_info = keke_user_class::get_user_info($reg_uid); 
	if(isset($unit)&&$unit){
		$unit_obj = new kk_client($app_key, $app_secret);
		$task_url = $unit_obj->clientlogin($user_info['uid']);
		keke_function_class::curl_request($task_url,"get",null);
	}
	 //用户登录
	$reg_obj->register_login($user_info, $type);
}

if($id){
	$uinfo = keke_user_class::get_user_info($id);
}
if($type=='email'){
	$arr = explode ( "@", $uinfo ['email'] );
	$mail_url = "http://mail." . $arr [1];
}
//重发邮件/短信
if(isset($resend) && isset($id)){
	if($resend=='email'){
		$info = array ('uid' => $uinfo['uid'], 'username' => $uinfo['username'], 'email' => $uinfo['email'] );
		keke_user_class::send_email_action_user ( $info );
	}elseif($resend=='sms'){
		$valid_code = kekezu::randomkeys(6);
		$msg_obj = new keke_msg_class();
		$moblie_obj = new Keke_witkey_auth_mobile_class();
		$content = "手机认证验证码："."{$valid_code}-IT帮手网"."{$kekezu->_sys_config[website_name]}";
		//发送手机验证码
		$msg_res = $msg_obj->send_phone_sms($uinfo['mobile'],$content);
		if($msg_res=='发送成功'){
			$_SESSION['mobile_count'] = 0;
			//写入短信认证记录
			$auth_info = db_factory::get_one(sprintf("select * from %switkey_auth_mobile where uid='%d'",TABLEPRE,$uinfo['uid']));
			if($auth_info){//修改数据
				$moblie_obj->setWhere('uid='.$uinfo['uid']);
				$moblie_obj->setValid_code($valid_code);
				$moblie_obj->edit_keke_witkey_auth_mobile();
			}else{//生成手机认证数据
				$moblie_obj->setUid($uinfo['uid']);
				$moblie_obj->setMobile($uinfo['mobile']);
				$moblie_obj->setValid_code($valid_code);
				$moblie_obj->setUsername($uinfo[username]);
				$moblie_obj->setCash(0);
				$moblie_obj->setAuth_time(time());
				$moblie_obj->setAuth_status(0);
				$moblie_obj->create_keke_witkey_auth_mobile();
			}
			kekezu::show_msg('手机认证',$_K['siteurl']."/index.php?do=register&type=mobile&step=step2&id=$id",2,'一条包含验证码的短信已发送到您的手机，请回填验证码完成注册。','success');
		}
	}
}

//短信验证
if(isset($ac) && $type=='mobile'){
	if($ac=='verify'){
		isset($_SESSION['mobile_count']) or $_SESSION['mobile_count']=0;
		//if($_SESSION['mobile_count']>2){
		//	kekezu::show_msg( '手机认证',"index.php?do=register&type=mobile&step=step2&id=$id", 3, '验证码填写错误超过3次，请重新发送验证码','warning' );
		//}
		$mobile_obj = new Keke_witkey_auth_mobile_class();
		$mobile_obj->setWhere('uid='.$id);
		$mobile_info = $mobile_obj->query_keke_witkey_auth_mobile();
		$mobile_info = $mobile_info[0];
		$valid_code = $mobile_info['valid_code'];
		if($user_code == $valid_code){
			//认证判定时间（认证成功或者失败）
			$end_time = time(); 			
			$auth_moble_obj = new keke_auth_mobile_class();
			//生成手机认证成功的时间
			$mobile_obj->setWhere('uid='.$id);
			$mobile_obj->setEnd_time($end_time);
			$mobile_obj->setAuth_status(1);
			$res2 = $mobile_obj->edit_keke_witkey_auth_mobile();
				
			parse_str($_SERVER['HTTP_REFERER'],$arr);
			$url = $_K['siteurl']."/index.php?do=register&type=mobile&step=step3&id=$id";
			if($res2){
				//添加进入认证记录
				$end_time=0;
				$res = $auth_moble_obj->add_auth_record($uinfo['uid'], $uinfo['username'], 'mobile',$end_time,'',1);
				//修改账号状态状态
				db_factory::execute(sprintf("update %switkey_space set status='1',auth_mobile=1 where uid='%d'",TABLEPRE,$id));
				//用户登录
				$_SESSION['uid'] = $uinfo['uid'];
				$_SESSION['username'] = $uinfo['username'];
				$_SESSION['mobile_count']=0;
				//注册推广结算
				$kekezu->init_prom();
				$kekezu->_prom_obj->dispose_prom_event($auth_moble_obj->_auth_name,$id,$id);
				kekezu::empty_cache();
				//kekezu::show_msg ( '手机认证',$url, 3, '手机认证成功！','success' );
				$return_info['id'] = $id;
				$return_info['url'] = $url;
				kekezu::echojson('手机认证成功',1,$return_info);
				die();
			}
		}else{
			$_SESSION['mobile_count'] = $_SESSION['mobile_count'] + 1;
			$url = $_K['siteurl']."/index.php?do=register&type=mobile&step=step2&id=$id";
			//kekezu::show_msg ( '手机认证',$url, 3, '手机认证失败！','warning' );
			$return_info['id'] = $id;
			$return_info['url'] = $url;
			kekezu::echojson('验证码填写有误，请重新填写',2,$return_info);
			die();
		}
	}
}

//异步检查
if (isset ( $check_email ) && ! empty ( $check_email )) {
	$res = keke_user_class::check_email ( $check_email );  
	echo  $res;
	die ();
}

if (isset ( $check_username ) && ! empty ( $check_username )) {
	 $res =  keke_user_class::check_username ( $check_username );
	 echo  $res;
	 die ();
}

if (isset($check_mobile) && !empty($check_mobile)){
	$res = keke_user_class::check_mobile($check_mobile);
	echo $res;
	die();
}if (isset($check_qq) && !empty($check_qq)){
	$res = keke_user_class::check_qq($check_qq);
	echo $res;
	die();
}

if ($step != ''){
	require keke_tpl_class::template ($do.'_'.$step);
}else{
	require keke_tpl_class::template ( $do );
}