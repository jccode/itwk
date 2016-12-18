<?php
/**
 * 用户管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 11 );
if($check_uid){
	CHARSET=='gbk' and $check_uid = kekezu::utftogbk($check_uid);
	$info = get_info($check_uid);
	$info and kekezu::echojson('',1,$info) or kekezu::echojson($_lang['none_exists_uid_or_username'],0);
	die();
}
if($is_submit){
	$url = "index.php?do=$do&view=$view";
	$user or kekezu::admin_show_msg($_lang['username_uid_can_not_null'],$url,3,'','warning');
	$info = get_info($user); 
	$cash = floatval($cash);$credit = floatval($credit);
	if($cash<-$info['balance']){ 
		kekezu::admin_show_msg($_lang['user_deduct_limit'].$info['balance'].$_lang['yuan'],$url,3,'','warning');
	}elseif($credit<-$info['credit']){
		kekezu::admin_show_msg($_lang['user_deduct_limit'].$info['balance'].CREDIT_NAME,$url,3,'','warning');
	}
	
	($cash==0&&$credit==0) and kekezu::admin_show_msg($_lang['cash_can_not_null'],$url,3,'','warning');
	$res = keke_finance_class::cash_in_two($info['uid'], floatval($cash),floatval($credit),'admin_charge','','','','','',$remark);
	
	if($cash < 1){
		$res and kekezu::admin_show_msg('手动扣除成功 ',$url,3,'','success') or kekezu::admin_show_msg('手动扣除失败 ',"index.php?do=$do&view=$view",5,'','warning');
	}else{
		$res and kekezu::admin_show_msg($_lang['charge_success'],$url,3,'','success') or kekezu::admin_show_msg($_lang['charge_fail'],"index.php?do=$do&view=$view",3,'','warning');	
	}
	
}
function get_info($uid){
	$sql = " select balance,credit,uid from %switkey_space where ";
	is_numeric($uid) and $sql.=" uid='%d'" or $sql.=" username='%s'";
	return  db_factory::get_one(sprintf($sql,TABLEPRE,$uid));
}

require keke_tpl_class::template ( 'control/admin/tpl/admin_user_charge' );