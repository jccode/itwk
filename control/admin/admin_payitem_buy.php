<?php
/**
 * 增值服务购买记录，统计服务销量，分类查询各威客与顾主的购买记录
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-11-5-下午02:03:21
 * @version V2.0
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role (139);

$add_service_type = keke_glob_class::get_value_add_type ();

$buy_use_type = array ("buy" => $_lang['buy'], "spend" => $_lang['spend'] );

$payitem_record_obj = new Keke_witkey_payitem_record_class ();

$url = "index.php?do=$do&view=$view&w[record_id]=$w[record_id]&w[username]=$w[username]&w[use_type]=$w[use_type]&w[item_code]=$w[item_code]&w[ord]=$w[ord]&page=$page&w[page_size]=$page_size";

//查询
$where = ' 1 = 1';
$w [record_id] and $where .= " and record_id = " . $w [record_id];

$w [username] and $where .= " and username like '%$w[username]%'";

$w [use_type] and $where .= " and use_type like '%$w[use_type]%'";

$w [item_code] and $where .= " and item_code like '%$w[item_code]%'";

is_array($w['ord']) and $where .= ' order by '.$w['ord'][0].' '.$w['ord'][1];

//$w [ord] and $where .= " order by $w[ord]" or $where .= " order by record_id desc";

//用户购买总支出
$all_buy_sql = "select sum(use_cash) as cash from " . TABLEPRE . "witkey_payitem_record where use_type='buy'";
$all_buy_pro = db_factory::query ( $all_buy_sql );
$all_buy_pro = $all_buy_pro [0] ? $all_buy_pro [0] : 0;

$page_obj = $kekezu->_page_obj;
$page = intval ( $page ); 
$page or $page = 1;
$w [page_size] and $page_size = intval ( $w [page_size] ) or $page_size = 10;

$payitem_record_obj->setWhere ( $where );
$count = $payitem_record_obj->count_keke_witkey_payitem_record ();
$page_obj->setAjax(1);
$page_obj->setAjaxDom("ajax_dom");
$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
$where .= $pages [where];
$payitem_record_obj->setWhere ( $where );
$payitem_record_arr = $payitem_record_obj->query_keke_witkey_payitem_record ();

require $template_obj->template ( 'control/admin/tpl/admin_payitem_' . $view );
 
