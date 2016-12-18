<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-21 16:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title = $_lang['mobi_version'].'- '.$_K['html_title'];

//
$task_list = db_factory::query("select * from ".TABLEPRE."witkey_task where task_status=4 and lottery_config is not null");

//行业
$indus_arr = kekezu::get_industry();

//友情链接
$link_tag = keke_core_class::link_make_tag( array(10=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
