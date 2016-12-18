<?php
/**
 * @copyright keke-tech
 * @author tank
 * 
 * 2012-5-23 
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 157 );

$levellist = epweike_vip_class::level_list();

$t_obj = keke_table_class::get_instance ( "witkey_vip_history" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size= intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&txt_level_id=$txt_level_id&txt_h_id=$txt_h_id&txt_username=$txt_username&txt_cash_cost=$txt_cash_cost&ord[]=$ord[0]&ord[]=$ord[1]";

 //条件
$where = ' 1 = 1  ';
$txt_h_id and $where .= " and h_id = '".$txt_h_id."' ";
$txt_username and $where .= " and username = '".$txt_username."' "; 
$txt_cash_cost and $where .= " and cash_cost = '".$txt_cash_cost."' ";	
$txt_level_id and $where .= " and level_id = '$txt_level_id' ";

if($ord [1]){
	//$where .= " order by $ord[0] $ord[1] "; 
	$where .= " order by h_id $ord[1] "; 
	
}else{
	$where .= " order by h_id asc";
}

$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
$history_arr = $d [data];
$pages = $d [pages];

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view);