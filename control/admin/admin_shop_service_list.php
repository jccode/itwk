<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-06-20 20:05:34
 */ 
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$table_obj = keke_table_class::get_instance('witkey_service');
$price_unit  = keke_glob_class::get_price_unit();//价格单位
//检索条件
$wh = "1=1";

$w[service_id] and $wh .= " and service_id= ".$w[service_id];

$w[title] and $wh.=" and title like '%$w[title]%'";

$w[username] and $wh.=" and username like '%$w[username]%' ";

$w['indus_id'] and $wh .= " and indus_id =" . $w['indus_id'];

$w['service_status'] and $wh .= " and service_status=" . $w['service_status'];

intval ( $page ) or $page = 1;

intval ( $w[page_size] ) and $page_size = intval ( $w[page_size] ) or $page_size = '10';


$w['ord'] and $wh.=" order by ".$w['ord'] or $wh.=" order by service_id desc ";

$url_str = "index.php?do=$do&view=$view&service_id=$w[service_id]&service_status=$w[service_status]&service_type=$w[service_type]&username=$w[username]&page=$page&page_size=$w[page_size]";

//查询
$table_arr = $table_obj->get_grid ( $wh, $url_str, $page, $page_size, null, 1, 'ajax_dom');
$service_arr = $table_arr['data'];
$pages = $table_arr['pages'];

//操作 1.删除；2..禁用；3.审核 4.启用
if($service_id){
	$service_arr = db_factory::get_one(sprintf("select * from %switkey_service where service_id='%d' ",TABLEPRE,$service_id));

	$log_ac_arr = array("del"=>$_lang['delete'],"use"=>$_lang['use'],"pass"=>$_lang['audit'],"disable"=>$_lang['disable']);
	$log_msg = $_lang['to_witkey_service_name_to'].$service_arr[title].$_lang['in'].$log_ac_arr[$ac].$_lang['operate'];
	kekezu::admin_system_log($log_msg);
	switch ($ac) {
		case 'del':
			$res = $table_obj->del('service_id', $service_id,$url_str);
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['delete_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['delete_fail'],"warning");
		break;
	}
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );