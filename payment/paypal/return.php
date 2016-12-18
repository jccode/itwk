<?php
/**
 * paypal 支付跳转页面
 */
define ( "IN_KEKE", true );
require (dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'app_comm.php');
require "Paypal.php";

$myPaypal = new Paypal ();

// Log the IPN results
$myPaypal->ipnLog = TRUE;
// Enable test mode if needed
$myPaypal->enableTestMode ();
// Check validity and write down it
list ( $_, $charge_type, $uid, $obj_id, $order_id, $model_id ) = explode ( '-', $myPaypal->ipnData ['custom'], 6 );
$total_fee = $myPaypal->ipnData ['payment_gross'];
if ($myPaypal->validateIpn ()) {
	if ($myPaypal->ipnData ['payment_status'] == 'Completed') {
		$charge_info = db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_order_charge where order_id= ' . $order_id );
		$res = true;
		if ($charge_info ['order_status'] == 'wait') {
			db_factory::execute ( 'update ' . TABLEPRE . 'witkey_order_charge set order_status="ok" where order_id=' . $order_id );
			$res = keke_finance_class::cash_in ( $charge_info ['uid'], $charge_info ['pay_money'], 0, 'online_charge' );
			$res && $charge_info ['return_order_id'] and keke_order_class::order_clear ( $charge_info ['return_order_id'] );
		}
		if($res){
			keke_glob_class::pay_return_notify('贝宝支付成功',$charge_info['obj_type'],$charge_info['obj_id']);
		}
		keke_glob_class::pay_return_notify('贝宝支付失败',$charge_info['obj_type'],$charge_info['obj_id'],'warning');
	} 
	keke_glob_class::pay_return_notify('贝宝支付失败',$charge_info['obj_type'],$charge_info['obj_id'],'warning');
}
keke_glob_class::pay_return_notify('贝宝支付失败',$charge_info['obj_type'],$charge_info['obj_id'],'warning');
