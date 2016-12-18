<?php
/**
 * 短信配置
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(198);
require '../../keke_client/sms/postmsg.php';
$account_info = $kekezu->_sys_config; //手机账号信息
$mobile_u = $account_info ['mobile_username'];
$mobile_p = $account_info ['mobile_password'];
$op and $op = $op or $op = 'config';

$url = "index.php?do=$do&view=$view&op=$op";
switch ($op) {
	case "config" :
		if (! isset ( $sbt_edit )) {
			$bind_info = check_bind ( 'mobile_username' );
		} else { //添加、编辑\
			/**mobile**/
			foreach ( $conf as $k => $v ) {
				if (check_bind ( $k )) {
					
					$res .= db_factory::execute ( " update " . TABLEPRE . "witkey_basic_config set v='$v' where k='$k'" );
				} else {
				//	kekezu::admin_system_log('创建了手机平台');
					$res .= db_factory::execute ( " insert into " . TABLEPRE . "witkey_basic_config values('','$k','$v','mobile','','')" );
				}
			}
			kekezu::admin_system_log($_lang['edit_mobile_log']);
			if ($res)
				kekezu::admin_show_msg ( $_lang['binding_cellphone_account_successfully'], "index.php?do=$do&view=$view&op=config",3,'','success' );
			else
				kekezu::admin_show_msg ( $_lang['binding_cellphone_account_fail'], "index.php?do=$do&view=$view&op=config",3,'','warning' );
		
		}
		break;
	case "manage" :
		if ($remain_fee) {
			if ($mobile_p && $mobile_u) {
				$config_info = Msg_GetConfigInfo ( $mobile_u, $mobile_p );
				if (! $config_info) {
					kekezu::echojson ( $_lang['get_user_info_fail'], "2" );
					die ();
				} else {
					$remain_fee = Msg_GetRemainFee ( $mobile_u, $mobile_p ); //账号余额
					kekezu::echojson ( $remain_fee / 100, "1" );
					die ();
				}
			} else {
				kekezu::admin_show_msg ( $_lang['not_bind_cellphone_account'], "index.php?do=$do&view=$view&op=config",3,'','warning' );
			}
		
		}
		break;
}
/**
 *检测绑定账号是否存在 
 */
function check_bind($k) {
	return db_factory::get_count ( " select k from " . TABLEPRE . "witkey_basic_config where k='$k'" );
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );