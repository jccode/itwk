<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];  

if($s_id ==0&&$s_id ==''){
	kekezu::show_msg('页面不存在或已被删除',$_K['siteurl'].'/index.php?do=prom','3','','warning');
}

 //推荐热图推广
$prom_style_obj = new Keke_witkey_prom_style_class();  
$prom_style_obj->setWhere ( ' s_id = '.$s_id.' AND s_type = "image" AND s_status = 1' ); 
$prom_info_list = $prom_style_obj->query_keke_witkey_prom_style();
$prom_info_list and $prom_info_list = $prom_info_list[0] or kekezu::show_msg('页面不存在或已被删除',$_K['siteurl'].'/index.php?do=prom','3','','warning');

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
