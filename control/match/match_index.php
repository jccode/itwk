<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-8-6 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$cash_cove_arr = kekezu::get_cash_cove ();
/**
 * 展示案例
 */
$show_task = db_factory::query(' select b.task_title,b.task_id,b.work_num,b.wiki_num from '
	.TABLEPRE.'witkey_article a left join '.TABLEPRE.'witkey_task b on a.task_id = b.task_id where a.art_cat_id=637 '
	.' and art_type="case"  order by a.is_recommend desc,b.task_cash desc limit 0,10',86400);
/**
 * 大赛专题
 */
$special_list = db_factory::query(' select url,img,title from '.TABLEPRE.'witkey_special  order by listorder desc,sp_id desc limit 0,12',1,86400);
//SEO
if ($indus_p_arr [$indus_id]) {
	$page_title = $indus_p_arr [$indus_id] ['seo_title'] . '_IT帮手网';
	$page_keyword = $indus_p_arr [$indus_id] ['seo_keywords'];
	$page_description = $indus_p_arr [$indus_id] ['seo_keywords'];
} elseif ($indus_c_arr [$indus_id]) {
	$page_title = $indus_c_arr [$indus_id] ['seo_title'];
	$page_keyword = $indus_c_arr [$indus_id] ['seo_keywords'];
	$page_description = $indus_c_arr [$indus_id] ['seo_keywords'];
} else {
	$page_title = '威客任务 一品任务平台汇聚赏金任务 简单任务 隐藏任务 创意任务这里有无尽的任务_IT帮手网';
	$page_keyword = '威客任务，任务平台，赏金任务，简单任务，隐藏任务，创意任务，无尽的任务';
	$page_description = 'IT帮手网创意服务，征集创意服务的理想平台，雇主发布威客任务、创意任务，寻求解决方案的理想地；威客网上兼职工作的优秀平台。IT帮手网，打造威客任务中国第一平台。';
}

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );