<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01 11:35:13
 * @desc 修改于2012-10-10，修改人：xxy
 */
defined ( "ADMIN_KEKE" ) or exit ( "Access Denied" ); //控制权限
kekezu::admin_check_role(70);
$realname_obj = new keke_table_class('witkey_auth_realname'); //实例化实名认证表
$url = "index.php?do=" . $do . "&view=" . $view . "&auth_code=" . $auth_code . "&w[page_size]=" . $w [page_size] . "&w[realname_a_id]=" . $w [realname_a_id] . "&w[username]=" . $w [username] . "&w[auth_status]=" . $w [auth_status]; //跳转地址
if($realname_a_id&&$_SESSION['brandType']){
	$auth=$admin_obj->agent_auth("witkey_auth_realname", ' 	realname_a_id='.$realname_a_id,'id_card');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url, 3, '', 'error' );
		die();
	}
}
if (isset ( $ac )) {
	switch ($ac) {
		case 'notpass_view': //任务审核不通过窗口
			$auth_realname_error_arr = keke_glob_class::get_auth_realname_error_mes();
			require $kekezu->_tpl_obj->template ( "auth/" . $auth_dir . "/control/admin/tpl/auth_nopass_box" );
			die();
			break;
		case "pass" : //单条通过认证操作	
			kekezu::admin_system_log($obj.$_lang['pass_realname_auth']);
			$auth_obj->review_auth ( $realname_a_id, 'pass' );			
		break;
		case "not_pass" : //单条不通过认证操作
			$auth_obj->mes_title = $mes_title; 
			$auth_obj->mes_content = $mes_content;
			
			kekezu::admin_system_log($obj.$_lang['nopass_realname_auth']);
			$auth_obj->review_auth ( $realname_a_id, 'not_pass' );
		break;
		case 'del' : //单条删除认证申请
			kekezu::admin_system_log($obj.$_lang['delete_realname_auth']);
			$auth_obj->del_auth ( $realname_a_id );
			//keke_auth_base_class::space_auth_status_update($obj, 'username', 'realname', ' ');
		break;
	}
} elseif (isset ( $sbt_action )) {
	$keyids = $ckb;

	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			//print_r($auth_obj);exit;
			kekezu::admin_system_log($_lang['mulit_delete_realname_auth']);
			$auth_obj->del_auth ( $keyids );
		break;
		case $_lang['mulit_pass'] : //批量审核
			kekezu::admin_system_log($_lang['mulit_pass_realname_auth']);
			$auth_obj->review_auth ( $keyids, 'pass' );
		break;
		case $_lang['mulit_nopass'] : //批量不审核
			kekezu::admin_system_log($_lang['mulit_nopass_realname']);
			$auth_obj->review_auth ( $keyids, 'not_pass' );
		break;
	}
} else //列表
{
	$field="realname.*,brand.brand";
	$table=array('realname'=>'witkey_auth_realname','brand'=>'witkey_brand');
	$join=array("left join");
	$on=array('realname.uid=brand.uid');
	isset($_SESSION['brandType'])?$where="realname.id_card like 'a%' and brand.brand in (".$_SESSION['brandType'].")":$where = " 1 = 1 "; //默认查询条件
	($w ['auth_status'] === "0" and $where .= " and realname.auth_status = 0 ") or ($w ['auth_status'] and $where .= " and realname.auth_status = '$w[auth_status]' "); //搜索认证状态
	intval ( $w ['realname_a_id'] ) and $where .= " and realname.realname_a_id = " . intval ( $w ['realname_a_id'] ) . ""; //搜索认证编号
	$w ['username'] and $where .= " and realname.username like '%" . $w ['username'] . "%' "; //搜索认证标题
	$order="realname.realname_a_id desc"; //order by auth_status asc 
	intval ( $w ['page_size'] ) and $page_size = intval ( $w ['page_size'] ) or $page_size = 10; //每页显示多少条，默认10
	intval ( $page ) or $page = 1 and $page = intval ( $page );
	$res = $realname_obj->get_multi_grid ($field,$table,$join,$on,$where,$order,$page, $page_size,$url);
	$realname_arr = $res ['data'];
	$pages = $res ['pages'];
	require $kekezu->_tpl_obj->template ( "auth/" . $auth_dir . "/control/admin/tpl/auth_list" );
}