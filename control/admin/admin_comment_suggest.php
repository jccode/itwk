<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (152);

$comment_obj = new Keke_witkey_comment_class();  //实例化留言表对象（意见和建议）
$page_obj = $kekezu->_page_obj;  //分页实例化

//分页
$w [page_size] and $page_size = intval($w [page_size]) or $page_size = 10;
$page and $page = intval($page) or $page = 1;
$url = "index.php?do=$do&view=$view&w[comment_id]=$w[comment_id]&w[obj_id]=$w[obj_id]&w[username]=$w[username]&w[content]=$w[content]&w[page_size]=$w[page_size]&page=$page";

if (isset ( $ac ) && $comment_id) { //删除单条留言

	switch($ac){
		case 'del': //删除
			 //一级评论
			$comment_obj->setWhere ( 'comment_id= ' . $comment_id );
			$res = $comment_obj->del_keke_witkey_comment ();
			
			 //子级评论
			$comment_obj->setWhere ( 'p_id= ' . $comment_id );
			$comment_obj->del_keke_witkey_comment ();

			kekezu::admin_system_log ( $_lang['delete_comment_log'] . "_" . $comment_id );
			$res and kekezu::admin_show_msg ( $_lang['list_comment_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' );
		break;
		case 'tpl_reply': //回复界面	
			 //一级评论
			$comment_obj->setWhere ( 'comment_id =' . intval($comment_id));	
			$comment_parent = $comment_obj->query_keke_witkey_comment ();
			
			 //子级评论
			$comment_obj->setWhere ( 'p_id =' . intval($comment_id) . ' order by comment_id desc');	
			$comment_child = $comment_obj->query_keke_witkey_comment (); 

			require $template_obj->template ( 'control/admin/tpl/admin_comment_reply');
			exit();
		break;
		case 'reply': //回复提交
			if($is_submit == 1){
				$comment_id and $where = 'comment_id =' . intval($comment_id) or kekezu::admin_show_msg ( $_lang['reply_comment_not_empty'] , "index.php?do=$do&view=$view&ac=reply_close&comment_id=$comment_id", 3,'','success' );
				$comment_obj->setWhere ( $where ); 
				$comment_parent_arr = $comment_obj->query_keke_witkey_comment (); 
				if(empty($comment_parent_arr)){
					kekezu::admin_show_msg ( $_lang['not_comment_or_delete'], "index.php?do=$do&view=$view&ac=reply_close&comment_id=$comment_id",3,'','warning' );
				}		

				 //添加			
				$content and $content or kekezu::admin_show_msg ( $_lang['reply_comment_not_empty'], $url,3,'','warning' );	
				$comment_obj->setContent($content);
				$comment_obj->setObj_id($comment_parent_arr[0]['obj_id']);
				$comment_obj->setOrigin_id($comment_parent_arr[0]['origin_id']);
				$comment_obj->setObj_type($comment_parent_arr[0]['obj_type']);
				$comment_obj->setP_id($comment_parent_arr[0]['comment_id']);
				$comment_obj->setUid($admin_info['uid']);
				$comment_obj->setUsername($admin_info['username']);
				$comment_obj->setOn_time(time());				
				$res = $comment_obj->create_keke_witkey_comment(); 
				//同时发送站内信
				$msg_obj = new Keke_witkey_msg_class();
				$msg_obj->setUid($admin_info['uid']);
				$msg_obj->setUsername($admin_info['username']);
				$msg_obj->setTo_uid($comment_parent_arr[0]['uid']);
				$msg_obj->setTo_username($comment_parent_arr[0]['username']);
				$msg_obj->setTitle('意见和建议回复');
				//组合站内信内容
				$msg_content = "您提到的建议：".$comment_parent_arr[0]['content'].'<br />';
				$msg_content = $msg_content."一品回复：".$content;
				$msg_obj->setContent($msg_content);
				$msg_obj->create_keke_witkey_msg();
							
				$res and kekezu::admin_show_msg ( $_lang['huida_success'], "index.php?do=$do&view=$view&ac=reply_close&comment_id=$comment_id",3,'','success' ) or kekezu::admin_show_msg ( $_lang['huida_error'], "index.php?do=$do&view=$view&ac=reply_close",3,'','warning' );
			}
		break;
		case 'reply_close': //关闭回复
			require $template_obj->template ( 'control/admin/tpl/admin_comment_reply');
			exit();
		break;
		case 'del_child': //删除子级评论
			$comment_obj->setWhere ( 'comment_id= ' . $comment_id );
			$res = $comment_obj->del_keke_witkey_comment ();
			kekezu::admin_system_log ( $_lang['delete_comment_log'] . "_" . $comment_id );
			$res and kekezu::admin_show_msg ( $_lang['list_comment_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' );
			exit();
		break;
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
	$where = " obj_type = 'suggest' and p_id = 0 ";   //默认查询条件
	$w ['comment_id'] and $where .= " and comment_id = '$w[comment_id]' ";
	$w ['obj_id'] and $where .= " and obj_id ='$w[obj_id]' ";
	$w ['username'] and $where .= " and username = '$w[username]' ";
	$w ['content'] and $where .= " and content like '%$w[content]%' ";
	is_array($w['ord']) and $where .= ' order by '.$w['ord'][0].' '.$w['ord'][1] or $where .= "order by comment_id desc ";
	
	//查询统计
	$comment_obj->setWhere ( $where ); 
	$count = $comment_obj->count_keke_witkey_comment ();
	//$page_obj->setAjax(1);
	//$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	//查询结果数组
	$comment_obj->setWhere ( $where . $pages [where] );
	$comment_arr = $comment_obj->query_keke_witkey_comment ();
}

require $template_obj->template ( 'control/admin/tpl/admin_comment_' . $view );