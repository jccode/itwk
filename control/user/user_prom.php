<?php
/**
 * 用户中心推广
 * @copyright keke-tech
 * @author deng
 * @version v 2.0
 * 2011-10-9 12:10
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$page_obj = $kekezu->_page_obj;
$page_size and $page_size=intval ( $page_size ) or $page_size = '10';
$page and $page=intval ( $page ) or $page = '1';
$event_id and $event_id= intval($event_id);
$url = "index.php?do=user&view=$view&op=$op&show=$show&page_size=$page_size&page=$page";

$status_arr = keke_prom_class::get_prelation_status();
$relation_obj=new Keke_witkey_prom_relation_class();
$ord_arr=array("on_time desc"=>$_lang['prom_time_desc'],"on_time asc"=>$_lang['prom_time_asc']);
//搜索条件
$where =  " prom_uid= '$uid' ";
$prom_status and $where .="and relation_status =".intval($prom_status);
if($start_time&&!$end_time){
	$where.=" and on_time >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$where.=" and on_time <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
	if($start_time==$end_time){
		$where.=" and on_time = ".intval(strtotime($start_time));
	}else{
		$where.=" and on_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}
$ord and $where .= "order by $ord" or $where .= " order by relation_id desc";
$relation_obj->setWhere($where);
$count=intval($relation_obj->count_keke_witkey_prom_relation());
$pages=$page_obj->getPages($count, $page_size, $page, $url,'#userCenter');
$relation_obj->setWhere($where . $pages['where']);
$relation_arr=$relation_obj->query_keke_witkey_prom_relation();
		
require keke_tpl_class::template ( SKIN_PATH."/user/" . $do . "_" .$view."_".$op);