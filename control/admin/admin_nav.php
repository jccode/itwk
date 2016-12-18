<?php
/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19下午09:25:13
 */
defined ( 'ADMIN_KEKE' )  or 	exit ( 'Access Denied' );

/**后台全局菜单信息**/
$menu_conf = $admin_obj->get_admin_menu();

/**子菜单列表**/
$sub_menu_arr = $menu_conf['menu'];
require  $template_obj->template ( 'control/admin/tpl/admin_' . $do );