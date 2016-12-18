<?php
/**
 * 财务--开票管理
 * @copyright keke-tech
 * @author shangk
 * @version v 20
 * 2012-05-25 15:18:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 159 );

$iv_status = keke_glob_class::get_iv_status ();
$iv_taxes_status = array ("0" => "未收取", "1" => "已收取" );
$iv_tm_status = array ("1" => "任务完成前", "2" => "任务完成后" );
$transport_type = array ("1" => "平邮", "2" => "快递" );
$table_obj = new Keke_witkey_invoice_class ();

$invoice_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_invoice where iv_id = " . intval ( $iv_id ) );
$task_info = db_factory::get_one ( "select model_id,task_status,task_type,task_cash,task_cash_coverage from " . TABLEPRE . "witkey_task where task_id=" . $invoice_info ['task_id'] );
if ($invoice_info ['iv_tm_status'] == 1) { //结束前开票
	//判断任务状态是否改变（结束前和结束后），修正开票时任务状态
	//任务已结束，并且之前的开票申请是在任务结束前，修正开票表数据
	$mid = $task_info ['model_id'];
	if ($task_info ['task_status'] >= 8) { //任务已结束了
		switch ($mid) {
			case 1 :
			case 2 :
				/**
					单人、多人、如果在结束前申请了开票，但是结束后才进行处理。
					逾期处理，那么自动更改为已处理状态,
				 */
				if ($invoice_info ['iv_status'] == 0) {
					$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
					$table_obj->setIv_status ( 1 ); //自动受理
					$table_obj->setIv_taxes_status ( 1 ); //已收税
					$table_obj->setIv_checktime (time() );
					$table_obj->setIv_checkuid ($_SESSION ['uid']);
					$table_obj->setIv_checkusername ($_SESSION ['username']);
					$res = $table_obj->edit_keke_witkey_invoice ();
					if (floatval ( $invoice_info ['iv_item_cash'] ) > 0) { //有增值费用
						//从雇主身上直接扣
						$iv_cash = $invoice_info ['iv_item_cash'] * 0.055;
						keke_finance_class::cash_out ( $invoice_info ['uid'], $iv_cash, 'invoice_taxes', 0, 'task', $invoice_info ['task_id'] );
					}
				}
				break;
			case 4 :
				$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
				$table_obj->setIv_tm_status ( 2 ); //更改为结束后申请...此后扣除雇主税金
				$res = $table_obj->edit_keke_witkey_invoice ();
			break;
		}
		$invoice_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_invoice where iv_id = " . intval ( $iv_id ) );
	}
}
//格式化省市区数据
$invoice_info ['iv_city'] = explode ( ",", $invoice_info ['iv_city'] );
$invoice_info ['iv_city'] = implode ( $invoice_info ['iv_city'] );

$back_url = "index.php?do=finance&view=invoice_info&iv_id=" . intval ( $iv_id );
if ($ac) {
	if ($ac == "pass") { //审核通过
		if ($invoice_info ['iv_status'] == 0) { //待审核状态
			if ($invoice_info ['iv_tm_status'] == 1) { //任务完成前开票
				$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
				$table_obj->setIv_status ( 1 );
				$table_obj->setIv_checkuid ( $_SESSION ['uid'] );
				$table_obj->setIv_checkusername ( $_SESSION ['username'] );
				$table_obj->setIv_checktime ( time () );
				
				if ($invoice_info ['iv_taxes_status'] == 0) { //税金未收取
					/**
					 * 更新任务实际金额。从威客身上收取税金
					 * 1 . 单人、多人由于之前申请时已经扣除了。所以这里不再重复扣
					 * 2 . 计件的只能结束后开票。所以这里不处理
					 */
					$t = true;
					if ($invoice_info ['model_id'] == 4) {
						//直接雇佣和雇佣任务.有实际金额
						if ($task_info ['task_type'] == 3 || ($task_info ['task_type'] == 1 && $task_info ['task_cash_coverage'] < 1)) {
							//由于总金可能改变实际金额应该重算
							$real_cash = $task_info['task_cash'] * 0.945;
							$sql = ' update ' . TABLEPRE . 'witkey_task set real_cash=' . $real_cash . ' where task_id=' . $invoice_info ['task_id'];
							$t = db_factory::execute ( $sql );
						}
					}
					if (floatval ( $invoice_info ['iv_item_cash'] ) > 0) { //有增值费用
						//从雇主身上直接扣
						$iv_cash = $invoice_info ['iv_item_cash'] * 0.055;
						keke_finance_class::cash_out ( $invoice_info ['uid'], $iv_cash, 'invoice_taxes', 0, 'task', $invoice_info ['task_id'] );
					}
					if ($t) { //更新收税状态
						$table_obj->setIv_taxes_status ( 1 );
					}
				}
				$res = $table_obj->edit_keke_witkey_invoice ();
				if ($res) {
					kekezu::admin_show_msg ( $_lang ['invoice_check_ok'], $back_url, 3, '', 'success' );
				} else {
					kekezu::admin_show_msg ( $_lang ['invoice_check_error'], $back_url, 3, '', 'warning' );
				}
			} else if ($invoice_info ['iv_tm_status'] == 2) { //任务完成后
				$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
				$table_obj->setIv_status ( 1 );
				$table_obj->setIv_checkuid ( $_SESSION ['uid'] );
				$table_obj->setIv_checkusername ( $_SESSION ['username'] );
				$table_obj->setIv_checktime ( time () );
				if ($invoice_info ['iv_taxes_status'] == 0) { //税金未收取
					//扣除用户余额...扣总的税金..任务税+增值税的
					$t = keke_finance_class::cash_out ( $invoice_info ['uid'], $invoice_info ['iv_taxes'], 'invoice_taxes', 0, 'task', $invoice_info ['task_id'] );
					if ($t) {
						kekezu::notify_user ( '开票申请受理通知', '您的#' . $iv_id . '开票申请已经通过审核.网站收取税金￥' . $invoice_info ['iv_taxes'] . '元', $invoice_info ['uid'], $invoice_info ['username'] );
						$table_obj->setIv_taxes_status ( 1 );
					} else {
						kekezu::admin_show_msg ( "该用户账户余额不足,无法扣取税金，请联系其充值.", $back_url, 3, '', 'warning' );
					}
				}
				$res = $table_obj->edit_keke_witkey_invoice ();
				if ($res) {
					kekezu::admin_show_msg ( $_lang ['invoice_check_ok'], $back_url, 3, '', 'success' );
				} else {
					kekezu::admin_show_msg ( $_lang ['invoice_check_error'], $back_url, 3, '', 'warning' );
				}
			}
		} else {
			kekezu::admin_show_msg ( $_lang ['iv_status_cannot_pass'], $back_url, 3, '', 'warning' );
		}
	} else if ($ac == "nopass") { //拒绝申请
		if ($invoice_info ['iv_status'] == 0) { //待审核状态
			$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
			$table_obj->setIv_status ( 2 );
			$table_obj->setIv_checkuid ( $_SESSION ['uid'] );
			$table_obj->setIv_checkusername ( $_SESSION ['username'] );
			$table_obj->setIv_checktime ( time () );
			$res = $table_obj->edit_keke_witkey_invoice ();
			if ($res) {
				/**
				 * 更新任务实际金额。返还威客税金
				 * 1 . 单人、多人由于之前申请时已经预先就扣除了税金部分
				 * 这里不通过的话就要把佣金real_cash还原
				 */
				if ($invoice_info ['model_id'] == 1 || $invoice_info ['model_id'] == 2) {
					$sql = ' update ' . TABLEPRE . 'witkey_task set real_cash=' . $task_info ['task_cash'] . ' where task_id=' . $invoice_info ['task_id'];
					db_factory::execute ( $sql );
				}
				kekezu::admin_show_msg ( $_lang ['invoice_check_nopass_ok'], $back_url, 3, '', 'success' );
			} else {
				kekezu::admin_show_msg ( $_lang ['invoice_check_nopass_error'], $back_url, 3, '', 'warning' );
			}
		} else {
			kekezu::admin_show_msg ( $_lang ['iv_status_cannot_pass'], $back_url, 3, '', 'warning' );
		}
	} else if ($ac == "gettaxes") { //收取税金
		if ($invoice_info ['iv_taxes_status'] == 0) { //税金未收取.扣总的税金..任务税+增值税的
			$res = keke_finance_class::cash_out ( $invoice_info ['uid'], $invoice_info ['iv_taxes'], 'invoice_taxes', 0, 'task', $invoice_info ['task_id'] );
			if ($res) {
				//更新开票表状态
				$table_obj->setWhere ( "iv_id=" . $invoice_info ['iv_id'] );
				$table_obj->setIv_taxes_status ( 1 );
				$table_obj->edit_keke_witkey_invoice ();
				kekezu::admin_show_msg ( $_lang ['get_taxes_ok'], $back_url, 3, '', 'success' );
			} else {
				kekezu::admin_show_msg ( $_lang ['get_taxes_error'], $back_url, 3, '', 'warning' );
			}
		} else {
			kekezu::admin_show_msg ( '税金已被收取,请勿重复操作', $back_url, 3, '', 'warning' );
		}
	} else if ($ac == "saveid") { //保存邮寄单号
		$table_obj->setWhere ( "iv_id=" . intval ( $iv_id ) );
		$table_obj->setTransport_orderid ( $transport_orderid );
		$res = $table_obj->edit_keke_witkey_invoice ();
		if ($res == 1 or $res == 0) {
			kekezu::admin_show_msg ( $_lang ['save_transport_orderid_ok'], $back_url, 3, '', 'success' );
		} else {
			kekezu::admin_show_msg ( $_lang ['save_transport_orderid_error'], $back_url, 3, '', 'warning' );
		}
	}
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );