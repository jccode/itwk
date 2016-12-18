<?php
define ( 'EXPIRESIN', 'expires_in' );
class msn_oauth_client_class extends base_client_class {
	
	const AUTHURL = 'https://oauth.live.com/authorize?';
	const TOKENURL = 'https://oauth.live.com/token?';
	function __construct($app_key, $app_secret) {
		$this->_app_key = $app_key;
		$this->_app_secret = $app_secret;
		parent::__construct ( $app_key, $app_secret );
	}
	
	//获得授权链接的
	function get_auth_url($callback) {
		$callback = '/slogin/callback.asp?type=msn';//测试用
		return self::AUTHURL . 'client_id=' . $this->_app_key . '&response_type=code&redirect_uri=' . $callback;
	}
	
	//验证是否有授权
	function get_access_token() {
		return $_SESSION ['auth_msn'] ['last_key'];
	}
	
	//销毁授权
	function clear_access_token() {
		unset ( $_SESSION ['auth_msn'] );
	}
	
	//通过授权
	function create_access_token($oauth_verifier = false) {
		$code = $oauth_verifier ['code']; //用户授权页面返回的auth code
		if ($code) {
			$token_url = self::TOKENURL . 'client_id=' . $this->_app_key . "&redirect_uri=" . urlencode ( $oauth_verifier ['redirect_uri'] ) . "&client_secret=" . $this->_app_secret . '&code=' . $code . '&grant_type=authorization_code';
			$data = kekezu::curl_request ( $token_url, false );
			$data = json_decode ( $data );
			if ($last_key ['access_token']) {
				$_SESSION ['auth_msnren'] ['last_key'] = $last_key ['access_token'];
			} else {
				kekezu::error_handler ( 001, 'access_token不存在或者已过期' );
				return false;
			}
		} else {
			kekezu::error_handler ( 001, 'Authorization Code missing!' );
		}
		return true;
	}
	
	function get_login_info() {
		global $_K;
		$p = $_SESSION ['auth_renren'] ['last_key'];
		$graph_url = "https://api.renren.com/v2/user?access_token=" . $p ['access_token'];
		$data = json_decode ( file_get_contents ( $graph_url ) );
		if ($data) {
			$data = $this->user_data_format ( $data );
			var_dump ( $data );
			die ();
			return $data;
		} else {
			kekezu::error_handler ( 001, '用户数据获取失败！错误代码:' . $data ['msg'] );
			return false;
		}
		return true;
	}
	
	/**
	 * 返回值是 生成的微博的id(int)
	 */
	function post_wb($msg, $img) {
	
	}
	
	//用户数据格式化
	function user_data_format($data) {
		$r = array ();
		$data = $data ['data'];
		$r ['account'] = $data ['name'];
		$r ['id'] = $data ['id'];
		$r ["name"] = $data ['name'];
		$r ["location"] = $data ['addresses'];
		$r ['url'] = $data ['link'];
		$r ['sex'] = $r ['male'] == 1 ? '男' : $r ['female'] == 1 ? '女' : '保密';
		$r ['create_at'] = strtotime ( $data ['birth_year'] . '-' . $data ['birth_month'] . '-' . $data ['birth_day'] ); //创建的日期(时间戳)
		return $r;
	}
	
	//微博数据格式化
	function wb_data_format($data) {
		$r = array ();
		$r ['id'] = $data ['id'];
		$r ['text'] = $data ['origtext'];
		$r ['uid'] = $data ['name'];
		$r ['username'] = $data ['nick'];
		$r ['img'] = $data ['image'] [0] ? $data ['image'] [0] . '/120' : null;
		$r ['url'] = "http://t.qq.com/p/t/{$r['id']}";
		return $r;
	}
	
	function get_operate() {
		return true;
	}
	
	function get_client() {
		return true;
	}
}
