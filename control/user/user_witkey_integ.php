<?php
/**
 * 诚信保障
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if (! $ac) {
	//已开通，查找是否有退款申请
	$user_info ['integrity'] and $int_time = db_factory::get_count( ' select on_time from ' . TABLEPRE . 'witkey_integrity where type = 2 and status=-1 and uid=' . $uid );
} else {
	switch ($ac) {
		case 'apply' :
			$int_id = db_factory::inserttable ( TABLEPRE . 'witkey_integrity',
					 array ('type' => 1, 'uid' => $uid, 'username' => $username,
					 	 'pay_cash' => 2000, 'status' => '-1', 'on_time' => time ()
					  )
			 );
			$order_obj = new Keke_witkey_order_class ();
			$order_obj->setOrder_name ( $username . "开通诚信保障服务" );
			$order_obj->setOrder_amount ( 2000 );
			$order_obj->setOrder_status ( 'wait' );
			$order_obj->setOrder_uid ( $uid );
			$order_obj->setOrder_username ( $username );
			$order_obj->setObj_type ( 'integrity' );
			$order_obj->setObj_id ( $int_id );
			$order_obj->setTask_id ( 0 );
			$order_obj->setModel_id ( 0 );
			$order_obj->setOrder_time ( time () );
			//因为完成付款之前可能会有重复操作
			$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='integrity' and obj_id='$int_id'" );
			if ($order_exsit) {
				$order_obj->setOrder_id ( $order_exsit ['order_id'] );
				$order_obj->edit_keke_witkey_order ();
				$order_id = $order_exsit ['order_id'];
			} else {
				$order_id = $order_obj->create_keke_witkey_order ();
			}
			if ($order_id) {
				kekezu::show_msg ('操作提示',$_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id&obj_type=integrity&obj_id=$int_id",1, "诚信保障开通申请提交成功,前往付款", "alert_right" );
			} else {
				kekezu::show_msg ('操作提示', $_SERVER ['HTTP_REFERER'], "3", "诚信保障开通申请提交失败", "warning" );
			}
			return $order_id;
			break;
		case 'refund' :
			$int_id = db_factory::inserttable ( TABLEPRE . 'witkey_integrity',
					 array ('type' => 2, 'uid' => $uid, 'username' => $username,
					 	 'pay_cash' => 0, 'status' => '-1', 'on_time' => time ()
					  )
			 );
			 $int_id and kekezu::show_msg('操作提示', $_SERVER ['HTTP_REFERER'],3,'熄灭图标申请成功，请等待客服审核','alert_right') or kekezu::show_msg('操作提示', $_SERVER ['HTTP_REFERER'],3,'熄灭图标申请失败','warning');
			break;
	}
}

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );