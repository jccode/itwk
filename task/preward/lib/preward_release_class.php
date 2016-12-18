<?php

/** 
 * @author michael
 * @property 计件悬赏任务发布类 
 */
keke_lang_class::load_lang_class('preward_release_class');
class preward_release_class extends keke_task_release_class {
	public static function get_instance($model_id,$pub_mode='professional') {
		static $obj = null;
		if ($obj == null) {
			$obj = new preward_release_class ( $model_id,$pub_mode);
		}
		return $obj;
	}
	function __construct($model_id,$pub_mode='professional') {
		parent::__construct ( $model_id,$pub_mode);
		$this->get_task_config ();
		$this->_std_obj->_release_info['txt_task_cash'] and $cash = $this->_std_obj->_release_info['txt_task_cash'] or $cash=$this->_task_config['min_cash'];
		$this->_default_max_day = keke_task_release_class::get_default_max_day($cash, $model_id,$this->_task_config['min_day']);
		$this->priv_init();
	}
/**
	 * 发布任务权限判断
	 */
	public function priv_init() {
		$priv_arr = preward_priv_class::get_priv ('',$this->_model_id, $this->_user_info, '2' );
		$this->_priv = $priv_arr ['pub'];
	}
	/**
	 * 初始化任务配置
	 * @return   void
	 */
	public function get_defalut_max_day(){
		$std_obj = $this->_std_obj;	
		$task_cash = intval($std_obj->_release_info['txt_task_cash']);
		return kekezu::get_show_day($task_cash,$this->_model_id);
	}
	public function get_task_config() {
		global $model_list;
		$model_list [$this->_model_id] ['config'] and $this->_task_config = unserialize ( $model_list [$this->_model_id] ['config'] ) or $this->_task_config = array ();
	}
	/**
	 * 发布模式进行信息
	 * @param $std_cache_name session名
	 * @param $data 外部传入参数
	 */
	function pub_mode_init($std_cache_name, $data = array()) {
		global $kekezu;
		global $_lang;
		$release_info = $this->_std_obj->_release_info;
		switch ($this->_pub_mode) {
			case "professional" :
				break;
			case "guide" :
				break;
			case "onekey" :
				if (! $release_info) {
					$sql = " select model_id,task_title,task_desc,indus_id,indus_pid,
						task_cash,work_count,single_cash from %switkey_task where task_id='%d' and model_id='%d'";
					$task_info = db_factory::get_one ( sprintf ( $sql, TABLEPRE, $data ['t_id'] ,$this->_model_id));
					$task_info or kekezu::show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],3,$_lang['not_exsist_relation_task_and_not_user_onekey'],"warning");
					
					$release_info = $this->onekey_mode_format($task_info);
					$allow_time = $kekezu->get_show_day ( $task_info['task_cash'], $this->_model_id );
					$task_day   = date('Y-m-d',$allow_time*24*3600+time());
					$release_info ['txt_task_day'] = $task_day;
					
					$release_info ['txt_task_cash'] = intval ( $task_info ['task_cash'] );
					$release_info ['txt_work_count'] = intval ( $task_info ['work_count'] );
					$release_info ['txt_single_cash'] = intval ( $task_info ['single_cash'] );
					$this->save_task_obj ( $release_info, $std_cache_name ); //信息保存
				}
				break;
		}
	}
	/**
	 * 获取任务最大允许时间
	 * @param $task_cash 任务金额
	 * @return json
	 */
	public function get_max_day($task_cash) {
		global $kekezu;
		global $_lang;
		if ($task_cash >= $this->_task_config ['min_cash']) {//任务金额满足最小要求
			$time = keke_task_release_class::get_default_max_day($task_cash, $this->_model_id,$this->_task_config['min_day']);
			
			kekezu::echojson ( $time, 1 ,$time);
		} else {//不满足
			kekezu::echojson ( $_lang['task_min_cash_limit_notice'] . $this->_task_config ['min_cash'], 0 );
			die ();
		}
	}
	
	/**
	 * 任务发布
	 * 		此方法只是用来产生任务记录
	 * @param $obj_name session存储对象名
	 */
	public function pub_task() {
		
		//增值服务处理 
		//任务状态的判断
		
		//写入task表
		$task_obj = $this->_task_obj;
		$std_obj = $this->_std_obj;
		
		//发布信息公共处理部
		$this->public_pubtask();
		//根据任务总花费来确顶任务发布状态
		$task_obj->setSingle_cash($std_obj->_release_info['txt_single_cash']);//稿件单价
		$task_obj->setWork_count($std_obj->_release_info['txt_work_count']);//稿件需求量
		$task_cash = $this->_std_obj->_release_info['txt_task_cash'];//任务金额
		$this->set_task_status($this->get_total_cash ($task_cash),$task_cash);
		//任务发布
		$task_id = $task_obj->create_keke_witkey_task ();
		return $task_id;
	}

	
}