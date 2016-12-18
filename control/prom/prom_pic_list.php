<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];  


$prom_style_obj = new Keke_witkey_prom_style_class();  

$page_obj = $kekezu->_page_obj;  //分页实例化
$page and $page = intval($page) or $page = 1;
$prom_style_obj->setWhere ( " s_type = 'image' and s_status = 1" ); 
$count = $prom_style_obj->count_keke_witkey_prom_style();
$url = $_K['siteurl'].'/index.php?do=prom&view=pic_list';
$pages = $page_obj->getPages ( $count, 20, $page, $url );

$prom_style_obj->setWhere ( " s_type = 'image' and s_status = 1 order by s_id desc".$pages [where] ); 
$prom_info_list = $prom_style_obj->query_keke_witkey_prom_style();
$page = $pages['page'];

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );