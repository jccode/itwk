<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

(!$user_info['uid']) and kekezu::show_msg('非法操作', $_K['siteurl'].'/index.php?do=login', 3, '请先登录再进行此操作！', 'warning');


 //邀请参与（人才大厅）
if($opp == 'task_list'){	
	($user_info['uid'] == $i_uid ) and kekezu::show_msg('消息提示', '', 3, '不能邀请自己！', 'warning');
	
	switch($ac){
		case 'ajax_invite': //确定邀请			
			$res = task_invite($task_id, $uid, $username, $i_uid);
			($res==1) and kekezu::echojson ( '邀请成功', "1" ) or kekezu::echojson ( $res, "0" );
			exit;
		break;
	}
	
	$task_obj = new Keke_witkey_task_class();	
	$page = $page ? intval($page) : 1;
	$page_size = $page_size ? intval($page_size) :5;
	$ac_url = $_K['siteurl']."/index.php?do=ajax&view=invite&opp=task_list&i_uid=$i_uid";
	
	$kekezu->_page_obj->setAjax(1);
	$kekezu->_page_obj->setAjaxDom('ajax_dom');
	$wh = " uid = '$uid' and task_status='2'";
	$count = db_factory::get_count(" select count(*) from " . TABLEPRE . "witkey_task where ".$wh);
	$pages = $kekezu->_page_obj->getPages($count, $page_size, $page, $ac_url);
	
	$task_obj->setWhere($wh.$pages[where]);
	$task_arr = $task_obj->query_keke_witkey_task();
	
	$title = "雇主操作——邀请高手来关注和参与我的任务";
	require keke_tpl_class::template("ajax/ajax_".$view."_tasklist");
}else{ //任务详细页
	
	switch($ac){
		case 'ajax_invite': //确定邀请
			is_array($ckb) and $i_uid = $ckb;
			$res = task_invite($task_id, $uid, $username, $i_uid);
			($res==1) and kekezu::echojson ( '邀请成功', "1" ) or kekezu::echojson ( $res, "0" );
			exit;
		break;
	}
	
	 //获取任务信息	
	$task_id and $task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id' and task_status = 2");
	$task_info or kekezu::show_msg('非法操作', '', 3, '您的任务无法使用邀请功能！', 'warning');
	
	$page = $page ? intval($page) : 1;
	$kekezu->_page_obj->setAjax(1);
	$kekezu->_page_obj->setAjaxDom('ajax_dom');
	$ac_url = $_K['siteurl']."/index.php?do=ajax&view=invite&task_id=$task_id";
	
	$where = " where a.uid <> '$uid' and a.shop_id is not null and a.group_id<1"; 
	$where .= " and b.skill_id = '$task_info[indus_id]'";
	$type_vip and $where .=" and a.isvip = 1";
	if($type_zone){
		$user_info['zone'] = explode(',', $user_info['residency']);
		$where .= " and a.residency like '".$user_info['zone']['0'].','.$user_info['zone']['1']."%'";
	}
	
	$sql_count = "select count(*) from ".TABLEPRE."witkey_space a left join ".TABLEPRE."witkey_member_skill b on a.uid = b.uid ";
	$sql_count .= $where;   
	$count = db_factory::get_count($sql_count);
	
	$sql = "select a.*,b.* from ".TABLEPRE."witkey_space a left join ".TABLEPRE."witkey_member_skill b on a.uid = b.uid ";	
	$pages = $kekezu->_page_obj->getPages ( $count, 5, $page, $url );
	$sql .= $where." order by a.w_level desc ".$pages['where'];
	$users_arr = db_factory::query($sql);	

	 //取不到精确值，就取其他数据
	if(($page > $pages['total']) && !$type_zone && !$type_vip){ 
		$where = '';
		$where = " where uid <> '$uid' and shop_id is not null"; 
		$sql_count = "select count(*) from ".TABLEPRE."witkey_space ";
		$sql_count .= $where;   
		$count = db_factory::get_count($sql_count);
		
		$sql = "select * from ".TABLEPRE."witkey_space";	
		$pages = $kekezu->_page_obj->getPages ( $count, 5, $page, $url );
		$sql .= $where." order by w_level desc ".$pages['where'];
		$users_arr = db_factory::query($sql);
	}
	
	$already_i_uid_arr = get_already_i_uid($task_info['task_id'],'arr');
	$title = "雇主操作——邀请高手来关注和参与我的任务";	
	require keke_tpl_class::template("ajax/ajax_".$view);
}	

 //获取已邀请的威客
function get_already_i_uid($task_id, $re_type = 'arr'){
	$task_invite = db_factory::query(" select * from ".TABLEPRE."witkey_task_invite where task_id = '$task_id'");
	$user_arr = array();
	foreach($task_invite as $val){
		$val['i_uid'] and $user_arr[] = $val['i_uid'];
	}
	
	switch($re_type){
		case 'arr': 
			return $user_arr;
		break;
		case 'str':
			return implode(',', $user_arr);
		break;
	}
}
function check_user_already_i($task_id, $i_uid){
	return db_factory::get_count(" select count(*) from " . TABLEPRE . "witkey_task_invite where task_id='$task_id' and i_uid = '$i_uid'");
}

 //获取威客技能
function get_attention_skill($uid, $indus_id = ''){
	global $indus_arr; 
	$skill_list = $skill_name = array();
	$skill_list = db_factory::query(" select sk.*,s.username from " . TABLEPRE . "witkey_member_skill sk left join 
	  	   " . TABLEPRE . "witkey_space s on sk.uid = s.uid where sk.uid = '$uid' ORDER BY sk.on_time DESC");
	
	foreach($skill_list as $v){
		if($v['skill_id'] == $indus_id){
			$skill_name[] = '<font color="red">'.$indus_arr[$v['skill_id']]['indus_name'].'</font>';
		}else{
			$skill_name[] = $indus_arr[$v['skill_id']]['indus_name'];
		}		
	}
	
	return implode(',', $skill_name);
}

 //任务邀请
function task_invite($task_id, $uid, $username, $i_uid, $check_count=20){
	global $_K;
	if(!$task_id || !$uid || !$username || !$i_uid) return '操作有误';
	if($uid == $i_uid) return '不能邀请自己';
	is_array($i_uid) and $i_uid = check_arr($i_uid);
	
	 //任务信息
	$task_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_task where task_id='$task_id' and uid='$uid' and task_status='2'");
	if(!$task_info) return '任务不存在'; 
	 
	 //已被邀请的总数
	$invite_count = db_factory::get_count(" select count(*) from ".TABLEPRE."witkey_task_invite where task_id = '$task_id'");
	if($invite_count > $check_count) return '任务邀请人数不能超于20个';

	 //将被邀请用户信息
	$i_user_info = db_factory::query(" select * from " . TABLEPRE . "witkey_space where uid in($i_uid)");
		
	 //已被邀请的记录
	$task_invite = db_factory::query(" select * from ".TABLEPRE."witkey_task_invite where task_id = '$task_id' and uid = '$uid' ");
	$already_i_uid = array();
	foreach($task_invite as $val){
		$already_i_uid[$val['i_uid']] = 1;
	}
	
	$msg_obj = new keke_msg_class();
	$msg_arr = array(
			'任务名称' => $task_info['task_title'],
			'任务链接' => $_K['siteurl'].'/index.php?do=task&task_id='.$task_id,
			'金额' => $task_info['task_cash'],
			'雇主' => $username
		);
	//print_r($msg_arr);exit;
	 //开始发送
	foreach($i_user_info as $val){
		if($already_i_uid[$val['uid']] == 1) continue;
		
		$invite_arr = array();
		$invite_arr['uid'] = $uid;
		$invite_arr['username'] = $username;
		$invite_arr['task_id'] = $task_id;
		$invite_arr['i_uid'] = $val['uid'];
		$invite_arr['i_username'] = $val['username'];
		$invite_arr['on_time'] = time();
		$res = db_factory::inserttable ( TABLEPRE ."witkey_task_invite", $invite_arr);
		
		//发送信息
		$msg_arr['用户'] = $val['username'];
		$msg_obj->setUid($val['uid']);		
		$msg_obj->setUsername($val['username']);

		$msg_obj->send_message ( $val['uid'], $val['username'], 'buyer_task_invite', '《'.$task_info['task_title'].'》任务邀请', $msg_arr, $val['email'] ); 
	}
	
	return true;
}

function check_arr($arr){
	$new_arr = array();
	foreach($arr as $v)
		$v and $new_arr[] = intval($v);
	
	return implode(',',$new_arr);
}