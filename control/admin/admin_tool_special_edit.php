<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (155);
$url = "index.php?do=$do&view=special";

$special_obj = new Keke_witkey_special_class(); 

if (isset ( $ac ) && $is_submit == 1) { 
	switch($ac){
		case 'add': //添加
			$special_obj->setCat_id($fds['cat_id']);
			$special_obj->setTitle($fds['title']);
			$special_obj->setUrl($fds['url']);
			$special_obj->setListorder($fds['listorder']);
			$special_obj->setSeo_title($fds['seo_title']);
			$special_obj->setSeo_keywords($fds['seo_keywords']);
			$special_obj->setSeo_catname($fds['seo_catname']);
			$special_obj->setSeo_desc($fds['seo_desc']);
			$special_obj->setDateline(time());
			$special_obj->setImg($fds['img']);		

			$res = $special_obj->create_keke_witkey_special();
			$res and kekezu::admin_show_msg ( $_lang['special_add_success'], $url,3,'',' success' ) or kekezu::admin_show_msg ( $_lang['special_add_error'], $url,3,'','warning' );
		break;
		case 'edit': //修改
			if(!empty($sp_id)){
				$special_obj->setSp_id($sp_id);
			}else{
				kekezu::admin_show_msg ( $_lang['special_empty'], $url,3,'',' warning' );
			}
			
			$special_obj->setCat_id($fds['cat_id']);
			$special_obj->setTitle($fds['title']);
			$special_obj->setUrl($fds['url']);
			$special_obj->setListorder($fds['listorder']);
			$special_obj->setSeo_title($fds['seo_title']);
			$special_obj->setSeo_keywords($fds['seo_keywords']);
			$special_obj->setSeo_catname($fds['seo_catname']);
			$special_obj->setSeo_desc($fds['seo_desc']);
			$special_obj->setDateline(time());
			$special_obj->setImg($fds['img']);	
			
			$res = $special_obj->edit_keke_witkey_special();
			$res and kekezu::admin_show_msg ( $_lang['special_edit_success'], $url,3,'',' success' ) or kekezu::admin_show_msg ( $_lang['special_edit_error'], $url,3,'','warning' );
			
		break;
	}
	
} else {//界面	
	if (isset( $sp_id )){
		$special_obj->setWhere ( " sp_id = " . $sp_id );
		$special_arr = $special_obj->query_keke_witkey_special ();	
		$special_arr = $special_arr[0];
	}
	
	$special_cat_list = keke_glob_class::get_special_cat();
	require $template_obj->template ( 'control/admin/tpl/admin_tool_' . $view);
}	

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}