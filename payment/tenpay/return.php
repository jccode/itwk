<?php
define ( "IN_KEKE", true );
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'app_comm.php');

require_once ("PayResponseHandler.php");
$pay_arr = kekezu::get_payment_config ( "tenpay" );
@extract ( $pay_arr );
$key = $safekey;

$resHandler = new PayResponseHandler ();
$resHandler->setKey ( $key );
chmod ( 'log.txt', 777 );
KEKE_DEBUG and $fp = file_put_contents ( 'log.txt', var_export ( $_GET, 1 ), FILE_APPEND ); //信息录入


$v_void = $resHandler->getParameter ( "sp_billno" ); //tentpay内部订单号
$v_attach = $resHandler->getParameter ( "attach" ); //商家数据包
$v_amount = $resHandler->getParameter ( "total_fee" );
$v_amount = $v_amount * 0.01;

$pay_result = $resHandler->getParameter ( "pay_result" );

list ( $_, $charge_type, $uid, $obj_id, $order_id, $model_id ) = explode ( '-', $v_attach, 6 );
if ($resHandler->isTenpaySign ()) {
	if ("0" == $pay_result && $_ == 'charge') {
		$charge_info = db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_order_charge where order_id= ' . $order_id );
		$res = true;
		if ($charge_info ['order_status'] == 'wait') {
			db_factory::execute ( 'update ' . TABLEPRE . 'witkey_order_charge set order_status="ok" where order_id=' . $order_id );
			$res = keke_finance_class::cash_in ( $charge_info ['uid'], $charge_info ['pay_money'], 0, 'online_charge' );
			$res && $charge_info ['return_order_id'] and keke_order_class::order_clear ( $charge_info ['return_order_id'] );
		}
		if ($res) {
			keke_glob_class::pay_return_notify ( '财付通支付成功', $charge_info ['obj_type'], $charge_info ['obj_id'] );
		}
		keke_glob_class::pay_return_notify ( '财付通支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );
	
	}
	keke_glob_class::pay_return_notify ( '财付通支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );

}
keke_glob_class::pay_return_notify ( '财付通支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );
	
