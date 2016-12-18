<?php

/**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-7-6上午11:52:51
 */

keke_lang_class::load_lang_class ( 'keke_register_class' );
class keke_register_class {
	
	protected $_space_obj;
	protected $_member_obj;
	protected $_sys_config;
	protected $_reg_username;
	protected $_reg_type;
	public $_reg_pwd;
	protected $_reg_email;
	protected $_reg_mobile;
	protected $_reg_qq;
	protected $_reg_role;
	protected $_reg_ip;
	protected $_r;
	protected $_message_obj;
	protected $_check_code;
	protected $_oltime_obj;
	
	function __construct($reg_type = 1) {
		global $kekezu;
		$this->_space_obj = new Keke_witkey_space_class ();
		$this->_member_obj = new Keke_witkey_member_class ();
		$this->_sys_config = $kekezu->_sys_config;
		$this->_message_obj = new keke_msg_class ();
		$this->_oltime_obj = new Keke_witkey_member_oltime_class ();
		$this->_reg_ip = kekezu::get_ip ();
		$this->_reg_type = intval ( $reg_type );
	
		//keke_lang_class::loadlang('keke_register_class','public'); 
	}
	//用户注册成功返回userinfo
	function user_register($reg_username, $reg_pwd, $reg_email, $reg_mobile, $type, $reg_code, $check_code = true, $old_pwd = null) {
		global $kekezu,$_K,$_lang,$oauth_type,$txt_qq,$user_role;
		$reg_uid = null;
		$type=='oauth' and $this->_r = $_K['siteurl'].'/index.php?do=oauth_register&type='.$oauth_type or $this->_r = $_K['siteurl'].'/index.php?do=register';
		//初始化信息
		$this->_reg_username = $reg_username;
		$this->_reg_pwd = $reg_pwd;
		$this->_reg_email = $reg_email;
		$this->_check_code = $check_code;
		$this->_reg_mobile = $reg_mobile;
		$this->_reg_qq = $txt_qq;
		$this->_reg_role = $user_role;
		
		//判断登录的规则
		
		$this->check_all ( $reg_username, $reg_email, $reg_code );
		
		
		//判断是否整合
		switch ($kekezu->_sys_config ['user_intergration']) {
			case 2 :
				$notify = "UCenter";
				require_once S_ROOT . './keke_client/ucenter/client.php';
				$reg_uid = uc_user_register ( $this->_reg_username, $old_pwd, $this->_reg_email );
				$exists = db_factory::get_count ( sprintf ( " select count(uid) from %switkey_member where uid='%d'", TABLEPRE, $reg_uid ) );
				if ($exists) { //与三方用户UID冲突
					$this->show_msg($_lang ['warning_local_and'] . $notify . $_lang ['user_table_primary_violation_notice'],'',2);
				}
				break;
			case 3 :
				$notify = "PW";
				require_once (S_ROOT . './keke_client/pw_client/uc_client.php');
				$reg_uid = uc_user_register ( $this->_reg_username, $this->_reg_pwd, $this->_reg_email );
				$exists = db_factory::get_count ( sprintf ( " select uid from %switkey_member where uid='%d'", TABLEPRE, $reg_uid ) );
				if ($exists) { //与三方用户UID冲突
					$this->show_msg($_lang ['warning_local_and'] . $notify . $_lang ['user_table_primary_violation_notice'],'',2);
				}
				break;
		}
		return $this->save_userinfo ( $this->_reg_username, $this->_reg_email, $this->_reg_mobile, $type, $reg_uid ); //将数据信息写入本地库
	}
	
	//注册成功后登录
	function register_login($userinfo, $type=null) {
		global $kekezu,$_lang,$_K;
		//写入session
		$_SESSION ['uid'] = $userinfo ['uid'];
		$_SESSION ['username'] = $userinfo ['username'];
		
		//发email通知
		if ($this->_message_obj->validate ( 'reg' ) && $this->_sys_config ['allow_reg_action'] == 0 && $type == 'reg') {
			$this->_message_obj->send_message ( $userinfo ['uid'], $userinfo ['username'], 'reg',
					"注册成功通知", array ('用户名'=>$userinfo['username']), $userinfo ['email'] );
		}
		$c = $_COOKIE;
		//$r = "index.php?do=register_wizard&refer=" . $_K ['refer'];
		$r = "index.php?do=register&type=".$type."&step=step2&id=".$userinfo['uid'];
		/**
		 * 产生推广
		 */
		if (isset ( $_COOKIE ['user_prom_event'] )) {
			$kekezu->init_prom ();
			$prom_obj = $kekezu->_prom_obj;
			$prom_obj->create_prom_relation ( $userinfo ['uid'], $userinfo ['username'], $prom_obj->extract_prom_cookie () );
			$prom_obj->clear_prom_cookie();
		}
		$synhtml = keke_user_class::user_synlogin ( $userinfo ['uid'], md5 ( $this->_reg_pwd ) );
		
		if ($userinfo ['status'] == '3'&&$this->_reg_type==1) {//平台注册
			$_SESSION ['uid'] = '';
			$_SESSION ['username'] = '';
			if($type=='mobile'){
				$this->show_msg('一条包含验证码的短信已发送到您的手机，请回填验证码完成注册。' . "$synhtml",$r,1);
			}
			header("Location:".$r);
		}elseif($this->_reg_type==2){//手机注册、不能使用邮箱激活
			$userinfo['pic'] = keke_user_class::get_user_pic ( $userinfo ['uid'] );
			
			
			$r = $userinfo;
			db_factory::execute(sprintf(" update %switkey_space set status=1 where uid='%d'",TABLEPRE,$userinfo['uid']));
			$this->show_msg('注册成功','',1,$r);
		} else {
			$type=='oauth' and $r= $this->_r.'&step=step2&id='.$userinfo['uid'];
			header("Location:".$r);
		}
	}
	
	//写入用户信息
	function save_userinfo($reg_username, $reg_email, $reg_mobile, $type, $reg_uid = null) {
		
		//获取密码		
		$slt = kekezu::randomkeys ( 6 );
		$pwd = keke_user_class::get_password ( $this->_reg_pwd, $slt );
		
		$this->_member_obj->setUid ( $reg_uid );
		$this->_member_obj->setEmail ( $reg_email );
		$this->_member_obj->setUsername ( $reg_username );
		$this->_member_obj->setPassword ( $this->_reg_pwd );
		$this->_member_obj->setRand_code ( $slt );
		$this->_member_obj->setMobile ($reg_mobile);
		$reg_member_uid = $this->_member_obj->create_keke_witkey_member ();
		//写入member_oltime
		$this->_oltime_obj->setUid ( $reg_member_uid );
		$this->_oltime_obj->setUsername ( $reg_username );
		$this->_oltime_obj->setLast_op_time ( time () );
		$this->_oltime_obj->setOnline_total_time ( 0 );
		$this->_oltime_obj->create_keke_witkey_member_oltime ();
		
		if ($reg_member_uid) {
			keke_user_class::set_union_relation ( $reg_member_uid ); //更新联盟关系
			/*初始化角色等级头衔*/
			$buyer_level = keke_user_mark_class::get_mark_level ( 0, '2' ); //雇主头衔
			$seller_level = keke_user_mark_class::get_mark_level ( '0', '1' ); //威客头衔
			$this->_space_obj->setUid ( $reg_member_uid );
			$this->_space_obj->setUsername ( $reg_username );
			
			$this->_space_obj->setPassword ( $this->_reg_pwd );
			$this->_space_obj->setSec_code ( $pwd );
			$this->_space_obj->setEmail ( $reg_email );
			$this->_space_obj->setMobile ($reg_mobile);
			$this->_space_obj->setReg_time ( time () );
			$this->_space_obj->setUser_role($this->_reg_role);
			$this->_space_obj->setQq($this->_reg_qq);
			// 			$this->_space_obj->setBalance(10000);
			$this->_space_obj->setReg_ip ( $this->_reg_ip );
			$this->_space_obj->setBuyer_level ( serialize ( $buyer_level ) );
			$this->_space_obj->setSeller_level ( serialize ( $seller_level ) );
			
			$space_id = $this->_space_obj->create_keke_witkey_space ();
		}
		
		$info = array ('uid' => $reg_member_uid, 'username' => $reg_username, 'email' => $reg_email );
		//判断是否需要邮件激活，并发发送激活码到指定的邮箱
		if($type=='reg'){
			$this->_sys_config ['allow_reg_action'] == 1 and keke_user_class::send_email_action_user ( $info );
		}
		if($type=='email'){
			keke_user_class::send_email_action_user ( $info );
		}
		//发送短信到指定手机号码，并修改账号状态
		if($type=='mobile'){
			$valid_code = kekezu::randomkeys(6);
			$msg_obj = new keke_msg_class();
			$mobile_obj = new Keke_witkey_auth_mobile_class();
			$content = "手机认证验证码："."{$valid_code}-IT帮手网"."{$kekezu->_sys_config[website_name]}";
			//发送手机验证码
			$msg_res = $msg_obj->send_phone_sms($reg_mobile,$content);
			if($msg_res=='发送成功'){
				//写入短信认证记录
				$auth_info = db_factory::get_one(sprintf("select * from %switkey_auth_mobile where uid='%d'",TABLEPRE,$reg_member_uid));
				if($auth_info){//修改数据
					$mobile_obj->setWhere('uid='.$reg_member_uid);
					$mobile_obj->setValid_code($valid_code);
					$mobile_obj->edit_keke_witkey_auth_mobile();
				}else{//生成手机认证数据
					$mobile_obj->setUid($reg_member_uid);
					$mobile_obj->setMobile($reg_mobile);
					$mobile_obj->setValid_code($valid_code);
					$mobile_obj->setUsername($reg_username);
					$mobile_obj->setCash(0);
					$mobile_obj->setAuth_time(time());
					$mobile_obj->setAuth_status(0);
					$mobile_obj->create_keke_witkey_auth_mobile();
				}
			}
			//修改账号状态为3
			db_factory::execute(sprintf("update %switkey_space set status='3' where uid='%d'",TABLEPRE,$reg_member_uid));
		}
		
		return $reg_member_uid;
	}
	
	//判断数据是否合法
	function check_all($reg_username, $reg_email, $reg_code) {
		global $_lang;
		$res1 = $this->check_ip ();
		$res1 === true or $this->show_msg($res1,$this->_r,2);
		
		$res2 = $this->check_username ( $reg_username );
		$res2 === true or $this->show_msg($res2,$this->_r,2);
		
		$res3 = $this->check_email ( $reg_email );
		$res3 === true or $this->show_msg($res3,$this->_r,2);
		
		
		if ($this->_check_code == true && $this->_reg_type!=2) {
			
			$res4 = $this->check_code ( $reg_code );
			$res4 === true or $this->show_msg($res4,$this->_r,2);
		}
	
	}
	
	//判断用户名
	function check_username($reg_username) {
		global $_lang;
		//判断是否是email
		if (function_exists ( "filter_var" )) {
			filter_var ( $reg_username, FILTER_VALIDATE_EMAIL ) and $this->show_msg($_lang ['username_can_not_email'],'index.php?do=register',2);
			kekezu::is_mobile ( $reg_username ) and $this->show_msg($_lang ['username_can_not_phone_number'],'index.php?do=register',2);
		} else {
			
			kekezu::is_email ( $reg_username ) and $this->show_msg($_lang ['username_can_not_email'],'index.php?do=register',2);
			kekezu::is_mobile ( $reg_username ) and $this->show_msg($_lang ['username_can_not_phone_number'],'index.php?do=register',2);
		}
		
		$check_username = trim ( $reg_username );
		if (empty ( $check_username )) {
			return $_lang ['username_is_empty'];
		}
		if (kekezu::k_strpos ( $check_username )) {
			return $_lang ['username_illegal'];
		}
		if (kekezu::check_user_by_name ( $check_username, 1 )) {
			return $_lang ['user_has_exist'];
		}
		if ($this->_sys_config ['user_intergration'] == 2) {
			$result = keke_user_class::user_checkname ( $check_username );
			if ($result == - 1) {
				return $_lang ['username_illegal'];
			} else if ($result == - 2) {
				return $_lang ['contain_allow_register_words'];
			} else if ($result == - 3) {
				return $_lang ['username_has_exist'];
			}
		} else if ($this->_sys_config ['user_intergration'] == 3) {
			$result = keke_user_class::user_checkname ( $check_username );
			if ($result == - 1) {
				return $_lang ['username_invalid'];
			} else if ($result == - 2) {
				return $_lang ['username_has_exist'];
			}
		}
		$limit_username = keke_user_class::user_getprotected ();
		if ($limit_username && in_array ( $check_username, $limit_username )) {
			return $_lang ['register_fail_limit_register'];
		}
		return true;
	}
	
	//检查email
	function check_email($reg_email) {
		global $_lang;
		$check_res = keke_user_class::user_checkemail ( $reg_email );
		if ($check_res == 1) {
			return true;
		} else if ($check_res == - 4) {
			return $_lang ['email_format_error'];
		} else if ($check_res == - 5) {
			return $_lang ['email_not_allow_register'];
		} else if ($check_res == - 6) {
			return $_lang ['email_not_allow_register'];
		}
	
	}
	
	//检查验证码
	function check_code($reg_code) {
		global $_lang;
		$img = new Secode_class ();
		$res_code = $img->check ( $reg_code, 1 );
		
		if (! $res_code) {
			$this->show_msg('验证码输入有误','',2);
		} else {
			return true;
		}
	}
	
	//检查ip重复注册
	function check_ip() {
		global $_lang;
		$check_time = time () - $this->_sys_config ['reg_limit'] * 60;
		$this->_space_obj->setWhere ( "reg_ip = '$this->_reg_ip' and $check_time< reg_time" );
		$res = $this->_space_obj->query_keke_witkey_space ();
		if ($res) {
			return $_lang ['this_IP_has_registered_notice'];
		} else {
			return true;
		}
	
	}
	
	//账号绑定
	public static function register_binding($oauth_user_info, $user_info, $type) {
		global $_lang;
		$csql = "select count(*) as c from %switkey_member_oauth where source='%s' and oauth_id ='%s'";
		$c = db_factory::get_one ( sprintf ( $csql, TABLEPRE, $type, $oauth_user_info ['account'] ) );
		if (intval ( $c ['c'] ) == 0) {
			$oauth_obj = new Keke_witkey_member_oauth_class ();
			$oauth_obj->setAccount ( $oauth_user_info ['name'] );
			$oauth_obj->setOauth_id ( $oauth_user_info ['account'] );
			$oauth_obj->setSource ( $type );
			$oauth_obj->setUid ( $user_info ['uid'] );
			$oauth_obj->setUsername ( $user_info ['username'] );
			$oauth_obj->setOn_time ( time () );
			$oauth_obj->create_keke_witkey_member_oauth () or $this->show_msg($_lang ['bind_fail'],'',2);
		} else {
			$this->show_msg($_lang ['this_user_has_bind'],'',2);
		}
		return true;
	}
	//绑定
	public static function is_oauth_bind($type, $oauth_id) {
		$sql = "select * from %switkey_member_oauth where source = '%s' and  oauth_id = '%s'";
		return db_factory::get_one ( sprintf ( $sql, TABLEPRE, $type, $oauth_id ) );
	}
	function show_msg($content,$url='',$status,$data=array()) {
		global $_lang,$_K;
		switch ($this->_reg_type) {
			case "2" ://mobile
				if(ISWAP==1){
					if($status==1){
						preg_match_all('/(http:\/\/.*)\'/U',$data['pic'],$m);
						if(intval ( $data ['g_level'] )==0){
							$data ['g_level']=1;
						}
						if(intval ( $data ['w_level'] )==0){
							$data ['w_level']=1;
						}
						
						$data = array ('uid' => $data ['uid'],
							 'username' => $data ['username'],
						     'email' => $data ['email'],
							 'phone' => $data ['mobile'],
							 'qq' => $data ['qq'],
							 'balance' => $data ['balance'],
						 	 'pic' => $m [1] [0],
						 	 'g_pic' => $_K['siteurl']."/".SKIN_PATH . "/theme/{$_K['theme']}/img/ico/g_level_" . intval ( $data ['g_level'] ). ".gif",
						 	 'w_pic' => $_K['siteurl']."/".SKIN_PATH . "/theme/{$_K['theme']}/img/ico/w_level_" . intval ( $data ['g_level'] ). ".gif" 
						);
					}else{
						$content = array('r'=>$content);
					}
				
				}
				kekezu::echojson($content,$status,$data);
				die ();
				break;
			default:
				kekezu::show_msg($_lang['operate_notice'],$url,2,$content,'success');
				break;
		}
	}
}