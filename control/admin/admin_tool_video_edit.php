<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (154);

$video_cat = keke_glob_class::get_video_cat();
$table_obj = keke_table_class::get_instance ( "witkey_video" );

if(intval($v_id)){
	$video_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_video where v_id =".intval($v_id) );
	strpos($video_info['v_path'],"data/")!==FALSE and $mode = 2 or $mode = 1;
}


$url ="index.php?do={$do}&view=video" ;

//编辑、添加案例
if (isset ( $sbt_edit )) { //编辑

	//文章推荐
	isset($fields['is_recommend']) or $fields['is_recommend']=0;

	$fields=kekezu::escape($fields);
	
	if($showMode==1){
		$fields['v_path'] = $fields['v_path'];
	}elseif($showMode==2){
		if($_FILES["fields[v_file]"]){
			$fields['v_path'] = keke_file_class::upload_file("fields[v_file]");
		}
	}
	/* if ($_FILES ['fields']['name']) {
		$fields ['img'] = keke_file_class::upload_file ("img");
	}*/ 
	
	$res = $table_obj->save ( $fields, $pk );

	if ($pk['v_id']) {//编辑
		kekezu::admin_system_log ( "编辑视频".':' . $pk['v_id'] ); //日志记录
		$res and kekezu::admin_show_msg ( "视频编辑成功", "index.php?do={$do}&view=video",3,'','success' ) or kekezu::admin_show_msg ( $_lang['modify_case_fail'], 'index.php?do=case&view=lise',3,'','warning' );
	}else{//添加
		kekezu::admin_system_log ( "添加视频".':' .$res ); //日志记录
		$res and kekezu::admin_show_msg ("视频添加成功","index.php?do={$do}&view=video",3,'','success' ) or kekezu::admin_show_msg ( $_lang['add_case_fail'],'index.php?do=case&view=add',3,'','warning' );
	}

}

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}

require $template_obj->template ( 'control/admin/tpl/admin_tool_' . $view );