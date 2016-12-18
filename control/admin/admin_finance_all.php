<?php
/**
 * 财务流水记录
 * @copyright keke-tech
 * @author Chen
 * @version v 20
 * 2011-09-03 15:18:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 4 );

$finace_obj = new Keke_witkey_finance_class (); //实例化财务清单表对象
$page_obj = $kekezu->_page_obj; //实例化分页对象


//分页
$w [page_size] and $page_size = intval ( $w [page_size] ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[fina_id]=$w[fina_id]&w[username]=$w[username]&w[fina_type]=$w[fina_type]&w[page_size]=$page_size&w[ord]=$w[ord]&page=$page&w[fina_action]=$w[fina_action]";
if (isset ( $ac ) && $fina_id) { //处理财务清单申请
	switch ($ac) {
		case "del" : //删除
			/* $finace_obj->setWhere ( 'fina_id=' . $fina_id );
			$res = $finace_obj->del_keke_witkey_finance ();
			kekezu::admin_system_log ( kekezu::lang(delete_financial_records) . "_$fina_id" );
			$res and kekezu::admin_show_msg ( $_lang['list_finance_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' ); */
			break;
		case "remark": //编辑备注
			if($is_submit){
				$finace_obj->setWhere("fina_id = $fina_id");
				$finace_obj->setRemark($remark);
				$res = $finace_obj->edit_keke_witkey_finance();
				$res and kekezu::echojson ( $_lang['edit_success'], "1" ) or kekezu::echojson ( $_lang['edit_fail'], "0" );
				die();
			}
						
			$finace_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_finance where fina_id ='$fina_id'");			
			require $template_obj->template ( 'control/admin/tpl/admin_finance_all_remark' );
			die();
		break;
	}
} elseif (isset ( $ckb )) { //批量删除
	/* $ckb_string = implode ( ',', $ckb );
	$finace_obj->setWhere ( 'fina_id in (' . $ckb_string . ')' );
	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			$res = $finace_obj->del_keke_witkey_finance ();
			kekezu::admin_system_log ( $_lang['mulit_delete_financial_records'] . "_$ids" );
			break;
	}
	$res and kekezu::admin_show_msg ( $_lang['mulit_operate_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' ); */

} else {
	$where = ' 1 = 1 '; //默认查询条件
	$w ['fina_type'] and $where .= " and fina_type = '$w[fina_type]' ";
	$w ['fina_action'] and $where .= " and fina_action ='$w[fina_action]' ";
	$w ['fina_id'] and $where .= " and fina_id = '$w[fina_id]' ";
	$w ['username'] and $where .= " and username like '%$w[username]%' ";

	is_array($w['ord']) and $where .= ' order by '.$w['ord'][0].' '.$w['ord'][1] or $where .= "order by fina_id desc ";
	
	//查询统计
	$finace_obj->setWhere ( $where );
	$count = $finace_obj->count_keke_witkey_finance ();
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
	//查询结果数组
	$finace_obj->setWhere ( $where . $pages [where] );
	$finace_arr = $finace_obj->query_keke_witkey_finance ();

}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );