<?php
/**
 * 系统日志
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-5-24下午03:46:14
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (19);
$group_arr = keke_admin_class::get_user_group ();
//初始化对象
$table_obj = keke_table_class::get_instance ( 'witkey_system_log' );
intval ( $page ) or $page = 1;
intval ( $wh ['page_size'] ) or $wh ['page_size'] = 10;
$url = "index.php?do=$do&view=$view&txt_username=$txt_username&txt_start_time=$txt_start_time&txt_log_id=$txt_log_id&txt_end_time=$txt_end_time&page=$page&w[ord]={$w['ord']}&wh[slt_page_size]={$wh['slt_page_size']}";
//删除
if ($ac == 'del') {
	$res = $table_obj->del ( 'log_id', $log_id );
	kekezu::admin_system_log($_lang['delete_sys_log'] . $log_id ); //日志记录
	$res and kekezu::admin_show_msg ($_lang['delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ($_lang['delete_fail'], $url,3,'','warning' );
} elseif ($ac == 'del_all') {
	$sql = sprintf ( "TRUNCATE TABLE %switkey_system_log", TABLEPRE );
	db_factory::execute ( $sql );
	kekezu::admin_system_log( $_lang['empty_system_log'] );
	kekezu::admin_show_msg ( $_lang['empty_system_log_success'], $url,3,'','success' );
} elseif ($sbt_action) {
	$res = $table_obj->del ( 'log_id', $ckb );
	kekezu::admin_system_log( $_lang['mulit_delete_log'] );
	$res and kekezu::admin_show_msg ($_lang['mulit_operate_success'], $url,3,'','success' ) or kekezu::admin_show_msg ($_lang['mulit_operate_fail'], $url,3,'','warning' );

} else {
	//获取查询条件
	$where = " 1 = 1";
	empty ( $txt_log_id ) or $where .= " and log_id =" . intval ( $txt_log_id );
	empty ( $txt_end_time ) or $where .= " and log_time <" . kekezu::sstrtotime ( $txt_end_time );
	empty ( $txt_start_time ) or $where .= " and log_time >" . kekezu::sstrtotime ( $txt_start_time );
	empty ( $txt_username ) or $where .= " and username like '%$txt_username%'";
	empty ( $txt_content ) or $where .= " and log_content like '%$txt_content%'";
	if(is_array($w['ord'])){
		$where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'];
	}else{
 
			$where .= " order by log_id desc";
	}
	 

	//查询
	$d = $table_obj->get_grid ( $where, $url, $page, $wh ['page_size'],null,1,'ajax_dom' );
	$log_data = $d ['data'];
	$pages = $d ['pages'];

}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );