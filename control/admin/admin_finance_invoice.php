<?php
/**
 * 财务--开票管理
 * @copyright keke-tech
 * @author shangk
 * @version v 20
 * 2012-05-25 15:18:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (159);

$iv_status = keke_glob_class::get_iv_status();
$table_obj = new Keke_witkey_invoice_class();
$page_obj = $kekezu->_page_obj; //实例化分页对象

//分页
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[pay_type]=".$w['pay_type']."&w[page_size]=$page_size&w[ord]=".$w['ord']."&page=$page";


$where = ' from  '.TABLEPRE.'witkey_invoice where 1=1 '; //默认查询条件
$w ['task_title'] and $where .= " and task_title like '%".$w['task_title']."%' ";
$w ['username'] and $where .= " and  username like '%".$w['username']."%' ";
if($start_time&&!$end_time){
	$where.=" and iv_datetime >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$where.=" and iv_datetime <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
	if($start_time==$end_time){
		$where.=" and iv_datetime = ".intval(strtotime($start_time));
	}else{
		$where.=" and iv_datetime between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}

is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $where .= "order by iv_id desc";

//查询统计
$count = db_factory::get_count(' select count(iv_id) '.$where);
$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
//查询结果数组
$data_arr = db_factory::query('select * '.$where.$pages['where']);

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );