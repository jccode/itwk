<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
require S_ROOT.'/payment/gopay/gopay_class.php';
//线下银行列表
$bank_list = gopay_class::bank_code();

if($btn_submit){
	$charge_cash or kekezu::show_msg("您没有输入金额","index.php?do=$do&view=$view&op=$op");
	
	if($charge_type!='offline_pay'&&$charge_type!='tw'){
		$recharge_obj = new Keke_witkey_order_charge_class(); //实例化充值表对象
		$recharge_obj->setOrder_type('online_charge');
		$recharge_obj->setPay_type($pay_mode);
		$recharge_obj->setUid($uid);
		$recharge_obj->setUsername($username);
		$recharge_obj->setPay_money($charge_cash);
		$recharge_obj->setObj_type('user_charge');
		$recharge_obj->setOrder_status('wait');
		$charge_id = $recharge_obj->create_keke_witkey_order_charge();
		
		$order_obj = new Keke_witkey_order_class ();
		$order_obj->setOrder_amount ( $charge_cash );
		$order_obj->setOrder_status ( 'wait' );
		$order_obj->setOrder_name ( '用户充值' );
		$order_obj->setOrder_uid ( $uid );
		$order_obj->setOrder_username ( $username );
		$order_obj->setObj_type ( 'online_recharge' );//
		$order_obj->setObj_id ( $charge_id );//存入支付单的id 供回调时处理
		$order_obj->setOrder_time(time());
		$order_id = $order_obj->create_keke_witkey_order ();
		
		header("location:index.php?do=pay&order_id=$order_id&charge_id=$charge_id&pay_mode=$pay_mode&center=1&bank_code=$bank_code");
		die();
	}else{
		$order_obj = new Keke_witkey_order_class ();
		$order_obj->setOrder_amount ( $charge_cash );
		$order_obj->setOrder_status ( 'wait' );
		$order_obj->setOrder_name ( '用户充值' );
		$order_obj->setOrder_uid ( $uid );
		$order_obj->setOrder_username ( $username );
		$order_obj->setObj_type ( 'user_recharge' );//
		$order_obj->setObj_id ( $charge_id );//存入支付单的id 供回调时处理
		$order_obj->setOrder_time(time());
		$order_id = $order_obj->create_keke_witkey_order ();
		$txt_pay_cash = $charge_cash;
		include S_ROOT.'./control/pay.php';die();
	}
}

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );


