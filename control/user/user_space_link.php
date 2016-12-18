<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2011-12-25 下午   2:32:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

/**店铺链接记录**/
$link_obj = new Keke_witkey_link_class ();
$page_obj = $kekezu->_page_obj;
intval ( $page ) or $page = '1';
intval ( $page_size ) or $page_size = '8';
$url = $ac_url."&page_size=$page_size&page=$page";

switch ($ac) {
	case "del" :
		$link_obj->setWhere ( " obj_type = 'shop' AND obj_id = '$shop_info[shop_id]' AND link_id = '$link_id'" );
		$res = $link_obj->del_keke_witkey_link();
		$res and kekezu::show_msg($_lang['delete_success'],$ac_url,3,'','success') or kekezu::show_msg($_lang['delete_fail'],$ac_url,3,'','warning');
		break;
	case "edit" :
		$link_obj->setWhere ( " link_id='$link_id'" );
		CHARSET == 'gbk' and $link[link_name] = kekezu::utftogbk ( $link[link_name] );
		$link_obj->setLink_name ( $link[link_name] );
		$link_obj->setLink_url ( $link[link_url] );
		$res = $link_obj->edit_keke_witkey_link ();
		$res and kekezu::show_msg($_lang['edit_success'],$ac_url,3,'','success') or kekezu::show_msg($_lang['edit_fail'],$ac_url,3,'','warning');
		break;
	case "add" : 
		$link_obj->_link_id = null;
		CHARSET == 'gbk' and $link[link_name] = kekezu::utftogbk ( $link[link_name] );
		$link_obj->setLink_name ( $link[link_name] );
		$link_obj->setLink_status ( 1 );
		$link_obj->setLink_url ( $link[link_url] );
		$link_obj->setObj_id ( $shop_info ['shop_id'] );
		$link_obj->setObj_type ( "shop" );
		$link_obj->setOn_time ( time () );
		$res = $link_obj->create_keke_witkey_link ();
		$res and kekezu::show_msg($_lang['add_success'],$ac_url,3,'','success') or kekezu::show_msg($_lang['add_fail'],$ac_url,3,'','warning');
		break;
}

$where = " obj_type='shop' and obj_id='{$shop_info['shop_id']}' order by link_id desc";

$link_obj->setWhere ( $where );
$count = intval ( $link_obj->count_keke_witkey_link () );
$pages = $page_obj->getPages ( $count, $page_size, $page, $url, "#userCenter" );

$link_obj->setWhere ( $where . $pages ['where'] );
$shop_link = $link_obj->query_keke_witkey_link ();

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );