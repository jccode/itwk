<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * @todo 后台认证顶级路由
 * 2011-9-01 11:35:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
keke_lang_class::package_init("auth");
keke_lang_class::loadlang("{$do}_{$view}");

$views = array ('item_list', 'info', 'list', 'item_edit' );

$view = (! empty ( $view ) && in_array ( $view, $views )) ? $view : 'item_list';
if (file_exists ( ADMIN_ROOT . 'admin_' . $do . '_' . $view . '.php' )) {
	keke_lang_class::package_init ( "auth" );
	keke_lang_class::loadlang ( "admin_$view" );
	if (! $auth_dir) { //在后台进行认证项安装时会传递此变量。所以以此来作为是否是安装动作的区别
		/**
		 *认证初始化
		 */
		$auth_item_list = keke_auth_base_class::get_auth_item (); //获取认证信息
		$keys = array_keys ( $auth_item_list );
		$auth_code or $auth_code = $keys ['0']; //默认认证项
		
		if($auth_item_list[$auth_code]){
			$auth_class = "keke_auth_" . $auth_code . "_class";
			
			$auth_obj = new $auth_class ( $auth_code ); //初始化认证对象
			
			$auth_item = $auth_item_list [$auth_code]; //获取单项认证配置信息
			$auth_dir = $auth_item ['auth_dir']; //认证文件夹路径
			keke_lang_class::loadlang ( $auth_dir );
		}else{
			kekezu::show_msg($_lang['illegal_parameter_or_authmadel_delete'],"index.php?do=auth&view=item_list",3,'','warning');
		}
	}
	require ADMIN_ROOT . 'admin_' . $do . '_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'',3,'','warning' );
}