<?php

define ( "IN_KEKE", true );
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'app_comm.php');
require_once ('alipay_notify.php');

//$pay_arr = kekezu::get_config('paypal');
//@extract($pay_arr);


$_input_charset = strtoupper ( CHARSET );
$payment_config = kekezu::get_payment_config ( 'alipayjs' );
$payment_config or die ( "支付配置错误，支付无法完成，请联系管理员。" );

$seller_id = $payment_config ['seller_id'];
$security_code = $payment_config ['safekey'];
$sign_type = 'MD5';
$transport = 'http';

//构造通知函数信息
$alipay = new alipay_notify ( $seller_id, $security_code, $sign_type, $_input_charset, $transport );
//计算得出通知验证结果
$verify_result = $alipay->return_verify ();

$out_trade_no = $_GET ['out_trade_no']; //获取订单号
$total_fee = $_GET ['total_fee']; //获取总价格  
list ( $_, $charge_type, $uid, $obj_id, $order_id, $model_id ) = explode ( '-', $out_trade_no, 6 );
if ($verify_result) {
	if ($_GET ['trade_status'] == 'TRADE_FINISHED' || $_GET ['trade_status'] == 'TRADE_SUCCESS') {
		$charge_info = db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_order_charge where order_id= ' . $order_id );
		$res = true;
		if ($charge_info ['order_status'] == 'wait') {
			db_factory::execute ( 'update ' . TABLEPRE . 'witkey_order_charge set order_status="ok",pay_time='.time().' where order_id=' . $order_id );
			$res = keke_finance_class::cash_in ( $charge_info ['uid'], $charge_info ['pay_money'], 0, 'online_charge' );
			$res && $charge_info ['return_order_id'] and keke_order_class::order_clear ( $charge_info ['return_order_id'] );
		}
		if ($res) {
			keke_glob_class::pay_return_notify ( '支付宝支付成功', $charge_info ['obj_type'], $charge_info ['obj_id'] );
		}
		keke_glob_class::pay_return_notify ( '支付宝支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );
	}
	keke_glob_class::pay_return_notify ( '支付宝支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );

}
keke_glob_class::pay_return_notify ( '支付宝支付失败', $charge_info ['obj_type'], $charge_info ['obj_id'], 'warning' );
	
