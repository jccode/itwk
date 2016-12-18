<?php

final class sreward_time_class extends time_base_class {

	function __construct() {
		parent::__construct ();
	}
	function validtaskstatus() {
//		$this->task_hand_end ();
//		$this->task_vote_end ();
//		$this->task_choose_end ();
//		$this->task_notice_end ();
//		$this->task_agreement_end();
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
		
		$task_info or $task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id' and task_union!=2 ");
		
		switch ($task_info['task_status']){
			//进行中状态
			case 2:
				$task_hand_obj = sreward_task_class::get_instance($task_info);
				$task_hand_obj->time_hand_end();
				break;
			case 3:
				$task_hand_obj = sreward_task_class::get_instance($task_info);
				$task_hand_obj->time_choose_end();
				break;
			case 4:
				$task_hand_obj = sreward_task_class::get_instance($task_info);
				$task_hand_obj->lottery_end();
				break;
			case 5:
				$task_hand_obj = sreward_task_class::get_instance($task_info);
				$task_hand_obj->time_notice_end();
				break;
			default :
				db_factory::execute("update ".TABLEPRE."witkey_task set exec_time = 0 where task_id='$task_id'");
				break;
		}
		return true;
	}
	
	
	/**
	 *投稿到期
	 *		入选稿
	 *		失败返还
	 */
	public function task_hand_end(){
		$task_list = db_factory::query(sprintf(" select * from %switkey_task where task_status=2 and  sub_time < '%s' and model_id = '1' ",TABLEPRE,time()));
		if(is_array($task_list)){
			foreach ( $task_list as $k => $v ) {
				$task_hand_obj = sreward_task_class::get_instance($v );
				$task_hand_obj-> time_hand_end();
			}
		}
	}
	/**
	 * 投票到期
	 * 		选标入公示
	 * 		失败返还
	 */
	public function task_vote_end(){
		$task_list = db_factory::query(sprintf(" select * from %switkey_task where task_status=4 and  sp_end_time < '%s' and model_id = '1' ",TABLEPRE,time()));
		if(is_array($task_list)){
			foreach ( $task_list as $k => $v ) {
				$task_vote_obj = sreward_task_class::get_instance($v );
				$task_vote_obj-> time_vote_end();
			}
		}
	}
	/**
	 * 选稿到期
	 * 			入公示
	 * 			入投票
	 * 			自动选稿
	 * 			失败返还
	 */
	public function task_choose_end(){
		$task_list = db_factory::query(sprintf(" select * from %switkey_task where task_status=3 and  end_time < '%s' and model_id = '1' ",TABLEPRE,time()));
		if(is_array($task_list)){
			foreach ( $task_list as $k => $v ) {
				$task_choose_obj = sreward_task_class::get_instance($v );
				$task_choose_obj-> time_choose_end();
			}
		}
		
	}
	/**
	 * 公示到期
	 * 		完成
	 * 		失败返还
	 */
	public function task_notice_end(){
		$task_list = db_factory::query(sprintf(" select * from %switkey_task where task_status=5 and  sp_end_time < '%s' and model_id = '1' ",TABLEPRE,time()));
		if(is_array($task_list)){
			foreach ( $task_list as $k => $v ) {
				$task_notice_obj = sreward_task_class::get_instance($v );
				$task_notice_obj-> time_notice_end();
			}
		}
	}
	/**
	 * 协议到期自动签署
	 */
	public function task_agreement_end(){
		global $model_list;
		$config = unserialize($model_list['1']['config']);
		$agree_list = db_factory::query(sprintf(" select agree_id,agree_status,on_time from %switkey_agreement where model_id=1 and agree_status<3",TABLEPRE));
		if(is_array($agree_list)){
			foreach ( $agree_list as $k => $v ) {
				$agree_obj = sreward_task_agreement::get_instance($v['agree_id']);
				switch($v['agree_status']){
					case "1"://默认签署
						if($v['on_time']+$config['auto_agree_time']*24*3600<time()){
						$agree_obj-> agreement_stage_one('1');
						$agree_obj-> agreement_stage_one('2');
						}
						break;
					case "2"://默认交付
						if($v['on_time']+$config['max_agree_time']*24*3600<time()){
							$agree_obj->accept_confirm();
						}
						break;
				}
			}
		}
	}


}