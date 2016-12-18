<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (153);

$comment_obj = new Keke_witkey_comment_class();  //实例化留言表对象
$page_obj = $kekezu->_page_obj;  //分页实例化

//分页
$w [page_size] and $page_size = intval($w [page_size]) or $page_size = 10;
$page and $page = intval($page) or $page = 1;
$url = "index.php?do=$do&view=$view&w[comment_id]=$w[comment_id]&w[task_id]=$w[task_id]&w[username]=$w[username]&w[content]=$w[content]&w[page_size]=$w[page_size]&page=$page";

if (isset ( $ac ) && $comment_id) { //删除单条留言

	if($ac == 'del'){
		 //一级评论
		$comment_obj->setWhere ( 'comment_id= ' . $comment_id );
		$res = $comment_obj->del_keke_witkey_comment ();
		
		 //子级评论
		$comment_obj->setWhere ( 'p_id= ' . $comment_id );
		$comment_obj->del_keke_witkey_comment ();

		kekezu::admin_system_log ( $_lang['delete_comment_log'] . "_" . $comment_id );
		$res and kekezu::admin_show_msg ( $_lang['list_comment_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' );
	}
	
} elseif (isset ( $ckb )) { //批量删除
	
	$ckb_string = implode ( ',', $ckb );
	$comment_obj->setWhere ( 'comment_id in (' . $ckb_string . ')' );	
	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			$res = $comment_obj->del_keke_witkey_comment ();
			kekezu::admin_system_log ( $_lang['mulit_delete_comment_log'] . "_" .$ckb_string);
		break;
	}
	$res and kekezu::admin_show_msg ( $_lang['mulit_operate_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['mulit_operate_delete_fail'], $url,3,'','warning' );

} else {	

	 //条件
	$where = " where c.obj_type = 'work' and c.p_id = 0 ";   //默认查询条件
	$w ['comment_id'] and $where .= " and c.comment_id = '$w[comment_id]' ";
	$w ['task_id'] and $where .= " and w.task_id = '$w[task_id]' ";
	$w ['username'] and $where .= " and c.username = '$w[username]' ";
	$w ['content'] and $where .= " and c.content like '%$w[content]%' ";
	is_array($w['ord']) and $where .= ' order by c.'.$w['ord'][0].' '.$w['ord'][1] or $where .= "order by c.comment_id desc ";
	
	 //行数
	$sql_count = "select count(*) from ".TABLEPRE."witkey_comment as c left join ".TABLEPRE."witkey_task_work as w on c.obj_id = w.task_id ";
	$sql_count .= $where; 
	$count = db_factory::get_count($sql_count);
	
	 //查询统计
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	//$kekezu->_page_obj->setAjax(1);
	//$kekezu->_page_obj->setAjaxDom("ajax_dom");
	
	$sql = "select c.*,w.*,c.username as username from ".TABLEPRE."witkey_comment as c left join ".TABLEPRE."witkey_task_work as w on c.obj_id = w.task_id ";
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	$sql .= $where.$pages['where'];
	$comment_arr = db_factory::query($sql);

	
	/*
	$where = " obj_type = 'work' and p_id = 0 ";   //默认查询条件
	$w ['comment_id'] and $where .= " and comment_id = '$w[comment_id]' ";
	$w ['obj_id'] and $where .= " and obj_id ='$w[obj_id]' ";
	$w ['username'] and $where .= " and username = '$w[username]' ";
	$w ['content'] and $where .= " and content like '%$w[content]%' ";
	is_array($w['ord']) and $where .= ' order by '.$w['ord'][0].' '.$w['ord'][1] or $where .= "order by comment_id desc ";
	
	//查询统计
	$comment_obj->setWhere ( $where ); 
	$count = $comment_obj->count_keke_witkey_comment ();
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	//查询结果数组
	$comment_obj->setWhere ( $where . $pages [where] );
	$comment_arr = $comment_obj->query_keke_witkey_comment ();
	*/
}

require $template_obj->template ( 'control/admin/tpl/admin_comment_' . $view );