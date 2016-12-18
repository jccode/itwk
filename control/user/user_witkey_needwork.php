<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2011-10-9 12:10
 *  待上传作品
 */
//待上传作品
$needupload_count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_task a right join "
.TABLEPRE."witkey_task_work b on a.task_id=b.task_id where b.uid='$uid' and b.work_status=11 and a.task_status = 4 and a.model_id = 4"); 

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page and $page = intval($page) or $page = 1;
$url = "index.php?do=user&view=witkey&op=needwork";

$where = " where b.uid='$uid' and b.work_status=11 and a.task_status = 4 and a.cash_status=1 and a.model_id = 4"; 
$sql_count = "select count(*) from ".TABLEPRE."witkey_task a right join ".TABLEPRE."witkey_task_work b on a.task_id=b.task_id";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

$sql = "select * from ".TABLEPRE."witkey_task a right join ".TABLEPRE."witkey_task_work b on a.task_id=b.task_id";
$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );
$sql .= $where.$pages['where'];
$task_work_arr = db_factory::query($sql);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );