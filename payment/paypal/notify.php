<?php
define ( "IN_KEKE", true );
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'app_comm.php');

include_once 'Paypal.php';

// Create an instance of the paypal library


$myPaypal = new Paypal ();

if (KEKE_DEBUG) {
	// Log the IPN results
	$myPaypal->ipnLog = TRUE;
	
	// Enable test mode if needed
	$myPaypal->enableTestMode ();
}
// Check validity and write down it


chmod ( 'log.txt', 777 );
if ($myPaypal->validateIpn ()) {
	if ($myPaypal->ipnData ['payment_status'] == 'Completed') {
		list ( $_, $charge_type, $uid, $obj_id, $order_id, $model_id ) = explode ( '-', $myPaypal->ipnData ['custom'], 6 );
		$total_fee = $myPaypal->ipnData ['payment_gross'];
		$charge_info = db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_order_charge where order_id= ' . $order_id );
		if ($charge_info ['order_status'] == 'wait') {
			db_factory::execute ( 'update ' . TABLEPRE . 'witkey_order_charge set order_status="ok" where order_id=' . $order_id );
			$res = keke_finance_class::cash_in ( $charge_info ['uid'], $charge_info ['pay_money'], 0, 'online_charge' );
			$res && $charge_info ['return_order_id'] and keke_order_class::order_clear ( $charge_info ['return_order_id'] );
		}
	}
	file_put_contents ( 'log.txt', var_export ( $myPaypal->ipnData, 1 ), FILE_APPEND );
} else {
	file_put_contents ( 'log.txt', var_export ( $myPaypal->ipnData, 1 ), FILE_APPEND );
}

