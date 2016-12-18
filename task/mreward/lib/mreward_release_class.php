<?php

/** 
 * @author michael
 * @property 悬赏任务发布类 
 */
keke_lang_class::load_lang_class('mreward_release_class');
class mreward_release_class extends keke_task_release_class {
	public static function get_instance($model_id,$pub_mode='professional') {
		static $obj = null;
		if ($obj == null) {
			$obj = new mreward_release_class ( $model_id,$pub_mode);
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
		$priv_arr = mreward_priv_class::get_priv ('',$this->_model_id, $this->_user_info, '2' );
		$this->_priv = $priv_arr ['pub'];
	}
	/**
	 * 初始化任务配置
	 * @return   void
	 */
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
						task_cash from %switkey_task where task_id='%d' and model_id='%d'";
					$task_info = db_factory::get_one ( sprintf ( $sql, TABLEPRE, $data ['t_id'] ,$this->_model_id));
					$task_info or kekezu::show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],3,$_lang['not_exsist_relation_task_and_not_user_onekey'],"warning");
					
					$release_info = $this->onekey_mode_format($task_info);
					
					$allow_time = $kekezu->get_show_day ( $task_info['task_cash'], $this->_model_id );
					$task_day   = date('Y-m-d',$allow_time*24*3600+time());
					$release_info ['txt_task_day'] = $task_day;
					
					$release_info ['txt_task_cash'] = intval ( $task_info ['task_cash'] );
					/** 获取奖项信息*/
					$prize_info = db_factory::query(sprintf("select * from %switkey_task_prize where task_id='%d'",TABLEPRE,$data['t_id']));
					foreach ($prize_info as $v){
						$release_info['txt_prize'.$v['prize'].'_num'] = $v['prize_count'];
						$release_info['txt_prize'.$v['prize'].'_cash'] = intval($v['prize_cash']);
					}
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
	 * 	此方法只是用来产生任务记录
	 * @param $obj_name session存储对象名
	 */
	public function pub_task() {
		$task_obj = $this->_task_obj;
		$std_obj = $this->_std_obj;
		
		//发布信息公共处理部
		$this->public_pubtask();
		//根据任务总花费来确顶任务发布状态
		$task_cash = $this->_std_obj->_release_info['txt_task_cash'];//任务金额
		$this->set_task_status($this->get_total_cash ($task_cash),$task_cash);
		//任务发布
		$task_id = $task_obj->create_keke_witkey_task ();
		//发布信息多人悬赏私有处理部
		$this->if_exsit_prize($task_id);
		return $task_id;
	}
	/**
	 * 	任务发布
	 * 	此方法用来判断存在的奖项
	 * 	@param $task_id 任务id
	 */
	public function if_exsit_prize($task_id){
		$std_obj = $this->_std_obj; //成员对象
		$release_info = $std_obj->_release_info; //任务发布信息
		
		//判断是否存在一等奖项
		if($release_info['txt_prize1_num']){
			$this->create_prize_record(1, $task_id, $release_info);
		}
		//判断是否存在二等奖项
		if($release_info['txt_prize2_num']){
			$this->create_prize_record(2, $task_id, $release_info);
		}
		//判断是否存在三等奖项
		if($release_info['txt_prize3_num']){
			$this->create_prize_record(3, $task_id, $release_info);
		}
	}
	/**
	 * 	任务发布
	 * 	此方法用来插入奖项数据
	 * 	@param $prize_level 几等奖
	 * 	@param $task_id 任务id
	 * 	@param $release_info 发布任务的信息
	 */
	public function create_prize_record($prize_level,$task_id,$release_info) {
		$prize_obj = new Keke_witkey_task_prize_class();//实例化对象
		$prize_num = 'txt_prize'.$prize_level.'_num';//根据$prize_level，判断是几等奖的奖项数目
		$prize_cash = 'txt_prize'.$prize_level.'_cash';//根据$prize_level，判断是几等奖的奖项金额
		
		$prize_obj->setPrize(intval($prize_level));//几等奖
		$prize_obj->setTask_id(intval($task_id));//任务id
		$prize_obj->setPrize_count(intval($release_info[$prize_num]));//奖项数目
		$prize_obj->setPrize_cash(floatval($release_info[$prize_cash]));//奖项金额
		$prize_obj->create_keke_witkey_task_prize();//生成记录
	}
}