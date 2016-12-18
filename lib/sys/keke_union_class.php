<?php
/**
 * @todo 联盟任务处理类
 * @author H.R.
 * task_union:
 * 1：站长推广任务
 * 2：站长获取任务
 * 3：用户发布任务
 */
require_once S_ROOT . '/keke_client/keke/keke_tool_class.php';
require_once S_ROOT . '/keke_client/keke/keke_service_class.php';
require_once S_ROOT . '/keke_client/keke/config.php';

class keke_union_class {
	private $_task_id; //任务id
	private $_model_id; //模型id
	public $_r_task_id; //联盟id
	private $_model_code; //模型code
	private $_uid;
	private $_config; //配置
	private $_data; //回调响应数组
	

	function __construct($task_id, $data = array()) {
		global $config;
		self::doExit ( $config ['application'] );
		if (! empty ( $task_id )) {
			$this->_config = $config;
			$this->_task_id = intval ( $task_id );
			$this->init_task ( $task_id ); //初始化联盟任务相关数据
		}
		$this->_data = $data;
	}
	private function init_task($task_id = '') {
		if (! $this->_task_id && $task_id) {
			$this->_task_id = $task_id;
		}
		$sql = "select `task_id`,`model_id`,`uid`,`task_union`,`r_task_id` from `%switkey_task` where task_id=%d";
		$result = db_factory::get_one ( sprintf ( $sql, TABLEPRE, $this->_task_id ) );
		if (! $result || ! $result ['task_union']) { //如果不是联盟任务,返回.
			return false;
		}
		$this->_model_id = $result ['model_id'];
		$this->_r_task_id = $result ['r_task_id'];
		$this->_model_code = $this->get_model_code ();
		$this->_uid       = $result['uid'];
	
	}
	/**
	 * 联盟socket请求
	 * @param  $service 接口类型
	 * @param  $comm_data 传递参数
	 * @param  $return_type 返回类型 url/form
	 * @param  $method 提交方式post
	 * @param  $sign_type 签名类型
	 * @param  $_input_charset 字符编码
	 */
	public static function union_request($service, $comm_data = array(), $return_type = 'url', $method = 'post', $sign_type = 'MD5', $_input_charset = 'GBK') {
		global $config;
		self::doExit ( $config ['application'] );
		$request = keke_tool_class::union_build ( $config, $service, $comm_data, $return_type, $method, $sign_type, $_input_charset );
		kekezu::get_remote_data ( $request );
	}
	/**
	 * 创建联盟任务
	 * @param int $task_id 任务信息
	 * @param boolean $is_return 是否回调
	 * 
	 */
	static function create_task($task_id, $is_return = false, $data = array(), $indetify = 1, $is_pub = false, $type = 'form') {
		global $config, $kekezu, $_K;
		self::doExit ( $config ['application'] );
		switch ($is_return) {
			case false :
				if(!$is_pub&&$indetify==2&&!$config['auto_commit']){//非发布的后台提交，且未开启自动提交，
					return false;
				}
				//如果任务信息数组
				if (is_array ( $task_id )) {
					$task_info = $task_id;
				} elseif (is_numeric ( $task_id )) {
					$sql = "select `task_id`,`model_id`,`task_cash_coverage`,`task_file`,`task_cash`,`task_title`,`task_desc`,`task_status`,`uid`,`username`,`start_time`,`sub_time` from %switkey_task where task_id=%d and task_union=0";
					$task_info = db_factory::get_one ( sprintf ( $sql, TABLEPRE, intval ( $task_id ) ) );
					if (! $task_info) {
						return false;
					}
				}
				$model_code = $kekezu->_model_list [$task_info ['model_id']] ['model_code'];
				$task_info ['task_cash_coverage'] and $task_info ['cash_coveage'] = self::get_cash_cove ( $model_code, $task_info ['task_cash_coverage'] );
				$task_info ['task_uid'] = $task_info ['uid'];
				$task_info ['task_owner'] = $task_info ['username'];
				$task_info ['outer_task_id'] = "{$config['log']}-{$model_code}-{$task_info['task_id']}";
				$task_info ['task_amount'] = $task_info ['task_cash'];
				if ($task_info ['task_file']) {
					$files = db_factory::query ( 'select CONCAT("' . $_K ['siteurl'] . '/",`save_name`) file,`file_name` from ' . TABLEPRE . 'witkey_file where file_id in (' . $task_info ['task_file'] . ')' );
					if ($files) {
						$file = '';
						foreach ( $files as $v ) {
							$file .= $v ['file_name'] . '#' . $v ['file'] . ',';
						}
						$files = rtrim ( $file, ',' );
						$task_info ['task_file'] = $files;
					}
				}
				$task_info ['indetify'] = $indetify;
				if ($is_pub) {
					$releation_id = db_factory::get_count ( ' select union_rid from ' . TABLEPRE . 'witkey_space where uid=' . $task_info ['task_uid'] );
					$task_info ['is_pub'] = $is_pub;
					$task_info ['releation_id'] = $releation_id;
					//更改至关联
					db_factory::execute('update '.TABLEPRE.'witkey_space set union_assoc=1 where uid='.$task_info ['task_uid']);
				}
				$inter = 'create_task'; //对应接口
				$request = keke_tool_class::union_build ( $config, $inter, $task_info, $type );
				return $request;
				break;
			case true :
				$response = array ();
				$url = $_K ['siteurl'] . "/index.php?do=task&task_id=" . $data ['task_id'];
				$response ['url'] = $url;
				switch ($data ['is_success']) {
					case "T" : //成功响应
						$data ['is_pub'] == 2 and $task_union = 3 or $task_union = 1;
						$sql = sprintf ( " update %switkey_task set r_task_id ='%d',task_union=%d where task_id='%d'", TABLEPRE, $data ['r_task_id'], $task_union, $data ['task_id'] );
						$res = db_factory::execute ( $sql );
						$response ['type'] = "success";
						$response ['notice'] = "联盟任务发布成功";
						break;
					case "F" :
						$response ['type'] = "error";
						$response ['notice'] = "联盟任务发布失败";
						break;
				}
				return $response;
				break;
		}
	}
	
	/**
	 * 任务结束结算
	 * @param array $data  数据
	 * [bid_uid,indetify]
	 */
	
	public function task_close($data = array(), $is_return = false, $resp = array()) {
		global $config;
		self::doExit ( $config ['application'] );
		switch ($is_return) {
			case false :
				$comm_data = array ();
				$inter = 'task_close'; //对应接口
				$url = keke_tool_class::union_build ( $config, $inter, $data );
				kekezu::get_remote_data ( $url );
				//清除关联状态
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set `union_user`=0,`union_assoc`=0,`union_rid`=0 where uid=' . $this->_uid );
				break;
			case true :
				switch ($resp ['indetify']) {
					case 1 : //任务成功结束
						db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set task_status=8 where r_task_id=' . $resp ['r_task_id'] );
						break;
					case - 1 : //任务异常关闭
						db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set task_status=9 where r_task_id=' . $resp ['r_task_id']);
						break;
				}
		}
	}
	/**
	 * 推广发布定向
	 * //localhost/get/?apu=2
	 */
	public static function pub_redirect($app_uid) {
		global $kekezu, $config;
		self::doExit ( $config ['application'] );
		$service = 'keke_login';
		$comm_data = array ('from_uid' => $kekezu->_uid, 'from_username' => $kekezu->_username, 'login_type' => 2, 'app_uid' => $app_uid );
		$jump_url = keke_tool_class::union_build ( $config, $service, $comm_data );
		self::jump ( $jump_url );
	}
	/**
	 * union查看任务->跳转至对应的目标页面
	 * @param $r_task_id 对应的联盟任务id
	 */
	public function view_task() {
		global $uid, $username;
		$r_task_id = $this->_r_task_id;
		if (! $r_task_id) {
			return false;
		}
		$inter = 'keke_login';
		$comm_data = array ('r_task_id' => intval ( $r_task_id ), 'from_uid' => $uid, 'from_username' => $username );
		$jump_url = keke_tool_class::union_build ( $this->_config, $inter, $comm_data );
		self::jump ( $jump_url );
	}
	
	/**
	 * 获取金额区间
	 */
	static function get_cash_cove($model_code, $rule_id) {
		global $kekezu;
		$cove_arr = $kekezu->get_cash_cove ( $model_code );
		$cove = $cove_arr [$rule_id];
		return $cove ['start_cove'] . '-' . $cove ['end_cove'];
	}
	/**
	 * 获取服务器上的 任务列表(帅选前)
	 */
	static function get_task_list() {
		global $config;
		self::doExit ( $config ['application'] );
		$inter = 'get_task'; //对应接口
		$config ['return_url'] = str_replace ( '&', '|', 'http://' . $_SERVER [SERVER_NAME] . $_SERVER [REQUEST_URI] );
		return keke_tool_class::union_build ( $config, $inter );
	}
	
	/**
	 * model_code
	 */
	private function get_model_code() {
		global $kekezu;
		$model_arr = $kekezu->_model_list;
		return $model_arr [$this->_model_id] ['model_code'];
	}
	public static function jump($url) {
		header ( "Location:" . $url );
		exit ();
	}
	public static function doExit($application = 1) {
		if(!$application){
			return false;
		}
	}
	/**
	 * union hand
	 */
	public static function hand_link($task_info) {
		global $_K, $config;
		if ($config ['application']) {
			if ($task_info ['task_union'] == 2 && $task_info ['r_task_id']) {
				return $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_info ['task_id'] . '&u=1';
			}
		} else {
			return false;
		}
	}
}