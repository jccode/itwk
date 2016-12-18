<?php
/**
 * 推广中心入口
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 1.3
 * 2011-07-09 14:35:20
 */

 defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );

$views = array ('config','item','event','prom_rule','item_edit','edit_event','relation_add','relation');

$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'config';

if ( file_exists ( ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php' ) ) {
	//$prom_arr = $kekezu->_prom_obj->_prom_rule_config_info; //定义事件数组
	require ADMIN_ROOT . 'admin_'.$do.'_' . $view . '.php';
} else {
	kekezu::admin_show_msg ($_lang['404_page'],'',3,'','warning' );
}