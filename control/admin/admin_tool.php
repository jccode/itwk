<?php
/**
 * 系统工具入口路由
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19下午09:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

//echo  trim($view);
$views = array ('dbbackup', 'dbrestore','dboptim', 'cache', 'file', 'log','payitem','video','video_edit','special','special_edit','statistics');
 
$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'log';
if (file_exists ( ADMIN_ROOT . 'admin_' . $do . '_' . $view . '.php' )) {
	require_once ADMIN_ROOT . 'admin_' . $do . '_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning');
}