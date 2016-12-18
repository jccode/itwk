<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$video_obj = new Keke_witkey_video_class ();

$page_obj = $kekezu->_page_obj; //分页实例化
$page and $page = intval ( $page ) or $page = 1;

$v_cat_arr = keke_glob_class::get_video_cat();
$where = " 1 = 1 ";
$v_cat = intval($v_cat);
if($v_cat){
	$where.=" and v_cat = ".$v_cat;
}
$sql = sprintf("select * from %switkey_video where $where",TABLEPRE,TABLEPRE);
$sql .= " order by v_id desc";
$count = db_factory::get_count(sprintf("select count(v_id) from %switkey_video where $where",TABLEPRE));
//$video_obj->setWhere($where);
//$count = $video_obj->count_keke_witkey_video ();
$url = $_K['siteurl'].'/index.php?do=special&view=video_list';

$pages = $page_obj->getPages ( $count, 9, $page, $url . '&v_cat='.$v_cat );

$video_list = db_factory::query($sql.$pages['where']);

//$video_list = $video_obj->query_keke_witkey_video ();


//SEO
$seo_title = $v_cat_arr[$v_cat] ? $v_cat_arr[$v_cat].' ' : '';
$page_title = $seo_title.'威客视频 威客视频收藏、视频素材、宣传视频_IT帮手网';
$page_keyword = '威客视频，视频素材，宣传视频，威客视频收藏';
$page_description ='IT帮手网服务视频频道，最新威客视频收藏，有趣的威客视频、宣传视频，打造IT帮手网威客视频中心、视频素材中心，服务视频频道让雇主与威客更直观的了解IT帮手网，轻松玩转IT帮手网。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );