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
$field="service.*,space.brand";
$table=array('service'=>'witkey_service','space'=>'witkey_space');
$join=array("inner join");
$on=array('service.uid=space.uid');
$wh="space.brand in(".$_SESSION['brandType'].")";
$w[service_id] and $wh .= " and service.service_id= ".$w[service_id];

$w[title] and $wh.=" and service.title like '%$w[title]%'";

$w[username] and $wh.=" and service.username like '%$w[username]%' ";

$w['indus_id'] and $wh .= " and service.indus_id =" . $w['indus_id'];

$w['service_status'] and $wh .= " and service.service_status=" . $w['service_status'];

intval ( $page ) or $page = 1;

intval ( $w[page_size] ) and $page_size = intval ( $w[page_size] ) or $page_size = '10';


$w['ord'] and $order=" service.".$w['ord'] or $order=" service.service_id desc ";

$url_str = "index.php?do=$do&view=$view&service_id=$w[service_id]&service_status=$w[service_status]&service_type=$w[service_type]&username=$w[username]&page=$page&page_size=$w[page_size]";

if($service_id&&$_SESSION['brandType']){
	$auth=$admin_obj->agent_auth("witkey_service", 'service_id='.$service_id,'service_id');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url_str, 3, '', 'error' );
		die();
	}
}
//查询
$table_arr = $table_obj->get_multi_grid ($field,$table,$join,$on,$wh,$order,$page, $page_size,$url_str, 1, 'ajax_dom');
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

require $template_obj->template ( 'control/agent/tpl/admin_' . $do . "_" . $view );