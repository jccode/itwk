<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2011-10-9 12:10
 * 待评价页
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page and $page = intval($page) or $page = 1;
$url = "index.php?do=user&view=employer&op=needmark";

$where = " where m.by_uid='$uid' and m.mark_status=0 and m.mark_type = 1";
$sql_count = "select count(*) from ".TABLEPRE."witkey_mark as m ";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

$sql = "select m.*,tw.*,tw.username as username from ".TABLEPRE."witkey_mark as m left join ".TABLEPRE."witkey_task_work as tw on m.origin_id = tw.task_id ";
$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );
$sql .= $where.$pages['where'];
$mark_arr = db_factory::query($sql);
	
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );