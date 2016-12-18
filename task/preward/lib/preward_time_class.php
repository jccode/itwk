<?php

final class preward_time_class extends time_base_class {

	function __construct() {
		parent::__construct ();
	}
	function validtaskstatus() {
//		$this->task_hand_end();
//		$this->task_choose_end();	
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
				$task_hand_obj = preward_task_class::get_instance($task_info);
				$task_hand_obj->time_hand_end();
				break;
			case 3:
				$task_hand_obj = preward_task_class::get_instance($task_info);
				$task_hand_obj->time_choose_end();
				break;
			default :
				db_factory::execute("update ".TABLEPRE."witkey_task set exec_time = 0 where task_id='$task_id'");
				break;
		}
		return true;
	}
	
}
?>