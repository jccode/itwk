<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$title = "地理位置";
$area and $area = urldecode($area);
$address and $address = urldecode($address);

switch($ac){
	case 'task':
		require $kekezu->_tpl_obj->template ( 'ajax/ajax_map_task' );
	break;
	case 'service':
		require $kekezu->_tpl_obj->template ( 'ajax/ajax_map_service' );
	break;
	default:
		require $kekezu->_tpl_obj->template ( 'ajax/ajax_map' );
	break;
}