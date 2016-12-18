<?php

/**
 * @copyright keke-tech
 */

defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ($wap_msg, 0);

$task_id = intval ( $task_id );
if ($task_id) {
	$model_list = $kekezu->_model_list;
	$views = array ('index', 'work_list','work','work_hand',"work_info");
	in_array ( $view, $views ) or $view = 'index';
	$task_info = wap_base_class::get_task_info($task_id);
	
	if($action=="upload"){
		wap_base_class::wap_upload('uploadedfile',$task_id,$work_id);
	}
	
	if($task_info['model_id']){
		$tmp_class = $model_list[$task_info['model_id']]['model_code'].'_wap_task_class';
		$Obj       = new $tmp_class($task_info); 
	}
	
	
	switch ($view){
		case "index"://任务首页
			$data = $Obj->wap_info();
			$data and kekezu::echojson('',1,$data) or kekezu::echojson(array('r'=>'Task does not exist'),0);
			die();
			break;
		case "work_list"://稿件列表
			$Obj->wap_list();
			break;
		case "work":
			break;
		case "work_info"://稿件详细
			$Obj->wap_workinfo($work_id);
			break;
	}
	
	switch ($action){
		case "work_bid"://中标
		case "work_in"://入围
		case "work_out"://淘汰
			$Obj->wap_process($work_id,$action);
			break;
		case 'favor':
			$Obj->wap_favor();
			break; 
		case 'report':
			$Obj->wap_report($desc);
			break;
		case "work_hand"://交稿 
			if(intval($user_info['auth_mobile'])!=1||intval($user_info['auth_email'])!=1){
				kekezu::echojson ( '', 0 , '参加任务请先通过手机和邮箱认证' );
			}
			if (kekezu::utf8_strlen ( kekezu::gbktoutf ($work_desc) ) < 20 || kekezu::utf8_strlen ( kekezu::gbktoutf ($work_desc) ) > 1500) {
				kekezu::echojson ( '', 0,'任务描述长度20-1500字' );
			}
			if(intval($task_info['model_id']==4)){
				$work_desc1['tar_content']=$work_desc;
				$work_desc1['task_over_time']=$cycle;
				$work_desc1['txt_cash']=$quote;
				if($area){
					$area_arr = explode(",",$area);
					if(is_array($area_arr)){
						$work_desc1['province']=$area_arr[0];
						$work_desc1['city']=$area_arr[1];
						$work_desc1['area']=$area_arr[2];
					}
				}
				$Obj->wap_hand($work_desc1);
			}else{
				$Obj->wap_hand($work_desc);
			}
			
			break;
		case 'work_list':
			$Obj->wap_work_count();
			break;
		case 'reqedit': 
			$Obj->wap_reqedit($desc);
			break;
		
	}
}else{
	kekezu::time2Units($time);
	kekezu::echojson (array('d'=>'Missing task id '),0);
}