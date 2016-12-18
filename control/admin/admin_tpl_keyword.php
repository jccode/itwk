<?php
/**
 * @copyright keke-tech
 * @author 九江
 * @version v 2.0
 * 2011-9-1 
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role (202);

$t_obj = keke_table_class::get_instance ( "witkey_article_keyword" );
$page and $page=intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size=intval ( $slt_page_size ) or $slt_page_size = 10;

$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&keywords=$keywords";
$status_arr = array('1'=>'启用','0'=>'禁用');

if ($ac == 'del') {
	if ($keyword_id) {
		$res = $t_obj->del ( "keyword_id", $keyword_id, $url );
		kekezu::admin_system_log ( '文章关键字删除'.$keyword_id );
		kekezu::admin_show_msg ( '文章关键字删除成功', $url,3,'','success' );
	}
} else {
	$where = ' 1 = 1  ';
	$keywords and $where .= "  and word like '%".$keywords."%'";

	$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size,null);
	$keyword_arr = $d [data];
	$pages = $d [pages];
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );