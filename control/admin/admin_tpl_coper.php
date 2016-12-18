<?php
/**
 * 
 * @author xxy
 * 2012-09-25
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (30);

$t_obj = keke_table_class::get_instance ( "witkey_coper" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size=intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&txt_location=$txt_location&txt_coper_id=$txt_coper_id&txt_coper_url=$txt_coper_url&txt_coper_name=$txt_coper_name&ord[]=$ord[0]&ord[]=$ord[1]";
if ($ac == 'del') {
	if ($coper_id) {
		$res = $t_obj->del ( "coper_id", $coper_id, $url );
		kekezu::admin_system_log ( $_lang['copers_delete'].$coper_id );
		kekezu::admin_show_msg ( $_lang['delete_success'], $url,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['delete_fail'], $url ,3,'','warning');
	}
} elseif (isset ( $sbt_action ) && $sbt_action == $_lang['mulit_delete']) { //批量操作
	empty ( $ckb ) and kekezu::admin_show_msg ( $_lang['choose_operate_item'], 'index.php?do=' . $do . '&view=' . $view ,3,'','warning');
	$res = $t_obj->del ( "coper_id", $ckb );
	if ($res) {
		kekezu::admin_system_log ( $_lang['copers_delete'] . implode ( ",", $ckb ) );
		kekezu::admin_show_msg ( $_lang['mulit_operate_success'], $url,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );
	}
} else {
	$where = ' coper_id>0  ';
	$txt_coper_id and $where .= "  and coper_id = ".intval($txt_coper_id);
	$txt_coper_name and $where .= " and name like '%" .$txt_coper_name.  "%'";

	if($txt_location){
		$location_tag = keke_core_class::coper_make_tag( array($txt_location=>1) );
		$where .= " AND (location & $location_tag = $location_tag)";
	}
	
	if($ord [1]){
		$where .= " order by $ord[0] $ord[1] ";
	}else{
		$where .= " order by coper_id desc";
	}
	$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
	$coper_arr = $d [data];
	$pages = $d [pages];
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );