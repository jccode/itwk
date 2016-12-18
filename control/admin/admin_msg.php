<?php
/**
 * @copyright keke-tech
 * @author Chen 更新2012-06-13 wrh 
 * @version v 1.4
 * 2011-9-19上午10:15:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('weibo','config', 'send', 'internal','intertpl','attention','map','list','edit','sms_list','email_list');

$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'weibo';

if (file_exists ( ADMIN_ROOT . 'admin_msg_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_msg_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}