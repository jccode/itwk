<?php
/**
 * 充值审核
 * @copyright keke-tech
 * @author Chen
 * @version v 20
 * 2011-10-21 09:18:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role (76 );

$recharge_obj = new Keke_witkey_order_charge_class(); //实例化充值表对象
$page_obj = $kekezu->_page_obj; //实例化分页对象
$charge_type_arr=keke_glob_class::get_charge_type();/*充值订单类型*/
$charge_action = keke_glob_class::get_charge_action();//充值用途
$status_arr = keke_order_class::get_order_status();
//$offline_pay=kekezu::get_table_data ( "*", "witkey_pay_api", " type='offline'", '', '', '', 'payment' ); //线下支付方式
//var_dump($offline_pay);
//分页
$w [page_size] and $page_size = intval ( $w [page_size] ) or $page_size =10;
intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[order_status]=$w[order_status]&w[order_id]=$w[order_id]&w[order_type]=$w[order_type]&w[username]=$w[username]&w[page_size]=$page_size&w[ord]=$w[ord]&w[brand]=$w[brand]&page=$page";
if($order_id&&$_SESSION['brandType']){
	$auth=$admin_obj->agent_auth("witkey_order_charge", 'order_id='.$order_id,'brand');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url, 3, '', 'error' );
		die();
	}
}
$bank_arr     = keke_glob_class::get_bank();
if (isset ( $ac )) { //处理财务清单申请
$order_info=db_factory::get_one(" select * from ".TABLEPRE."witkey_order_charge where order_id = ".intval($order_id));
//邮件
$message_obj = new keke_msg_class ();
$order_info or kekezu::admin_show_msg ( $_lang['charge_num_not_exist'], $url,3,'','warning');
	switch ($ac) {
		case 'pass' : //审核充值订单
				if ($order_info [order_status] == 'ok') {
 
					kekezu::admin_show_msg ( $_lang['payment_has_been_success_no_need_repeat'], $url,3,'','warning');
 
				}
				$recharge_obj->setWhere ( 'order_id =' . $order_id );
				$recharge_obj->setOrder_status('ok' );//充值审核通过
				$res = $recharge_obj->edit_keke_witkey_order_charge();
				$user_info = kekezu::get_user_info ( $order_info [uid] );
				/** 通知用户*/ 
				$v_arr = array ("充值金额"=> $order_info['pay_money']);
				keke_shop_class::notify_user ( $user_info[uid], $user_info[username], "pay_success","支付成功通知", $v_arr );
					/*新增财务记录*/
				keke_finance_class::cash_in($user_info['uid'], $order_info['pay_money'],0,'offline_recharge');
				
				$o_info = db_factory::get_one("select * from ".TABLEPRE."witkey_order_charge where order_id='$order_id'");
				
				$o_info['return_order_id'] and keke_order_class::order_clear($o_info['return_order_id']);
				
				
				kekezu::admin_system_log ( $_lang['confirm_payment_recharge'].$order_id);
				
				kekezu::admin_show_msg ( $_lang['message_about_recharge_success'], $url,3,'','success' );
				
		break;
	//删除充值订单
	case 'del' :
			$recharge_obj->setWhere ( ' order_id=' . $order_id );
			$res = $recharge_obj->del_keke_witkey_order_charge();
			 
			$user_info = kekezu::get_user_info ( $order_info [uid] );
			$v = array ("充值单号"=> $order_id);
			$message_obj->send_message ( $user_info ['uid'], $user_info ['username'], 'recharge_fail',"充值审核失败通知", $v, $user_info [email], $user_info ['mobile'] );
				
			kekezu::admin_system_log ( $_lang['delete_apply_forwithdraw'] . $order_id );
			kekezu::admin_show_msg ( $_lang['message_about_delete'], $url,3,'','success' );
		;
		break;	
	}
 
}elseif (isset ( $ckb )) { //批量删除
	$ids = implode ( ',', $ckb );
	if (count ( $ids )) {
	
		$recharge_obj->setWhere ( " order_id in ($ids) and order_status = 'wait' " );
		$nodraw_arr = $recharge_obj->query_keke_witkey_order_charge();	//待审核的充值记录
		$del_ids=array();
		switch ($sbt_action) {
			case $_lang['mulit_delete'] : //批量删除
				//待审核的退款处理后，
				foreach ( $nodraw_arr as $k=>$v ) {
					$del_ids[$k]=$v[order_id];
					
					$message_obj = new keke_msg_class ();//邮件
					$user_info=keke_user_class::get_user_info($v[uid]);//用户信息
					$v = array ($_lang['recharge_single_num'] =>$v['order_id'],$_lang['recharge_cash'] => $v [pay_money] );
					$message_obj->send_message ( $user_info ['uid'], $user_info ['username'], 'recharge_fail', $_lang['recharge_fail'], $v, $user_info [email], $user_info ['mobile'] );
				}
				//审核通过的直接删除
				$del_ids=implode(",", $del_ids);
				if($del_ids){
					$recharge_obj->setWhere ( " order_id in ($del_ids)" );
					$res = $recharge_obj->del_keke_witkey_order_charge();
					kekezu::admin_system_log ( $_lang['delete_recharge_order'].$del_ids );
				}
				break;
		}
		
		if ($res) {
			kekezu::admin_show_msg ( $_lang['mulit_operate_success'], $url,3,'','success');
		} else {
			kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning');
		}
	
	} else {
		kekezu::admin_show_msg ( $_lang['please_select_an_item_to_operate'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
	}
}elseif($show=='export'){
	require "admin_finance_recharge_export.php";
	die();
} else {
	$table_class = new keke_table_class ( 'witkey_order_charge' );
	$field="*";
	$table='witkey_order_charge';
	
	$where="brand in(".$_SESSION['brandType'].")";
	$w ['brand'] and $where.= " and brand='".$w[brand]."'";
	$w ['order_id'] and $where .= " and order_id = '$w[order_id]' ";
	$w ['order_type'] and $where .= " and order_type = '$w[order_type]'";
	$w ['order_status'] and $where .= " and order_status = '$w[order_status]' ";
	$w ['username'] and $where .= " and username like '%$w[username]%' ";

	is_array($w['ord']) and $order .= ' '.$w['ord'][0].' '.$w['ord'][1] or $order.=' order_id desc' ;
	
	$res = $table_class->get_multi_grid ($field,$table,'','',$where,$order,$page, $page_size,$url);
	$recharge_arr = $res ['data'];
	$pages = $res ['pages'];
}

require $template_obj->template ( 'control/agent/tpl/admin_' . $do . '_' . $view );