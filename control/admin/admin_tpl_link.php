<?php
/**
 * @copyright keke-tech
 * @author 九江
 * @version v 2.0
 * 2011-9-1 
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role (30);

$t_obj = keke_table_class::get_instance ( "witkey_link" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size=intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&txt_location=$txt_location&txt_link_id=$txt_link_id&txt_link_url=$txt_link_url&txt_link_name=$txt_link_name&ord[]=$ord[0]&ord[]=$ord[1]";
if ($ac == 'del') {
	if ($link_id) {
		$res = $t_obj->del ( "link_id", $link_id, $url );
		kekezu::admin_system_log ( $_lang['links_delete'].$link_id );
		kekezu::admin_show_msg ( $_lang['delete_success'], $url,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['delete_fail'], $url ,3,'','warning');
	}
} elseif (isset ( $sbt_action ) && $sbt_action == $_lang['mulit_delete']) { //批量操作
	empty ( $ckb ) and kekezu::admin_show_msg ( $_lang['choose_operate_item'], 'index.php?do=' . $do . '&view=' . $view ,3,'','warning');
	$res = $t_obj->del ( "link_id", $ckb );
	if ($res) {
		kekezu::admin_system_log ( $_lang['links_delete'] . implode ( ",", $ckb ) );
		kekezu::admin_show_msg ( $_lang['mulit_operate_success'], $url,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );
	}

} else {
	$where = ' 1 = 1  ';
	$txt_link_id and $where .= "  and link_id = ".intval($txt_link_id);
	$txt_link_name and $where .= " and link_name like '%" .$txt_link_name.  "%'";
	$txt_link_url and $where .= "  and link_url = '".$txt_link_url."'";

	if($txt_location){
		$location_tag = keke_core_class::link_make_tag( array($txt_location=>1) );
		$where .= " AND (location & $location_tag = $location_tag)";
	}
	
	if($ord [1]){
		$where .= " order by $ord[0] $ord[1] ";
	}else{
		$where .= " order by listorder asc";
	}
	$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
	$link_arr = $d [data];
	$pages = $d [pages];
}

$link_cat_list = keke_glob_class::get_link_cat();
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );