<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//SEO
$page_title = '优秀威客 如何做威客，第一威客★一品★开设威客教育使你走向成功之路_IT帮手网';
$page_keyword = '优秀威客，如何做威客，第一威客，一品，威客教育，成功之路';
$page_description ='IT帮手网优秀威客频道，诚邀优秀威客做客一品，就如何做威客、如何成为优秀威客等问题进行威客教育，以优秀威客通向成功之路的经验开设威客教育使你走向成功之路，向第一威客发起冲击！';

intval ( $page_size ) or $page_size = '12';
$opp=='remote' and $page_size = 6;
intval ( $page ) or $page = '1';
$url = $_K['siteurl']."/index.php?do=$do&view=$view&page=$page&page_size=$page_size";
$page_obj = $kekezu->_page_obj;

//$sql = sprintf("select a.art_id,a.art_title,a.art_cat_id,a.art_pic,a.user_desc,s.username,s.w_level from %switkey_article as a left outer join %switkey_space as s on a.r_uid=s.uid where a.art_cat_id=594",TABLEPRE,TABLEPRE);
$sql = sprintf("select art_id,art_title,art_cat_id,art_pic,content from %switkey_article where art_cat_id=594",TABLEPRE);
$count = db_factory::get_count(sprintf("select count(art_id) from %switkey_article where art_cat_id=594",TABLEPRE));

$sql .= " order by listorder asc,art_id desc";

$pages = $page_obj->getPages($count, $page_size, $page, $url);
$art_witkey = db_factory::query($sql.$pages['where']); 

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );