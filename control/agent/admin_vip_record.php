<?php
/**
 * @copyright keke-tech
 * @author xxy
 * 
 * 2012-10-10
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
global $admin_info;
kekezu::admin_check_role ( 157 );
//根据不同代理商得到不同店铺分类，要求店铺分类有代理商地域的名称，如台湾代理商，则提取vip店铺中有“台湾”字样的店铺
$levellist = epweike_vip_class::level_list();
//foreach($levellist as $v){
//	if(strpos($_SESSION['brandType'],'tw')!==false&&strpos($v['level_name'], '台湾')!==false) $agent_levellist[]=$v;
//}

$t_obj = keke_table_class::get_instance ( "witkey_vip_history" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size= intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&txt_level_id=$txt_level_id&txt_h_id=$txt_h_id&txt_username=$txt_username&txt_cash_cost=$txt_cash_cost&ord[]=$ord[0]&ord[]=$ord[1]";

$field="h.*,b.brand";
$table=array('h'=>'witkey_vip_history','b'=>'witkey_space');
$join=array("inner join");
$on=array('h.uid=b.uid');
 //条件
foreach($levellist as $v){
	$levelarray[].=$v['level_id'];
}
$levelstr=implode(',', $levelarray);
$where="b.brand in (".$_SESSION['brandType'].") and h.level_id in(".$levelstr.")";
$txt_h_id and $where .= " and h.h_id = '".$txt_h_id."' ";
$txt_username and $where .= " and h.username = '".$txt_username."' "; 
$txt_cash_cost and $where .= " and h.cash_cost = '".$txt_cash_cost."' ";	
$txt_level_id and $where .= " and h.level_id = '$txt_level_id' ";

if($ord [1]){
	//$where .= " order by $ord[0] $ord[1] "; 
	$order = " h.h_id $ord[1] "; 
	
}else{
	$order = " h.h_id desc";
}
//$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
$d=$t_obj->get_multi_grid($field,$table,$join,$on,$where,$order,$page,$slt_page_size,$url);
$history_arr = $d [data];
$pages = $d [pages];

require $template_obj->template ( 'control/agent/tpl/admin_' . $do . '_' . $view);