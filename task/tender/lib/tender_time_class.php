<?php
keke_lang_class::load_lang_class('tender_time_class');
final class tender_time_class extends time_base_class {
	
	public $_task_obj;
	public $_task_bid_obj;
	
	function __construct() {
		parent::__construct ();
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
		
		$task_info or $task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id'");
		
		switch ($task_info['task_status']){
			//进行中状态
			case 2:
				db_factory::execute("update ".TABLEPRE."witkey_task set exec_time = ".$task_info['end_time'].",task_status=3 where task_id='$task_id'");
				//通知雇主
				$notify_url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_info['task_id'] . '" target="_blank" >' . $task_info['task_title'] . '</a>';
				$v = array ("任务编号" => $task_info['task_id'], '任务标题' => $task_info['task_title'], '任务链接' => $notify_url, '选稿开始' => date('Y-m-d h:m:s', $task_info['sub_time']), '选稿结束' => date('Y-m-d h:m:s', $task_info['end_time']) );
				$tender_obj = new tender_task_class($task_info);
				$tender_obj->notify_user('tender_choose_remind', "任务选稿通知", $v, 2, $task_info['uid']);
				
				if(strpos(' '.$task_info['pay_item'],'top')){//重设增值属性
				 	$payitem = str_replace('top','',$task_info['pay_item']);
					$payitem = implode(',',array_filter(explode(',',$payitem)));
					db_factory::updatetable ( TABLEPRE . "witkey_task", array (//'is_top'=>0,//清空置顶属性
						'pay_item'=>$payitem),array ('task_id' => $task_id ) );
				}
				break;
			case 3:
				//修改任务状态信息
				db_factory::execute("update keke_witkey_task set exec_time=0,task_status=7 where task_id=".$task_id);
				//插入冻结记录
				$frost_obj = new Keke_witkey_task_frost_class();
				$frost_obj->setFrost_status(3);
				$frost_obj->setTask_id($task_id);
				$frost_obj->setFrost_time(time());
				$frost_obj->setRestore_time(0);
				$frost_obj->setFrost_key('admin');
				$frost_obj->setFrost_reason('任务到期没有选标');
				$frost_obj->setAdmin_uid(1);
				$frost_obj->setAdmin_username('admin');
				
				$frost_obj->create_keke_witkey_task_frost();
				//通知雇主
				$notify_url = '<a href ="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_info['task_id'] . '" target="_blank" >' . $task_info['task_title'] . '</a>';
				$v = array ("任务编号" => $task_info['task_id'], '任务标题' => $task_info['task_title'], '任务链接' => $notify_url, '选稿开始' => date('Y-m-d h:m:s', $task_info['sub_time']), '选稿结束' => date('Y-m-d h:m:s', $task_info['end_time']) );
				$tender_obj = new tender_task_class($task_info);
				$tender_obj->notify_user('tender_look_remind', "任务冻结通知", $v, 2, $task_info['uid']);
			default :
				db_factory::execute("update ".TABLEPRE."witkey_task set exec_time = 0 where task_id='$task_id'");
				break;
		}
		return true;
	}
	
	
	
	function validtaskstatus() {
		$this->_task_bid_obj = new Keke_witkey_task_bid_class();
		$this->_task_obj = new Keke_witkey_task_class();
		$this->task_tbtime_out();
		$this->task_xbtime_out ();


		//$this->task_tptime_end ();
	
	}
	/**
	 * 投标时间结束
	 */
	function task_tbtime_out() {
		global $_lang;
		//获取投标时间结束的任务
		$sql = sprintf("select * from %switkey_task where model_id = 4 and task_status=2 and ".time().">sub_time",TABLEPRE);
		$task_arr = db_factory::query($sql);
		foreach ($task_arr as $k=>$v){//改变任务状态
			$count = $this->get_task_work($v['task_id'], 0);
			if($count){
				kekezu::notify_user($_lang['tender_notice'], $_lang['you_pub_tender_task']."<a href=".$_K['siteurl']."/index.php?do=task&task_id=".$v['task_id']." >".$v['task_title']."</a>".$_lang['has_choose_tender_please_choose'], $v['uid']);
				$this->set_task_status($v['task_id'],3);
			}else{ 
				$this->set_task_status($v['task_id'],9);
				if ($v['task_union']=='1'){//如果是联盟任务
					$union_obj = new keke_union_class($v['task_id']);
					$union_obj -> change_status('failure');
				}
			//	kekezu::feed_add("$v['username']发布的招标任务:<a href=index.php?do=task&task_id=".$v['task_id']." >".$v['task_title']."</a>,投标期没有威客投标，已失败", $v['uid'], $v['username']);
				kekezu::notify_user($_lang['tender_fail'],$_lang['you_pub_tender_task']."<a href=".$_K['siteurl']."/index.php?do=task&task_id=".$v['task_id']." >".$v['task_title']."</a>".$_lang['submit_tender_no_witkey_fail'], $v['uid']);
			}
		}
	}
	
	/**
	 * 选标时间结束
	 */
	function task_xbtime_out() {
		global $_lang;
		$sql = sprintf("select * from %switkey_task where model_id = 4 and task_status=3 and ".time().">end_time",TABLEPRE);

		$task_arr = db_factory::query($sql);
				
		foreach ($task_arr as  $k=>$v){
			$count = $this->get_task_work($v['task_id'],4);
			if($count){
				kekezu::notify_user($_lang['tender_notice'], $_lang['you_pub_tender_task']."<a href=".$_K['siteurl']."/index.php?do=task&task_id=".$v['task_id']." >".$v['task_title']."</a>".$_lang['has_success_end'], $v['uid']);
				$this->set_task_status($v['task_id'],5);
			}else{  
				$this->set_task_status($v['task_id'],9);
				if ($v['task_union']=='1'){//如果是联盟任务
					$union_obj = new keke_union_class($v['task_id']);
					$union_obj -> change_status('failure');
				}
				kekezu::notify_user($_lang['tender_fail'],$_lang['you_pub_tender_task']."<a href=".$_K['siteurl']."/index.php?do=task&task_id=".$v['task_id']." >".$v['task_title']."</a>".$_lang['choose_tender_no_choose_fail'], $v['uid']);
			}			
		}
	}
	
//获取稿件信息
	function get_task_work($task_id,$bid_status){
		$this->_task_bid_obj->setWhere("task_id = $task_id and bid_status = $bid_status");
		$count = $this->_task_bid_obj->count_keke_witkey_task_bid();
		if($count>0){
			return $count;
		}else{
			return false;
		}
	}
	
	//改变任务状态
	
	function set_task_status($task_id,$task_status){
			$this->_task_obj->setWhere("task_id = $task_id");
			$this->_task_obj->setTask_status($task_status);
			$res = $this->_task_obj->edit_keke_witkey_task();
			if($res){
				return $res;
			}else{ 
				return false;
			} 
	}
	
	
	
	
}
?>