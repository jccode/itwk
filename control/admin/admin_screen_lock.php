<?php
/**
 * 后台锁屏
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-08-30 09:51:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
switch ($ac){
	case "lock":
		$admin_obj->screen_lock();
		break;
	case "unlock":
		$admin_obj->screen_unlock($unlock_times,$unlock_pwd);
		break;
		
}
require $kekezu->_tpl_obj->template("control/admin/tpl/admin_" .$do);