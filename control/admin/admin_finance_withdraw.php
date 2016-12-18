<?php
/**
 * 财务--提现审核
 * @copyright keke-tech
 * @author Chen
 * @version v 20
 * 2011-09-03 15:18:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 5 );
$withdraw_obj = new Keke_witkey_withdraw_class (); //实例化提现表对象
$user_space_obj = new Keke_witkey_space_class (); //实例化用户信息表对象
$page_obj = $kekezu->_page_obj; //实例化分页对象
$paytype_list = kekezu::get_table_data ( "payment,config", "witkey_pay_api", " type!='trust'", "", "", "", "payment" );
$status_arr  = keke_glob_class::withdraw_status();
$bank_arr = keke_glob_class::get_bank();
$k_arr   = array_keys($bank_arr);
//分页
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[pay_type]=".$w['pay_type']."&w[page_size]=$page_size&w[ord]=".$w['ord']."&w[brand]=".$w['brand']."&page=$page";

$withdraw_id and $withdraw_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_withdraw where withdraw_id = '$withdraw_id'" );

if (isset ( $ac )) { //处理财务清单申请

	switch ($ac) {
		case 'pass' : //审核提现申请
			if ($withdraw_info['withdraw_status']) {
				if ($is_submit) {
					
					$user_space_info = kekezu::get_user_info ( $withdraw_info ['uid'] );
					
					if ($withdraw_info ['withdraw_status'] != 1) {
						kekezu::admin_show_msg ( $_lang['no_need_to_repeat'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
					}
					
					$withdraw_obj->setWhere ( 'withdraw_id=' . $withdraw_id );
					//提现审核通过
					$withdraw_obj->setWithdraw_status ( 2 );
					$withdraw_obj->setProcess_uid ( $admin_info ['uid'] );
					$withdraw_obj->setProcess_username ( $admin_info ['username'] );
					$withdraw_obj->setFee(keke_finance_class::get_to_cash($withdraw_info['withdraw_cash'],$withdraw_info['pay_type']));
					$withdraw_obj->setProcess_time ( time () );
					$withdraw_obj->setRemark($remark);
					$res = $withdraw_obj->edit_keke_witkey_withdraw ();
					
					$feed_arr = array ("feed_username" => array ("content" => $withdraw_info ['username'],
					 "url" => "index.php?do=shop&u_id=".$user_space_info['uid']),
					 "action" => array ("content" => $_lang['withdraw'], "url" => "" )
					, "event" => array ("content" => $_lang['withdraw_le'] . $withdraw_info ['withdraw_cash'] . $_lang['yuan'], "url" => "" )
					 );
					kekezu::save_feed ( $feed_arr, $user_space_info ['uid'], $user_space_info ['username'], 'withdraw' );
					
					//kekezu::feed_add ( '<a href="index.php?do=space&member_id=' . $user_space_info ['uid'] . '" target="_blank">' . $withdraw_info ['username'] . '</a>成功提现了' . $withdraw_info ['withdraw_cash'] . $_lang['yuan'], $user_space_info ['uid'], $user_space_info ['username'], 'withdraw' );
					//邮件
					$message_obj = new keke_msg_class ();
					$t_userinfo = db_factory::get_one ( " select mobile,email from " . TABLEPRE . "witkey_space where uid ='".$withdraw_info['uid']."'" );
					$v = array ("提现金额"=> $withdraw_info ['withdraw_cash'] );
					$message_obj->send_message ( $withdraw_info ['uid'], $withdraw_info ['username'], 'draw_success',"提现成功通知", $v, $t_userinfo ['email'], $t_userinfo ['mobile'] );
					
					$space_info = kekezu::get_user_info ( intval ( $withdraw_info ['uid'] ) );
					
					//	kekezu::update_score_value ( $withdraw_info ['uid'], 'withdraw', 2 );
					kekezu::admin_system_log ( $_lang['audit_withdraw_apply'] . $withdraw_id );
					kekezu::admin_show_msg ( $_lang['audit_withdraw_pass'], 'index.php?do=' . $do . '&view=' . $view,3,'','success');
				}	//print_r($withdraw_info);
				require $template_obj->template ( 'control/admin/tpl/admin_finance_withdraw_info' );
				die ();
			} else {
				kekezu::admin_show_msg ( $_lang['audit_withdraw_not_exist'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
			}
			;
			break;
		case 'cancel' : //审核提现取消申请		
			if ($withdraw_info['withdraw_status'] == 1 && $remark) {				
				if ($is_submit) {		
					$user_space_info = kekezu::get_user_info ( $withdraw_info ['uid'] );
					
					 //提现审核失败
					$withdraw_obj->setWhere ( 'withdraw_id=' . $withdraw_id );
					$withdraw_obj->setWithdraw_status ( 3 );
					$withdraw_obj->setProcess_time ( time() );
					$withdraw_obj->setRemark( $remark );
					$res_1 = $withdraw_obj->edit_keke_witkey_withdraw ();
				
					if($res_1){	//退款到用户账户
						$res_2 = keke_finance_class::cash_in ( $withdraw_info ['uid'], abs ( floatval ( $withdraw_info['withdraw_cash'] ) ), 0,'withdraw_fail', 'self', 'withdraw', $withdraw_id );
					}
					
					if($res_1 && $res_2){						 
						$t_userinfo = db_factory::get_one ( " select email from " . TABLEPRE . "witkey_space where uid ='".$withdraw_info['uid']."'" );
						kekezu::send_mail ($t_userinfo ['email'], "提现失败通知", $remark);//邮件	
						kekezu::notify_user ( "提现失败通知", $remark , $withdraw_info['uid'], $withdraw_info['username']); //站内信
											
						kekezu::admin_system_log ( "提现失败：" . $withdraw_id );					
						kekezu::echojson ( '提现申请取消成功', "1" );
						
					}else{
						kekezu::echojson ( '提现申请取消失败', "0" );
					}
				}	
				die ();
			} else {
				kekezu::admin_show_msg ( $_lang['audit_withdraw_not_exist'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
			}
			;
			break;
		 //拒绝通过并,删除提现申请
		case 'del' :
			if ($withdraw_info) {
				$withdraw_obj->setWhere ( 'withdraw_id=' . $withdraw_id );
				$withdraw_info = $withdraw_obj->query_keke_witkey_withdraw ();
				$withdraw_obj->setWhere ( 'withdraw_id=' . $withdraw_id );
				$res = $withdraw_obj->del_keke_witkey_withdraw ();
				//现提金额
				$withdraw_cash = $withdraw_info ['0'] ['withdraw_cash'];
				$uid = $withdraw_info ['0'] ['uid'];
				$username = $withdraw_info ['0'] ['username'];
				keke_finance_class::cash_in ( $uid, $withdraw_cash, 0, 'withdraw_fail' );
				kekezu::notify_user ( $_lang['fail_and_check_you_account'], $uid, $username );
				if ($space_info ['email']) {
					kekezu::send_mail ( $space_info ['email'], $_lang['audit_withdraw_apply_fail'], $_lang['not_pass_and_check_you_account'] );
				}
				kekezu::admin_system_log ( $_lang['delete_audit_withdraw'] . $withdraw_id );
				kekezu::admin_show_msg ( $_lang['delete_audit_withdraw_success'], 'index.php?do=' . $do . '&view=' . $view,3,'','success' );
			} else {
				kekezu::admin_show_msg ( $_lang['fail_item_not_exist'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
			}
			;
			break;
	
	}
} elseif (isset ( $ckb )) { //批量删除
	$ids = implode ( ',', $ckb );
	if (count ( $ids )) {
		//待审核的提出记录
		$withdraw_obj->setWhere ( " withdraw_id in ('$ids') and withdraw_status =0 " );
		$nodraw_arr = $withdraw_obj->query_keke_witkey_withdraw ();		
		$withdraw_obj->setWhere ( ' withdraw_id in (' . $ids . ') ' );				
		switch ($sbt_action) {
			case $_lang['mulit_delete']: //批量删除
				//待审核的退款处理后，
				foreach ( $nodraw_arr as $v ) {
					$withdraw_id = $v ['withdraw_id'];
					$where = "withdraw_id = '$withdraw_id' ";
					$withdraw_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_withdraw where $where" );
					$withdraw_cash = $withdraw_info ['withdraw_cash'];
					$uid = $withdraw_info ['uid'];
					$username = $withdraw_info ['username'];
					keke_finance_class::cash_in ( $uid, $withdraw_cash, 0, 'withdraw_fail' );
					kekezu::notify_user ( $_lang['audit_withdraw_fail'], $_lang['not_pass_and_check_you_account'], $uid, $username );
					if ($space_info ['email']) {
						kekezu::send_mail ( $space_info ['email'], $_lang['audit_withdraw_fail'], $_lang['not_pass_and_check_you_account'] );
					}
				}
				//审核通过的直接删除
				$res = $withdraw_obj->del_keke_witkey_withdraw ();
				kekezu::admin_system_log ( $_lang['delete_audit_withdraw'] . $ids );
				break;
			case $_lang['mulit_review']: //批量审核
				$withdraw_arr = $withdraw_obj->query_keke_witkey_withdraw ();
				$withdraw_obj->setWhere ( ' withdraw_id in (' . $ids . ') ' );
				$withdraw_obj->setWithdraw_status ( 2);
				$withdraw_obj->setProcess_uid ( $admin_info ['uid'] );
				$withdraw_obj->setProcess_username ( $admin_info ['username'] );
				$withdraw_obj->setProcess_time ( time () );
				$res = $withdraw_obj->edit_keke_witkey_withdraw ();
				
				foreach ( $withdraw_arr as $withdraw_info ) {
					$space_info = kekezu::get_user_info ( $withdraw_info ['uid'] );
					$withdraw_id = $withdraw_info ['withdraw_id'];
					if ($withdraw_info ['withdraw_status'] != 1) {
						continue;
					}
					$fee = keke_finance_class::get_to_cash($withdraw_info['withdraw_cash'],$withdraw_info['pay_type']);
					db_factory::execute('update '.TABLEPRE.'witkey_withdraw set fee="'.$fee.'" where whthdraw_id='.$withdraw_id);
					
					//邮件
					$message_obj = new keke_msg_class ();
					$t_userinfo = db_factory::get_one ( " select mobile,email from " . TABLEPRE . "witkey_space where uid ='".$withdraw_info['uid']."'" );
					$v = array ($_lang['withdraw_cash'] => $withdraw_info ['withdraw_cash'] );
					$message_obj->send_message ( $withdraw_info ['uid'], $withdraw_info ['username'], 'draw_success', $_lang['withdraw_success'], $v, $t_userinfo ['email'], $t_userinfo ['mobile'] );
					$feed_arr = array ("feed_username" => array ("content" => $withdraw_info ['username'], "url" => "index.php?do=shop&u_id=".$space_info['uid']), "action" => array ("content" => $_lang['withdraw'], "url" => "" ), "event" => array ("content" =>$_lang['withdraw_le'].$withdraw_info['withdraw_cash']. $_lang['yuan'],"url" => "" ) );
					kekezu::save_feed ( $feed_arr, $user_space_info ['uid'], $user_space_info ['username'], 'withdraw' );
				
	//kekezu::update_score_value ( $withdraw_info ['uid'], 'withdraw', 2 );
				//kekezu::feed_add ( '<a href="index.php?do=space&member_id=' . $space_info ['uid'] . '" target="_blank">' . $withdraw_info ['username'] . '</a>成功提现了' . $withdraw_info ['withdraw_cash'] . '元', $user_space_info ['uid'], $user_space_info ['username'], 'withdraw' );
				}
				
				kekezu::admin_system_log ( $_lang['audit_withdraw_apply'] . $ids );
				break;
		
		}
		
		if ($res) {
			kekezu::admin_show_msg ( $_lang['mulit_operate_success'], 'index.php?do=' . $do . '&view=' . $view,3,'','success');
		} else {
			kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], 'index.php?do=' . $do . '&view=' . $view ,3,'','warning');
		}
	
	} else {
		kekezu::admin_show_msg ( $_lang['choose_operate_item'], 'index.php?do=' . $do . '&view=' . $view,3,'','warning' );
	}

} elseif ($type == 'batch' && $pay_type == 'alipayjs') { 
	$payment_config = kekezu::get_payment_config('alipayjs');
	require S_ROOT . "/payment/alipayjs/order.php";
	$detail_data = db_factory::query ( sprintf ( " select withdraw_id,pay_type,pay_account,pay_username,withdraw_cash fee,uid from %switkey_withdraw where withdraw_id in (%s) and withdraw_status='1'", TABLEPRE, $ids ) );
	echo get_batch_url ($payment_config, $detail_data,'url');
	die();
}elseif($show=='add_audit'){	
	require keke_tpl_class::template ( 'control/admin/tpl/admin_finance_withdraw_audit');
	die();
}elseif($show=='cancel_audit'){
	require keke_tpl_class::template ( 'control/admin/tpl/admin_finance_withdraw_cancelaudit');
	die();
}elseif($show=='export'){
	require "admin_finance_withdraw_export.php";
	die();
} else {
	$table_class = new keke_table_class ( 'witkey_withdraw' );
	$field="*";
	$table='witkey_withdraw';
	
	$where="1=1";
	$w ['brand'] and $where.= " and brand='".$w['brand']."'";
	$w ['withdraw_id'] and $where .= " and withdraw_id = '".$w['withdraw_id']."' ";
	$w ['username'] and $where .= " and username like '%".$w['username']."%' ";
	$w ['pay_type'] and $where .= " and pay_type = '".$w['pay_type']."' ";

	is_array($w['ord']) and $order= ' '.$w['ord']['0'].' '.$w['ord']['1'] or $order= " withdraw_id desc";
	
	$res = $table_class->get_multi_grid ($field,$table,'','',$where,$order,$page, $page_size,$url);
	$withdraw_arr = $res ['data'];
	$pages = $res ['pages'];
}
if ($withdraw_id) {
	$withdraw_obj->setWhere ( 'withdraw_id=' . $withdraw_id );
	$withdraw_info = $withdraw_obj->query_keke_witkey_withdraw ();
	$withdraw_info = $withdraw_info ['0']; 
} 
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );