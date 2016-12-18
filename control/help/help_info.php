<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title="问题解答".'- '.$_K['html_title'];

$cat_p_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_name", "witkey_article_category", "cat_type='help' and art_cat_pid = 100", " listorder asc", "", "", "art_cat_id", 3600 );
$cat_c_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_name", "witkey_article_category", "cat_type='help'", " listorder asc", "", "", "art_cat_id", 3600 );

//读取文章数据
if(isset($art_id)){
	$art_info = db_factory::get_one(sprintf("select * from %switkey_article where art_id=%d",TABLEPRE,$art_id));
	//读取文章分类信息
	if($art_info['art_cat_id']){
		$cat_info = $cat_c_arr[$art_info['art_cat_id']];
		$pcat_info = $cat_c_arr[$cat_info['art_cat_pid']];
	}
}

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );