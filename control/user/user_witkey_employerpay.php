<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2011-10-9 12:10
 * 待雇主确认付款
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");//价格区间
$model_arr = kekezu::get_table_data("*","witkey_model","","","","","model_id");//任务模型

$page and $page = intval($page) or $page = 1;
$url = "index.php?do=user&view=witkey&op=employerpay";

$where = " where a.uid ='$uid' and a.exec_time>0"; 
$sql_count = "select count(*) from ".TABLEPRE."witkey_task_work as a ";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

$sql = "select a.*,b.* from ".TABLEPRE."witkey_task_work as a left join ".TABLEPRE."witkey_task b on a.task_id = b.task_id";
$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );
$sql .= $where.$pages['where']; 
$task_work_arr = db_factory::query($sql);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );