<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 任务相关的ajax调用
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
switch ($ajax) {
	case "user_detail" : 
		$user_info 		= keke_user_class::get_user_info ( $user_id ); 
		$level          = keke_glob_class::get_w_level_ico($user_info['w_level']);
		$take_num  		= intval($user_info['take_num']);
		$income         = db_factory::get_one(' select sum(fina_cash) cash,sum(fina_credit) credit from '.TABLEPRE.'witkey_finance where uid='.$user_id.' and fina_type="in" and fina_action="task_bid" ');
		$buyer_aid	    = keke_user_mark_class::get_user_aid ( $user_id, '2', null, '1' );
	break;
	case 'show_secode':
		 
		require keke_tpl_class::template('ajax/ajax_menu');
		exit();
	break;
	case "set_comment" :
		$comment_arr = array ("obj_id" => $obj_id, "origin_id" => $origin_id, "obj_type" => $obj_type, "p_id" => $p_id, "uid" => $uid, "username" => $username, "content" => $tar_content, "on_time" => time () );
		kekezu::set_leave ( $comment_arr, $type, $pk, '', 'json' );
		break;
	case "load_comment" :
	case "load_reply" :
		if ($comment_id) {
			$comm_info = db_factory::get_one ( sprintf ( " select * from %switkey_comment where comment_id = '%d'", TABLEPRE, $comment_id ) );
		} else {
			die ();
		}
		break;
	case "del_comment" :
		kekezu::del_comment ( $pk, $obj_type, $comment_id, $origin_id, '', 'json' );
		break;
	case "prom_link" :
		$title = "获取推广链接";
		$link = $_K [siteurl] . $_K['siteurl']."/index.php?do=prom&u=" . $user_info ['uid'] . "&p=" . $prom_code;
		$o and $link .= "&o=" . $o;
		$l and $link .= "&l=" . $l;
		$promtext or $promtext = $_K ['html_title'];
		break;
	case "prom_list" :
		$model_list = $kekezu->_model_list;
		$page_obj = $kekezu->_page_obj;
		$page_obj->setAjax ( '1' );
		$page_obj->setAjaxDom ( 'ajax_list' );
		$page or $page = 1;
		$page_size or $page_size = 10;
		$url = $_K['siteurl']."/index.php?do=ajax&view=menu&ajax=prom_list&prom_code=$prom_code";
		$prom_rule = keke_prom_class::get_prom_rule ( $prom_code );
		switch ($prom_code) { 
			case "bid_task" :
				$table_title = array ($_lang['belong_model'], $_lang['task_title'], $_lang['task_cash'], $_lang['pro_cash'] );
				$task_obj = new Keke_witkey_task_class ();
				$where = " task_status='2' ";
				$prom_rule ['model'] and $where .= " and model_id in (" . $prom_rule [model] . ") ";
				$prom_rule ['indus_string'] and $where .= " and indus_id in (" . $prom_rule [indus_string] . ") ";
				$task_obj->setWhere ( $where );
				$count = intval ( $task_obj->count_keke_witkey_task () );
				$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
				$task_obj->setWhere ( $where . $pages ['where'] );
				$prom_list = $task_obj->query_keke_witkey_task ();
				break;
			case "service" :
				$table_title = array ($_lang['belong_model'], $_lang['goods_title'], $_lang['goods_cash'], $_lang['pro_cash'] );
				$ser_obj = new Keke_witkey_service_class ();
				$where = " service_status='2' ";
				$prom_rule ['model'] and $where .= " and model_id in (" . $prom_rule [model] . ") ";
				$prom_rule ['indus_string'] and $where .= " and indus_id in (" . $prom_rule [indus_string] . ") ";
				$ser_obj->setWhere ( $where );
				$count = intval ( $ser_obj->count_keke_witkey_service () );
				$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
				$ser_obj->setWhere ( $where . $pages ['where'] );
				$prom_list = $ser_obj->query_keke_witkey_service ();
				break;
		}
		break;
}
require keke_tpl_class::template ( "ajax/ajax_" . $view );



