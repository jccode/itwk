<?php
/**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(16);
$art_obj = keke_table_class::get_instance ( "witkey_article" );

$types = array ('help', 'art', 'single','case');
(! empty ( $type ) && in_array ( $type, $types )) or $type = 'art';

switch ($type) {
	case 'art' :
		//kekezu::admin_check_role ( 31 );
		$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "art_index like '%{1}%'", " listorder asc", '', '', 'art_cat_id', null );

		break;
		;
	case 'help' :
		//kekezu::admin_check_role (43);
		$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "art_index like '%{100}%'", "listorder asc", '', '', 'art_cat_id', null );
		break;
		;
	case 'single' :
		//kekezu::admin_check_role (54);
		$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "art_index like '%{200}%'", " listorder asc", '', '', 'art_cat_id', null );
		break;
		;
	case 'case' :
		//kekezu::admin_check_role (44);
		$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "art_index like '%{500}%'", " listorder asc", '', '', 'art_cat_id', null );
		break;
		;
}
(isset ( $art_id ) and intval ( $art_id ) > 0) and $art_info = $art_obj->get_table_info ( 'art_id', $art_id );
empty ( $art_info ) or extract ( $art_info );
/**
 * 处理页面表单的提交
 */
if ($sbt_edit) { 

	 
	//文章发布时间
	$fields ['pub_time'] = time ();
	//文章推荐
	isset($fields['is_recommend']) or $fields['is_recommend']=0;
	isset($fields['listorder']) or $fields['listorder']=999;
	$fields['art_type'] = $type;
	$fields['indus_id'] = intval($slt_indus_id);
	$fields['username'] = $admin_info['username'];
	$fields['uid'] = $admin_info['uid'];
	//跳转地址
	$url = "index.php?do=$do&view=list&type=$type";
	//$fields=kekezu::escape($fields);
	

	if($_FILES['art_pic']['name']){
		$fields[art_pic] = keke_file_class::upload_file ( "art_pic" );
	}
	
	$res = $art_obj->save ( $fields, $pk );
	
	$log_ac = array('edit'=>$_lang['edit_art'],'add'=>$_lang['add_art']);
	if($pk['art_id']){
		kekezu::admin_system_log($log_ac['edit'].":".$fields['art_title']) ;
	}else{
		kekezu::admin_system_log($log_ac['add'].":".$fields['art_title']) ;
	} 
	if($res){
		kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
	}else{
	
		kekezu::admin_show_msg($_lang['operate_fail'],$url,3,'','warning');
	}
}
 

//递归分类列表
$cat_arr = array ();
kekezu::get_tree ( $art_cat_arr, $cat_arr, 'option', $art_id, 'art_cat_id', 'art_cat_pid', 'cat_name' );


require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}