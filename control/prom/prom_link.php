<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];
$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];
global  $_K, $user_info;
$user_id=$user_info['uid'];
$linkUrl=$_K [siteurl];
$take_num= intval($user_info['take_num']);//任务发布数目
$accepted_num = intval($user_info['accepted_num']);//接收任务数目
$url="$linkUrl/index.php?do=prom&epi=$user_id";
$where  = " prom_code in('index','register','pu_task','service','employer','player')";
$linkList = db_factory::get_table_data ( '*', 'witkey_prom_rule', $where, '', '', '' );

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
