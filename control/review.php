<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-7 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//SEO
$page_title = '一句话点评，无论褒贬，您对网站意见反馈、服务质量评价，我们都细心聆听——IT帮手网';
$page_keyword = '网站点评，网站意见反馈，服务质量评价，点评网站的平台';
$page_description ='IT帮手网一句话点评，不管是鼓励的、批评的……您的网站点评、网站意见反馈、服务质量评价我们都细心聆听，心存感激。IT帮手网是是威客和雇主最信赖的威客网站，我们深知没有雇主和威客两头带动，网站的前行，将会是一句空话。您给我们一句话的点评，对我们就是个很大的鼓舞。';


$page_obj = $kekezu->_page_obj;  
$page and $page = intval( $page ) or $page = 1;
$url = $_K['siteurl']."/index.php?do=review";

$comment_obj = new Keke_witkey_comment_class();
$where = " obj_type = 'ask' and p_id = 0";
$where .= " order by on_time DESC ";

$comment_obj->setWhere ( $where ); 
$count = $comment_obj->count_keke_witkey_comment();
$opp=='remote' and $limit=20 or $limit=15;
$pages = $page_obj->getPages ( $count, $limit, $page, $url );

$comment_obj->setWhere ( $where . $pages [where] );
$comment_arr = $comment_obj->query_keke_witkey_comment(); 

require keke_tpl_class::template ( $do );

function get_comment_ask_reply($p_id){
	$arr = db_factory::query(" select * from " . TABLEPRE . "witkey_comment where p_id = '$p_id' ORDER BY on_time DESC");
	
	return $arr;
}