<?php

final class mreward_time_class extends time_base_class {

	
	function __construct() {
		parent::__construct ();
	}
	function validtaskstatus() {
//		$this->task_tg_timeout();
//		$this->task_xg_timeout();
//		$this->task_gs_timeout ();
	}
	
//added for epweike  by tank
	public function exec_task($task_id = null, $task_info = null) {
		if(!$task_id&&!$task_info){
			return false;
		}
		//参数保障
		$task_id or $task_id = $task_info['task_id'];
		
		//这里开始加缓存锁
		global $kekezu;
		if ($kekezu->_cache_obj->get('taskexec_lock_'.$task_id)){
			return false;
		}
		$kekezu->_cache_obj->set('taskexec_lock_'.$task_id,1,30);
		
		$task_info or $task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id' and task_union!=2");
		
		switch ($task_info['task_status']){
			//进行中状态
			case 2:
				$task_hand_obj = mreward_task_class::get_instance($task_info);
				$task_hand_obj->time_hand_end();
				break;
			case 3:
				$task_hand_obj = mreward_task_class::get_instance($task_info);
				$task_hand_obj->time_choose_end();
				break;
			case 4:
				$task_hand_obj = mreward_task_class::get_instance($task_info);
				$task_hand_obj->lottery_end();
				break;
			case 5:
				$task_hand_obj = mreward_task_class::get_instance($task_info);
				$task_hand_obj->time_notice_end();
				break;
			default :
				db_factory::execute("update ".TABLEPRE."witkey_task set exec_time = 0 where task_id='$task_id'");
				break;
		}
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 投稿期结束，进入选稿期
	 */
	function  task_tg_timeout(){
		$task_tg = db_factory::query(sprintf("select * from %switkey_task where model_id=2 and task_status=2 and '%d' > sub_time",TABLEPRE,time()));
		if(is_array($task_tg)){
			foreach ($task_tg as $k=>$v)  {
				$task_tg_obj = mreward_task_class::get_instance($v);
				$task_tg_obj->task_tg_timeout();
			}
		}
	}
	/**
	 * 选稿期结束，进入公示期
	 */
	function task_xg_timeout() {
		$task_xg = db_factory::query(sprintf("select * from %switkey_task where model_id=2 and task_status=3 and '%d' > end_time",TABLEPRE,time()));
		if(is_array($task_xg)){
		foreach ( $task_xg as $k => $v ) {
			$task_xg_obj = mreward_task_class::get_instance($v);
			$task_xg_obj->task_xg_timeout ();
		 }
		}
	}
	/**
	 * 公式中结束
	 */
	function task_gs_timeout() {
		$task_gs = db_factory::query(sprintf("select * from %switkey_task where model_id=2 and task_status=5 and '%d' > sp_end_time",TABLEPRE,time()));
		if(is_array($task_gs)){
		foreach ( $task_gs as $k => $v ) {
			$task_gs_obj = mreward_task_class::get_instance ( $v );
			$task_gs_obj->task_gs_timeout ();
		}
		}
	}

}
?>