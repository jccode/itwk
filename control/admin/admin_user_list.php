<?php
/**
 * 用户管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 11 );
//初始化 
$page_obj = $kekezu->_page_obj;
$edituid and $memberinfo_arr = kekezu::get_user_info ( $edituid );
$table_class = new keke_table_class ( 'witkey_space' );
$member_class = new keke_table_class ( 'witkey_member' );
//查询
$url_str = "index.php?do=$do&view=$view&condit=$condit&txt_val=$txt_val&page_size=$page_size&ord[0]=$ord[0]&$ord[1]=$ord[1]&slt_static=$static";

$grouplist_arr = keke_admin_class::get_user_group ();
switch ($op) {
	case "del" : //删除
		/* $del_uid = keke_user_class::user_delete ( $edituid );
		kekezu::admin_system_log ( kekezu::lang ( 'delete_member}' ) . $memberinfo_arr ['username'] );
		$del_uid and kekezu::admin_show_msg ( $_lang ['operate_success'], "index.php?do=user&view=list", 3, '', 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_fail'], "index.php?do=user&view=list", 3, '', 'warning' ); */
		break;
	case "disable" : //冻结用户
		$sql = sprintf ( "update  %switkey_space set status=2 where uid =%d", TABLEPRE, $edituid );
		db_factory::execute ( $sql );
		$v_arr = array ("用户名" => $memberinfo_arr ['username'], "网站名称" => $kekezu->_sys_config ['website_name'] );
		keke_shop_class::notify_user ( $memberinfo_arr ['uid'], $memberinfo_arr ['username'], 'freeze', "账户冻结通知", $v_arr );
		kekezu::admin_system_log ( $_lang ['unfreeze_member'] . $memberinfo_arr ['username'] );
		kekezu::admin_show_msg ( $_lang ['operate_success'], "index.php?do=user&view=list", 3, '', 'success' );
		break;
	case "able" : //解冻用户
		kekezu::admin_check_role ( 24 );
		
		$sql = sprintf ( "update  %switkey_space set status=1 where uid =%d", TABLEPRE, $edituid );
		db_factory::execute ( $sql );
		$v_arr = array ($_lang ['username'] => $memberinfo_arr ['username'], $_lang ['website_name'] => $kekezu->_sys_config ['website_name'] );
		keke_shop_class::notify_user ( $memberinfo_arr ['uid'], $memberinfo_arr ['username'], 'unfreeze', $_lang ['user_freeze'], $v_arr );
		kekezu::admin_system_log ( $_lang ['unfreeze_member'] . $memberinfo_arr ['username'] );
		kekezu::admin_show_msg ( $_lang ['operate_success'], "index.php?do=user&view=list", 3, '', 'success' );
		break;
	case 'login' :
		if ($loguid && $admin_info) {
			if ($admin_info ['group_id'] > 0) { //客服
				$user_info = kekezu::get_user_info($loguid);
				$_SESSION ['uid'] = $user_info ['uid'];
				$_SESSION ['username'] = $user_info ['username'];
				kekezu::admin_system_log ("客服#".$admin_info['username']."从后台登陆账户#". $user_info['username'] );
				header('Location:'.$_K['siteurl']);
			}
		}
		break;
}

if ($sbt_action && is_array ( $ckb )) {
	
	$ids = implode ( ',', $ckb );
	$sql = sprintf ( "select uid,username from %switkey_space where uid in (%s)", TABLEPRE, $ids );
	$space_arr = db_factory::query ( $sql );
	switch ($sbt_action) {
		case $_lang ['mulit_delete'] : //批量删除
			/* $table_class->del ( 'uid', $ckb );
			$member_class->del ( 'uid', $ckb );
			kekezu::admin_system_log ( $_lang ['delete_user'] . "$ids" );
			kekezu::admin_show_msg ( $_lang ['operate_success'], 'index.php?do=user&view=list', 3, $_lang ['mulit_operate_success'], 'success' ); */
			break;
		case $_lang ['mulit_disable'] : //批量禁用
			

			$sql = sprintf ( "update  %switkey_space set status=2 where uid in (%s)", TABLEPRE, $ids );
			db_factory::execute ( $sql ); //改变用户状态 
			foreach ( $space_arr as $v ) { //邮件通知
				$v_arr = array ($_lang ['username'] => $v ['username'], $_lang ['website_name'] => $kekezu->_sys_config ['website_name'] );
				keke_shop_class::notify_user ( $v ['uid'], $v ['username'], 'freeze', $_lang ['user_freeze'], $v_arr );
			}
			kekezu::admin_system_log ( $_lang ['freeze_user'] . "$ids" );
			kekezu::admin_show_msg ( $_lang ['operate_success'], 'index.php?do=user&view=list', 3, $_lang ['mulit_disable'], 'success' );
			break;
		case $_lang ['mulit_use'] : //批量开启
			

			$sql = sprintf ( "update  %switkey_space set status=1 where uid in (%s)", TABLEPRE, $ids );
			db_factory::execute ( $sql ); //改变用户状态 
			foreach ( $space_arr as $v ) { //邮件通知
				$v_arr = array ($_lang ['username'] => $v ['username'], $_lang ['website_name'] => $kekezu->_sys_config ['website_name'] );
				keke_shop_class::notify_user ( $v ['uid'], $v ['username'], 'unfreeze', $_lang ['user_open'], $v_arr );
			}
			kekezu::admin_show_msg ( $_lang ['operate_success'], 'index.php?do=user&view=list', 3, $_lang ['mulit_open_operate_success'], 'success' );
			break;
	}
} else {
	$where_str = " 1=1 ";
	//每页显示多少条，默认10
	$page or $page = 1;
	$slt_page_size = intval ( $slt_page_size ) ? intval ( $slt_page_size ) : 10;
	if ($condit && $txt_val) {
		switch ($condit) {
			case 'uid' :
			case 'qq' :
				$where_str .= ' and ' . $condit . '=' . intval ( $txt_val );
				break;
			case 'username' :
			case 'truename' :
				$where_str .= "and " . $condit . " like '%{$txt_val}%' ";
				break;
			case 'email' :
				$where_str .= "and email = '{$txt_val}'";
				break;
			case 'mobile' :
				$where_str .= "and (mobile = '{$txt_val}' or phone = '{$txt_val}' )";
				break;
		}
	}
	intval ( $slt_status ) and $where_str .= ' and status = ' . intval ( $slt_status );
	$ord [0] && $ord [1] and $where_str .= " order by {$ord['0']} {$ord['1']}" or $where_str .= " order by uid desc";
	$res = $table_class->get_grid ( $where_str, $url_str, $page, $slt_page_size, null );
	$userlist_arr = $res ['data'];
	$pages = $res ['pages'];
}

require $template_obj->template ( 'control/admin/tpl/admin_user_list' );
		
 
