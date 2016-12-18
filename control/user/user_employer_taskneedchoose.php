<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2011-10-9 12:10
 * 待选稿任务页
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");//价格区间
$model_arr = kekezu::get_table_data("*","witkey_model","","","","","model_id");//任务模型

$page_obj = $kekezu->_page_obj;  
$page and $page = intval( $page ) or $page = 1;
$url = "index.php?do=user&view=employer&op=taskneedchoose";

$task_obj = new Keke_witkey_task_class();
$where = " uid='$uid' and task_status = '3'";
$where .= " order by task_id DESC ";

$task_obj->setWhere ( $where ); 
$count = $task_obj->count_keke_witkey_task();
$pages = $page_obj->getPages ( $count, 10, $page, $url );

$task_obj->setWhere ( $where . $pages [where] );
$task_arr = $task_obj->query_keke_witkey_task(); 

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );