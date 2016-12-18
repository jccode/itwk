<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

(!$user_info['uid']) and kekezu::show_msg('非法操作', $_K['siteurl'].'/index.php?do=login', 3, '请先登录再进行此操作！', 'warning');

switch($ac){
	case 'ajax_add_indus':
		$task_attention_obj = new Keke_witkey_task_attention_class();
		$task_attention_obj->setWhere("uid = ". $user_info['uid']);
		$res = $task_attention_obj->query_keke_witkey_task_attention();
		
		
		if( $res ){ //修改			
			$task_attention_obj->setWhere("uid = ". $user_info['uid']);	
			$task_attention_obj->setIndus_id($clist);
			$res = $task_attention_obj->edit_keke_witkey_task_attention();
		}else{ //添加
			$task_attention_obj->setUid($user_info['uid']);
			$task_attention_obj->setIndus_id($clist);
			$res = $task_attention_obj->create_keke_witkey_task_attention();
		}
		
		kekezu::echojson ( $_lang['success'], "1" ) or kekezu::echojson ( $_lang['fail'], "0" );
		die();
	break;
}

$task_attention_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_task_attention where uid='$user_info[uid]'");
if( $task_attention_info['indus_id'] ){
	$industry_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_industry where indus_id IN(".$task_attention_info[indus_id].")");
}

$title = '设置关注';
require $template_obj->template ( 'ajax/ajax_task_tag' );