<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$model_info ['config'] and $tender_config = unserialize ( $model_info ['config'] ); //任务配置


//防错验证
if (! $task_info) {
	kekezu::keke_show_msg ( $_K ['siteurl'] . "/index.php?do=release", '该任务不存在或者参数错误，请重新发布', 'error' );
}

//招标区间
$cash_cove = kekezu::get_cash_cove ( 'tender' );
if ($r_step == 'step2') {
	if ($rdo_tender_set_price && intval ( $txt_task_cash ) < $tender_config['min_cash']) { //金额为负数。防止页面修改注入
		kekezu::show_msg ( '操作提示', $_SERVER ['HTTP_REFERER'], 2, '非法注入', 'warning' );
	}
}
switch ($r_step) { //任务发布步骤
	case "step2" :
		$task_obj = new Keke_witkey_task_class ();
		$task_obj->setStart_time ( time () );
		$sub_time = time () + ($txt_task_period * 24 * 3600);
		$end_time = $sub_time + ($tender_config ['choose_time'] * 24 * 3600);
		$task_obj->setEnd_time ( $end_time );
		$task_obj->setSub_time ( $sub_time );
		
		if ($rdo_tender_set_price) {
			$task_obj->setTask_cash ( $txt_task_cash );
			$task_obj->setTask_cash_coverage ( 0 );
		} else {
			$cove = $cash_cove [$slt_task_cash_coverage];
			$task_cash = $cove ['end_cove'];
			$task_cash or $task_cash = $cove ['start_cove'];
			$task_obj->setTask_cash ( $task_cash );
			$task_obj->setTask_cash_coverage ( $slt_task_cash_coverage );
		}
		$task_obj->setTask_type ( 1 );
		$task_obj->setModel_id ( $model_id );
		$task_obj->setTask_id ( $task_id );
		$task_obj->edit_keke_witkey_task ();
		if ($ac == 'save') {
			echo json_encode ( array ('msg' => 1 ) );
		} else {
			header ( "location:" . $_K ['siteurl'] . "/index.php?do=release&pub_mode=$pub_mode&task_id=$task_id&model_id={$model_id}&r_step=step3" );
		}
		die ();
		
		break;
	case "step3" :
		$payitem_arr = keke_payitem_class::get_payitem_info ( 'employer', $model_info ['model_code'] ); //获取该任务所有的增值服务  
		$payitem_standard = keke_payitem_class::payitem_standard (); //收费标准
		$limit_max = ceil ( (strtotime ( $release_info ['txt_task_day'] ) - time ()) / 3600 / 24 );
		
		if (kekezu::submitcheck ( $formhash )) {
			
			$update_arr = array ();
			$task_info ['att_cash'] != $item_total_cash and $update_arr ['att_cash'] = $item_total_cash; //增值服务金额
			$task_info ['att_credit'] != $item_total_credit and $update_arr ['att_credit'] = $item_total_credit; //增值服务余额
			$ac != 'save' and $task_info ['task_status'] == - 1 and $update_arr ['task_status'] = 1; //状态改变监测
			$pay_item_str = '';
			$item and $pay_item_str = implode ( ',', $item );
			//$item && in_array ( 'top', $item ) and $update_arr ['is_top'] = 1;
			$pay_item_str != $task_info ['pay_item'] and $update_arr ['pay_item'] = $pay_item_str; //服务项是否有变化
			

			//有增值服务费的情况下是需要先付款的
			if ($item_total_cash) {
				$update_arr ['task_status'] = 0;
			} else {
				if ($tender_config ['zb_audit']) {
					$update_arr ['task_status'] = 1;
				} else {
					$update_arr ['task_status'] = 2;
				}
			}
			
			$update_arr and db_factory::updatetable ( TABLEPRE . "witkey_task", $update_arr, array ('task_id' => $task_id ) ); //执行更新
			

			//这是上一步模式 只保存  不生产订单  不确认
			if ($ac == 'save') {
				echo json_encode ( array ('msg' => 1 ) );
				die ();
			}
			
			if ($item_total_cash) {
				//生成支付的订单
				$order_obj = new Keke_witkey_order_class ();
				$order_obj->setOrder_name ( $task_info ['task_title'] . "的增值服务" );
				$order_obj->setOrder_amount ( $item_total_cash );
				$order_obj->setOrder_status ( 'wait' );
				$order_obj->setOrder_body ( '' );
				$order_obj->setOrder_uid ( $uid );
				$order_obj->setOrder_username ( $username );
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
				header ( "location:" . $_K ['siteurl'] . "/index.php?do=pay&order_id=$order_id&obj_type=task&obj_id=$task_id" );
				die ();
			}
			header ( "location:" . $_K ['siteurl'] . "/index.php?do=task&task_id=$task_id" );
			die ();
		} else {
			$item_list = $payitem_arr;
			$dz_credit = keke_user_class::get_credit ( $uid );
			
			$item_info = array ();
			$task_info ['pay_item'] and $item_info = explode ( ',', $task_info ['pay_item'] );
		}
		break;
	case "step4" :
		//		$release_obj->check_access ( $r_step, $model_id, $release_info,$task_id ); //页面进入权限检测
		break;
}

require keke_tpl_class::template ( 'release' );
		