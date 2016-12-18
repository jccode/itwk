<?php
/**
 * 后台服务管理入口
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2010-6-14下午10:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('list','edit');

(in_array ( $view, $views )) or  $view ='list';

if (file_exists ( ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}