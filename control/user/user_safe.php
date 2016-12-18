<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$opps = array ('change_password', "sec_code" );

in_array ( $opp, $opps ) or $opp = "change_password";
$ac_url = "index.php?do=$do&view=$view&op=$op";
/**
 * 子集菜单
 */
$third_nav = array ("change_password" => array ($_lang['change_pwd'], $_lang['change_pwd'] ),
				   "sec_code" => array ($_lang['safe_code_set'], $_lang['change_safe_code'] ) 
				) ;
switch ($opp) {
	case "change_password" :
		if ($check_old) {
			if(md5($check_old)==$user_info['password']){
				$notice = true;
			}else{
				$notice = $_lang['pwd_enter_err'];
				CHARSET=='gbk' and $notice =  kekezu::gbktoutf($notice);
			}
			echo $notice;
			die();
		}
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$old_pass = $old_password;
			$new_pass = $new_password;
			$new_equal = $new_equal;
			if ($basic_config ['user_intergration'] != "2" && $old_pass == $new_pass) {
				kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1',  $_lang['old_new_pwd_like'], 'alert_error' ) ;
			} elseif ($basic_config ['user_intergration'] != "2" && md5 ( $old_pass ) != $user_info ['password']) {
				kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1',  $_lang['current_pwd_err'], 'alert_error' ) ;
			} elseif ($new_pass != $new_equal) {
				kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1',  $_lang['pwd_enter_not_consistent'], 'alert_error' ) ;
			}
			$message_obj = new keke_msg_class ();
			$v = array ($_lang['new_pwd'] => $new_pass );
			$message_obj->send_message ( $user_info ['uid'], $user_info ['username'], 'update_password', $_lang['change_pwd'], $v, $user_info ['email'], $user_info ['mobile'] );
			
			$user_obj = new Keke_witkey_space_class ();
			$user_obj->setWhere ( "uid='$uid'" );
			$user_obj->setPassword ( md5 ( $new_password ) );
			$user_obj->edit_keke_witkey_space ();
			$member_obj = new Keke_witkey_member_class ();
			$member_obj->setWhere ( "uid='$uid'" );
			$member_obj->setPassword ( md5 ( $new_password ) );
			$res = $member_obj->edit_keke_witkey_member ();
			
			$flag = keke_user_class::user_edit ( $username, $old_password, $new_password, '', 0 ) > 0 ? 1 : 0;
			
			if ($flag && $res == 1){
				unset ( $_SESSION );
			}
			kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
		}
		break;
	case "sec_code" :
		if ($check_old) {
			$pwd = keke_user_class::get_password ( $check_old, $user_info ['rand_code'] );
			if($pwd==$user_info['sec_code']){
				$notice = true;
			}else{
				$notice = $_lang['safe_code_enter_err'];
				CHARSET=='gbk' and $notice =  kekezu::gbktoutf($notice);
			}echo $notice;
			die();
		}
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			if($user_info['sec_code']){
				$pwd = keke_user_class::get_password ( $old_sec_code, $user_info ['rand_code'] );
				if ($pwd != $user_info ['sec_code']) {
					kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1',$_lang['current_safe_code_err'], 'alert_error' ) ;
				} elseif ($new_sec_code == $old_sec_code) {
					kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1',$_lang['please_enter_again'], 'alert_error' ) ;
				}
			}
			if ($new_sec_code != $new_equal) {
				kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1', $_lang['safe_code_enter_inconsistent'], 'alert_error' ) ;
			}
			
			$message_obj = new keke_msg_class ();
			$v = array ($_lang['safe_code'] => $new_sec_code );
			$message_obj->send_message ( $user_info ['uid'], $user_info ['username'], 'update_sec_code', $_lang['change_safe_code'], $v, $user_info ['email'], $user_info ['mobile'] );
			
			$user_obj = new Keke_witkey_space_class ();
			$user_obj->setWhere ( "uid='$uid'" );
			$user_obj->setSec_code ( keke_user_class::get_password ( $new_sec_code, $user_info ['rand_code'] ) );
			$res = $user_obj->edit_keke_witkey_space ();
			
			kekezu::show_msg ( "操作提示", $ac_url."&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
		}
		break;
}

require keke_tpl_class::template('user/user_'.$op.'_' . $opp);

