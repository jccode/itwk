<?php
/**
 * @copyright keke-tech
 * @author tank
 * 
 * 2012-5-23 
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role (168);

$t_obj = keke_table_class::get_instance ( "witkey_shop_match" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size= intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&txt_m_id=$txt_m_id&txt_username=$txt_username&txt_shop_name=$txt_shop_name&ord[]=$ord[0]&ord[]=$ord[1]";

if ($ac == 'del') {
	if ($m_id) {
		$res = $t_obj->del ( "m_id", $m_id);
		kekezu::admin_system_log ( '同城速配记录删除成功'.$m_id );
		kekezu::admin_show_msg ( $_lang['delete_success'], $url,3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['delete_fail'], $url ,3,'','warning');
	}
}

 //条件
$where = ' 1 = 1  ';
$txt_m_id and $where .= " and m_id = '".$txt_m_id."' ";
$txt_username and $where .= " and username = '".$txt_username."' "; 
$txt_shop_name and $where .= " and shop_name = '".$txt_shop_name."' ";	

if($ord [1]){
	//$where .= " order by $ord[0] $ord[1] "; 
	$where .= " order by m_id $ord[1] "; 
	
}else{
	$where .= " order by m_id asc";
}

$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
$shop_match_arr = $d [data];
$pages = $d [pages];

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view);