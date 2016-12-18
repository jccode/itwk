<?php

/**
 * 订单充值页面
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-8-11上午08:05:04
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
kekezu::check_login ();
$page_title = '收银台 - ' . $_K ['html_title'];
$payment_list = kekezu::get_payment_config ();

if (! $pay_mode) {
	require 'payment/gopay/gopay_class.php';
	$bank_codes = gopay_class::bank_code ();
}

//订单信息获取
if (! $order_id) {
	$order_info = db_factory::get_one ( "select order_id,order_name,obj_type,obj_id,order_time,order_amount,order_status,order_body,order_uid,order_username,seller_uid,seller_username,task_id,model_id from " . TABLEPRE . "witkey_order where obj_id ='$obj_id' and obj_type='$obj_type'" );
	$order_id = $order_info ['order_id'];
} else {
	$order_info = db_factory::get_one ( "select order_id,order_name,obj_type,obj_id,order_time,order_amount,order_status,order_body,order_uid,order_username,seller_uid,seller_username,task_id,model_id from " . TABLEPRE . "witkey_order where order_id ='$order_id'" );
	$obj_type = $order_info ['obj_type'];
	$obj_id = $order_info ['obj_id'];
}

$order_info or kekezu::show_msg ( "参数错误", "index.php", 3, "无法获得该订单", 'error' );
$uid != $order_info ['order_uid'] and kekezu::show_msg ( "登录信息错误", "index.php", 3, "该支付请求不是由您触发，请重新登录", 'error' );
$order_info ['order_status'] == 'ok' and kekezu::show_msg ( "该订单已完结", "index.php" );
//格式化
$order_amount = number_format ( $order_info ['order_amount'], 2 );

$model_id = $order_info ['model_id'];

//计算用户余额
$kekezu->_sys_config ['credit_is_allow'] == 1 and $user_balance = $user_info ['credit'] + $user_info ['balance'] or $user_balance = $user_info ['balance'];
//应付金额
$pay_amount = ( float ) $order_info ['order_amount'] - ( float ) $user_balance;
//$pay_amount < 0 and kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=user&view=finance&op=order", 2, $_lang['this_order_need_pay'] );
$pay_amount < 0 and $pay_amount = 0;
$bank_list = keke_glob_class::get_bank ();
//确认支付方式，返回请求的url
if (isset ( $pay_mode )) {
	if ($pay_mode == 'offline') {
		//线下付款  生成充值记录
		$recharge_obj = new Keke_witkey_order_charge_class (); //实例化充值表对象
		switch($charge_type){//根据代理商标签进行充值类型赋值
			case 'tw':
				$order_type='tw';
				break;
			default:
				$order_type="offline_charge";
				break;
		}
		$recharge_obj->setOrder_type ($order_type);
		$recharge_obj->setReturn_order_id ( $order_id );
		$recharge_obj->setPay_type ( $slt_bank_type );
		$recharge_obj->setObj_type ( $obj_type );
		$recharge_obj->setObj_id ( $obj_id );
		$recharge_obj->setUid ( $uid );
		$recharge_obj->setUsername ( $username );
		$recharge_obj->setPay_money ( $txt_pay_cash );
		$recharge_obj->setOrder_status ( 'wait' );
		switch ($charge_type){//根据代理商标签进行品牌馆字段赋值
			case 'tw':
				$brand='tw';
				break;
			default:
				$brand="cn";
				break;
				
		}
		$recharge_obj->setBrand($brand);
		switch ($charge_type){//根据代理商标签进行品牌馆字段赋值
			case 'tw':
				$bank_name = '爽购网帐号';
				break;
			default:
				$bank_name = $bank_list [$slt_bank_type];
				break;
		}
		
		$slt_bank_type == 'other' and $bank_name = $txt_bank_type;
		$send_str = "";
		$txt_send_cash and $send_str = "实汇金额为{$txt_send_cash}元，";
		switch ($charge_type){//根据代理商标签进行支付信息组合
			case 'tw':
				$pay_info = "通过{$bank_name}申请付款{$txt_pay_cash}元，申请人：<b>{$name}</b>，手机：<b>{$mobile}</b>，email：<b>{$email}</b>";
				break;
			default:
				$pay_info = "通过{$bank_name}付款{$txt_pay_cash}元，$send_str汇款帐号是:<b>{$txt_bank_account}</b>，汇款人:<b>{$txt_real_name}</b>，汇到帐号<b>{$slt_to_bank}</b>上";
				break;
		}
		
		
		$recharge_obj->setPay_info ( $pay_info );
		$recharge_obj->setPay_time ( time () );
		$id=$recharge_obj->create_keke_witkey_order_charge ();
		switch($charge_type){
			case 'tw':
				$params='order_id='.$id.'&username='.$_SESSION['username'].'&uid='.$_SESSION['uid'].'&realname='.$txt_name.'&email='.$txt_email.'&mobile='.$txt_mobile;
				//$rs=file_get_contents("https://www.songogo.com/ecpay/seller_china.php?id=0001&".$params);//发送信息至爽购网
				//if($rs==1){
					$msg="台湾会员若欲使用台币进行充值，将由IT帮手网台湾总代理「关键数位行销公司」进行充值服务，请连结至「http://epweike.songogo.com/」，并按指示申请。";
				//}else{
				//	$msg="操作失败请重新提交信息！".$rs;
			//	}
				break;
			default:
				$msg="线下付款信息已提交,请耐心等待审核";
				break;
				
		}
		if($id){
		    switch($charge_type){
			case 'tw':
				kekezu::echojson ( $msg, 1,array('order_id'=>$id));
				die ();
			break;
			default:
		        kekezu::show_msg ( "提交成功", "index.php?do=user&view=finance&op=detail&action=charge", 5, $msg, "success" );
		    break;
		  }
		}
	} elseif ($pay_mode == 'usercash') {
		//订单

		//订单结算函数   
		$r = keke_order_class::order_clear ( $order_id, $order_info );
		
		if ($r) {
			
			if ($order_info ['task_id']) {
				kekezu::show_msg ( "付款成功", "index.php?do=task&task_id={$order_info['task_id']}", 3, "付款成功", "success" );
			} else {
				$order_info ['obj_type'] == 'payitem' && $t = '&view=witkey&op=toolbox&show=my';
				kekezu::show_msg ( "付款成功", "index.php?do=user" . $t, 3, "", "success" );
			}
		} else {
			kekezu::show_msg ( "操作失败" );
		}
	} else {
		$pay_amount = ( float ) $order_info ['order_amount'];
		$charge_id  = intval($charge_id);
		if (! $center) {//不是来自用户中心的
			$recharge_obj = new Keke_witkey_order_charge_class (); //实例化充值表对象
			$recharge_obj->setOrder_type ( 'online_charge' );
			$center or $recharge_obj->setReturn_order_id ( $order_id ); 
			$recharge_obj->setPay_type ( $pay_mode );
			$recharge_obj->setObj_type ( $obj_type );
			$recharge_obj->setObj_id ( $obj_id );
			$recharge_obj->setUid ( $uid );
			$recharge_obj->setUsername ( $username );
			$recharge_obj->setPay_money ( $pay_amount );
			$recharge_obj->setOrder_status ( 'wait' );
			$charge_id = $recharge_obj->create_keke_witkey_order_charge ();
		}
		$payment_config = kekezu::get_payment_config ( $pay_mode );
		require S_ROOT . "/payment/" . $pay_mode . "/order.php";
		$payurl = get_pay_url ( 'order_charge', $pay_amount, $payment_config, $order_info ['order_name'], $charge_id, $model_id, $order_info ['obj_id'], $bank_code );
		
		header ( "location:$payurl" );
	
	//$title=$_lang['confirm_pay'];
	//require keke_tpl_class::template ( "pay_cash");die();
	}
}

//已存在的线下付款记录
$exsit_offline_order = db_factory::get_one ( "select pay_type,pay_money from " . TABLEPRE . "witkey_order_charge where order_type='offline_charge' and order_status = 'wait' and obj_type='$obj_type' and obj_id = '$obj_id'" );

require $kekezu->_tpl_obj->template ( $do );