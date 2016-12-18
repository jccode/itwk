<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$special_obj = new Keke_witkey_special_class ();
$cat_arr =keke_glob_class::get_special_cat(); 
$cat_seoarr = keke_glob_class::get_special_seocatname();
$page and $page = intval ( $page ) or $page = 1;


//$url = $_K['siteurl'].'/zt/index.php?do=special&view=special_list';
$url = $_K['siteurl'].'/index.php?do=special&view=special_list';
 
$where = " 1 = 1 ";
if(intval($cat_id)){
	$where .=" and cat_id =$cat_id";
	$url = $_K['siteurl'].'/'.$cat_seoarr[$cat_id].'/index.php?do=special&view=special_list&cat_id='.$cat_id;
}

$special_obj->setWhere($where);
$count = $special_obj->count_keke_witkey_special ();
$pages = $page_obj->getPages ( $count, 15, $page, $url );
$sql = sprintf("select * from %switkey_special where $where",TABLEPRE);
$sql .= " order by sp_id desc".$pages['where']; 
$special_list = db_factory::query($sql);

 //友情链接
if($page < 2){
	$cat_id and $link_where = " and obj_id = '$cat_id'" or $link_where = " and obj_id = ''";
	$link_tag = keke_core_class::link_make_tag( array(8=>1) );
	$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) $link_where order by listorder limit 0,30", TABLEPRE ), 1, 0 );
}

//SEO
$seo_title = $cat_arr[$cat_id] ? $cat_arr[$cat_id].' ' : '';

$page_title = $seo_title.'活动专题 企业专题、专题策划、专题组织生活会等创意服务_IT帮手网';
$page_keyword = '活动专题，企业专题，专题策划，题组织生活会，创意专题';
$page_description ='IT帮手网精彩专题，企业专题展示企业文化及企业活动专题，专题策划可以更好的起到活动专题的宣传效果，提供专题组织生活会等创意服务，打造IT帮手网精彩的创意专题频道。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );