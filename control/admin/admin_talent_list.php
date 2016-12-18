<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

// kekezu::admin_check_role ( 205 );

kekezu::get_tree ( $kekezu->_indus_arr, $indus_option_arr, "option", $catid );

$s = array();
$ps = $ps ? intval($ps) : 10;
$page = $page ? intval($page) : 1;
$keyword = trim($keyword);
$cat = $kekezu->_indus_arr;
$url = 'index.php?do=' . $do;

$sql = 'SELECT a.*, b.* FROM ' . TABLEPRE . 'witkey_talent_link a, ' . TABLEPRE . 'witkey_shop b ';
$where = ' WHERE a.uid = b.uid ';

if ( $condit && $keyword ) {

    switch( $condit ){
            case 'uid':
                $where .= sprintf(' AND b.uid = %d', $keyword);
                break;

            case 'username': 
                $where .= ' AND b.username LIKE "%' . $keyword . '%"';
                break;

            case 'shopname': 
               $where .= ' AND b.shop_name LIKE "%' . $keyword . '%"';
                break;    
    }
    $url .= '&' . $condit . '=' . $keyword;
}
if ( isset($catid) && $catid != '--' ) {
    $where .= ' AND a.catid = ' . $catid;
    $url .= '&catid=' . $catid;
}

$sql2 = 'SELECT COUNT(*) FROM ' . TABLEPRE . 'witkey_talent_link a, ' . TABLEPRE . 'witkey_shop b ' . $where;

$count = db_factory::get_count( $sql2 );

if ( $order ) {
    $where .= ' ORDER BY ' . $order[0] . ' ' . $order[1];
    $url .= '&order[]=' . $order[0] . '&order[]=' . $order[1];
} else {
    $where .= ' ORDER BY a.tid DESC';
}

$pages = $kekezu->_page_obj->getPages ( $count, $ps, $page, $url );
$sql .= $where . $pages['where'];

$list = db_factory::query($sql);

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );