<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (155);

$special_cat = keke_glob_class::get_special_cat();

$special_obj = new Keke_witkey_special_class();  
$page_obj = $kekezu->_page_obj;  //分页实例化

//分页
$w [page_size] and $page_size = intval($w [page_size]) or $page_size = 10;
$page and $page = intval($page) or $page = 1;
$url = "index.php?do=$do&view=$view&w[sp_id]=".$w['sp_id']."&w[title]=".$w['title']."&w[cat_id]=".$w[cat_id]."&w[page_size]=$page_size&w[ord]=".$w['ord']."&page=$page";
//$url = "index.php?do=$do&view=$view";

if (isset ($ac) && $sp_id){
	if($ac == 'del'){
		$special_obj->setWhere ( 'sp_id= ' . $sp_id );
		$res = $special_obj->del_keke_witkey_special ();

		kekezu::admin_system_log ( $_lang['delete_comment_log'] . "_" . $comment_id );
		$res and kekezu::admin_show_msg ( $_lang['special_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['delete_fail'], $url,3,'','warning' );
	}
	
}elseif (isset ( $ckb )) { //批量删除
	
	$ckb_string = implode ( ',', $ckb );	
	$special_obj->setWhere ( 'sp_id in (' . $ckb_string . ')' );	
	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			$res = $special_obj->del_keke_witkey_special ();
			kekezu::admin_system_log ( $_lang['mulit_delete_comment_log'] . "_" .$ckb_string);
		break;
	}
	
	$res and kekezu::admin_show_msg ( $_lang['mulit_operate_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['mulit_operate_delete_fail'], $url,3,'','warning' );

} else {	

	$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
	$page and $page = intval ( $page ) or $page = '1';
	
	 //条件
	$where = '  1 = 1'; //查询
	$w ['sp_id'] and $where .= " and sp_id = '".$w['sp_id']."' ";
	$w ['title'] and $where .= " and title like '%".$w['title']."%' ";
	$w['cat_id'] and $where .= " and cat_id= '".$w['cat_id']."'";
	is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'];

	 //查询统计
	$special_obj->setWhere ( $where ); 
	$count = $special_obj->count_keke_witkey_special ();
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	 //查询结果数组
	$special_obj->setWhere ( $where . $pages [where] );
	$special_arr = $special_obj->query_keke_witkey_special ();
}

require $template_obj->template ( 'control/admin/tpl/admin_tool_' . $view );