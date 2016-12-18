<?php
keke_lang_class::load_lang_class('keke_msg_class');
class keke_msg_class {
	public $_key;
	public $_k;
	public $_v=array("send_sms"=>null,"send_email"=>null,"send_mobile_sms"=>null);
	public $_title;
	public $_config;
	public $_uid;
	public $_username;
	public $_sitename;
	public $_sms_content;
	public $_email_content;
	public $_normal_content;
	public $_mobile_content;
	public $_email;
	public $_mobile;
	public $_basicconfig;
	 
	function __construct() { //构造方法
		$this->basic_init ();
	}
	
	function basic_init() {
		global $_lang;
		global $basic_config;
		$this->_basicconfig = $basic_config;
		$this->_key = array ();
		$this->_title = $_lang['msg_notice'];
		$this->_k = "";
		$this->_sitename = $basic_config ['website_name'];
	}
	function config_init($k) {
		$this->_k = $k;
		$this->_config = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_msg_config where k='$k'" );
		$this->_v = unserialize ( $this->_config ['v'] );
	}
	function setUid($uid) {
		$this->_uid = $uid;
	}
	function setUsername($username) {
		$this->_username = $username;
	}
	function setTitle($title) {
		$this->_title = $title;
	}
	/** Aaron add start **/
	function setSms_content($sms_content) {
		$this->_sms_content = $sms_content;
	}
	function setEmail_content($email_content) {
		$this->_email_content = $email_content;
	}
	function setMobile_content($mobile_content) {
		$this->_mobile_content = $mobile_content;
	}
	/** Aaron add end **/
	function setEmail($email) {
		$this->_email = $email;
	}
	function setValue($key, $value) {
		$this->_key [$key] = $value;
	}
	function setMobile($str_mobile) {
		$this->_mobile = $str_mobile;
	}
	/**
	 * 
	 * 验证并且指定模板
	 * @param unknown_type $k
	 */
	function validate($k) {
		$this->config_init ( $k );
		if (array_sum ( $this->_v ) <= 0) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 手机短信。请传递手机号码
	 * Enter description here ...
	 * @param unknown_type $str_mobile
	 */
	function send() {
		if (! $this->_uid) {
			return false;
		}
		if (! $this->validate ( $this->_k )) {
			return false;
		}
		$this->pregmessage ( $this->_k );
		$sum = array_sum ( $this->_v );

		switch ($sum) {
			case 1 :		
				($this->_v ['send_sms'] == 1) and $this->sendmessage ();
				(isset($this->_v ['send_email'])&& $this->_v ['send_email']== 1) and $this->sendmail ();
				(isset($this->_v ['send_mobile_sms'])&& $this->_v ['send_mobile_sms'] == 1) and $this->send_phone_sms ();
				break;
			case 2 :
				(($this->_v ['send_sms'] == 1 && $this->_v ['send_email'] == 1)) and ($this->sendmessage () or $this->sendmail ());
				(($this->_v ['send_sms'] == 1 && $this->_v ['send_mobile_sms'] == 1)) and ($this->sendmessage () or $this->send_phone_sms ());
				(($this->_v ['send_mobile_sms'] == 1 && $this->_v ['send_email'] == 1)) and ($this->sendmail () or $this->send_phone_sms ());
				break;
			case 3 :
				$this->sendmessage ();
				$this->sendmail ();
				$this->send_phone_sms ();
				break;
		}
		$this->_key = array ();
	}
	/**
	 * 获取消息模板
	 * @tips   长度为2的数组
	 * -->   有些消息可能开启的手机短息。所以这些消息类型的模板有2个
	 */
	private function getmessagetpl() {
		global $_cache_obj;
		$tpl = $_cache_obj->get ( "msg_tpl_" . $this->_k . "_cache" );
		if (! $tpl) {
			$msg_obj = new Keke_witkey_msg_tpl_class ();
			$msg_obj->setWhere ( "tpl_code='$this->_k' order by send_type " );
			$tpl = $msg_obj->query_keke_witkey_msg_tpl ();
			$_cache_obj->set ( "msg_tpl_" . $this->_k . "_cache", $tpl );
		}
		return $tpl;
	}
	
	private function pregmessage() {
		$tpl = $this->getmessagetpl ();
		$open_phone_sms = sizeof ( $tpl ) - 1;
		
		switch ($open_phone_sms > 0) { //结果为0.说明没开启过手机短息模板。
			case 0 :
				$cont = $tpl [0] ['content'];
				/** Aaron update start **/				
				if( $this->_email_content ) {
					$this->_normal_content = $this->_email_content;
				}else {
					$this->_normal_content = $this->tpl_format ( $cont );
				}
				//$this->_normal_content = $this->tpl_format ( $cont );
				/** Aaron update end **/
				// var_dump($this->_normal_content);exit;
				break;
			case 1 :
				if (! $this->_username) {
					$userinfo = kekezu::get_user_info ( $this->_uid );
					$this->_username = $userinfo ['username'];
				}
				$cont0 = $tpl [0] ['content'];
				$cont0 = $this->tpl_format ( $cont0 );
				$this->_normal_content = $cont0;
				
				if (! empty ( $tpl [1] )) {
					$cont1 = $tpl [1] ['content'];
					$cont1 = $this->tpl_format ( $cont1 );
					$this->_mobile_content = $cont1;
				}
			break;
		}
	}
	
	 //模板解析
	public function tpl_format($content) {
		global $_lang;
		$this->_username and $cont = str_replace ( '{用户名}', $this->_username, $content );
		$this->_uid and $cont = str_replace ( '{用户ID}', $this->_uid, $cont );
		$this->_sitename and $cont = str_replace ( '{网站名称}', $this->_sitename, $cont );
		foreach ( $this->_key as $k2 => $v2 ) {
			$cont = str_replace ( '{' . $k2 . '}', $v2, $cont );
		}
		return $cont;
	}
	
	private function sendmessage() {
		$message_obj = new Keke_witkey_msg_class ();
		$message_obj->setTitle ( kekezu::k_input( $this->_title) );
		$message_obj->setContent (keke_base_class::filter_input($this->_normal_content )); 
		$message_obj->setTo_uid ( $this->_uid );
		$message_obj->setTo_username ( $this->_username );
		$message_obj->setOn_time ( time ( 'now()' ) );
		$message_obj->create_keke_witkey_msg ();
	}
	
	public function log_messqueue($type,$title,$contents,$targetno){
		$messq_obj = new Keke_messqueue_class();
		$messq_obj->setMessagetype($type);
		$messq_obj->setTitle( kekezu::k_input($title));
		$messq_obj->setContents( kekezu::k_input($contents));
		$messq_obj->setIntime(time());
		$messq_obj->setTargetno($targetno);
		$messq_obj->create_keke_messqueue();
		
	}
	public function sendmail() { 
		global $_K;
		if (! $this->_email || ! $this->_username) {
			$userinfo = kekezu::get_user_info ( $this->_uid );
			
			$this->_username = $userinfo ['username'];
			$this->_email = $userinfo ['email'];
		}
		if (! $this->_email) {
			return false;
		}
		$this->_basicconfig and $basicconfig = $this->_basicconfig or $basicconfig = kekezu::get_config ( 'basic' );
		//echo var_dump($this->_basicconfig);exit;
		/** Aaron add  **/
		$this->_email_content and $this->_normal_content = $this->tpl_format ( $this->_email_content );
			
		if ($basicconfig ['mail_server_cat'] == 'mail') {
			if ($basicconfig ['post_account'] && $basicconfig ['mail_replay'] && $this->_email && $this->_title && $this->_normal_content) {
				$hearer = "From:{$basicconfig['post_account']}\nReply-To:{$basicconfig['mail_replay']}\nX-Mailer: PHP/" . phpversion () . "\nContent-Type:text/html";
				mail ( $this->_email, $this->_title, $this->_normal_content, $hearer );
			}
		} else if ($basicconfig ['smtp_url'] && $basicconfig ['mail_server_port'] && $basicconfig ['post_account'] && $basicconfig ['account_pwd'] && $basicconfig ['website_name']) {
			
			kekezu::send_mail ( $this->_email, $this->_title, $this->_normal_content );
		}
		
		$this->log_messqueue('email',$this->_title,$this->_normal_content,$this->_email);
	}
	/**
	 * 手机短信发送
	 *
	 * @param 手机号码 $str_mobile
	 * @param 短信内容 $content
	 * @return unknown
	 */
	public function send_phone_sms( $mobile_arr = '', $tar_content = '') {
			include_once S_ROOT . '/keke_client/sms/postmsg.php';
			global $kekezu;
			$account_info = $kekezu->_sys_config; //手机账号信息
			$mobile_u = $account_info ['mobile_username'];
			$mobile_p = $account_info ['mobile_password'];
			$this->_mobile and $mobile = $this->_mobile or $mobile = $mobile_arr;
			$this->_mobile_content and $content = $this->_mobile_content or $content = $tar_content;
			if(is_array ( $mobile ) ){

				$res = Msg_PostBlockNumber ( $mobile_u, $mobile_p, $mobile, $content, '' );
				
			}else{
				
				$res = Msg_PostSingle ( $mobile_u, $mobile_p, $mobile, $content, '' );
			}
			
		 	//is_array ( $mobile ) and $res = Msg_PostBlockNumber ( $mobile_u, $mobile_p, $mobile, $content, '' ) or $res = Msg_PostSingle ( $mobile_u, $mobile_p, $mobile, $content, '' );
		 	$this->log_messqueue('sms',$this->_title,$this->_normal_content,$this->_mobile);
		 	return Desc_ReturnInfo ( $res );
 
	}
	/**
	 * 短信发送
	 * @param int $uid
	 * @param string $username
	 * @param string $action
	 * @param string $title
	 * @param array $v_arr
	 * @param string $email
	 * @param string $mobile
	 */
	public function send_message($uid, $username, $action, $title, $v_arr = array(), $email = null, $mobile = null) {
	
		if ($this->validate ( $action )) {
		
			$this->setUid ( $uid );
			$this->setUsername ( $username );
			$this->setEmail ( $email );
			$this->setMobile ( $mobile );
			foreach ( $v_arr as $k => $v ) {
				$this->setValue ( $k, $v );
			}
			$this->setTitle ( $title );
			$this->send ();
		}
	
	}
	
	/**
	 * 站内消息发送函数
	 * @param int $to_uid 消息接受方
	 * @param string $to_username
	 * @param string $tar_content 内容
	 * @param string $url    操作提示链接  具体参见 kekezu::keke_show_msg
	 * @param string $output 消息输出方式 具体参见 kekezu::keke_show_msg
	 */
	public static function send_private_message($title, $tar_content, $to_uid, $to_username, $url = '', $output = 'normal') {
		global $uid, $username;
		global $_lang;
		if (CHARSET == 'gbk') {
			$title = kekezu::utftogbk ( $title );
			$tar_content = kekezu::utftogbk ( $tar_content );
			$to_username = kekezu::utftogbk ( $to_username );
		}
		
		//$tar_content and $tar_content = $this->tpl_format($tar_content);		
		
		$msg_obj = new Keke_witkey_msg_class ();
		$msg_obj->_msg_id = null;
		$msg_obj->setUid ( $uid );
		$msg_obj->setUsername ( $username );
		$msg_obj->setTitle ( kekezu::k_input($title) );
		$msg_obj->setTo_uid ( $to_uid );
		$msg_obj->setTo_username ( $to_username );
		$msg_obj->setContent ( kekezu::k_input($tar_content ));
		$msg_obj->setOn_time ( time () );
		$msg_id = $msg_obj->create_keke_witkey_msg ();
		
		$msg_id and kekezu::keke_show_msg ( $url, $_lang['sms_send_success'], "", $output ) or kekezu::keke_show_msg ( $_lang['operate_notice'], $url, $_lang['sms_send_fail'], "error", $output );
	}
}