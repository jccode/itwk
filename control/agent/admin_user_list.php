<?php
/**
 * 用户管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 11 );
global $admin_info;
//初始化 
$page_obj = $kekezu->_page_obj;
if($edituid){
	$auth=$admin_obj->agent_auth("witkey_space", 'uid='.$edituid);
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], "index.php?do=user&view=list", 3, '', 'error' );
		die();
	}
}

$edituid and $memberinfo_arr = kekezu::get_user_info ( $edituid );
$table_class = new keke_table_class ( 'witkey_space' );
$member_class = new keke_table_class ( 'witkey_member' );
//查询
!$page_size and $page_size=$slt_page_size = intval ( $slt_page_size ) ? intval ( $slt_page_size ) : 10;
$url_str = "index.php?do=$do&view=$view&condit=$condit&txt_val=$txt_val&page_size=$page_size&ord[0]=$ord[0]&$ord[1]=$ord[1]&slt_static=$static&vip_status=$vip_status";

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
	$sql = sprintf ( "select uid,username from %switkey_brand where uid in (%s)", TABLEPRE, $ids );
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
	$field="s.*,b.brand";
	$table=array('s'=>'witkey_space','b'=>'witkey_brand');
	$join=array("left join");
	$on=array('s.uid=b.uid');
	$where_str = " s.brand in(".$_SESSION['brandType'].") and b.app_status=1";
	//每页显示多少条，默认10
	$page or $page = 1;
	if ($condit && $txt_val) {
		switch ($condit) {
			case 'uid' :
			case 'qq' :
				$where_str .= ' and s.' . $condit . '=' . intval ( $txt_val );
				break;
			case 'username' :
			case 'truename' :
				$where_str .= " and s." . $condit . " like '%{$txt_val}%' ";
				break;
			case 'email' :
				$where_str .= " and s.email = '{$txt_val}'";
				break;
			case 'mobile' :
				$where_str .= " and (s.mobile = '{$txt_val}' or s.phone = '{$txt_val}' )";
				break;
		}
	}
	intval ( $slt_status ) and $where_str .= ' and s.status = ' . intval ( $slt_status );
    intval($vip_status) and $where_str .= ' and s.isvip = ' . intval ( $vip_status );
	$ord [0] && $ord [1] and $order= "s.{$ord['0']} {$ord['1']}" or $order= "s.uid desc";
	$res = $table_class->get_multi_grid ($field,$table,$join,$on,$where_str,$order,$page, $page_size,$url_str);
	
	$userlist_arr = $res ['data'];
	$pages = $res ['pages'];
}

require $template_obj->template ( 'control/agent/tpl/admin_user_list' );
		
 
