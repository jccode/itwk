<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-8-6 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$cash_cove_arr = kekezu::get_cash_cove ();
$url = "index.php?do=match&view=case&page_size=$page_size";

$page_title ="创意大赛案例展示" . '-' . $_K ['html_title'];
$sql = sprintf('select a.art_title,a.art_id,a.art_cat_id,b.task_title,a.art_pic,a.content,a.task_id,b.task_cash,b.task_cash_coverage,b.wiki_num,b.work_num '
 	.' from %switkey_article a left join %switkey_task b on a.task_id=b.task_id where a.task_id!=0 '
 	.' and a.art_cat_id=637 order by a.is_recommend desc,a.art_id desc ',TABLEPRE,TABLEPRE);
$page_size = 5;
$count_sql = sprintf("select count(art_id) from %switkey_article a left join %switkey_task b on a.task_id=b.task_id where a.task_id!=0 and a.art_cat_id=637",TABLEPRE,TABLEPRE);
$count = db_factory::get_count ( $count_sql );
$page = isset ( $page ) ? $page : 1;
$pages = $page_obj->getPages ( $count, $page_size, $page, $url);
$art_arr = db_factory::query ( $sql . $pages ['where'] );

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );