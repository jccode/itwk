<?php
/**
 * @copyright keke-tech
 * @author tank
 * 
 * 2012-5-23 
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('config','record');

$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'weibo';

if (file_exists ( ADMIN_ROOT . 'admin_vip_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_vip_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}