<?php

/**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-7-6上午11:52:51
 */
keke_lang_class::load_lang_class ( 'keke_user_login_class' );
class keke_user_login_class {
	protected $_space_obj;
	protected $_login_uid;
	protected $_username;
	protected $_password;
	protected $_account;
	protected $_space_info;
	//protected $_pwd;
	protected $_auth_record_obj;
	protected $_auth_email_obj;
	protected $_sys_config;
	protected $_accout_type;
	public $_login_type;
	
	static function &get_instance() {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_user_login_class ();
		}
		return $obj;
	}
	
	function __construct() {
		//信息初始化
		global $kekezu;
		$this->_space_obj = new Keke_witkey_space_class ();
		$this->_auth_record_obj = new Keke_witkey_auth_record_class ();
		$this->_auth_email_obj = new Keke_witkey_auth_email_class ();
		$this->_sys_config = $kekezu->_sys_config;
	
		//用户登录	
	

	}
	function account_init($account) {
		$this->_account = $account;
	
		//CHARSET=='gbk' and $account = kekezu::utftogbk($account);
	

	}
	function password_init($password) {
		$this->_password = $password;
	}
	function login_type($login_type) {
		$this->_login_type = intval ( $login_type );
	}
	/**
	 * 用户登录
	 * @param string $account
	 * @param string $password
	 * @param string $code   验证码
	 * @param int  $login_type 0=普通登录,1=oauthlogin,3=头部登录
	 * @return string|multitype:unknown Ambigous <>
	 */
	function user_login($account, $password, $code = null, $login_type = 0) {
		global $_lang;
		($login_type == 3 and strtolower ( CHARSET ) == 'gbk') and $account = kekezu::utftogbk ( $account );
		$this->account_init ( $account );
		$this->password_init ( $password );
		$this->login_type ( $login_type );
		
		$code and $this->check_code ( $code );
		$accout_type = $this->get_login_type ();
		switch ($this->_sys_config ['user_intergration']) {
			case "1" :
				//判断登录方式
				switch ($accout_type) {
					case 'mobile' :
						$user_info = $this->valid_moble_auth ();
						break;
					case 'email' :
						$user_info = $this->valid_email_auth ();
						break;
					case 'username' :
						$user_info = $this->valid_username ();
						break;
				}
				if ($user_info ['password'] !== $password) {
					$this->show_msg ( '用户名或密码错误', 3 );
				} elseif ($user_info ['status'] == 2) {
					$this->show_msg ( $_lang ['account_has_freeze_or_unactivate'], 4 );
				} elseif ($user_info ['status'] == 3) {
					$this->show_msg ( $_lang ['account_has_not_excited'], 6 );
				}
				break;
			case "2" :
			case "3" :
				$accout_type != 'username' && $this->show_msg ( $_lang ['integrated_model_nust_use_name'], 5 );
				$user_info = $this->user_intergration ( $account, $password );
				break;
		}
		
		return $user_info;
	}
	
	//检查验证码
	function check_code($login_code) {
		global $_lang;
		if ($login_code) {
			$img = new Secode_class ();
			$login_code = $img->check ( $login_code, 1 );
			if (! $login_code) {
				$this->show_msg ( $_lang ['verification_code_input_error'], 7 );
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	//匹配登录方式      返回账号类型
	function get_login_type() {
		if (kekezu::is_email ( $this->_account )) {
			$this->_account_type = 'email';
		} elseif (kekezu::is_mobile ( $this->_account )) {
			$this->_account_type = 'mobile';
		} else {
			$this->_account_type = 'username';
		}
		return $this->_account_type;
	}
	
	//手机类型判断
	function valid_moble_auth() {
		global $_lang;
		//$sql = sprintf ( "select * from %switkey_auth_record where auth_code='mobile' and auth_status = 1", TABLEPRE, $this->_account );
		$sql = sprintf ( "select * from %switkey_auth_mobile where mobile='$this->_account' and auth_status=1", TABLEPRE );
		$auth_arr = db_factory::get_one ( $sql );
		if ($auth_arr) {
			$user_info = keke_user_class::get_user_info ( $auth_arr [uid] );
			$user_info [login_type] = 'mobile';
			return $user_info;
		} else {
			$this->show_msg ( $_lang ['no_tel_auth_not_login'], 5 );
		}
	}
	
	//邮箱类型判断
	function valid_email_auth() {
		global $_lang;
		$this->_auth_email_obj->setWhere ( "email  = '$this->_account' and auth_status=1" );
		$auth_info = $this->_auth_email_obj->query_keke_witkey_auth_email ();
		$auth_info = $auth_info [0];
		if ($auth_info) {
			$user_info = keke_user_class::get_user_info ( $auth_info [uid] );
			$user_info [login_type] = 'email';
			return $user_info;
		} else {
			$this->show_msg ( $_lang ['no_email_auth_not_login'], 5 );
		}
	
	}
	
	//账号登录判断
	function valid_username() {
		global $_lang;
		$user_info = kekezu::get_user_info ( $this->_account, 1 );
		if ($user_info) {
			$user_info ['login_type'] = 'username';
			return $user_info;
		} else {
			$this->show_msg ( '用户名或密码错误', 3 );
		}
	}
	
	//判断是否开启用户整合
	function user_intergration($username, $pwd) {
		global $_lang;
		//没有开用户整合
		switch ($this->_sys_config ['user_intergration']) {
			case 2 :
				$notify = "UCenter";
				require_once S_ROOT . './keke_client/ucenter/client.php';
				//$pwd 为非MD5
				$uc_info = uc_user_login ( $username, $pwd );
				if ($uc_info ['0'] > 0) {
					$u = array ('uid' => $uc_info ['0'], 'username' => $uc_info ['1'], 'email' => $uc_info ['3'] );
				} else {
					$u = $uc_info ['0'];
				}
				break;
			case 3 :
				
				$notify = "PW";
				require_once (S_ROOT . './keke_client/pw_client/uc_client.php');
				//$PWD  
				$uc_info = uc_user_login ( $username, $pwd, 0 );
				if ($uc_info ['status'] != 1) {
					
					$u = $uc_info ['status'];
				} else {
					$u = array ('uid' => $uc_info ['uid'], 'username' => $uc_info ['username'], 'email' => $uc_info ['email'] );
				}
				
				break;
		}
		
		if ($u == - 2) {
			$this->show_msg ( '用户名或密码错误', 3 );
		} elseif ($u == - 1) {
			$this->show_msg ( '用户名或密码错误', 4 );
		} else {
			$exists = db_factory::get_count ( sprintf ( " select uid from %switkey_member where uid='%d' ", TABLEPRE, $u ['uid'] ) );
			if (! $exists) { //与三方用户UID冲突
				$reg_obj = new keke_register_class ();
				$reg_obj->_reg_pwd = $pwd;
				$reg_obj->save_userinfo ( $u ['username'], $u ['email'], $u ['uid'] );
			}
		}
		
		//is_array($u) and $user_info = kekezu::get_user_info($u['uid']);
		

		return $u;
	}
	
	//保存用户信息
	public function save_user_info($user_info, $ckb_cookie = 1, $login_type = 0, $oauth_login = 1) {
		global $kekezu, $_K, $type;
		global $_lang;
		
		if (! $user_info)
			return false;
		
		$_SESSION ['uid'] = $user_info ['uid'];
		$_SESSION ['username'] = $user_info ['username'];
		$_SESSION ['brand'] = $user_info ['brand'];
		/*	$_SESSION [$uid."_".$kekezu->_sys_config[website_url]] = 1;*/
		$oauth_login = intval ( $oauth_login );
		$login_type = $this->_login_type;
		if (isset ( $ckb_cookie ) && $ckb_cookie == 1) {
			setcookie ( 'epautologin', keke_encrypt_class::encode ( $user_info ['uid'] ), time () + 604800, COOKIE_PATH, COOKIE_DOMAIN );
		}
		
		if ($_K ['refer']) {
			$r = $_K ['refer'];
		} else {
			$r = 'index.php';
		}
		if ($this->_sys_config ['user_intergration'] != 1) { //整合之后的  获取同步登录的js 
			$synhtml = keke_user_class::user_synlogin ( $user_info ['uid'], $this->_password );
		}
		$synhtml = isset ( $synhtml ) ? $synhtml : "";
		if ($_K ['refer'] == 'shop') {
			if ($user_info ['shop_id']) {
				$r = 'index.php?do=shop&sid=' . $user_info ['shop_id'];
				$synhtml .= ',现在调往商品页面';
			} else {
				$r = 'index.php?do=user&&view=space';
				$synhtml .= ',请先前往用户中心完善资料后再开通店铺';
			}
		}
		$user_obj = new Keke_witkey_space_class ();
		$user_obj->setLast_login_time ( time () );
		$user_obj->setWhere ( "uid = '{$user_info['uid']}'" );
		$user_obj->edit_keke_witkey_space ();
		db_factory::execute ( sprintf ( "update %switkey_member_oltime set last_op_time=%d where uid = %d", TABLEPRE, time (), $user_info ['uid'] ) );
		if ($login_type == 1) {
			if (strtolower ( $_SERVER ['REQUEST_METHOD'] ) == 'post') {
				kekezu::show_msg ( $_lang ['notice_message'], $r, 1, $_lang ['login_success'] . "$synhtml", "alert_right" );
			} elseif (strtolower ( $_SERVER ['REQUEST_METHOD'] ) == 'get') {
				echo "$synhtml<script>window.location.href='$r';</script>";
				die ();
			}
		
		} else if ($login_type == 3 || $login_type == 4) {
			$info = $user_info;
			$return_info ['uid'] = $info ['uid'];
			$return_info ['username'] = $info ['username'];
			$return_info ['email'] = $info ['email'];
			$return_info ['mobile'] = $info ['mobile'];
			$return_info ['qq'] = $info ['qq'];
			$return_info ['balance'] = intval ( $info ['balance'] );
			$return_info ['credit'] = intval ( $info ['credit'] );
			$return_info ['pic'] = keke_user_class::get_user_pic ( $user_info ['uid'] );
			$return_info ['syn'] = "$r";
			if ($synhtml) {
				preg_match_all ( "/http(.*)\"/iU", $synhtml, $sys );
				$uc_url = rtrim ( $sys [0] [0], '"' );
				$return_info ['sys'] = $uc_url;
			}
			($user_info ['uid'] == ADMIN_UID || $user_info ['group_id'] > 0) and $return_info ['is_admin'] = 1;
			$return_info ['w_level'] = $info ['w_level'];
			$return_info ['g_level'] = $info ['g_level'];
			$this->show_msg ( $_lang ['login_success'], 1, $return_info );
			//kekezu::echojson ( $_lang ['login_success'], 1, $return_info );
			die ();
		
		} else {
			$oauth_login == 1 and $r = "index.php?do=oauth_login&type=" . $type . "&step=step2&id=" . $user_info ['uid'];
			echo "$synhtml<script>window.location.href='$r';hideWindow('oauth_login_frm1')</script>";
		}
	}
	function show_msg($content, $status, $data = array()) {
		global $_K, $_lang;
		switch ($this->_login_type) {
			case "3" :
				kekezu::echojson ( $content, $status, $data );
				die ();
				break;
			case "4" :
				if (ISWAP == 1) {
					if ($status == 1) {
						preg_match_all ( '/src=\'(.*)\'/iU', $data ['pic'], $m );
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
					} else {
						$content = array ('r' => $content );
					}
				}
				kekezu::echojson ( $content, $status, $data );
				break;
			case "0" :
			case "1" :
				kekezu::echojson ( $content, 5 );
				die ();
				break;
			case 2 : //oauth
				kekezu::show_msg ( '操作提示', $_SERVER ['HTTP_REFERER'], 3, $content, 'warning' );
				break;
		}
	}
	// 检测用户状态
	function check_status($user_info) {
		global $_lang;
		
		if ($user_info ['status'] == 2) {
			unset ( $_SESSION ['uid'] );
			setcookie ( 'epautologin', '', 0, COOKIE_PATH, COOKIE_DOMAIN );
			kekezu::show_msg ( '操作提示', '/', 3, $_lang ['account_has_freeze_or_unactivate'], 'warning' );
		} elseif ($user_info ['status'] == 3) {
			unset ( $_SESSION ['uid'] );
			setcookie ( 'epautologin', '', 0, COOKIE_PATH, COOKIE_DOMAIN );
			kekezu::show_msg ( '操作提示', '/', 3, $_lang ['account_has_not_excited'], 'warning' );
		}
	}

}