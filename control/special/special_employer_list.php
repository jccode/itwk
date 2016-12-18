<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//SEO
$page_title = '成功雇主 成功经验分享、成功人的故事、雇主满意100%_IT帮手网';
$page_keyword = '成功雇主，成功经验，成功人的故事';
$page_description ='成功雇主讲述他们在IT帮手网雇佣的成功故事、传授在雇佣过程中的的成功经验：如何成功找到好威客，威客如何让雇主满意100%等。指点其他雇主与威客的成功之道，让更多的雇主与威客们明白在交易中怎样才能成功。';

intval ( $page_size ) or $page_size = '12';
$opp=='remote' and $page_size = 6;
intval ( $page ) or $page = '1';
$url = $_K['siteurl']."/index.php?do=$do&view=$view&page=$page&page_size=$page_size";
$page_obj = $kekezu->_page_obj;

//$sql = sprintf("select a.art_id,a.art_cat_id,a.art_title,a.art_pic,a.user_desc,s.username from %switkey_article as a left outer join %switkey_space as s on a.r_uid=s.uid where a.art_cat_id=593",TABLEPRE,TABLEPRE);
$sql = sprintf("select art_id,art_title,art_cat_id,art_pic,content from %switkey_article where art_cat_id=593",TABLEPRE);
$count = db_factory::get_count(sprintf("select count(art_id) from %switkey_article where art_cat_id=593",TABLEPRE));

$sql .= " order by listorder asc,art_id desc";

$pages = $page_obj->getPages($count, $page_size, $page, $url);
$art_employer = db_factory::query($sql.$pages['where']);

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );