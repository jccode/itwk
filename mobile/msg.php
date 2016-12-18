<?php

/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' )&&defined('ISWAP')&&ISWAP or kekezu::echojson ($wap_msg, 0);
$views = array('list','send');
in_array($view,$views) or $view='list';

switch ($view){
	case "list"://收
		$ops = array('get','send');
		in_array($op,$ops) or $op='get';
		$ls = max($ls,0);
		$le = max($le,10);
		$sql = 'select * from %switkey_msg where 1=1 ';
		$op=='get' and $sql.=' and to_uid=%d' or $sql.=' and uid=%d';
		$sql.=' limit %d,%d';
		$info = db_factory::query(sprintf($sql,TABLEPRE,$uid,$ls,$le));
		$count = db_factory::execute(sprintf($sql,TABLEPRE,$uid,$ls,$le));
		kekezu::echojson($count,1,$info);
		break;
	case "send"://发
		$space_obj = new Keke_witkey_space_class ();
		$space_info = kekezu::get_user_info ( $to_username, 1 );
		
		if (! $space_info) {
			kekezu::echojson(array('r'=>'用户名不存在'),0);
		} 
		if (! $msg_title) {
			kekezu::show_msg ( "操作提示",$url, '1', $_lang['input_the_title'], 'alert_error' ) ;
		}
		
		if (! $msg_content) {
			kekezu::show_msg ( "操作提示",$url, '1', $_lang['input_the_content'], 'alert_error' ) ;
		}
		$msg_obj = new Keke_witkey_msg_class ();
		$msg_obj->setUid ( $uid );
		$msg_obj->setUsername ( $username );
		$msg_obj->setTo_uid ( $space_info ['uid'] );
		$msg_obj->setTo_username ( $space_info ['username'] );
		$msg_obj->setTitle ( kekezu::str_filter ( kekezu::escape($msg_title) ) );
		$msg_obj->setContent ( kekezu::str_filter (kekezu::escape($msg_content) ) );
		$msg_obj->setOn_time ( time () );
		$msg_id = $msg_obj->create_keke_witkey_msg();
		if($msg_id){
			kekezu::echojson(array('r'=>'发布成功'),1,intval($task_id));
		}else{
			kekezu::echojson(array('r'=>'服务器繁忙，请稍后...'),0);
		}

		break;
}
die();
