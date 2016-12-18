<?php
/**
 * 任务配置控制类
 * @author Chen
 * 
 */
keke_lang_class::load_lang_class ( 'keke_task_config' );
class keke_task_config {
	/**
	 * 获取任务时间规则
	 * @param int $model_id 模型ID
	 * @param int $cache_time 缓存时间
	 */
	public static function get_time_rule($model_id, $cache_time = null) {
		return kekezu::get_table_data ( "*", "witkey_task_time_rule", "model_id='$model_id'", "rule_cash", "", "", "", "", $cache_time );
	}
	/**
	 * 获取任务延期规则
	 * @param int $model_id 模型ID
	 * @param int $cache_time 缓存时间
	 */
	public static function get_delay_rule($model_id, $cache_time = null) {
		return kekezu::get_table_data ( "*", "witkey_task_delay_rule", "model_id='$model_id'", "defer_rate", "", "", "", "", $cache_time );
	}
	/**
	 * 设置任务时间规则
	 * @param int $model_id 模型ID
	 * @param array $timeOld 旧配置
	 * @param array $timeNew 新配置
	 * @return boolean
	 */
	public static function set_time_rule($model_id, $timeOld = array(), $timeNew = array()) {
		if (is_array ( $timeOld )) {
			foreach ( $timeOld as $k => $v ) {
				$res = db_factory::execute ( sprintf ( " update %switkey_task_time_rule set rule_day='%d',rule_cash='%s' where day_rule_id='%d' and model_id='%d'", TABLEPRE, $v ['rule_day'], $v ['rule_cash'], $k, $model_id ) );
			}
		}
		if (is_array ( $timeNew )) {
			foreach ( $timeNew as $v2 ) {
				! empty ( $v2 ['rule_day'] ) && ! empty ( $v2 ['rule_cash'] ) and $res = db_factory::execute ( sprintf ( " insert into %switkey_task_time_rule values('','%f','%d','%d')", TABLEPRE, floatval ( $v2 ['rule_cash'] ), intval ( $v2 ['rule_day'] ), $model_id ) );
			}
		}
		return $res;
	
	}
	
	/**
	 * 设置任务延期规则
	 * @param int $model_id 模型ID
	 * @param array $delayOld 旧配置
	 * @param array $delayNew 新配置
	 * @return boolean
	 */
	public static function set_delay_rule($model_id, $delayOld = array(), $delayNew = array()) {
		if (is_array ( $delayOld )) {
			foreach ( $delayOld as $k => $v ) {
				$res = db_factory::execute ( sprintf ( " update %switkey_task_delay_rule set defer_rate='%d' where defer_rule_id='%d' and model_id='%d'", TABLEPRE, $v ['defer_rate'], $k, $model_id ) );
			}
		}
		if (is_array ( $delayNew )) {
			foreach ( $delayNew as $v2 ) {
				! empty ( $v2 ['defer_times'] ) && ! empty ( $v2 ['defer_rate'] ) and $res = db_factory::execute ( sprintf ( " insert into %switkey_task_delay_rule values('','%d','%s','%d')", TABLEPRE, intval ( $v2 ['defer_times'] ), intval ( $v2 ['defer_rate'] ), $model_id ) );
			}
		}
		return $res;
	}
	/**
	 * 任务扩展配置保存
	 * @param int $model_id 模型ID
	 * @param array $conf 扩展配置
	 * @return boolean
	 */
	public static function set_task_ext_config($model_id, $conf = array()) {
		return db_factory::execute ( sprintf ( " update %switkey_model set config='%s' where model_id='%d'", TABLEPRE, kekezu::k_input ( serialize ( $conf ) ), $model_id ) );
	
	}
	/**
	 * 删除时间规则
	 * @param int $rule_id 规则编号
	 * @return boolean
	 */
	public static function del_time_rule($rule_id) {
		return db_factory::execute ( sprintf ( " delete from %switkey_task_time_rule where day_rule_id='%d'", TABLEPRE, $rule_id ) );
	}
	
	/**
	 * 删除延期规则
	 * @param int $rule_id 规则编号
	 * @return boolean
	 */
	public static function del_delay_rule($rule_id) {
		return db_factory::execute ( sprintf ( " delete from %switkey_task_delay_rule where defer_rule_id='%d'", TABLEPRE, $rule_id ) );
	}
	/**
	 * 冻结task,任务状态为!('6','7','8','10','11')
	 * (2,3,4,5) 可以冻结
	 * @param int/array $task_ids
	 */
	public static function task_freeze($task_ids, $reason = '') {
		global $admin_info;
		global $_lang;
		if ($task_ids && is_array ( $task_ids )) {
			$ids = implode ( ',', $task_ids );
			//生成要冻结的记录,并发送冻结通知,生成一系统日志
			$sql2 = sprintf ( "select task_id,task_status,task_title,uid from %switkey_task where task_id in(%s) and task_status in (2,3,4,5)", TABLEPRE, $ids );
			$task_arr = db_factory::query ( $sql2 );
			foreach ( $task_arr as $v ) {
				$sql3 = sprintf ( "insert into %switkey_task_frost (frost_status,task_id,frost_time,frost_key,frost_reason,admin_uid,admin_username) 
        					values('%d','%d','%d','admin','%s','%d','%s')", TABLEPRE, $v ['task_status'], $v ['task_id'], time (), $reason, $admin_info ['uid'], $admin_info ['username'] );
				db_factory::execute ( $sql3 );
				kekezu::admin_system_log ( $_lang ['freeze_task'] . ":{$v['task_title']}" );
				kekezu::notify_user ( $_lang ['freeze_notcie'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $v [task_id] . '>' . $v [task_title] . '</a>' . $_lang ['has_freeze'], $v [uid] );
			}
		} elseif ($task_ids) { //单条冻结
			$ids = $task_ids;
			$sql2 = sprintf ( "select task_id,task_status,task_title,uid from %switkey_task where task_id = %d and task_status  in (2,3,4,5)", TABLEPRE, $task_ids );
			$task_info = db_factory::get_one ( $sql2 );
			$sql3 = sprintf ( "insert into %switkey_task_frost (frost_status,task_id,frost_time,frost_key,frost_reason,admin_uid,admin_username) 
        					values(%d,%d,%d,'admin','%s',%d,'%s')", TABLEPRE, $task_info ['task_status'], $task_info ['task_id'], time (), $reason, $admin_info ['uid'], $admin_info ['username'] );
			db_factory::execute ( $sql3 );
			kekezu::admin_system_log ( $_lang ['freeze_task'] . ":{$task_info['task_title']}" );
			kekezu::notify_user ( $_lang ['freeze_notcie'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $task_info [task_id] . '>' . $task_info [task_title] . '</a>' . $_lang ['has_freeze'], $task_info [uid] );
		}
		$sql = sprintf ( "update %switkey_task set task_status = '7' ,exec_time = 0  where task_id in(%s) and task_status   in (2,3,4,5)", TABLEPRE, $ids );
		return db_factory::execute ( $sql ); //执行冻结
	}
	/**
	 * 取消冻结,还原弱冻结任务到之前的状态，删除冻结记录
	 * @param int/array $task_ids
	 */
	public static function task_unfreeze($task_ids) {
		global $admin_info;
		global $_lang;
		if ($task_ids && is_array ( $task_ids )) { //批量恢复
			$ids = implode ( ',', $task_ids );
			//要恢复的任务，删除 的冻结记录
			$sql = sprintf ( "select task_id,task_title,task_status,end_time,sub_time,exec_time,uid from %switkey_task where task_status=7 and task_id in(%s)", TABLEPRE, $ids );
			$task_arr = db_factory::query ( $sql );
			foreach ( $task_arr as $v ) {
				$sqlf = sprintf ( "select task_id,frost_status,frost_time from %switkey_task_frost", TABLEPRE, $v ['task_id'] );
				$frost_info = db_factory::get_one ( $sqlf );
				$end_time = (time () - $frost_info ['frost_time']) + $v [end_time];
				$sub_time = (time () - $frost_info ['frost_time']) + $v [sub_time];
				$sp_end_time = $v [sp_end_time] ? ($v [sp_end_time] + (time () - $frost_info ['frost_time'])) : 0;
				$exec_time = 0;
				switch ($frost_info ['frost_status']) {
					case '2' :
						$exec_time = $sub_time;
						break;
					case '3' :
						$exec_time = $end_time;
						break;
					case '4' :
						//摇奖冻结无时间判断依据  默认到3天后
						$exec_time = time () + 3 * 24 * 3600;
						break;
					case '5' :
						$exec_time = $sp_end_time;
						break;
				}
				
				$sql2 = sprintf ( "update %switkey_task set task_status = %d,end_time='%s',sub_time='%s',sp_end_time,exec_time='%s'  where task_id = '%d'", TABLEPRE, $frost_info ['frost_status'], $end_time, $sub_time, $sp_end_time, $exec_time, $v ['task_id'] );
				db_factory::execute ( $sql2 );
				db_factory::execute ( sprintf ( "delete from %switkey_task_frost where task_id = '%d'", TABLEPRE, $frost_info ['task_id'] ) );
				kekezu::admin_system_log ( $_lang ['unfreeze_task'] . ":{$v['task_title']}" );
				kekezu::notify_user ( $_lang ['task_unfreeze_notice'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $v [task_id] . '>' . $v [task_title] . '</a>' . $_lang ['has_unfreeze'], $v [uid] );
			}
		} elseif (task_ids) { //单条恢复
			$sql = sprintf ( "select task_id,task_title,task_status,end_time,sub_time,exec_time,uid from %switkey_task where task_status=7 and task_id ='%d'", TABLEPRE, $task_ids );
			$task_info = db_factory::get_one ( $sql );
			$sqlf = sprintf ( "select task_id,frost_status,frost_time from %switkey_task_frost where task_id=%d", TABLEPRE, $task_info ['task_id'] );
			$frost_info = db_factory::get_one ( $sqlf );
			$end_time = (time () - $frost_info ['frost_time']) + $task_info [end_time];
			$sub_time = (time () - $frost_info ['frost_time']) + $task_info [sub_time];
			$sp_end_time = $task_info [sp_end_time] ? ($task_info [sp_end_time] + (time () - $frost_info ['frost_time'])) : 0;
			$exec_time = 0;
			switch ($frost_info ['frost_status']) {
				case '2' :
					$exec_time = $sub_time;
					break;
				case '3' :
					$exec_time = $end_time;
					break;
				case '4' :
					//摇奖冻结无时间判断依据  默认到3天后
					$exec_time = time () + 3 * 24 * 3600;
					break;
				case '5' :
					$exec_time = $sp_end_time;
					break;
			}
			$sql2 = sprintf ( "update %switkey_task set task_status = %d,end_time='%s',sub_time='%s',sp_end_time='%s',exec_time='%s' where task_id = '%d'", TABLEPRE, $frost_info ['frost_status'], $end_time, $sub_time, $sp_end_time, $exec_time, $task_info ['task_id'] );
			db_factory::execute ( $sql2 );
			db_factory::execute ( sprintf ( "delete from %switkey_task_frost where task_id = '%d'", TABLEPRE, $frost_info ['task_id'] ) );
			kekezu::admin_system_log ( $_lang ['unfreeze_task'] . ":{$task_info['task_title']}" );
			kekezu::notify_user ( $_lang ['task_unfreeze_notice'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $task_info [task_id] . '>' . $task_info [task_title] . '</a>' . $_lang ['has_unfreeze'], $task_info [uid] );
		}
		return true;
	}
	/**
	 * 任务通过审核  task_staus = 1 是待审核的任务 将任务状态改2
	 * @param int/array $task_ids
	 */
	public static function task_audit_pass($task_ids) {
		global $_lang;
		global $model_list;
		if ($task_ids && is_array ( $task_ids )) {
			foreach ( $task_ids as $tid ) {
				self::task_audit_pass ( $tid );
			}
		} elseif ($task_ids) {
			
			$ids = $task_ids;
			$task_info = db_factory::get_one ( sprintf ( "select * from %switkey_task where task_id = '%d' and task_status=1", TABLEPRE, $ids ) );
			if ($task_info) {
				
				$model_dir = $model_list [$task_info ['model_id']] ['model_dir'];
				
				if (file_exists ( S_ROOT . "./task/$model_dir" ))
					$m = strtolower ( $model_dir ) . "_task_class";
				if (class_exists ( $m )) {
					eval ( '$t_obj = ' . $m . '::get_instance($task_info);' );
					$t_obj->task_begin ();
				}
			
		//增值服务到期时间更新
			//				$payitem_add_time =time()- $task_info['start_time'];//所需增加的时间 
			//				$payitem_arr = unserialize($task_info['payitem_time']);
			//			
			//				$payitem_arr['top']>1000000000 and $top_add_time = $payitem_add_time or $top_add_time=false;
			//				$payitem_arr['urgent']>1000000000 and $urgent_add_time = $payitem_add_time or $urgent_add_time=false;
			//			
			//				$payitem_time = keke_task_class::get_payitem($task_info['payitem_time'],$top_add_time,$urgent_add_time);
			//				
			//              
			//				$sub_time = time()+(intval($task_info['sub_time'])-intval($task_info['start_time']));
			//				$end_time = time()+($task_info['end_time']-$task_info['start_time']);
			//				$sql =  sprintf ( "update %switkey_task set task_status=2 ,start_time='%d',sub_time='%d',end_time='%d',payitem_time='%s'  where task_id  ='%d' ", TABLEPRE,time(),$sub_time,$end_time,$payitem_time,$task_info['task_id'] ) ;
			//		
			//				$res = db_factory::execute ($sql);
			//			
			//				kekezu::admin_system_log ( $_lang['audit_task'].":{$task_info['task_title']}".$_lang['pass'] );
			//				kekezu::notify_user ( $_lang['task_audit_notice'], $_lang['you_pub_task'].':<a href=index.php?do=task&task_id=' . $task_info [task_id] . '>' . $task_info [task_title] . '</a>'.$_lang['audit_pass'], $task_info [uid] );
			//				
			//				$feed_arr = array ("feed_username" => array ("content" => $task_info['username'], "url" => "index.php?do=shop&u_id={$task_info['uid']}" ), "action" => array ("content" => "发布了任务", "url" => "" ), "event" => array ("content" => $task_info['task_title'] , "url" => "index.php?do=task&task_id=".$task_info['task_id']) );
			//				kekezu::save_feed ($feed_arr,$task_info['uid'],$task_info['username'],'pub_task',$task_info[task_id]);
			}
		}
		return $res;
	}
	/**
	 * 
	 * 该方法用于任务的推荐
	 * @param int $task_id
	 */
	public static function task_recommend($task_id) {
		return db_factory::execute ( sprintf ( "update %switkey_task set is_recommend=1 where task_id='%d' ", TABLEPRE, $task_id ) );
	}
	/**
	 * 判断是否能冻结
	 */
	public static function valid_frost_type($task_id) {
		$frost_key = db_factory::get_count ( 'select frost_key from ' . TABLEPRE . 'witkey_task_frost where task_id=' . $task_id );
		return $frost_key == 'admin' ? true : false;
	}
	/**
	 * 
	 * 该方法用于取消任务的推荐
	 * @param int $task_id
	 */
	public static function task_unrecommend($task_id) {
		return db_factory::execute ( sprintf ( "update %switkey_task set is_recommend=0 where task_id='%d' ", TABLEPRE, $task_id ) );
	}
	/**
	 * 任务审核不过，将task_staus =1 改为  10,审核失败
	 * 审核失败后，任务退款给雇主
	 * @param unknown_type $task_ids
	 * @param $trust_response 担保回调响应
	 */
	public static function task_audit_nopass($task_ids, $trust_response = false) {
		global $kekezu;
		global $_lang;
		if ($task_ids && is_array ( $task_ids )) {
			$ids = implode ( ',', $task_ids );
			$task_arr = db_factory::get_one ( sprintf ( "select task_id,task_title,task_status,task_cash,model_id,uid from %switkey_task where task_id in(%s)", TABLEPRE, $ids ) );
			foreach ( $task_arr as $v ) {
				
				$res = db_factory::execute ( sprintf ( "update %switkey_task set task_status=10 where task_id ='%d' ", TABLEPRE, $v ['task_id'] ) );
				if($res){
					$v ['model_id'] < 4 and keke_finance_class::cash_in ( $v ['uid'], $v ['task_cash'], 0, 'task_fail', 'admin', 'task', $v ['task_id'] );
					kekezu::admin_system_log ( $_lang ['audit_task'] . ":{$v['task_title']}" . $_lang ['not_pass'] );
					kekezu::notify_user ( $_lang ['task_audit_notice'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $v [task_id] . '>' . $v [task_title] . '</a>' . $_lang ['audit_not_pass'], $v [uid] );
				}
			}
		
		} elseif ($task_ids) {
			$ids = $task_ids;
			$task_info = db_factory::get_one ( sprintf ( "select task_id,task_title,task_status,task_cash,model_id,uid,is_trust,trust_type,model_id from %switkey_task where task_id = '%d'", TABLEPRE, $ids ) );
			if ($task_info) {
				
				$res = db_factory::execute ( sprintf ( "update %switkey_task set task_status=10 where task_id  ='%d' ", TABLEPRE, $ids ) );
				if($res){
					$task_info ['model_id'] < 4 and keke_finance_class::cash_in ( $task_info ['uid'], $task_info ['task_cash'], 0, 'task_fail', 'admin', 'task', $task_info ['task_id'] );
					kekezu::admin_system_log ( $_lang ['audit_task'] . ":{$task_info['task_title']}" . $_lang ['not_pass'] );
					kekezu::notify_user ( $_lang ['task_audit_notice'], $_lang ['you_pub_task'] . ':<a href=index.php?do=task&task_id=' . $task_info [task_id] . '>' . $task_info [task_title] . '</a>' . $_lang ['audit_not_pass'], $task_info [uid] );
				}
			}
		}
		return $res;
	}
	/**
	 * 任务批量删除，可以删除的任务 0,8,9,10
	 * @param int/array $task_ids
	 */
	public static function task_del($task_ids) {
		global $_lang;
		is_array ( $task_ids ) and $ids = implode ( ",", $task_ids ) or $ids = $task_ids;
		foreach ( $task_ids as $v ) {
			$del_title = db_factory::get_count ( sprintf ( "select task_title from %switkey_task where task_id='%d'", TABLEPRE, $v ) );
			kekezu::admin_system_log ( $_lang ['delete_task'] . ":{$del_title}" );
		}
		return db_factory::execute ( sprintf ( "delete from %switkey_task where task_status in(0,8,9,10) and task_id in(%s)", TABLEPRE, $ids ) );
	}
	/***任务后台编辑动作***/
	public static function can_operate($status) {
		global $_lang;
		$operate = array ();
		switch ($status) {
			case "1" : //待审核
				$operate ['pass'] = "通过审核";
				$operate ['nopass'] = $_lang ['pass_audit'];
				break;
			case "2" : //投稿
			case "3" : //选稿
			case "4" : //投票
			case "5" : //公示
			case "6" : //交付
				break;
			case "7" : //冻结
		

		}
		return $operate;
	}
	/**
	 * 获取用户参加的任务统计。分模型 
	 * @param int $uid
	 */
	public static function get_user_join_task($uid = '') {
		global $user_info;
		$count_arr = array ();
		$uid or $uid = $user_info ['uid']; //不传递默认为当前用户信息
		/*悬赏类任务统计**/
		$reward_sql = " select count(c.task_id) count,c.model_id from (select DISTINCT a.task_id,b.model_id from %switkey_task_work a left join %switkey_task b on a.task_id=b.task_id where a.uid='%d') c  group by c.model_id";
		$reward_arr = db_factory::query ( sprintf ( $reward_sql, TABLEPRE, TABLEPRE, $uid ), 3600 );
		/**招标类任务统计**/
		$tender_sql = " select count(c.task_id) count,c.model_id from (select DISTINCT a.task_id,b.model_id from %switkey_task_bid a left join %switkey_task b on a.task_id=b.task_id where a.uid='%d') c  group by c.model_id";
		$tender_arr = db_factory::query ( sprintf ( $tender_sql, TABLEPRE, TABLEPRE, $uid ), 3600 );
		/**合并**/
		$total_arr = array_merge ( $reward_arr, $tender_arr );
		foreach ( $total_arr as $v ) {
			$count_arr [$v ['model_id']] = intval ( $v ['count'] );
		}
		return $count_arr;
	}
	/**
	 * 后台增值服务,操作输出
	 */
	public static function payitem_operate($model_id, $task_id, $pay_items, $type = 'small') {
		global $_K, $item_list, $model_list;
		$checkbox = '';
		foreach ( $item_list as $k => $v ) {
			if (strpos ( $v ['model_code'], $model_list [$model_id] ['model_code'] ) !== FALSE) {
				$checkbox .= '<input type="checkbox" name="item"';
				strpos ( $pay_items, $k ) !== FALSE && $checkbox .= ' checked="checked" ';
				$checkbox .= ' id=' . $k . ' value=' . $k . ' desc="' . $v ['item_name'] . '">';
				$checkbox .= '<img src="' . $_K ['siteurl'] . '/' . $v [$type . '_pic'] . '"> ';
				$checkbox .= $v ['item_name'];
			}
		}
		$url = 'index.php?do=model&model_id=' . $model_id . '&view=edit&task_id=' . $task_id;
		
		$checkbox .= ' 　　<a class="button dbl_target" href="javascript:void(0);" onclick="modifyItem();">
						<span class="pen icon"></span>修改增值项</a>';
		$script = <<<SCRIPT
			<script type="text/javascript">
				function modifyItem(){
					var c = $(":checkbox[name='item']:checked").length;
					var item = '';
					var desc = '';
					var top  = 0;
					if(c>0){
						desc += '您设置的增值服务为:<br>';
						$(":checkbox[name='item']:checked").each(function(i,n){
							item+=$(n).val();
							desc+=$(n).attr("desc");
							$(n).val()=='top'?top=1:'';
							if(i<c-1){
								item+=',';
								desc+=',';
							}
						})
					}else{
						desc += '您此项操作将清空任务增值项';
					}
						art.dialog({
							title:'操作提示',
							content:desc,
							icon:'succeed',
							yesFn:function(){
									$.getJSON('{$url}',{ac:'modify',item:item,top:top},function(json){
										art.dialog.tips(json.msg,1.5);
									})
								},
							noFn:function(){
							 		this.close();return false;
								}
						})
				}
			</script>
SCRIPT;
		$checkbox .= $script;
		return $checkbox;
	}
	/**
	 * 获取增值项ico
	 */
	public static function payitem_ico($model_id, $pay_items, $type = 'small') {
		global $_K, $item_list, $model_list;
		$str = '';
		if ($pay_items) {
			$tmp = array_filter ( explode ( ',', $pay_items ) );
			if ($tmp) {
				foreach ( $tmp as $v ) {
					if (strpos ( $item_list [$v] ['model_code'], $model_list [$model_id] ['model_code'] ) !== FALSE) {
						$str .= '<img src="' . $_K ['siteurl'] . '/' . $item_list [$v] [$type . '_pic'] . '" class="ml_5">';
					}
				}
			}
		}
		return $str;
	}
	/**
	 * 后台任务附件编辑
	 */
	public static function task_file_edit($task_id, $file_ids) {
		global $_K, $file_list, $kekezu;
			$flie_types = kekezu::get_ext_type();
			$max_size   = $kekezu->_sys_config['max_size'];
			$xyq 		= session_id();
			$list_str   = '';
			if($file_list){
				foreach($file_list as $v){
					$list_str .='<div class="uploadify-queue"><div class="uploadify-queue-item" style="width:450px"><div class="cancel">';
					$list_str.='<a href="javascript:void(0);" onclick="fileDel(this,'.$v['file_id'].')">X</a></div>';
					$list_str.='<span class="fileName">'.$v['file_name'].'('.$v['file_size'].')</span>';
					$list_str.='<span class="data"> - 已完成</span><div class="uploadify-progress"><div class="uploadify-progress-bar" style="width: 100%;"></div>';
					$list_str.='</div></div></div>';
				}
			}
		$str = <<<STRING
		<ul class="pay_item" style="float:left;">
			<li>
				<b>任务附件管理</b>：一次最多上传5个， 每个不超过{$max_size}M
				<a class="button dbl_target" onclick="subFile();" href="javascript:void(0);">
					<span class="pen icon"></span>
					确定更改
				</a>
			</li>
			<li>
	     	<div class="fl_l file_box">	
				<input type="hidden" name="file_ids" id="file_ids" value="{$file_ids}">
				<input type="hidden" name="del_ids" id="del_ids" value="">
				<!--{eval \$flie_types = kekezu::get_ext_type();}-->
				 	<div class="file_box">
				 		<input type="file" class="file" name="upload" id="upload">{$list_str}
					</div>
			  	 </div>
				 
				 <style type="text/css">
				 	.uploadify-queue{width:450px}
				 </style>
				<script src="{$_K[siteurl]}/resource/js/uploadify/jquery.uploadify-3.1.min.js" type="text/javascript"></script>
				<link href="{$_K[siteurl]}/resource/js/uploadify/uploadify.css" rel="stylesheet">
				<script type="text/javascript">
				var xyq = "{$xyq}";
					$(function(){
						if($('#SWFUpload_0').length==0){
							construct();
						}
						$(":checkbox[name='hidemode']").click(function(){
							$(this).nextAll('.messages').toggle();
						})
					})
					function construct(){
						uploadify({
								auto:true,
								size:"{$max_size}MB",
								exts:'{$flie_types}',
								limit:5},
								{
									objType:'task',
									task_id:'{$task_id}',
									mode:'back',
									obj_id:'{$task_id}'
								}
							);
					}
					function uploadResponse(json){
						if($("#"+json.fid).length<1){
								var file_ids = $("#file_ids").val();
								 if(file_ids){
									$("#file_ids").val(file_ids+','+json.fid)
								}else{	
									$("#file_ids").val(json.fid);
									$("#upload").val('');
								}
						}
					}
					function subFile(){
						var file_ids= $('#file_ids').val();
						var del_ids = $('#del_ids').val();
							var url = "$_K[siteurl]/index.php?do=ajax&view=task&ajax=back_upload&task_id={$task_id}";
							$.getJSON(url+'&file_ids='+file_ids+'&del_ids='+del_ids,function(json){
								art.dialog.tips(json.msg,1.5);
							});
					}
					function fileDel(o,f_id){
						var del_ids = $('#del_ids').val();
						$('#del_ids').val(del_ids+','+f_id);
						$(o).parents('.uploadify-queue').fadeOut();
					}
			</script>
			</li>
		</ul>
STRING
;
		return $str;
	}
	/**
	 * 后台查询条件
	 */
	static function condit_format($w, $url_str, $ord) {
		$cond = array ();
		$k_arr = array ();
		foreach ( $_REQUEST as $k => $v ) {
			switch ($k) {
				case "model_id" : //模型编号
					// 40 雇佣, 41 招标, 42 服务, 43 直接雇佣
					if ( $v > 4 ) {
						if ( $v == '43' ) {
							$w .= ' and model_id = 4 and task_type = 3 ';
						} elseif ( $v == '42' ) {
							$w .= ' and model_id = 4 and task_type = 2 ';
						}elseif ( $v == '41' ) {
							$w .= ' and model_id = 4 and task_cash_coverage > 0 ';
						} else {
							$w .= ' and model_id = 4 and !ifnull(task_cash_coverage,0) and task_type < 2 ';
						}
						$k_arr [$k] = $v;
						break;
					}
				case "task_id" : //任务编号
				case 'indus_id' :
					intval ( $v ) and $w .= ' and ' . $k . '=' . intval ( $v );
					$k_arr [$k] = $v;
					break;
				case "task_status" : //任务状态
					$v === 0 and $w .= ' and task_status=0' or ($v != '' and $w .= ' and task_status=' . intval ( $v ));
					$k_arr ['task_status'] = $v;
					break;
				case "task_title" : //任务标题
				case "username" : //用户名
					strval ( $v ) and $w .= ' and ' . $k . ' like "%' . strval ( $v ) . '%" ';
					$k_arr [$k] = $v;
					break;
				case "start_time" : //开始日期
					$v and $w .= ' and start_time >="' . strtotime($v) . '" ';
					$k_arr ['start_time'] = $v;
					break;
				case "end_time" : //结束日期
					$v and $w .= ' and start_time <="' . strtotime($v) . '" ';
					$k_arr ['end_time'] = $v;
					break;
				case "cash_status" : //托管与否
				case "must_choosework" : //保证选稿
					$v != 'all' and $w .= ' and ' . $k . '=' . intval ( $v );
					$k_arr [$k] = $v;
					break;
			}
		}
		$ord [0] && $ord [1] and $w .= ' order by ' . $ord [0] . ' ' . $ord [1] or $w .= ' order by task_id desc';
		$cond ['w'] = $w;
		$cond ['url'] = $url_str . '&' . http_build_query ( $k_arr );
		return $cond;
	}
	/**
	 * 任务稿件、附件、留言、收藏、举报删除
	 * @param $model_id 模型ID
	 * @param $task_id 任务ID 可能为数组
	 * @param $is_array 判断传递ID是否为数组
	 */
	public static function delete_task_releate_item($model_id, $task_id, $is_array = false) {
		global $kekezu;
		$model_code = $kekezu->_model_list [$model_id] ['model_code'];
		$model_code == 'tender' || $model_code == 'dtender' and $tab_work = "task_bid" or $tab_work = 'task_work';
	/** 任务稿件*/
	
	/** 任务附件*/
	
	/** 任务留言*/
	
	/** 任务收藏*/
	
	/** 任务交易维权*/
	
	}
}