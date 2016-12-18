<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$v_cat_arr = keke_glob_class::get_video_cat();
$url=$_K['siteurl']."/index.php?do=special&view=video_list";
if(isset($v_id) && intval($v_id)){
	$sql=sprintf("select * from %switkey_video where v_id='%d'",TABLEPRE,$v_id);
	$vide_info=db_factory::get_one($sql);

	$page_title = $vide_info['v_title'].'_IT帮手网 '; 
	$page_keyword = $vide_info['seo_keywords'];
	$page_description = $vide_info['seo_desc'];
    $where = " and v_id=".$vide_info['v_id'];
	//获取当前精彩专题的上一篇和下一篇文章信息
	$video_up_info = db_factory::get_one(sprintf("select v_id,v_title from %switkey_video  where 1 =1 and v_id<'%d'   order by v_id desc limit 0,1",TABLEPRE,$v_id,$where));
	$video_down_info = db_factory::get_one(sprintf("select v_id,v_title  from %switkey_video  where 1 =1 and v_id>'%d'  order by v_id asc limit 0,1",TABLEPRE,$v_id,$where));
	//读取相关精彩专题
	$about_info = kekezu::get_table_data("*","witkey_video"," v_id <> $v_id",'v_id desc','','0,8');
}else{
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=special&view=video_info",3,"精彩专题参数错误，请返回列表页重试！","warning");
}


require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );