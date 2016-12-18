<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-9 12:10
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$obj_types = array ("task", "service" );

in_array ( $obj_type, $obj_types ) or $obj_type = 'task';
$role or $role = 2; //用户角色 1为收到。2为下单
//$where ="1=1";
$page = intval ( $page );
$page or $page = 1;
$page_size = intval ( $page_size );
$page_size or $page_size = 10;
$url = $origin_url . "&op=$op&obj_type=$obj_type&role=$role&page_size=$page_size&status=$status&page=$page";

if (isset ( $ac ) && $order_id && $model_id) {
	$model_info = $kekezu->_model_list [$model_id];
	$class_name = $model_info ['model_code'] . "_" . $model_info ['model_type'] . "_class";
	if ($model_info ['model_type'] == "task") {
		$task_id = db_factory::get_count ( sprintf ( "select obj_id from %switkey_order_detail where order_id='%d'", TABLEPRE, intval ( $order_id ) ) );
		$task_info = db_factory::get_one ( sprintf ( "select * from %switkey_task where task_id='%d'", TABLEPRE, $task_id ) );
	}
	$obj = new $class_name ( $task_info );
	$res = $obj->dispose_order ( $order_id,$ac);
	$model_info ['model_type'] == "task" and header("Location:".$res['url']);
} elseif ($mark) {
	$title = $_lang['both_mark'];
	$obj_id = $obj_id;
	require S_ROOT . 'control/mark.php';
	die ();
}elseif($download){
	$title=$_lang['works_file_upload'];
	$view ="file";
	$ajax ="goods_filedown";
	require "control/ajax/ajax_file.php";
}else {
	$model_list = $kekezu->_model_list;
	$obj_arr = keke_order_class::get_order_obj (); //订单对象数组
	$sql = " select a.*,b.obj_type,b.obj_id from " . TABLEPRE . "witkey_order a left join " . TABLEPRE . "witkey_order_detail b on a.order_id = b.order_id 
			where b.obj_type = '$obj_type' ";
	$role == '1' and $sql .= " and seller_uid = '$uid' " or $sql .= " and order_uid = '$uid' ";
	switch ($obj_type) {
		case "task":
		/*任务订单状态*/
		$status_arr = array ("wait" => $_lang['wait_pay'], "ok" =>$_lang['has_pay'], 'fail' => $_lang['pay_fail'], "close" => $_lang['trans_close'] );
			break;
		case "service" :
		/*服务订单状态*/
		$status_arr = array ("wait" => $_lang['wait_buyer_pay'], "ok" => $_lang['buyer_has_pay'], 'accept' => $_lang['seller_has_accept'], "send" =>$_lang['seller_has_severice'], "confirm" => $_lang['confirm_complete'], "close" => $_lang['trans_close'], "arbitral" => $_lang['order_arbitral'],'arb_confirm'=>$_lang['confirm_complete'] );
			break;
	}
	/* 排序方式*/
	$ord_arr = array ('a.order_id desc' => $_lang['order_id_desc'], "a.order_id asc" => $_lang['order_id_asc']);
	
	$order_obj = new Keke_witkey_order_class ();
	$page_obj = $kekezu->_page_obj;
	
	$order_id && $order_id != $_lang['please_input_order_id'] and $sql .= " and a.order_id = ".$order_id;
	$order_title && $order_title != $_lang['please_input_order_name'] and $sql .= " and a.order_name like '%$order_title%'";
	$status and $sql .= " and a.order_status = '$status'";
	$ord and $sql.=" order by $ord " or $sql.=" order by order_id desc ";
	$count = intval ( db_factory::execute ( $sql ) );
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
	$order_arr = db_factory::query ( $sql . $pages ['where'] );
}


if($action =='delete'){
	$detail_obj = new Keke_witkey_order_detail_class();
	$task_obj = new Keke_witkey_task_class();
	//删除对应的订单  
	$order_obj->setWhere("order_id = $order_id");
	$order_obj->del_keke_witkey_order();
	
	$detail_obj->setWhere("order_id = $order_id");
	$detail_info = $detail_obj->query_keke_witkey_order_detail();
	$detail_info = $detail_info['0'];
	
	$detail_obj->setWhere("order_id = $order_id");
	$detail_obj->del_keke_witkey_order_detail();
	//删除对应的任务
	$task_id = $detail_info['obj_id'];
	$task_obj->setWhere("task_id = $task_id");
	$res =  $task_obj->del_keke_witkey_task();
	kekezu::echojson('',1);
	
}

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op . "_" . $obj_type );

