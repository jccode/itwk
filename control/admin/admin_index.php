<?php
/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19下午09:25:13
 */
defined ( 'ADMIN_KEKE' ) or 	exit ( 'Access Denied' );

/**后台全局菜单信息**/
$menu_conf = $admin_obj->get_admin_menu();

/**子菜单列表**/
$sub_menu_arr = $menu_conf['menu'];
/**快捷方式列表**/
$fast_menu_arr=$admin_obj->get_shortcuts_list();

/***锁屏状态判断***/
$check_screen_lock=$admin_obj->check_screen_lock();
/**动作集**/


switch ($ac){
	case "nav_search"://导航搜索
			$nav_search=$admin_obj->search_nav($keyword);
			require $kekezu->_tpl_obj->template("control/admin/tpl/admin_" .$ac);
			die();
		break;
	case "lock":
		$admin_obj->screen_lock();//锁屏
		break;
	case "unlock":
		$unlock_times=$admin_obj->times_limit($unlock_num);//允许登录尝试次数
		$admin_obj->screen_unlock($unlock_num,$unlock_pwd);//解屏
		require $kekezu->_tpl_obj->template("control/admin/tpl/admin_screen_lock");
		die();
		break;
	case "add_shortcuts":
		$admin_obj->add_fast_menu($r_id);
		break;
	case "rm_shortcuts":
		$admin_obj->rm_fast_menu($r_id);
		break;
}

require $template_obj->template('control/admin/tpl/admin_'.$do);