<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-6-26 14:09
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (167);

$table_obj = new keke_table_class ( "witkey_shop_cate" );
$cate_obj = new Keke_witkey_shop_cate_class();
$url="index.php?do=$do&view=$view&w[member_id]=".$w['member_id']."&w[truename]=".$w['truename']."&w[shop_id]=".$w['shop_id']."&page=".$page."&w[page_size]=".$w['page_size'];

if(isset($ac) && $ac=='del'){
	if($cate_id){
		$res =$table_obj->del ( 'cate_id', $cate_id, $url );
		kekezu::admin_system_log('删除商铺案例分类'.':' . $cate_id );//日志记录
		$res and kekezu::admin_show_msg ( "删除成功", $url,3,'','success' ) or kekezu::admin_show_msg ("删除失败", $url,3,'','warning' );
	}else {
		kekezu::admin_show_msg ( "删除失败", $url );
	}
} elseif (isset ( $sbt_action )) { //批量删除
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	if (count ( $ckb_string )) {
		$cate_obj->setWhere ( 'cate_id in (' . $ckb_string . ')' );
		$res = $cate_obj->del_keke_witkey_shop_cate();//删除
		kekezu::admin_system_log('删除多个商铺案例分类'.':' . $ckb_string );//日志记录
		$res and kekezu::admin_show_msg ( "批量操作成功", $url ,3,'','success') or kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );
	} else {
		kekezu::admin_show_msg ( "批量操作失败", $url,3,'','warning' );
	}
} else {
	$where = '  1 = 1'; //查询
	$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
	$page and $page = intval ( $page ) or $page = '1';
	//组合查询条件
	$w ['cate_id'] and $where .= " and cate_id = '".$w['cate_id']."' ";
	$w ['cate_name'] and $where .= " and cate_name like '%".$w['cate_name']."%' ";
	$w ['shop_id'] and $where.=" and shop_id = '".$w['shop_id']."' ";
	is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $where .= ' order by cate_id desc';
	
	$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null);
	$cate_arr = $r [data];
	$pages = $r [pages];
}

require $template_obj->template ( 'control/admin/tpl/admin_shop_' . $view );