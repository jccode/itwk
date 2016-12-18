<?php
keke_lang_class::load_lang_class('keke_auth_bank_class');
class keke_auth_bank_class extends keke_auth_base_class {
	public static function get_instance($auth_code='bank') {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_auth_bank_class ( $auth_code );
		}
		return $obj;
	}
	
	public function __construct($auth_code='bank') {
		parent::__construct ($auth_code);
		$this->_primary_key = 'bank_a_id';

		$this->_tab_obj = keke_table_class::get_instance ( $this->_auth_table_name );
	}
	public static function get_auth_step($auth_step=null,$auth_info=array()){
		$step='step1';
		if ($auth_step)
			$step = $auth_step;
		elseif ($auth_info) {
			if($auth_info ['auth_status']){
				$step = "step4";
			}else{
				$auth_info[1] and $step="step4" or $step='step3';
			}
		}
		return $step;
	}
	/**
	 * 前台申请认证项目
	 * @param $data 外部传入认证数据
	 * @param $file_name 上传文件名 **请保持与数据库字段一致的命名
	 */
	public function add_auth($data, $file_name = '') {
		global $kekezu,$user_info,$_lang; 		
		$data = $this->format_auth_apply ( $data ); //格式化提交数据
		$data['pay_to_user_cash']='0';
		$data['user_get_cash']   ='0';
		$data['pay_time']   ='0';
		$data ['bank_name']&&$data ['bank_account'] or kekezu::show_msg ( "操作提示", $_SERVER ['HTTP_REFERER'], '1', $this->auth_lang() .$_lang['apply_fail_and_info_fail'], 'alert_error' ) ;
		$this->clear_auth_repeat(); 
		$success=$this->_tab_obj->save($data);
		if ($success) { //财务记录
			//认证收费。产生财务记录
			$data ['cash'] > 0 and keke_finance_class::cash_out ( $data ['uid'], $data ['cash'], $this->_auth_name, $data ['cash'], $this->_auth_name, $success );
			$data ['start_time'] == $data ['end_time'] and $end_time = $data ['end_time'] or $end_time = 0;
			$this->add_auth_record ( $data ['uid'], $data ['username'], $this->_auth_code, $end_time,$success); //添加进入认证记录
			
			kekezu::show_msg ( "操作提示","index.php?do=user&view=setting&op=auth&auth_code=bank&ver=1&auth_step=step3&show_id=".$success, '1', $this->auth_lang() . $_lang['apply_success_and_wait_audit'], 'alert_right' ) ;
		}
	}
	
	/**
	 *  清除重新认证数据
	 * */
	private function clear_auth_repeat(){
		global $kekezu,$user_info,$_lang; 
		
		db_factory::execute ( " delete from ". TABLEPRE ."witkey_auth_bank where uid='$user_info[uid]' and auth_status <> '1'");
	}
	
	/**
	 *  认证确认打款
	 * @param $auth_info 认证详细信息
	 * @param $pay_to_user_cash 管理打款金额
	 * @param  $user_get_cash 确认金额
	 */
	public function auth_confirm($auth_info,$user_get_cash){
		global $user_info,$kekezu,$_lang;
		$uid=$user_info['uid'];
		$username=$user_info['username'];
		$auth_id=$auth_info[$this->_primary_key];
		$ac_url="index.php?do=user&view=payitem&op=auth&auth_code=".$this->_auth_code."&ver=1&show_id=".$auth_id."#userCenter";
		
		$user_get_cash or kekezu::show_msg ( "操作提示", $ac_url, '1', $_lang['input_your_get_cash'], 'alert_error' ) ;
		
		$data['user_get_cash']=$user_get_cash;
		$pk[$this->_primary_key]=$auth_id;
		$this->_tab_obj->save($data,$pk);//用户确认打款]更新用户确认金额
		if (floatval ( $user_get_cash ) ==$auth_info['pay_to_user_cash']) {//输入金额符合
			$res=$this->set_auth_status($auth_id,'1');//更新认证状态
			$this->set_auth_record_status($uid,'1');//更新认证记录状态
			if ($res) {
				
				 //更改space认证状态(0待审核)
				$space_obj = new Keke_witkey_space_class();
				$space_obj->setUid($user_info['uid']); 
				$space_obj->setAuth_bank(1);
				$space_obj->edit_keke_witkey_space();
				 $feed_arr = array(	
			 		"feed_username"=>array("content"=>$username,"url"=>"index.php?do=shop&u_id=$uid "),
					"action"=>array("content"=>$_lang['have_passed'],"url"=>""),
					"event"=>array("content"=>$this->auth_lang(),"url"=>"")
			 	);
			 	/**
			 	 * 推广
			 	 */
			 	$kekezu->init_prom();
			 	$kekezu->_prom_obj->dispose_prom_event('auth','',$uid);
			 	
				kekezu::save_feed($feed_arr, $uid, $username,$this->_auth_name ); 
				kekezu::notify_user ( $this->auth_lang().$_lang['success'], $_lang['your'].$this->auth_lang().$_lang['have_passed_and_to'].'<a href="'.$ac_url.'">'.$_lang['auth_center'].'</a>'.$_lang['look_detail'], $uid, $username );
				kekezu::empty_cache();
				kekezu::show_msg ( "操作提示", $ac_url, '1', $this->auth_lang().$_lang['success'], 'alert_right' ) ;
			}
		} else {
			$res=$this->set_auth_status($auth_id,'2');//更新认证状态
			$this->set_auth_record_status($uid,'2');//更新认证记录状态
			kekezu::notify_user ($this->auth_lang().$_lang['fail'], $_lang['your'].$this->auth_lang().$_lang['no_pass_connect_kf'], $uid, $username );
			kekezu::show_msg ( "操作提示", $ac_url, '1', $_lang['input_cash_not_equal_admin_cash'].$this->auth_lang().$_lang['input_cash_not_equal_admin_cash'], 'alert_error' ) ;
		}
	}
}