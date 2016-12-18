<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-8-31 09:58:34
 * @property 手机认证类
 * 
 */
keke_lang_class::load_lang_class('keke_auth_mobile_class');
class keke_auth_mobile_class extends keke_auth_base_class{
	
	public static function get_instance($auth_code='mobile') {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_auth_mobile_class($auth_code);
		}
		return $obj;
	}
	
	/**
	 * 手机认证没建表，认证信息保存在record表的ext_data字段
	 * 初始化表信息 
	 * @param string $auth_code
	 * @return void() 
	 */
	public function __construct($auth_code='mobile') {
		parent::__construct($auth_code);
		$this->_primary_key     ='mobile_a_id';
		$this->_tab_obj         =keke_table_class::get_instance($this->_auth_table_name);
	}
	public static function get_auth_step($auth_step=null,$auth_info=array()){
		$step='step1';
		if($auth_step){ 
			$step=$auth_step;
		}elseif($auth_info){
			$auth_info['auth_status'] and $step="step3" or $step="step2";
		}
		return $step;
	}
	/**
	 * 前台申请认证项目
	 * @param $data 外部传入认证数据
	 * @param $file_name 上传文件名 **请保持与数据库字段一致的命名
	 */
	public function add_auth($data,$file_name = '') {
		global $kekezu,$user_info,$_lang;
	
		$moblie_obj = new Keke_witkey_auth_mobile_class();
		
		$fdata=$this->format_auth_apply($data);//格式化提交数据
		

		if (!$fdata ['mobile']) {
			kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', $this->auth_lang().$_lang['apply_submit_fail_for_info_little'], 'alert_error' ) ;
		} 
		
		//6位数的手机应证码
		
		$valid_code = kekezu::randomkeys(6);
		$fdata['valid_code'] = $valid_code;
		$fdata[auth_time]=time();//认证时间 
		$msg_obj = new keke_msg_class(); 
		 //$_lang['mobile_auth_code'].           {$kekezu->_sys_config[website_name]} .$_lang['from'].$kekezu->_sys_config[website_name]
		if(preg_match('/^09\d{8}$/', $fdata ['mobile'])||preg_match('/^(00886)?[-| ]?0?9\d{8}$/', $fdata ['mobile'])){
		    $msg="為了您的帳號安全，請務將驗證碼輕易告訴他人";
		}else{
			$msg='为了您的账号安全，请勿将验证码轻易告诉他人';
		}
		$content = $fdata['valid_code']."（".$msg."）";
		
		//print_r($content);exit;
		if($_SESSION['brand']=='tw'){
			//code为对方判断我方身份标识，mobile为客户手机号，msg为发送过去的信息，对方程式会直接echo讯息，1 ：成功 2： 失败 3 ：暂停服务(暂定)
			$params=array(
					'code'=>'uRe5h7trAr',
					'msg'=>$content,
					'mobile'=>$fdata['mobile']
					);
			$msg_res=kekezu::do_post_request("https://www.songogo.com/ecpay/epweike_api.php",$params);
		}else{
			//发送手机应证码
			$msg_res = $msg_obj->send_phone_sms($fdata['mobile'],$content);
		}

		if(strpos($msg_res,'发送成功')!==false||$msg_res=='1'){
			$auth_info = db_factory::get_one(sprintf("select * from %switkey_auth_mobile where uid='%d'",TABLEPRE,$user_info[uid]));
			if($auth_info){//修改数据
				$moblie_obj->setWhere('uid='.$fdata[uid]);
				$moblie_obj->setMobile($fdata[mobile]);
				$moblie_obj->setValid_code($fdata[valid_code]);
				$moblie_obj->edit_keke_witkey_auth_mobile();
			}else{//生成手机认证数据
				$moblie_obj->setUid($fdata[uid]);
				$moblie_obj->setMobile($fdata[mobile]);
				$moblie_obj->setValid_code($fdata[valid_code]);
				$moblie_obj->setUsername($fdata[username]);
				$moblie_obj->setCash($fdata[cash]);
				$moblie_obj->setAuth_time($fdata[auth_time]);
				$moblie_obj->setAuth_status(0);
				$moblie_obj->create_keke_witkey_auth_mobile();
			}
			db_factory::execute(" update ".TABLEPRE."witkey_space set mobile='$fdata[mobile]' where uid = '$fdata[uid]' ");
			db_factory::execute(" update ".TABLEPRE."witkey_member set mobile='$fdata[mobile]' where uid = '$fdata[uid]' ");
			$fdata['start_time']==$fdata['end_time'] and $end_time=$fdata['end_time'] or $end_time=0;
			$res = $this->add_auth_record($fdata['uid'], $fdata['username'], $this->_auth_code,$end_time,0);//添加进入认证记录

			kekezu::show_msg ( "操作提示", "index.php?do=user&view=setting&op=auth&auth_code=mobile&auth_step=step2", '1', $this->auth_lang().$_lang['apply_submit_fail_and_get_code'], 'alert_right' ) ;
		  	//kekezu::show_msg ( $this->auth_lang().$_lang['apply_submit_success'],$url, 3, $this->auth_lang().$_lang['apply_submit_fail_and_get_code'],'success' ) ;
		} else{
			kekezu::show_msg ( "操作提示","index.php?do=user&view=setting&op=auth&auth_code=mobile&ver=1&auth_step=step1", '1', $this->auth_lang()."验证码发送失败，请重试!", 'alert_error' ) ;
		}
		
		
	}
	/**
	 * 验证用户输入的手机验证码，输入正确，auth_status =1 ,并跳转到下一步
	 * @param array $data
	 * @return void();
	 */
	public function valid_auth($data){
		
		global $user_info,$_lang,$kekezu;
		$uid = $user_info['uid'];
		//获取手机认证提交的信息
		$mobile_obj = new Keke_witkey_auth_mobile_class();
		$mobile_obj->setWhere('uid='.$user_info[uid]);
		$mobile_info = $mobile_obj->query_keke_witkey_auth_mobile();
		$mobile_info = $mobile_info[0];
		$valid_code = $mobile_info[valid_code];
		
		if($mobile_info['fail'] >= 3){
			$mobile_obj->setWhere('uid='.$user_info[uid]);
			$res = $mobile_obj->del_keke_witkey_auth_mobile();
			kekezu::show_msg ( "操作提示","index.php?do=user&view=setting&op=auth&auth_code=mobile", '1', $this->auth_lang().$_lang['code_error'], 'alert_error' ) ;
		}
		
		if($data['valid_code'] == $valid_code){
			//认证判定时间（认证成功或者失败）
			$end_time = time();
			//认证记录表生成数据
			$this->set_auth_record_status($mobile_info['uid'], 1);
			//生成手机认证成功的时间
			$mobile_obj->setWhere('uid='.$user_info[uid]);
			$mobile_obj->setEnd_time($end_time);
			$mobile_obj->setAuth_status(1);
			$res2 = $mobile_obj->edit_keke_witkey_auth_mobile();
	
			if($res2){
				
				$space_obj = new Keke_witkey_space_class();
				$space_obj->setUid($user_info['uid']); 
				$space_obj->setAuth_mobile(1);
				$space_obj->edit_keke_witkey_space();
			
			 	/**
			 	 * 推广
			 	 */
			 	$kekezu->init_prom();
			 	$kekezu->_prom_obj->dispose_prom_event('auth','',$uid);
			 	
				kekezu::empty_cache();
				kekezu::show_msg ( "操作提示", "index.php?do=user&view=setting&op=auth&auth_code=mobile&ver=1&auth_step=step3", '1', $this->auth_lang().$_lang['auth_audit_success'], 'alert_right' ) ;
			}
		}else{
			$mobile_obj->setWhere('uid='.$user_info[uid]);
			$mobile_obj->setFail(($mobile_info['fail']+1));
			$mobile_obj->edit_keke_witkey_auth_mobile();
			kekezu::show_msg ( "操作提示","index.php?do=user&view=setting&op=auth&auth_code=mobile&ver=1&auth_step=step2", '1', $this->auth_lang().$_lang['code_error'], 'alert_error' ) ;
		}
	}
 
}