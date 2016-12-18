<?php
/**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2011-9-2
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$feed_obj = new Keke_witkey_feed_class ();
$feed_type = keke_glob_class::get_feed_type ();

intval ( $page ) or $page = 1;
$t_uid or $t_uid = -1;
$where = " uid = '$t_uid' order by feed_id desc"; 
$feed_obj->setWhere ( $where );
$count = $feed_obj->count_keke_witkey_feed ();

//分页条件
$url = "index.php?do=$do&view=$view&t_uid=$t_uid&page=$page";
$kekezu->_page_obj->setAjax(1);
$kekezu->_page_obj->setAjaxDom("ajax_dom");
$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );

//查询结果数组
$feed_obj->setWhere ( $where . $pages [where] ); 
$feed_arr = $feed_obj->query_keke_witkey_feed ();

foreach ($feed_arr as $k=>$v) {
	$title_arr = unserialize($v[title]);
	$title_str =' <a href="'.$title_arr[feed_username][url].'">'.$title_arr[feed_username][content].'</a>'.$title_arr[action][content].'
	<a href="'.$title_arr[event][url].'">'.$title_arr[event][content].'</a>'; 
	$v[title] = $title_str;
	$new_feed_arr[] = $v;
}

$feed_arr = $new_feed_arr;

require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_user_' . $view );