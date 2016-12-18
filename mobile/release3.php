<?php

/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );
//语言包
keke_lang_class::package_init ( "task" );
keke_lang_class::loadlang ( $do );

if ($ac == 'pub_task') {
	$task_info = db_factory::get_one ( ' select model_id,uid,username,task_title,task_cash from ' . TABLEPRE . 'witkey_task where task_id=' . $task_id );
	//生成支付的订单
	$order_obj = new Keke_witkey_order_class ();
	$order_obj->setOrder_name ( $task_info ['task_title'] . "托管佣金" );
	$order_obj->setOrder_amount ( $task_info ['task_cash'] );
	$order_obj->setOrder_status ( 'wait' );
	$order_obj->setOrder_name ( $task_info ['task_title'] );
	$order_obj->setOrder_uid ( $task_info ['uid'] );
	$order_obj->setOrder_username ( $task_info ['username'] );
	$order_obj->setObj_type ( 'pub_task' );
	$order_obj->setObj_id ( $task_id );
	$order_obj->setTask_id ( $task_id );
	$order_obj->setModel_id ( $task_info ['model_id'] );
	$order_obj->setOrder_time ( time () );
	//因为完成付款之前可能会有重复操作
	$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='pub_task' and obj_id='$task_id'" );
	if ($order_exsit) {
		$order_obj->setOrder_id ( $order_exsit ['order_id'] );
		$order_obj->edit_keke_witkey_order ();
		$order_id = $order_exsit ['order_id'];
	} else {
		$order_id = $order_obj->create_keke_witkey_order ();
	}
	
	$pay = keke_order_class::order_clear ( $order_id );
	
	if ($pay==true){
		kekezu::echojson ( array ('r' => '托管成功！' ), 1, intval ( $task_id ));
	} else {
		kekezu::echojson ( array ('r' => '服务器繁忙，请稍后...' ), 0 );
	}
}