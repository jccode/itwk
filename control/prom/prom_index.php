<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];
$page_title= '推广员 '.$_K['html_title'];  

//友情链接
$link_tag = keke_core_class::link_make_tag( array(3=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );
 
 //频道公告
$announcement_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_article where art_type='help' and is_delineas=0 and art_cat_id in(613,614,615) order by art_id desc limit 5",1,86400);

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
