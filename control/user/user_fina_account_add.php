<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$user_info = kekezu::get_user_info ( $uid );
 
 //判断是否实名认证
$user_info['auth_realname']!=1 and kekezu::show_msg('必须先通过身份认证才能进行此操作',"index.php?do=user&view=setting&op=auth&auth_code=realname",'3','','warning');

$step_list = array (
		"step1" => array (
				$_lang ['step_one'],
				$_lang ['complete_bank_account_info'] 
		),
		"step2" => array (
				$_lang ['step_two'],
				$_lang ['account_setting_successful'] 
		) 
);

$ac_url = $origin_url . "&op=$op&opp=$opp";
$step=$step?$step:'step1';
switch ($step) {
	case "step1" : 
		$real_pass = keke_auth_fac_class::auth_check ( "realname", $uid );
		//! $real_pass && $bank_type == '1' and kekezu::show_msg ( $_lang ['realname_auth_not_pass'], "index.php?do=user&view=payitem&op=auth&auth_code=realname#userCenter", "3", '', 'warning' );
		$bank_arr = keke_glob_class::get_bank (); // 银行列表
	
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$conf or kekezu::show_msg ( $_lang ['submit_fail_retry_later'], $ac_url . "&step=step3&bank_type=$bank_type#userCenter", "3", '', 'warning' );
			
			$auth_realname = db_factory::get_count(" select realname from " . TABLEPRE . "witkey_auth_realname where uid='$user_info[uid]'");
			if(!$auth_realname){
				kekezu::show_msg ( '提交失败', $ac_url . "index.php?do=user&view=setting&op=fina_account&opp=add&step=step1", "3", '必须先进行身份认证才能进行此操作', 'warning' );
			}
			
			$bank_obj = keke_table_class::get_instance ( "witkey_member_bank" );
			$bank_name&&$conf['bank_name'] = $bank_name;
			$conf['real_name'] = $auth_realname;
			$conf ['uid'] = $uid;
			$conf ['bank_type'] = $bank_type;
			$conf ['on_time'] = time ();
			$conf['bank_address'] = $province.",".$city.",".$area;
			$conf = kekezu::escape ( $conf );
			$bank_id = $bank_obj->save ( $conf );
			
			if ($bank_id) {
				db_factory::execute ( sprintf ( " update %switkey_member_bank set bind_status='1' where bank_id='%d'", TABLEPRE, $bank_id ) );
				kekezu::show_msg ( "操作提示", $ac_url . "&step=step2&bank_id=$bank_id", '1', '编辑成功！', 'alert_right' ) ;
			} else {
				kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '系统繁忙请稍后！', 'alert_error' ) ;
			}
		
		}
		
		$auth_realname_arr = db_factory::get_one(" select * from " . TABLEPRE . "witkey_auth_realname where uid='$user_info[uid]'");
	case "step2" :
		$bank_arr = keke_glob_class::get_bank (); // 银行列表
		$bank_info = db_factory::get_one ( sprintf ( " select * from %switkey_member_bank where bank_id='%d' and uid='%d' and bind_status='1' ", TABLEPRE, $bank_id, $uid ) );
	
		break;
}

if (isset($check_bank) && !empty($check_bank)){
	$res = keke_user_class::check_bank($check_bank);
	echo $res;
	die();
}

require keke_tpl_class::template ( "user/" . $do . "_" . $op . "_" . $opp );