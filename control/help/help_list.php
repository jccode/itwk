<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title="问题解答".'- '.$_K['html_title'];

$url=$_K['siteurl']."/index.php?do=help&view=list&page=$page&page_size=$page_size&art_cat_id=$art_cat_id&keyword=$keyword";
$table_obj = new keke_table_class('witkey_article');
$cat_p_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_name", "witkey_article_category", "cat_type='help' and art_cat_pid = 100", " listorder asc", "", "", "art_cat_id", 3600 );
$cat_c_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_name", "witkey_article_category", "cat_type='help'", " listorder asc", "", "", "art_cat_id", 3600 );

//查询文章列表
if(isset($art_cat_id) || isset($keyword)){
	$where = "art_type='help' and is_delineas=0";
	
	$page_size and $page_size = intval ( $w ['page_size'] ) or $page_size = 10;
	$page and $page = intval ( $page ) or $page = '1';
	
	$art_cat_id and $where .= " and art_cat_id=".$art_cat_id;
	$keyword and $where .= " and art_title like '%".$keyword."%'";
	$where .= " order by art_id desc"; 
	$r = $table_obj->get_grid($where,$url,$page,$page_size,null);
	$art_count = $table_obj->_count;
	$art_arr = $r [data];
	$pages = $r [pages];
	//获取大类信息
	$pcat_info = array($cat_p_arr[$cat_c_arr[$art_cat_id][art_cat_pid]][art_cat_id],$cat_c_arr[$cat_c_arr[$art_cat_id][art_cat_pid]][cat_name]);
	//读取查询分类信息
	if($art_cat_id){
		$cat_info = db_factory::get_one(sprintf("select art_cat_id,art_cat_pid,cat_name from %switkey_article_category where art_cat_id=%d",TABLEPRE,$art_cat_id));
	}
}
else{
	$art_rec = kekezu::get_table_data("art_id,art_cat_id,art_title","witkey_article","is_delineas=0 and art_type='help' and is_recommend=1","art_id desc","","0,6",art_id,3600);
	$art_new = kekezu::get_table_data("art_id,art_cat_id,art_title","witkey_article","is_delineas=0 and art_type='help'","art_id desc","","0,6",art_id,3600);
}

$page_title = '问题解答 威客知道互动百科帮助用户进行的常见问题解答_IT帮手网';
$page_keyword = '问题解答，威客知道，互动百科，常见问题解答';
$page_description = '会员互动，提供一个提问交流的的互动问答平台，IT帮手网会员在享受服务的过程中有什么疑问或建议，可以与会员或工作人员彼此进行互动问答，更好的享受IT帮手网提供的服务。 ';

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );