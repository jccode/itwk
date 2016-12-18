<?php
/**
 * 后台财务入口路由
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-21下午10:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$fina_action_arr = keke_glob_class::get_finance_action();

$views = array ('withdraw', 'report', 'all','analysis','recharge','invoice','invoice_info');

(in_array ( $view, $views )) or  $view ='all';

if (file_exists ( ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}
