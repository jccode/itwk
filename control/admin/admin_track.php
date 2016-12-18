<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2010-10-31 下午13：30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(169);

$where = "1=1";
$order = "";

$task_id and $where .= " and task_id = '$task_id' ";
$task_title and $where .= " and task_title like '$task_title%' ";
$t_username and $where .= " and t_username = '$t_username' ";
$g_username and $where .= " and username = 'g_username' ";

$ord[0] or $ord[0] = 'dateline';
$ord[1] or $ord[1] = 'desc';
$order = " order by {$ord[0]} {$ord[1]} ";

	

$page_obj = new keke_page_class();
$count = db_factory::get_count("select count(t_id) from ".TABLEPRE."witkey_task_track where $where");
intVal($page) or $page = 1;
$page_size or $page_size = 10;
$url = "index.php?do=track&task_id=$task_id&$task_title=".urlencode($task_title)."&t_username=".urlencode($t_username)."&g_username=".urlencode($g_username)."&ord[0]={$ord[0]}&ord[1]={$ord[1]}&page_size=$$page_size";

$pages = $page_obj->getPages($count, $page_size,$page, $url);



$query_list = db_factory::query("select * from ".TABLEPRE."witkey_task_track where $where $order ".$pages['where']);


//格式化
$track_list = array();
foreach($query_list as $v){
	$arr = array();
	$v['ext'] and $arr = unserialize($v['ext']);
	unset($v['ext']);
	$track_list[] = array_merge($v,$arr);
}

require keke_tpl_class::template ( 'control/admin/tpl/admin_track' );




