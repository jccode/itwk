<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-21 16:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title = $_lang['mobi_version'].'- '.$_K['html_title'];

$page_obj = new keke_page_class();
$count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_task where task_status!=4 and lottery_config is not null and lottery_config != ''");
$page or $page = 1;
$perpage or $perpage = 12;
$pages = $page_obj->getPages($count, $perpage,$page, $_K['siteurl']."/index.php?do=lottery&view=list");



//列表
$task_list = db_factory::query("select * from ".TABLEPRE."witkey_task where task_status!=4 and lottery_config is not null and lottery_config != '' ".$pages['where']);

//行业
$indus_arr = kekezu::get_industry();

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
