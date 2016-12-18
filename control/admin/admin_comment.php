<?php
/**
 * 后台comment入口
 * @copyright keke-tech
 * @author Shangk
 * @version v 2.0
 * 2012-5-16下午02:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$views = array ('task', 'article', 'shop','suggest','ask','work');
$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'task';
if (file_exists ( ADMIN_ROOT . 'admin_comment_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_comment_' . $view . '.php';
} else {
	kekezu::admin_show_msg ($_lang['404_page'],'',3,'','warning');
}
