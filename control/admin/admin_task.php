<?php
/**
 * 后台task入口路由
 * @copyright keke-tech
 * @author Michael
 * @version v 2.0
 * 2010-5-17下午02:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$views = array ('industry', 'industry_edit','ext_add', 'skill', 'skill_edit','comment','report','tpl' ,'mail','custom','union_industry','check_comment','all_list','unpublished_list','unpublished_edit','unpublished_user','track');
$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'industry';
if (file_exists ( ADMIN_ROOT . 'admin_task_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_task_' . $view . '.php';
} else {
	kekezu::admin_show_msg ($_lang['404_page'],'',3,'','warning');
}
