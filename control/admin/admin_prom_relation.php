<?php
/**
 * 推广关系
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-09-02 11:40:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 58 );
$prom_relation_obj = new Keke_witkey_prom_relation_class ();
//分页
$page_size = isset ( $w ['page_size'] ) ? intval ( $w ['page_size'] ) : 10;
$page = $page ? intval ( $page ) : '1';

$url = "index.php?do=$do&view=$view&w[relation_id]={$w['relation_id']}&w[prom_username]={$w['prom_username']}&w[username]={$w['username']}&w[page_size]=$page_size&w[ord]={$w['ord']}&page=$page";
$ac_url = "index.php?do=$do&view=$view&w[page_size]=$page_size&w[ord]={$w['ord']}&page=$page";
if (isset ( $ac )) { //单个操作
	if ($relation_id && $ac = 'del') {
		$prom_relation_obj->setWhere ( 'relation_id=' . intval($relation_id) );
		$res = $prom_relation_obj->del_keke_witkey_prom_relation ();
		kekezu::admin_system_log ( $_lang['delete_prom_relation'] . $relation_id );
		kekezu::admin_show_msg ( $res ? $_lang['delete_success'] : $_lang['delete_fail'], $url,3,'',$res?'success':'warning' );
	} else
		kekezu::admin_show_msg ( $_lang['delete_fail_please_choose_operate'], $url,3,'','warning' );
} elseif (isset ( $sbt_action )) { //批量删除
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	if (count ( $ckb_string )) {
		$prom_relation_obj->setWhere ( ' relation_id in (' . $ckb_string . ') ' );
		$res = $prom_relation_obj->del_keke_witkey_prom_relation ();
		kekezu::admin_system_log ( $_lang['mulit_delete_prom_relation'] . $ckb_string );
		kekezu::admin_show_msg ( $res ? $_lang['mulit_operate_success'] : $_lang['mulit_operate_fail'], $url,3,'',$res?'success':'warning' );
	} else
		kekezu::admin_show_msg ( $_lang['mulit_delete_fail_please_choose'], $url,3,'','warning' );
} else {
	//查询条件
	$where = '1=1';
	$w ['relation_id'] and $where .= " and relation_id = '{$w['relation_id']}'";
	$w ['username'] and $where .= " and username like '%{$w['username']}%'";
	$w ['prom_username'] and $where .= " and prom_username like '%{$w['prom_username']}%'";
	//var_dump($w['ord']);

	is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'];
	
	//查询统计
	//echo $where;
	$prom_relation_obj->setWhere ( $where );
	$count = $prom_relation_obj->count_keke_witkey_prom_relation ();
	//$kekezu->_page_obj->setAjax(1);
	//$kekezu->_page_obj->setAjaxDom('ajax_dom');
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url ); //分页
	//查询结果数组
	$prom_relation_obj->setWhere ( $where . $pages ['where'] );
	$prom_relation_arr = $prom_relation_obj->query_keke_witkey_prom_relation ();
}
//echo 'control/admin/tpl/admin_' . $do . '_' .$view ;
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );