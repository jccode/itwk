<?php
/**
 * 站内信息管理
 * @author wrh
 * @version v 1.3
 * 2012-06-13 11:53:12
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (160); //权限
$msg_obj = new Keke_witkey_msg_class ();

//分页 
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[title]=$w[title]&w[username]=$w[username]&w[to_username]=$w[to_username]";
if (isset ( $ac )) {
	if ($msg_id && $ac = 'del') {
		$msg_obj->setWhere ( ' msg_id = ' . $msg_id . '' );
		$res = $msg_obj->del_keke_witkey_msg ();
		kekezu::admin_system_log ( $_lang ['delete_msg_material'] . "msg_id" );
		kekezu::admin_show_msg ( $res ? $_lang ['delete_msg_material_success'] : $_lang ['operate_fail_please_choose_again'], $url, 3, '', $res ? 'success' : 'warning' );
	} else {
		kekezu::admin_show_msg ( $_lang ['delete_fail_please_choose_operate'], $url, 3, '', 'warning' );
	}
}
if (isset ( $sbt_action )) { //量删除批
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	if (count ( $ckb_string )) {
		$msg_obj->setWhere ( ' msg_id in (' . $ckb_string . ') ' );
		$res = $msg_obj->del_keke_witkey_msg();
		kekezu::admin_system_log ($_lang['delete_msg_material']."$ids" );
		kekezu::admin_show_msg ( $res ? $_lang['mulit_operate_success'] : $_lang['mulit_operate_fail_please_again'], $url,3,'', $res?'success':'warning');
	} else
		kekezu::admin_show_msg ( $_lang['mulit_delete_fail_please_choose'], $url,3,'','warning' );
}
//查询
$where = '1=1';
//条件
$w ['msg_id'] and $where .= " and msg_id = '{$w['msg_id']}'";
$w ['title'] and $where .= " and title like '%{$w['title']}%'";
$w ['username'] and $where .= " and username ='{$w['username']}'";
$w ['to_username'] and $where .= " and to_username ='{$w['to_username']}'";
$w ['on_time'] and $where .= " and on_time ='{$w['on_time']}'";

$w ['ord'] and $where .= " order by {$w['ord']} " or $where .= " order by msg_id desc "; //排序

$msg_obj->setWhere ( $where );
$count = $msg_obj->count_keke_witkey_msg();
$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
$msg_obj->setWhere ( $where . $pages ['where'] );
$msg_arr = $msg_obj->query_keke_witkey_msg();

require $template_obj->template('control/admin/tpl/admin_'.$do.'_'.$view);
?>