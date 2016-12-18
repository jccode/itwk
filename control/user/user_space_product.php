<?php
/**
 * @copyright keke-tech
 * @author wrh
 * @version v 2.0
 * 2012-6-16   10:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//分页
$page_obj = $kekezu->_page_obj;
$page_size and $page_size = intval ( $page_size ) or $page_size = '10';
$page and $page = intval ( $page ) or $page = '1';
$url = $origin_url . "&op=$op&page_size=$page_size&page=$page";
$url_str="index.php?do=user&view=space&op=$op";
$porduct_obj = new Keke_witkey_service_class ();
//搜索条件

$where = "uid= '$uid' ";
$title and $where .= "and title like '%$title%'";
$on_time and $where .= "or on_time=" . intval(strtotime( '$on_time' ) );
$ord and $where .= "order by $ord" or $where .= " order by service_id desc";
$porduct_obj->setWhere ( $where );
$count = intval ( $porduct_obj->count_keke_witkey_service () );
$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
$porduct_obj->setWhere ( $where . $pages ['where'] );
$porduct_arr = $porduct_obj->query_keke_witkey_service ();


//删除和批量删除
$sp_obj = keke_table_class::get_instance("witkey_service");
if($ac=='del'&& $service_id){
	 $res = $sp_obj->del("service_id", $service_id);
    $res and kekezu::show_msg('删除成功',$url_str."&page=$page",3,'','success') or kekezu::show_msg('删除失败',$url_str."&page=$page",3,"","warning");
}elseif($ckb){
   $res = $sp_obj->del("service_id", array_filter($ckb));
   if($ckb){
	   	$sql = "delete from keke_witkey_service where service_id in(".implode(',', $ckb).")";
		$res = db_factory::execute($sql);
	    $res and kekezu::show_msg( '批量删除成功',$url_str."&page=$page",3,'','success') or kekezu::show_msg( '批量删除成功',$url_str."&page=$page",3,"","warning") ;
   }else{
    	kekezu::show_msg( '删除失败',$url_str."&page=$page",3,"","warning") ;
   }
}
require keke_tpl_class::template ( "user/" . $do . "_".$view."_$op");