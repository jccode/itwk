<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$url = $ac_url."&view=$view&page=$page";
$sql = sprintf("select * from %switkey_article_flagship ",TABLEPRE);
$count_sql = "select count(art_id) from ".TABLEPRE."witkey_article_flagship ";
	$where = " where 1= 1 and shop_id=$sid ";
	$art_type and $where.=" and art_type=$art_type ";
	$where .= " order by art_id desc";
$p_s = isset ( $p_s ) ? intval ( $p_s ) : 10;

$count_sql = "select count(art_id) from ".TABLEPRE."witkey_article_flagship ";
$count = db_factory::get_count($count_sql . $where );
$page = isset($page) ? $page : 1;
$pages = $page_obj->getPages ( $count, $p_s, $page, $url );
$art_arr = db_factory::query ( $sql . $where . $pages ['where'] );
//热门公司新闻
$hot_news_arr = db_factory::query("select art_id,art_title from ".TABLEPRE."witkey_article_flagship where shop_id=".$sid." order by views desc limit 0,10");
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );