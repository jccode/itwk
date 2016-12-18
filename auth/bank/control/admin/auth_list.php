<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01 11:35:13
 * @desc 修改于2012-10-10，修改人：xxy
 */
defined ( "ADMIN_KEKE" ) or exit ( "Access Denied" ); //控制权限
kekezu::admin_check_role(68);
$bank_obj = new keke_table_class('witkey_auth_bank'); //实例化银行认证表
$url = "index.php?do=" . $do . "&view=" . $view . "&auth_code=" . $auth_code . "&w[page_size]=" . $w [page_size] . "&w[bank_a_id]=" . $w [bank_a_id] . "&w[username]=" . $w [username] . "&w[auth_status]=" . $w [auth_status]; //跳转地址
if($bank_a_id&&$_SESSION['brandType']){
	$auth=$admin_obj->agent_auth("witkey_auth_bank", ' 	bank_a_id='.$bank_a_id,'deposit_area');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url, 3, '', 'error' );
		die();
	}
}
if (isset ( $ac )) {

	switch ($ac) {
		case 'notpass_view': //任务审核不通过窗口
			$auth_blank_error_arr = keke_glob_class::get_auth_blank_error_mes();
			require $kekezu->_tpl_obj->template ( "auth/" . $auth_dir . "/control/admin/tpl/auth_nopass_box" );
			die();
			break;
		case "pass" : //单条通过认证操作
			kekezu::admin_system_log($obj.$_lang['bank_auth_pass']);
			$auth_obj->review_auth ( $bank_a_id, 'pass' ); 
			break;
		case "not_pass" : //单条不通过认证操作
			$auth_obj->mes_title = $mes_title; 
			$auth_obj->mes_content = $mes_content;

			kekezu::admin_system_log($obj.$_lang['bank_auth_nopass']);
			$auth_obj->review_auth ( $bank_a_id, 'not_pass' ); 
			break;
		case 'del' : //单条删除认证申请
			kekezu::admin_system_log($obj.$_lang['bank_auth_delete']);
			$auth_obj->del_auth ( $bank_a_id );
			break;
	}
} elseif (isset ( $sbt_action )) {
	$keyids = $ckb;
	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			kekezu::admin_system_log($_lang['mulit_delete_bank_auth']);
			$auth_obj->del_auth ( $keyids );
			break;
			;
		case $_lang['mulit_pass'] : //批量审核
			kekezu::admin_system_log($_lang['mulit_pass_bank_auth']);
			$auth_obj->review_auth ( $keyids, 'pass' );
			break;
			;
		case $_lang['mulit_nopass'] : //批量不审核
			kekezu::admin_system_log($_lang['mulit_nopass_bank_auth']);
			$auth_obj->review_auth ( $keyids, 'not_pass' );
			break;
	}
} else //列表
{
	$field="bank.*,brand.brand";
	$table=array('bank'=>'witkey_auth_bank','brand'=>'witkey_brand');
	$join=array("left join");
	$on=array('bank.uid=brand.uid');
	
	isset($_SESSION['brandType'])?$where="bank.deposit_area like '%台湾%' and brand.app_status=1 and brand.brand in(".$_SESSION['brandType'].")":$where = " 1 = 1 "; //默认查询条件

	($w ['auth_status'] === "0" and $where .= " and bank.auth_status = 0 ") or ($w ['auth_status'] and $where .= " and bank.auth_status = '$w[auth_status]' "); //搜索认证状态
	intval ( $w ['bank_a_id'] ) and $where .= " and bank.bank_a_id = " . intval ( $w ['bank_a_id'] ) . ""; //搜索认证编号
	$w ['username'] and $where .= " and bank.username like '%" . $w ['username'] . "%' "; //搜索认证标题
	$w ['realname'] and $where .= " and bank.uid IN(".get_realname_uid($w ['realname']).")";
	
	$order="bank.bank_a_id desc";
	intval ( $w ['page_size'] ) and $page_size = intval ( $w ['page_size'] ) or $page_size = 10; //每页显示多少条，默认10	
	intval ( $page ) or $page = 1 and $page = intval ( $page );
	
	$res = $bank_obj->get_multi_grid ($field,$table,$join,$on,$where,$order,$page, $page_size,$url);
	$bank_arr = $res ['data'];
	$pages = $res ['pages'];
	require $kekezu->_tpl_obj->template ( "auth/" . $auth_dir . "/control/admin/tpl/auth_list" );
}

function get_bank_realname($uid){
	$member_bank_arr = db_factory::get_one(" select * from " . TABLEPRE . "witkey_member_bank where uid='$uid'");
	
	return $member_bank_arr['real_name'];
}

function get_realname_uid($realname){
	$member_bank_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_member_bank where real_name='$realname'");
	$uids = array();
	foreach($member_bank_arr as $val){
		$uids[] = $val['uid'];
	}
	
	$uids and $where = implode(',',$uids) or $where = 0;
	
	return $where;
}
