<?php
/**
 * 国付宝 前台处理地址
 */
define ( "IN_KEKE", true );
require (dirname ( dirname ( dirname ( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'app_comm.php');
require "gopay_class.php";
$payment_config = kekezu::get_payment_config ( 'gopay' );
$payment_config or die ( "支付配置错误，支付无法完成，请联系管理员。" );

$gopay = new gopay_class($payment_config);
$gopay->set_params($_POST);
list ( $uid, $obj_id, $order_id, $model_id ) = explode ( '-',$gopay->_params['merOrderNum'],4);
file_put_contents('log.txt', var_export ( $_POST, 1 ), FILE_APPEND);
if ($gopay->valid_sign($_POST['signValue'])) {//签名验证
	if ($gopay->_params['respCode'] == '0000') {//
	$charge_info = db_factory::get_one(' select * from '.TABLEPRE.'witkey_order_charge where order_id= '.$order_id);
		$res = true;
		if($charge_info['order_status']=='wait'){
			db_factory::execute('update '.TABLEPRE.'witkey_order_charge set order_status="ok",pay_time='.time().' where order_id='.$order_id);
			$res = keke_finance_class::cash_in($charge_info['uid'],$charge_info['pay_money'],0,'online_charge');
			$res&&$charge_info['return_order_id'] and keke_order_class::order_clear($charge_info['return_order_id']);
		}
		if($res){
			keke_glob_class::pay_return_notify($gopay->_params['bankCode'],'国付宝支付成功',$charge_info['obj_type'],$charge_info['obj_id']);
			die();
		}
		keke_glob_class::pay_return_notify($gopay->_params['bankCode'],'国付宝支付失败,原因:'.$gopay->output(),$charge_info['obj_type'],$charge_info['obj_id'],'warning');
	} else {
		keke_glob_class::pay_return_notify($gopay->_params['bankCode'],'国付宝支付失败,原因:'.$gopay->output(),$charge_info['obj_type'],$charge_info['obj_id'],'warning');
	}
}else{
	keke_glob_class::pay_return_notify($gopay->_params['bankCode'],'国付宝支付失败,原因:'.$gopay->output(),$charge_info['obj_type'],'','warning');
}
