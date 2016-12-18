<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = '帮助中心 帮助和支持用户在平台上遇到的问题进行处理的中心_IT帮手网';
$page_keyword = '帮助中心,帮助和支持用户,帮助和支持用户中心';
$page_description = 'IT帮手网新手帮助中心，无论是雇主发布任务有问题，威客赚取赏金遇到问题，还是其他问题，均可在帮助和支持用户中心寻求帮助和答案。帮助中心，帮助和支持用户在平台上遇到的问题进行处理的中心。';


$art_rec = kekezu::get_table_data("art_id,art_cat_id,art_title","witkey_article","art_type='help' and is_show=1 and is_recommend=1","art_id desc","","0,6",art_id,3600);
$art_new = kekezu::get_table_data("art_id,art_cat_id,art_title","witkey_article","art_type='help' and is_show=1","art_id desc","","0,9",art_id,3600);

//友情链接
$link_tag = keke_core_class::link_make_tag( array(4=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

/**
*平台公告
 */
$bulletin = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 媒体报道
 */
$media = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=589 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 中标公告
 */
$bid = db_factory::query("select a.task_id,a.uid,a.username,a.work_status,a.op_time,b.model_id from ".TABLEPRE
				."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id=b.task_id "
				." where a.work_status between 1 and 12 order by  a.op_time desc limit 0,4",1,86400 );





require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );