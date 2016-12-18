<?php
/**
 * 友连编辑
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-9-1
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 30 );
//实例化友情链接表对象
$link_obj = new Keke_witkey_link_class ();

//单条友情链接信息
if ($link_id) {
	$link_info = $link_obj->setWhere ( 'link_id=' . $link_id );
	$link_info = $link_obj->query_keke_witkey_link ();
	$link_info = $link_info [0];
	$link_info['location'] = keke_core_class::link_parse_tags($link_info['location']);	
	strpos($link_info['link_pic'],"data/")!==FALSE and $mode = 2 or $mode = 1;
} 

//编辑友情链接
if ($sbt_edit) { //print_r($txt_location);exit;
	if($txt_location[11]){ //全部任务
		$txt_obj_type = 'indus';
		$txt_obj_id = $indus_obj_id;
	}elseif($txt_location[8]){ //精彩专题
		$txt_obj_type = 'special'; 
		$txt_obj_id = $special_obj_id;
	}elseif($txt_location[7]){ //成功案例
		$txt_obj_type = 'case';
		$txt_obj_id = $case_obj_id;
	}
	
	$txt_obj_type or $txt_obj_id = '';
	
	 //链接位置
	//$txt_location and $link_obj->setLocation ( serialize($txt_location) ); 
	if ( $txt_location ){
		$link_obj->setLocation ( keke_core_class::link_make_tag( $txt_location ) ) ;
	}else{
		$link_obj->setLocation (0);
	}

	//友情链接状态
	$link_status = $link_status ? $link_status : 0;
	$link_obj->setLink_status ( $link_status );

	 //链接类型(展示方式)：1为图片、2为文本
	$link_type = $link_type ? $link_type : 1;
	$link_obj->setLink_type ( $link_type ); 
	
	
	$link_obj->setLink_name ( $txt_link_name ); 
	if($showMode==1){ //远程
		$link_pic = $txt_link_pic;
	}elseif($showMode==2 && !empty($_FILES['fle_link_pic']['name'])){ //上传图片
		$link_pic = keke_file_class::upload_file("fle_link_pic");  
	}
	
	$link_obj->setLink_url ( $txt_link_url );
	$link_obj->setListorder ( intval ( $txt_listorder ) );
	$link_obj->setOn_time ( time () );
	if ($hdn_link_id) {
		if (!empty($txt_obj_type)) {
			$link_obj->setObj_type($txt_obj_type);
			$link_obj->setObj_id(intval($txt_obj_id));
		}else{
			$link_obj->setObj_type(' ');
			$link_obj->setObj_id(0);
		}
	
		$link_pic and $link_obj->setLink_pic ( $link_pic );
		$link_obj->setLink_id ( $hdn_link_id );
		$res = $link_obj->edit_keke_witkey_link (); //编辑友情链接
		if ($res) {
			kekezu::admin_system_log ( $_lang['links_edit'] . $hdn_link_id );
			kekezu::admin_show_msg ( $_lang['links_edit_success'], 'index.php?do=' . $do . '&view=link&page='.$page,3,'','success' );
		}
	} else { 	
		if ($txt_obj_type) {
			$link_obj->setObj_type($txt_obj_type);
			$link_obj->setObj_id(intval($txt_obj_id));
		}
		
		if(!$link_pic ){
			$link_pic = 0;
		} 
		$link_obj->setLink_pic ( $link_pic );
		
		$link_obj->setUid($admin_info['uid']);
		$link_obj->setUsername($admin_info['username']);
		$res = $link_obj->create_keke_witkey_link (); //添加友情链接
		if ($res) {
			kekezu::admin_system_log ( $_lang['links_add'] . $res );
			kekezu::admin_show_msg ( $_lang['links_edit_success'], 'index.php?do=' . $do . '&view=link&page='.$page,3,'','success' );
		}
	}
}

/*行业*/
$indus_arr = $kekezu->_indus_arr;
$temp_arr = array ();
$indus_option_arr = $indus_arr;
kekezu::get_tree ( $indus_option_arr, $temp_arr, "option", $link_info['obj_id'] );
$indus_option_arr = $temp_arr;
/*案例分类*/
$cat_arr =keke_glob_class::get_special_cat();  
/*成功案例*/
$cate_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_type,cat_name", "witkey_article_category", "cat_type='case' and art_cat_pid = 500 ", " listorder asc", "", "", "art_cat_id", 3600 );

$link_cat_list = keke_glob_class::get_link_cat(); 
require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );