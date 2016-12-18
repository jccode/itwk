<?php

class wap_base_class {
	
	public static function is_wrap() {
		return strpos ( $_SERVER ['HTTP_VIA'], "wap" ) > 0 ? true : false; //会屏蔽wap模拟器
	}
	/**
	 * 检测登陆状态
	 */
	public static function check_login() {
		$uid = intval ( $_SESSION ['uid'] );
		if ($uid) {
			return true;
		} else {
			kekezu::echojson ( array ('a' => 'relogin', 'r' => 'Connection timed out' ), 0 );
			die ();
		}
	}
	/**
	 * 更改登陆状态
	 * @param $s  1:online 2:offline
	 */
	public static function update_load_status($uid, $s = 1) {
		if ($s == 1) {
			$s = 'online';
		} elseif ($s == 2) {
			$s = 'offline';
		}
		$uid && db_factory::execute ( sprintf ( " update %switkey_space set client_status='%s' where uid='%d'", TABLEPRE, $s, $uid ) );
	}
	/**
	 * 获取任务信息
	 */
	public static function get_task_info($task_id) {
		return db_factory::get_one ( sprintf ( " select * from %switkey_task where task_id='%d'", TABLEPRE, $task_id ) );
	}
	/**
	 * 任务搜索
	 */
	public static function get_task_list() {
		$_D = $_REQUEST;
		$_D ['action'] and $ac = $_D ['action'] or $ac = 'task';
		switch ($ac) {
			case 'task' :
				$ords = array (1 => 'start_time desc ', 2 => 'start_time asc', 3 => 'task_cash asc', 4 => 'task_cash desc' );
				$pre1 = 'select * ';
				$pre2 = 'select count(task_id) as count ';
				$sql = 'from ' . TABLEPRE . 'witkey_task where 1=1 and task_type != 3  and task_status !=-1 and task_status !=0 and task_status !=1 and task_status !=10  ';
				//$mid = max ( $_D ['model_id'], 1 ); //模型编号
				
				if($_D ['model_id']==5){
					$_D ['model_id'] and  $sql .= ' and model_id=4 and task_type =1 and ifnull(task_cash_coverage,0)<1 ' ;
				}else{
					$_D ['model_id'] and  $sql .= ' and model_id=' . $_D ['model_id'] or $sql .= ' and model_id in(1,2,3,4)';
				}
				
			
			
				$i_id = intval ( $_D ['indus_id'] ); //子行业
				$i_id && $sql .= ' and indus_id=' . $i_id;
				$t = trim ( strval ( $_D ['search_key'] ) ); //搜索标题\
				$t && $sql .= ' and (task_title like "%' . $t . '%" or username like "%' . $t . '%" or task_id = '.intval($t).' ) ';
				if (isset ( $_D ['status'] )) {
					if ($_D ['status'] != "all") {
						$sql .= ' and task_status=' . intval ( $_D ['status'] );
					}
				} else {
					$sql .= " and task_status in(2,3)";
				}
				$puid = intval ( $_D ['u_id'] ); //发布者ID
				$puid && $sql .= ' and uid=' . $puid;
				$ord = max ( $_D ['order'], 1 ); //排序条件
				$sql .= ' order by ' . $ords [$ord];
				$count = db_factory::get_count ( $pre2 . $sql );
				$ls = $_D ['ls']; //开始
				$le = $_D ['le']; //结束
				$sql .= ' limit ' . $ls . ',' . $le;
				$data = db_factory::query ( $pre1 . $sql );
				kekezu::echojson ( intval ( $count ), $_D ['ord'], $data );
				break;
			case 'join' :
				self::get_join_task ( $_D );
				break;
			case 'favor' :
				self::get_favor_task($_D);
				break;
		}
		die ();
	}
	/**
	 * 参加的任务 
	 */
	static function get_join_task($d) {
		global $uid;
		$c_sql = ' select count(DISTINCT a.task_id) c ';
		$q_sql = ' select DISTINCT a.* ';
		$sql = ' from ' . TABLEPRE . 'witkey_task a left join ' . TABLEPRE . 'witkey_task_work b on a.task_id=b.task_id where 
						b.uid=' . $uid . ' order by a.task_id desc ';
		$ls = $d ['ls']; //开始
		$le = $d ['le']; //结束
		$sql .= ' limit ' . $ls . ',' . $le;
		$count = db_factory::get_count ( $c_sql . $sql );
		$data = db_factory::query ( $q_sql . $sql );
		kekezu::echojson ( intval ( $count ), 1, $data );
	}
	
	/**
	 * 获取收藏
	 */
	static function get_favor_task($d) {
		global $uid,$kekezu;
		$ls = $d ['ls']; //开始
		$le = $d ['le']; //结束
		$model_id = max ( $d['model_id'], 1 );
		$obj_type = $kekezu->_model_list [$model_id] ['model_code'];
		$csql = " select count(b.f_id) c ";
		$qsql = " select a.* ";
		$sql  = " from %switkey_favorite b left join %switkey_task a on 
				  a.task_id = b.obj_id where b.uid='%d' and b.obj_type='%s' and keep_type='task' ";
		$count = db_factory::get_count(sprintf($csql.$sql,TABLEPRE,TABLEPRE, $uid, $obj_type));
		$sql.=" limit %d,%d ";
		$data = db_factory::query ( sprintf ( $qsql.$sql, TABLEPRE,TABLEPRE, $uid, $obj_type, $ls, $le ) );
		kekezu::echojson (intval($count), 1, $data );
		die ();
	}
	/**
	 * 文件上传
	 */
	static function wap_upload($f='uploadedfile',$task_id,$work_id=0){
		global $kekezu;
	 	$work_id and $type="work" or $type="task";
		$path = keke_file_class::upload_file($f);

		$file_obj = new Keke_witkey_file_class(); 
		$file_obj->setFile_name($_FILES[$f][name]); 
		$file_obj->setTask_id($task_id);
		$file_obj->setObj_type($type);
		$file_obj->setSave_name($path);
		$file_obj->setOn_time(time());
		$file_obj->setUid($kekezu->_uid);
		$file_obj->setUsername($kekezu->_username);
		$file_obj->create_keke_witkey_file();
 
		if($path){
			$fid = db_factory::get_count(sprintf(" select file_id from %switkey_file where save_name='%s'",TABLEPRE,$path));
			if($work_id){
				db_factory::execute(sprintf("update %switkey_task_work set work_file='%d' where task_id='%d' and work_id='%d'",TABLEPRE,$fid,$task_id,$work_id));
				db_factory::execute(sprintf(" update %switkey_file set obj_type='work',task_id='%d',obj_id='%d' where file_id='%d'",TABLEPRE,$task_id,$work_id,$fid));
			}else{
				db_factory::execute(sprintf("update %switkey_task set task_file='%d' where task_id='%d'",TABLEPRE,$fid,$task_id));
				db_factory::execute(sprintf(" update %switkey_file set obj_type='task',task_id='%d' where file_id='%d'",TABLEPRE,$task_id,$fid));
			}
			kekezu::echojson('',1);
		}else{
			kekezu::echojson(array('r'=>'Upload failed'),0);
		}
		die();
	}
	/**
	 * 获取附件
	 */
	static function get_wap_file($task_info){
		global $_K;
		$data = array();
		$f    = $task_info['task_file'];
		if($f){
			$data=db_factory::query(" select file_name as name,CONCAT('".$_K['siteurl']."/',`save_name`) path from ".TABLEPRE."witkey_file where file_id in (".$f.")");
		}
		return $data;
	}
}