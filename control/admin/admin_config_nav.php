<?php
/**
 * 导航菜单配置
 * @author S
 * @version v 2.0
 * 2011-12-13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 41 );

$nav_list = kekezu::get_table_data ( '*', 'witkey_nav', '', 'listorder', '', '', "nav_id");

$nav_obj = new Keke_witkey_nav_class ();

$table_obj = new keke_table_class ( "witkey_nav" );

$url = "index.php?do=$do&view=$view";

//是否编辑
if ($submit) {
	
	$res = 0;
	 //添加导航
	if ($ruleitem ['new'])
		foreach ( $ruleitem ['new'] as $value ) {
			if ($value ['nav_title'] && $value ['nav_url']) {
				$nav_obj->_nav_id = NULL;
				$nav_obj->setNav_title ( $value ['nav_title'] );
				$nav_obj->setNav_url ( $value ["nav_url"] );
				$nav_obj->setNav_style ( $value ["nav_style"] );
				$nav_obj->setIshide ( $value ["ishide"] ? $value ["ishide"] : 0 );
				$nav_obj->setNewwindow ( $value ["newwindow"] ? 1 : 0 );
				$value ["listorder"] ? $nav_obj->setListorder ( $value ["listorder"] ) : "";
				$res += $nav_obj->create_keke_witkey_nav ();
			
			}
			kekezu::admin_system_log ( $_lang['create_nav'] );
		}
	 //编辑导航
	if ($ruleitem ['old']) {
		foreach ( $ruleitem ['old'] as $key => $value ) {
			$nav_obj->_nav_id = NULL;
			$nav_obj->setWhere ( "nav_id = {$key}" );
			$nav_obj->setNav_title ( $value ['nav_title'] );
			$nav_obj->setNav_url ( $value ["nav_url"] );
			$nav_obj->setNav_style ( $value ["nav_style"] );
			$nav_obj->setIshide ( $value ["ishide"] ? $value ["ishide"] : 0 );
			$nav_obj->setNewwindow ( $value ["newwindow"] ? 1 : 0 );
			$value ["listorder"] ? $nav_obj->setListorder ( $value ["listorder"] ) : "";
			$res += $nav_obj->edit_keke_witkey_nav ();
		}
	}
	kekezu::admin_system_log ( $_lang['edit_nav'] );
	$nav_list = kekezu::get_table_data ( "*", "witkey_nav", "", "listorder", "nav_id" );
	if ($res) {
		kekezu::admin_show_msg ( $_lang['custom_nav_set_success'], "index.php?do=config&view=nav",3,'','success' );
	} else {
		kekezu::admin_show_msg ( $_lang['no_change'], "index.php?do=config&view=nav",3,'','warning' );
	}
}
//删除导航
if ($ac == 'del') {
	$table_obj->del ( 'nav_id', $nav_id, $url );
	kekezu::admin_show_msg ($_lang['delete_nav_success'], "index.php?do=config&view=nav",3,'','success' );
}

require $template_obj->template ( 'control/admin/tpl/admin_config_' . $view );